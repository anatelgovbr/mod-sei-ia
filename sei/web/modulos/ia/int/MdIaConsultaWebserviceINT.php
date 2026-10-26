<?php

require_once dirname(__FILE__) . '/../../../SEI.php';
require_once dirname(__FILE__) . '/../rn/MdIaAutenticacaoSSLRN.php';


class MdIaConsultaWebserviceINT extends InfraRN
{
    const TIME_OUT = '900000';
    const MAX_CORPO_ERRO = 4000;
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
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatEventRN = new MdIaInteracaoChatEventRN();
        $topico = null;
        $urlEndpoint = '';
        $httpcode = 500;
        $metadataString = [];
        $erroStream = [];
        $corpoBrutoErro = '';
        $numIdTopicoInteracao = null;
        $strInputPromptInteracao = '';

        try {
            $this->inicializar();

            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatDTO->retStrInputPrompt();
            $objMdIaInteracaoChatDTO->retNumIdMdIaTopicoChat();
            $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);
            $strInputPromptInteracao = (string)$interacao->getStrInputPrompt();
            $numIdTopicoInteracao = $interacao->getNumIdMdIaTopicoChat();

            $objMdIaTopicoChatRN = new MdIaTopicoChatRN();
            $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
            $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat($numIdTopicoInteracao);
            $objMdIaTopicoChatDTO->retStrNomeUsuario();
            $objMdIaTopicoChatDTO->retStrSiglaUsuario();
            $topico = $objMdIaTopicoChatRN->consultar($objMdIaTopicoChatDTO);

            $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
            $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();
            $urlEndpoint = $urlApi['linkEndpoint'];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $urlEndpoint,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: text/event-stream',
                ],
                CURLOPT_POSTFIELDS => $interacao->getStrInputPrompt(),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);
            MdIaAutenticacaoSSLRN::configurarAutenticacaoSSL($curl);

            $respostaParcial = '';
            $reasoningParcial = '';
            $objMdIaInteracaoChatEventoDtoReasoning = null;

            curl_setopt($curl, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$respostaParcial, &$reasoningParcial, &$objMdIaInteracaoChatEventoDtoReasoning, &$metadataString, &$erroStream, &$corpoBrutoErro, $objMdIaInteracaoChatRN, $objMdIaInteracaoChatEventRN, $idInteracao) {
                $linhas = explode("\n", $data);

                $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
                $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);

                foreach ($linhas as $linha) {
                    $linha = trim($linha);
                    if ($linha === '') {
                        continue;
                    }

                    if (substr($linha, 0, 1) === ':') {
                        continue;
                    }

                    if (substr($linha, 0, 5) === 'data:') {
                        $json = trim(substr($linha, 5));
                        if ($json === '') {
                            continue;
                        }

                        $payload = json_decode($json, true);
                        if (!is_array($payload) || !isset($payload['type'])) {
                            continue;
                        }

                        switch ($payload['type']) {
                            case 'status':
                                $status = isset($payload['data']) ? (string)$payload['data'] : '';
                                $status = trim($status);
                                if ($status !== '') {
                                    $status = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $status);
                                    $objMdIaInteracaoChatEventRN->registrarStatusInteracao((int)$idInteracao, $status);
                                }
                                break;

                            case 'reasoning':
                                $token = isset($payload['data']) ? (string)$payload['data'] : '';
                                if ($token !== '') {
                                    $token = mb_convert_encoding($token, 'HTML-ENTITIES', 'UTF-8');
                                    $reasoningParcial .= iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $token);

                                    if ($objMdIaInteracaoChatEventoDtoReasoning === null) {
                                        $objMdIaInteracaoChatEventoDtoReasoning = $objMdIaInteracaoChatEventRN->registrarReasoningInteracao((int)$idInteracao, $reasoningParcial);
                                    } else {
                                        $objMdIaInteracaoChatEventoDtoReasoning->setStrConteudo($reasoningParcial);
                                        $objMdIaInteracaoChatEventRN->alterar($objMdIaInteracaoChatEventoDtoReasoning);
                                    }
                                }
                                break;

                            case 'content':
                                $token = $payload['data'] ?? '';
                                if ($token !== '') {
                                    $token = mb_convert_encoding($token, 'HTML-ENTITIES', 'UTF-8');
                                    $respostaParcial .= iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $token);
                                    $objMdIaInteracaoChatDTO->setStrResposta($respostaParcial);
                                    $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
                                }
                                break;

                            case 'metadata':
                                $metadataString = $payload['data'] ?? [];
                                if ($metadataString) {
                                    if (isset($metadataString['id_message'])) {
                                        $objMdIaInteracaoChatDTO->setNumIdMessage($metadataString['id_message']);
                                    }
                                    if (isset($metadataString['usage']['total_tokens'])) {
                                        $objMdIaInteracaoChatDTO->setNumTotalTokens($metadataString['usage']['total_tokens']);
                                    }
                                    $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
                                }
                                break;

                            case 'error':
                                $erroStream = $payload;
                                break;

                            case 'end':
                                break;
                        }
                    } else {
                        if (strlen($corpoBrutoErro) < self::MAX_CORPO_ERRO) {
                            $corpoBrutoErro .= $linha . "\n";
                        }
                    }
                }

                return strlen($data);
            });

            curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $numeroErroCurl = curl_errno($curl);
            $mensagemErroCurl = curl_error($curl);
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $tempoExecucao = str_replace(',', '.', $this->numSeg);
            $tempoExecucaoInteiro = intval($tempoExecucao);
            curl_close($curl);

            $codigoRetorno = $httpcode;
            if ($httpcode === 200 && !empty($erroStream['status_code'])) {
                $codigoRetorno = intval($erroStream['status_code']);
            }

            if ($codigoRetorno !== 200 && empty($erroStream['detail'])) {
                if (!empty($erroStream['message'])) {
                    $erroStream['detail'] = $erroStream['message'];
                } else if (!empty($erroStream['error'])) {
                    $erroStream['detail'] = is_array($erroStream['error']) ? json_encode($erroStream['error']) : $erroStream['error'];
                } else if ((int)$httpcode === 0 && $numeroErroCurl !== 0) {
                    $erroStream['detail'] = 'cURL(' . $numeroErroCurl . '): ' . $mensagemErroCurl;
                } else if ($corpoBrutoErro !== '') {
                    $erroStream['detail'] = 'HTTP ' . $codigoRetorno . ': ' . trim($corpoBrutoErro);
                }
            }

            if ($codigoRetorno !== 200) {
                try {
                    $this->tratarCodigoDiferente200($metadataString, $erroStream, $topico, $urlEndpoint, $codigoRetorno, $idInteracao, $tempoExecucaoInteiro, $numIdTopicoInteracao, $strInputPromptInteracao);
                } catch (Exception $e) {
                    LogSEI::getInstance()->gravar('Falha no tratarCodigoDiferente200 para interacao ' . $idInteracao . '. Erro: ' . $e->getMessage(), InfraLog::$ERRO);
                }
            }

            $dtoFinal = new MdIaInteracaoChatDTO();
            $dtoFinal->setNumIdMdIaInteracaoChat($idInteracao);
            $dtoFinal->setNumTempoExecucao($tempoExecucaoInteiro);
            $dtoFinal->setNumStatusRequisicao($codigoRetorno);
            $objMdIaInteracaoChatRN->alterar($dtoFinal);
        } catch (Exception $e) {
            if (!empty($this->numSeg)) {
                $tempoExecucao = str_replace(',', '.', InfraUtil::verificarTempoProcessamento($this->numSeg));
                $tempoExecucaoInteiro = intval($tempoExecucao);
            } else {
                $tempoExecucaoInteiro = 0;
            }

            $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();

            $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $erroStream = ['detail' => $e->getMessage()];
            try {
                $arrMensagemApresentadaUsuario = $this->tratarCodigoDiferente200($metadataString, $erroStream, $topico, $urlEndpoint, 500, $idInteracao, $tempoExecucaoInteiro, $numIdTopicoInteracao, $strInputPromptInteracao);
                $objMdIaInteracaoChatDTO->setStrResposta($arrMensagemApresentadaUsuario['resposta']);
            } catch (Exception $eTratamento) {
                $objMdIaInteracaoChatDTO->setStrResposta('Tivemos um erro inesperado. O administrador do sistema ja foi informado sobre o erro.');
                LogSEI::getInstance()->gravar('Falha ao processar tratarCodigoDiferente200 no catch da interacao ' . $idInteracao . '. Erro original: ' . $e->getMessage() . '. Erro no tratamento: ' . $eTratamento->getMessage(), InfraLog::$ERRO);
            }
            $objMdIaInteracaoChatDTO->setNumTempoExecucao($tempoExecucaoInteiro);
            $objMdIaInteracaoChatDTO->setNumStatusRequisicao(500);
            $objMdIaInteracaoChatRN->alterar($objMdIaInteracaoChatDTO);
            return;
        }
    }

    private function tratarCodigoDiferente200($metadataString, $erroStream, $topico, $urlApi, $httpcode, $idInteracao, $tempoExecucaoInteiro, $numIdTopicoInteracao = null, $strInputPromptInteracao = '')
    {
        $this->removerArquivosAvulsosDoInputPrompt($strInputPromptInteracao);

        if (!empty($erroStream['detail'])) {
            $detalheErro = $erroStream['detail'];
            if (is_array($detalheErro)) {
                $detalheErro = print_r($detalheErro, true);
            }

            $resposta = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', (string)$detalheErro);
        } else if (isset($metadataString['choices'][0]['message']['content'])) {
            $resposta = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $metadataString['choices'][0]['message']['content']);
        } else {
            $resposta = 'Erro nao detalhado pela solucao de IA.';
        }

        $mensagemApresentadaUsuario = MdIaConfigAssistenteINT::retornaMensagemAmigavelUsuario($httpcode, $resposta);
        $strNomeUsuario = 'Nao informado';
        $strSiglaUsuario = 'Nao informado';

        if (is_object($topico)) {
            try {
                $strNomeUsuarioTopico = $topico->getStrNomeUsuario();
                if (!InfraString::isBolVazia($strNomeUsuarioTopico)) {
                    $strNomeUsuario = $strNomeUsuarioTopico;
                }

                $strSiglaUsuarioTopico = $topico->getStrSiglaUsuario();
                if (!InfraString::isBolVazia($strSiglaUsuarioTopico)) {
                    $strSiglaUsuario = $strSiglaUsuarioTopico;
                }
            } catch (Exception $e) {
            }
        }

        $strEndpoint = ($urlApi !== '') ? $urlApi : 'Não informado';

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $paramLiberarAutoAvaliacao = $objInfraParametro->getValor('MODULO_IA_LOGAR_WARNING', false);

        if ($mensagemApresentadaUsuario['tipoCritica'] == 'error' || $paramLiberarAutoAvaliacao) {
            $strAssunto = 'ERRO DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA';
            $log = "00001 - ERRO DE RECURSO NO ENVIO DE MENSAGEM DO ASSISTENTE DO SEI IA \n";
            $log .= '00002 - Usuário: ' . $strNomeUsuario . ' - Unidade: ' . $strSiglaUsuario . " \n";
            $log .= '00003 - Endpoint do Recurso: ' . $strEndpoint . " \n";
            $log .= '00004 - Tipo de Indisponibilidade: ' . $httpcode . " \n";
            $log .= '00005 - Mensagem retornada pelo Servidor: ' . $resposta . " \n";
            $log .= '00006 - Mensagem apresentada ao usuário: ' . $mensagemApresentadaUsuario['resposta'] . " \n";
            $log .= '00007 - ID da interação no SEI: ' . $idInteracao . " \n";
            $log .= '00008 - ID da interação na Solução de IA: ' . (!empty($metadataString['id_message']) ? $metadataString['id_message'] : 'Não informado') . " \n";
            $log .= '00009 - Data e hora: ' . InfraData::getStrDataHoraAtual() . " \n";
            $log .= '00010 - Tempo de Execução: ' . $tempoExecucaoInteiro . " segundos \n";
            $log .= "00011 - FIM \n";
            LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . '<' . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . '>';
            $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
            $strConteudo = nl2br($log);
            InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, '', '', $strAssunto, $strConteudo, 'text/html');
        }

        return $mensagemApresentadaUsuario;
    }

    private function removerArquivosAvulsosDoInputPrompt($strInputPromptInteracao)
    {
        if (InfraString::isBolVazia($strInputPromptInteracao)) {
            return;
        }

        $arrInputPrompt = json_decode($strInputPromptInteracao, true);
        if (!is_array($arrInputPrompt)) {
            $strInputPromptUtf8 = @iconv('ISO-8859-1', 'UTF-8//IGNORE', (string)$strInputPromptInteracao);
            if (!InfraString::isBolVazia($strInputPromptUtf8)) {
                $arrInputPrompt = json_decode($strInputPromptUtf8, true);
            }
        }

        if (!is_array($arrInputPrompt) || empty($arrInputPrompt['arquivos_avulsos']) || !is_array($arrInputPrompt['arquivos_avulsos'])) {
            return;
        }

        $arrIdsRemocao = [];
        foreach ($arrInputPrompt['arquivos_avulsos'] as $arrArquivoAvulso) {
            if (!is_array($arrArquivoAvulso) || !isset($arrArquivoAvulso['id_arquivo_avulso'])) {
                continue;
            }

            $numIdArquivoAvulso = intval($arrArquivoAvulso['id_arquivo_avulso']);
            if ($numIdArquivoAvulso <= 0 || in_array($numIdArquivoAvulso, $arrIdsRemocao, true)) {
                continue;
            }

            $arrIdsRemocao[] = $numIdArquivoAvulso;
            try {
                $objUploadDTO = new MdIaInteracaoArqAvulsoDTO();
                $objUploadDTO->setNumIdMdIaInteracaoArqAvulso($numIdArquivoAvulso);
                $objUploadDTO->retStrNomeTemporario();
                $objUploadDTO = (new MdIaInteracaoArqAvulsoRN())->consultar($objUploadDTO);

                if (!is_null($objUploadDTO)) {
                    $strCaminhoArquivo = DIR_SEI_TEMP . '/' . $objUploadDTO->getStrNomeTemporario();
                    if (file_exists($strCaminhoArquivo)) {
                        @unlink($strCaminhoArquivo);
                    }
                }
            } catch (Exception $e) {
                LogSEI::getInstance()->gravar('Falha ao remover arquivo avulso no tratamento de erro da IA. IdArquivoAvulso=' . $numIdArquivoAvulso . '. Erro: ' . $e->getMessage(), InfraLog::$AVISO);
            }
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
