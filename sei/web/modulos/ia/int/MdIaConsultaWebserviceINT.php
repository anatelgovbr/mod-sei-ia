<?php

require_once dirname(__FILE__) . '/../../../SEI.php';


class MdIaConsultaWebserviceINT extends InfraRN
{
    const TIME_OUT = '900000';
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
                CURLOPT_URL => $urlApi['linkEndpoint'],
                CURLOPT_RETURNTRANSFER => false, // streaming
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: text/event-stream',
                ],
                CURLOPT_POSTFIELDS => $interacao->getStrInputPrompt(),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);

            $respostaParcial = '';
            $metadataString = [];
            $erroStream = [];

            // -----------------------------------------
            // CALLBACK: Processa o streaming linha a linha
            // -----------------------------------------
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$respostaParcial, &$metadataString, &$erroStream, $objMdIaInteracaoChatRN, $idInteracao) {

                $linhas = explode("\n", $data);

                $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
                $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);

                foreach ($linhas as $linha) {
                    $linha = trim($linha);
                    if ($linha === '') continue;

                    if (substr($linha, 0, 1) === ':') continue;

                    if (substr($linha, 0, 5) === 'data:') {

                        $json = trim(substr($linha, 5));
                        if ($json === '') continue;

                        $payload = json_decode($json, true);
                        if (!is_array($payload) || !isset($payload['type'])) {
                            continue;
                        }

                        switch ($payload['type']) {

                            case 'content':
                                $token = $payload['data'] ?? '';
                                if ($token !== '') {
                                    $token = mb_convert_encoding($token, 'HTML-ENTITIES', 'UTF-8');
                                    $respostaParcial .= iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $token);
                                    $objMdIaInteracaoChatDTO->setStrResposta($respostaParcial);
                                    $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
                                }
                                break;

                            case 'metadata':
                                $metadataString = $payload['data'] ?? [];

                                if ($metadataString) {
                                    $objMdIaInteracaoChatDTO->setNumIdMessage($metadataString['id_message']);
                                    $objMdIaInteracaoChatDTO->setNumTotalTokens($metadataString['usage']['total_tokens']);
                                    $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
                                }
                                break;

                            case 'error':
                                $erroStream = $payload;
                                break;

                            case 'end':
                                break;
                        }
                    }
                }

                return strlen($data);
            });

            curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $tempoExecucao = str_replace(",", ".", $this->numSeg);
            $tempoExecucaoInteiro = intval($tempoExecucao);
            curl_close($curl);

            $codigoRetorno = $httpcode;

            if ($httpcode === 200 && !empty($erroStream['status_code'])) {
                $codigoRetorno = intval($erroStream['status_code']);
            }

            if ($codigoRetorno !== 200) {
                $this->tratarCodigoDiferente200($metadataString, $erroStream, $topico, $urlApi['linkEndpoint'], $codigoRetorno, $idInteracao, $tempoExecucaoInteiro);
            }

            // Atualiza final com sucesso
            $dtoFinal = new MdIaInteracaoChatDTO();
            $dtoFinal->setNumIdMdIaInteracaoChat($idInteracao);
            $dtoFinal->setNumTempoExecucao($tempoExecucaoInteiro);
            $dtoFinal->setNumStatusRequisicao($codigoRetorno);
            $objMdIaInteracaoChatRN->alterar($dtoFinal);
        } catch (Exception $e) {

            $tempoExecucao = microtime(true) - ($inicio ?? microtime(true));
            $tempoExecucaoInteiro = intval($tempoExecucao);

            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatDTO->setStrResposta("Ocorreu um erro no Assistente de IA.");
            $objMdIaInteracaoChatDTO->setNumTempoExecucao($tempoExecucaoInteiro);

            $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);

            LogSEI::getInstance()->gravar("Erro no streaming IA: " . $e->getMessage(), InfraLog::$ERRO);
        }
    }

    private function tratarCodigoDiferente200($metadataString, $erroStream, $topico, $urlApi, $httpcode, $idInteracao, $tempoExecucaoInteiro)
    {
        if (!empty($erroStream['detail'])) {
            $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $erroStream['detail']);
        } else if (isset($metadataString['choices'][0]['message']['content'])) {
            $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $metadataString['choices'][0]['message']['content']);
        } else {
            $resposta = 'Erro nao detalhado pela solucao de IA.';
        }

        $mensagemApresentadaUsuario = MdIaConfigAssistenteINT::retornaMensagemAmigavelUsuario($httpcode, $resposta);

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $paramLiberarAutoAvaliacao = $objInfraParametro->getValor('MODULO_IA_LOGAR_WARNING', false);

        if ($mensagemApresentadaUsuario["tipoCritica"] == "error" || $paramLiberarAutoAvaliacao) {
            $strAssunto = "ERRO DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA";
            $log = "00001 - ERRO DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA \n";
            $log .= "00002 - Usuario: " . $topico->getStrNomeUsuario() . " - Unidade: " . $topico->getStrSiglaUsuario() . " \n";
            $log .= "00003 - Endpoint do Recurso: " . $urlApi . " \n";
            $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
            $log .= "00005 - Mensagem retornada pelo Servidor: " . $resposta . " \n";
            $log .= "00006 - Mensagem apresentada ao usuário: " . $mensagemApresentadaUsuario["resposta"] . " \n";
            $log .= "00007 - ID da interação no SEI: " . $idInteracao . " \n";
            $log .= "00008 - ID da interação na Solução de IA: " . (!empty($metadataString['id_message']) ? $metadataString['id_message'] : 'Nao informado') . " \n";
            $log .= "00009 - Data e hora: " . InfraData::getStrDataHoraAtual() . " \n";
            $log .= "00010 - Tempo de Execução: " . $tempoExecucaoInteiro . " segundos \n";
            $log .= "00011 - FIM \n";
            LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
            $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
            $strConteudo = nl2br($log);
            InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo, 'text/html');
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
    echo (InfraException::inspecionar($e));
    try {
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}
