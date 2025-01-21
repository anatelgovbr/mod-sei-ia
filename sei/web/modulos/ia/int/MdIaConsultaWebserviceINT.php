<?php

require_once dirname(__FILE__) . '/../../../SEI.php';


class MdIaConsultaWebserviceINT extends InfraRN
{
    const TIME_OUT = '600000';
    private $numSeg = 0;

    public function __construct()
    {
        parent::__construct();
    }

    private function inicializar()
    {
        session_start();
        SessaoSEI::getInstance(false);

        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        ini_set('mysql.connect_timeout', '216000');
        ini_set('default_socket_timeout', '216000');
        ini_set('allow_persistent', '1');
        @ini_set('implicit_flush', '1');
        set_time_limit(0);
        ob_implicit_flush();

       $this->numSeg = InfraUtil::verificarTempoProcessamento();

    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function enviarMensagemAssistenteIaConectado($idInteracao)
    {
        try {
            $this->inicializar();

            $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatDTO->retStrInputPrompt();
            $objMdIaInteracaoChatDTO->retNumIdMdIaTopicoChat();
            $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

            $objMdIaTopicoChatRN = new MdIaTopicoChatRN();
            $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
            $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat($interacao->getNumIdMdIaTopicoChat());
            $objMdIaTopicoChatDTO->retStrNomeUsuario();
            $objMdIaTopicoChatDTO->retStrSiglaUsuario();
            $topico = $objMdIaTopicoChatRN->consultar($objMdIaTopicoChatDTO);

            $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
            $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $urlApi["linkEndpoint"],
                CURLOPT_POSTFIELDS => $interacao->getStrInputPrompt(),
                CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Content-Length: ' . strlen($interacao->getStrInputPrompt())),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);

            $response = curl_exec($curl);

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $idMensagem = "";
            $totalTokens = "";
            if ($httpcode == "200") {
                $objResponse = json_decode($response);
                $response = mb_convert_encoding($objResponse->choices[0]->message->content, 'HTML-ENTITIES', 'UTF-8');
                $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $response);
                $idMensagem = $objResponse->id_message;
                $totalTokens = $objResponse->usage->total_tokens;
            }  else {
                $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $response);
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA \n";
                $log .= "00002 - Usuario: " . $topico->getStrNomeUsuario() . " - Unidade: " . $topico->getStrSiglaUsuario() . " \n";
                $log .= "00003 - Endpoint do Recurso: " . $urlApi["linkEndpoint"] . " \n";
                $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00005 - Mensagem retornada pelo Servidor: " . utf8_decode($resposta) . " \n";
                $log .= "00006 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
            }

            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);

            // Substitui a vírgula por um ponto
            $tempoExecucao = str_replace(",", ".", $this->numSeg);

            // Converte o número para um inteiro
            $tempoExecucaoInteiro = intval($tempoExecucao);

            $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatDTO->setStrResposta($resposta);
            $objMdIaInteracaoChatDTO->setNumIdMessage($idMensagem);
            $objMdIaInteracaoChatDTO->setNumTotalTokens($totalTokens);
            $objMdIaInteracaoChatDTO->setNumTempoExecucao($tempoExecucaoInteiro);
            $objMdIaInteracaoChatDTO->setNumStatusRequisicao($httpcode);
            $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
        } catch (Exception $e) {

            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);

            // Substitui a vírgula por um ponto
            $tempoExecucao = str_replace(",", ".", $this->numSeg);

            // Converte o número para um inteiro
            $tempoExecucaoInteiro = intval($tempoExecucao);

            $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();

            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatDTO->setStrResposta("Ocorreu um erro no Assistente de IA.");
            $objMdIaInteracaoChatDTO->setNumTempoExecucao($tempoExecucaoInteiro);
            $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);

            $log = "00001 - INDISPONIBILIDADE DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA \n";
            $log .= "00002 - Usuario: " . $topico->getStrNomeUsuario() . " - Unidade: " . $topico->getStrSiglaUsuario() . " \n";
            $log .= "00003 - Endpoint do Recurso: " . $urlApi["linkEndpoint"] . " \n";
            $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
            $log .= "00005 - Mensagem retornada pelo Servidor: " . utf8_decode($resposta) . " \n";
            $log .= "00006 - FIM \n";
            LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
            $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
            $strAssunto = "INDISPONIBILIDADE DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA";
            $strConteudo = $log;
            InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
            return false;
        }
    }
}

try {
    SessaoSEI::getInstance(false);
    BancoSEI::getInstance()->setBolScript(true);

    $idParametro = $argv[1];
    $objConsultaWebserviceINT = new MdIaConsultaWebserviceINT();

    return $objConsultaWebserviceINT->enviarMensagemAssistenteIa($idParametro);
} catch (Exception $e) {
    echo(InfraException::inspecionar($e));
    try {
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}