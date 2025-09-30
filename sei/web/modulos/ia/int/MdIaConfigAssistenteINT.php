<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 23/11/2022 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaConfigAssistenteINT extends InfraINT
{

    public static function montarCssChat()
    {
        $css = '';
        if (version_compare(VERSAO_INFRA, '2.29.0') >= 0 && $_REQUEST['acao'] == "editor_montar") {
            $css .= '<link rel="stylesheet" type="text/css" href="/infra_css/bootstrap/bootstrap-5.3.1.min.css" />';
            $css .= '<link rel="stylesheet" type="text/css" href="/infra_css//bootstrap/bootstrap-migracao-4-5.css" />';
        }
        $css .= '<link rel="stylesheet" type="text/css" href="modulos/ia/lib/highlight.js/atom-one-light.css" />';
        $css .= '<link rel="stylesheet" type="text/css" href="modulos/ia/css/md_ia_chat.css" />';
        if (version_compare(VERSAO_INFRA, '2.23.8') >= 0) {
            $css .= "
            <style>
                #btnInfraTopo {
                        bottom: 0.8rem;
                    }  
            </style>";
        }
        return $css;
    }

    public static function montarChat()
    {
        $retorno = self::montarHtmlChat();
        $retorno .= self::montarCssChat();

        return $retorno;
    }

    public static function montarHtmlChat()
    {
        ob_start();
        include 'modulos/ia/template/md_ia_html_chat.phtml';
        return ob_get_clean();
    }

    public static function htmlBotoesNovoChatChat()
    {
        ob_start();
        include 'modulos/ia/template/md_ia_html_chat_quebra_gelo.phtml';
        // captura tudo que foi impresso no buffer
        $html = ob_get_clean();

        // comprime espaços em branco (igual ao que você já tinha)
        return preg_replace('/\s+/', ' ', $html);
    }

    public static function enviarMensagemAssistenteIa($mensagem)
    {
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSystemPrompt();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $budget = MdIaConfigAssistenteINT::calcularConsumoDiarioToken();

        if ($budget["extrapolouLimiteTokens"]) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = mb_convert_encoding("O volume de conteúdo permitido nas interações diárias foi excedido. Tente novamente amanhã, quando o volume de conteúdo permitido para interação terá sido renovado.", 'UTF-8', 'ISO-8859-1');
            return json_encode($retorno);
        }
        $systemPrompt = $objMdIaAdmConfigAssistIADTO->getStrSystemPrompt();

        $systemPrompt = str_replace('@descricao_orgao_origem@', SessaoSEI::getInstance()->getStrDescricaoOrgaoUsuario(), $systemPrompt);
        $systemPrompt = str_replace('@sigla_orgao_origem@', SessaoSEI::getInstance()->getStrSiglaOrgaoSistema(), $systemPrompt);

        $dadosMensagem = array();
        $dadosMensagem["text"] = addslashes(urldecode($mensagem["text"]));
        $dadosMensagem["id_usuario"] = SessaoSEI::getInstance()->getNumIdUsuario();
        $dadosMensagem["system_prompt"] = addslashes(mb_convert_encoding($systemPrompt, 'UTF-8', 'ISO-8859-1'));
        $dadosMensagem["use_thinking"] = $mensagem["refletir"];
        $dadosMensagem["use_websearch"] = $mensagem["buscarWeb"];
        if ($mensagem["dadosCitacoes"] != "") {
            if (!is_null($mensagem["dadosCitacoes"][0]["relacaoProtocolos"])) {
                //QUANDO CITA PROCESSO ENTRA AQUI
                // decodifica o JSON que veio em relacaoProtocolos
                $protocolos = json_decode($mensagem["dadosCitacoes"][0]["relacaoProtocolos"], true);

                // array temporário para agrupar
                $procedimentosAgrupados = [];

                foreach ($protocolos as $proto) {
                    $idProc = $proto['id_procedimento'];

                    // se ainda não existe este procedimento, inicializa
                    if (!isset($procedimentosAgrupados[$idProc])) {
                        $procedimentosAgrupados[$idProc] = [
                            'id_procedimento' => $idProc,
                            'id_documentos'   => []
                        ];
                    }

                    // monta o documento (você pode remover o 'id_procedimento' interno se não precisar dele)
                    $doc = [
                        'id_documento'    => $proto['id_documento'],
                        'download_ext'    => isset($proto['download_ext']) ? $proto['download_ext'] : true
                    ];

                    // adiciona ao array de documentos daquele procedimento
                    $procedimentosAgrupados[$idProc]['id_documentos'][] = $doc;
                }

                // reindexa para descartar as chaves associativas
                $dadosMensagem['id_procedimentos'] = array_values($procedimentosAgrupados);
            } else {
                // QUANDO CITEI UM DOCUMENTO ENTRA AQUI
                // Array auxiliar para agrupar procedimentos
                $procedimentosAgrupados = [];

                foreach ($mensagem["dadosCitacoes"] as $dadosCitacoes) {
                    $idProc = $dadosCitacoes["idProcedimento"];

                    // Se ainda não existe esse procedimento, inicializa
                    if (!isset($procedimentosAgrupados[$idProc])) {
                        $procedimentosAgrupados[$idProc] = [
                            "id_procedimento"  => $idProc,
                            "id_documentos"    => []
                        ];
                    }

                    // Prepara o documento
                    $doc = [
                        "id_documento" => $dadosCitacoes["idDocumento"]["id_documento"],
                        "download_ext" => $dadosCitacoes["idDocumento"]["download_ext"],
                        'pag_doc_init'    => !empty($dadosCitacoes["idDocumento"]['pag_doc_init']) ? $dadosCitacoes["idDocumento"]['pag_doc_init'] : 0,
                        'pag_doc_end'    => !empty($dadosCitacoes["idDocumento"]['pag_doc_end']) ? $dadosCitacoes["idDocumento"]['pag_doc_end'] : 0
                    ];

                    // Adiciona ao array de documentos daquele procedimento
                    $procedimentosAgrupados[$idProc]["id_documentos"][] = $doc;
                }

                // Reindexa o array (remove as chaves pelos IDs dos procedimentos)
                $dadosMensagem["id_procedimentos"] = array_values($procedimentosAgrupados);
            }
        }

        $objMdIaTopicoChatINT = new MdIaTopicoChatINT();

        if ($mensagem["topicoTemporario"] == "true") {
            $objMdIaTopicoChatINT->adicionarTopico();
        }

        if (!SessaoSEI::getInstance()->isSetAtributo('MD_IA_ID_TOPICO_CHAT_IA')) {
            $idTopico = $objMdIaTopicoChatINT->consultarUltimoTopico();
            $dadosMensagem["id_topico"] = intval($idTopico);
            SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
        } else {
            $dadosMensagem["id_topico"] = intval(MdIaTopicoChatINT::verificaSessaoTopico());
        }
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($dadosMensagem["id_topico"]);
        $pergunta = mb_convert_encoding(urldecode($mensagem["text"]), 'HTML-ENTITIES', 'UTF-8');
        $pergunta = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $pergunta);
        $objMdIaInteracaoChatDTO->setStrPergunta($pergunta);
        $objMdIaInteracaoChatDTO->setStrInputPrompt(json_encode($dadosMensagem));
        $objMdIaInteracaoChatDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        $interacao = $objMdIaInteracaoChatRN->cadastrar($objMdIaInteracaoChatDTO);

        self::executarConsultaWebService($interacao->getNumIdMdIaInteracaoChat());

        return $interacao->getNumIdMdIaInteracaoChat();
    }

    public static function executarConsultaWebService($parametros)
    {
        $commandJob = 'php ' . dirname(__FILE__) . '/MdIaConsultaWebserviceINT.php ' . $parametros;
        $command = $commandJob . ' > /dev/null 2>&1 & echo $!';
        exec($command, $op);
    }

    public static function consultarDisponibilidadeApi()
    {
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();
        $dadosMensagem = array();
        $dadosMensagem["url"] = $urlApi['urlBase'] . $urlApi["linkConsultaDisponibilidade"];
        $retornoMensagem = array();
        $retornoMensagem["retornoApi"] = $objMdIaConfigAssistenteRN->consultarDisponibilidadeApi($dadosMensagem["url"]);
        $retornoMensagem["janelaContexto"] = $urlApi['janelaContexto'];
        return $retornoMensagem;
    }

    public static function calcularConsumoDiarioToken()
    {
        $retornoMensagem["extrapolouLimiteTokens"] = false;
        $retornoMensagem["quantidadeTokensUsados"] = 0;
        $retornoMensagem["tamanhoBudget"] = 0;

        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retNumLimiteGeralTokens();
        $objMdIaAdmConfigAssistIADTO->retNumLimiteMaiorUsuariosTokens();
        $objMdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $configAssistIa = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $objMdIaAdmCfgAssiIaUsuRN = new MdIaAdmCfgAssiIaUsuRN();
        $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();
        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdMdIaAdmConfigAssistIA();
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdMdIaAdmCfgAssiIaUsu(1);
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $usuarioLimiteMaior = $objMdIaAdmCfgAssiIaUsuRN->consultar($objMdIaAdmCfgAssiIaUsuDTO);

        $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD(BancoSEI::getInstance());
        $total_token_utilizado = $objMdIaInteracaoChatBD->calcularConsumoTokenDiario(SessaoSEI::getInstance()->getNumIdUsuario());

        if (!is_null($usuarioLimiteMaior)) {
            $retornoMensagem["tamanhoBudget"] = ($configAssistIa->getNumLimiteMaiorUsuariosTokens() * 1000000);
        } else {
            $retornoMensagem["tamanhoBudget"] = ($configAssistIa->getNumLimiteGeralTokens() * 1000000);
        }
        if ($total_token_utilizado >= $retornoMensagem["tamanhoBudget"]) {
            $retornoMensagem["extrapolouLimiteTokens"] = true;
        }
        $retornoMensagem["quantidadeTokensUsados"] = $total_token_utilizado;
        return $retornoMensagem;
    }

    public static function enviarFeedbackResposta($feedback)
    {
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();

        $urlEndpoint = $urlApi["urlBase"] . $urlApi["linkFeedback"];

        $dadosFeedback = array();
        $dadosFeedback["id_mensagem"] = $feedback["id_mensagem"];
        $dadosFeedback["stars"] = $feedback["stars"];
        $retornoMensagem = $objMdIaConfigAssistenteRN->enviarFeedbackResposta(array(json_encode($dadosFeedback), $urlEndpoint));

        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->retNumIdMdIaInteracaoChat();
        $objMdIaInteracaoChatDTO->setNumIdMessage($dadosFeedback["id_mensagem"]);
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

        $interacao->setNumFeedback($dadosFeedback["stars"]);
        $objMdIaInteracaoChatRN->alterar($interacao);

        return $retornoMensagem[1];
    }

    public static function consultaProtocoloDocumento($documento)
    {
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->retStrStaProtocolo();
        $objProtocoloDTO->retDblIdProtocolo();
        $objProtocoloDTO->retStrStaNivelAcessoGlobal();
        $objProtocoloDTO->retStrStaNivelAcessoLocal();
        $objProtocoloDTO->retDblIdProcedimentoDocumento();
        $objProtocoloDTO->setStrProtocoloFormatado($documento);
        $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
        return $objProtocoloDTO;
    }

    public static function consultaDocumento($documento)
    {
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retStrStaEstadoProtocolo();
        $objDocumentoDTO->retNumIdUnidadeGeradoraProtocolo();
        $objDocumentoDTO->retStrSinPublicado();
        $objDocumentoDTO->retStrSinAssinado();
        $objDocumentoDTO->retStrNomeArvore();
        $objDocumentoDTO->setStrProtocoloDocumentoFormatado($documento);
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
        return $objDocumentoDTO;
    }

    public static function retornaProtocoloLimpo($citacao)
    {
        // Verifica se quer pesquisar em um intervalo de paginas do documento
        if (preg_match('/#[0-9]+\[[0-9]+(:[0-9]+)?]/', $citacao)) {
            $documento = substr(explode('[', $citacao)[0], 1);
            $paginas = explode(':', substr(explode('[', $citacao)[1], 0, -1));
        } else {
            $documento = substr($citacao, 1);
        }
        return array("documento" => $documento, "paginas" => $paginas);
    }

    public static function verificaCriticaNCitacoes($protocolo, $documento)
    {
        foreach ($protocolo['documento'] as $citacoes) {
            $retornoProtocoloLimpo = self::retornaProtocoloLimpo($citacoes);
            $documentoCritica = $retornoProtocoloLimpo["documento"];
            if (!is_null($documentoCritica) && $documentoCritica != $documento) {
                $objProtocoloDocumentoCriticaDTO = self::consultaProtocoloDocumento($documentoCritica);
                if (!is_null($objProtocoloDocumentoCriticaDTO)) {
                    if ($objProtocoloDocumentoCriticaDTO->getStrStaProtocolo() != ProtocoloRN::$TP_PROCEDIMENTO) {
                        return ["result" => "false", "mensagem" => mb_convert_encoding("Não é permitida a interação com protocolo de processo e de documento ao mesmo tempo.", 'UTF-8', 'ISO-8859-1')];
                    }
                }
            }
        }
        return ["result" => "false", "mensagem" => mb_convert_encoding("Não é permitida a interação com mais de um protocolo de processo.", 'UTF-8', 'ISO-8859-1')];
    }

    public static function capturaExtensaoDocumento($documento)
    {
        $objAnexoDTO = (new MdIaConfigAssistenteRN())->consultarAnexo($documento);
        $extensaoArquivo = pathinfo($objAnexoDTO->getStrNome(), PATHINFO_EXTENSION);
        $extensaoArquivo = str_replace(' ', '', InfraString::transformarCaixaBaixa($extensaoArquivo));
        return $extensaoArquivo;
    }

    public static function consultaProtocolo($protocolo)
    {

        $documento = $paginas = null;
        $arrExtensoesAceitas = ["pdf", "html", "htm", "txt", "ods", "xlsx", "csv", "xml", "odt", "odp", "doc", "docx", "json", "ppt", "pptx", "rtf", "xls", "xlsm"];

        foreach ($protocolo['documento'] as $citacao) {
            $retornoProtocoloLimpo = self::retornaProtocoloLimpo($citacao);
            $documento = $retornoProtocoloLimpo["documento"];
            $paginas = $retornoProtocoloLimpo["paginas"];
            if (!is_null($documento)) {
                try {
                    $objProtocoloDTO = self::consultaProtocoloDocumento($documento);
                    if (!is_null($objProtocoloDTO)) {
                        if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_SIGILOSO) {
                            $sigiloso = false;
                        } else {
                            $sigiloso = true;
                        }
                        $download_ext = $sigiloso;
                        if (!is_null($paginas)) {
                            $download_ext = true;
                            $pag_doc_init = $paginas[0];
                            $pag_doc_end = isset($paginas[1]) ? $paginas[1] : $paginas[0];
                        }
                        if (!is_null($objProtocoloDTO)) {
                            if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO) {
                                if (count($protocolo['documento']) > 1) {
                                    return (self::verificaCriticaNCitacoes($protocolo, $documento));
                                } else {
                                    if ($sigiloso) {
                                        if (!$protocolo["consultaTopico"]) {
                                            if (($protocolo["acao_origem"] != "usuario_validar_acesso" && $protocolo["acao_origem"] != "arvore_visualizar" && $protocolo["acao_origem"] != "procedimento_gerar")
                                                || $protocolo["id_procedimento"] != $objProtocoloDTO->getDblIdProtocolo()
                                            ) {
                                                return ["result" => "false", "mensagem" => mb_convert_encoding("Para interagir com o Assistente IA em documentos de Processos com o nível de acesso Sigiloso é necessário que você tenha o acesso e esteja dentro do processo desejado.", 'UTF-8', 'ISO-8859-1')];
                                            }
                                        }
                                    }
                                    $arr = MdIaRecursoINT::listarDocumentosProcesso($objProtocoloDTO->getDblIdProtocolo());

                                    $objProcedimentoDTO = $arr[0];

                                    $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();

                                    $arrayProcessos = [];
                                    $arrayProcessos[] = $arrObjRelProtocoloProtocoloDTO;
                                    $objMdIaRecursoRN = new MdIaRecursoRN();

                                    foreach ($arrObjRelProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO) {
                                        if ($objRelProtocoloProtocoloDTO->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO) {

                                            $objProcedimentoDTOAnexado = $objRelProtocoloProtocoloDTO->getObjProtocoloDTO2();

                                            $arr = MdIaRecursoINT::listarDocumentosProcesso($objProcedimentoDTOAnexado->getDblIdProcedimento());

                                            $objProcedimentoDTO = $arr[0];

                                            $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();
                                            $arrayProcessos[] = $arrObjRelProtocoloProtocoloDTO;
                                        }
                                    }
                                    foreach ($arrayProcessos as $processos) {
                                        foreach ($processos as $documentoConsiderado) {
                                            if ($documentoConsiderado->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO) {
                                                $objDocumentoDTO = $documentoConsiderado->getObjProtocoloDTO2();
                                                if ($objMdIaRecursoRN->verificarSelecaoDocumentoAlvo($objDocumentoDTO)) {
                                                    if ($objDocumentoDTO->getStrStaProtocoloProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {

                                                        $extensaoArquivo = self::capturaExtensaoDocumento($objDocumentoDTO->getDblIdDocumento());

                                                        if (in_array($extensaoArquivo, $arrExtensoesAceitas)) {
                                                            $idProtocolo["id_documento"] = $objDocumentoDTO->getDblIdDocumento();
                                                            $idProtocolo["id_procedimento"] = $objDocumentoDTO->getDblIdProcedimento();
                                                            $idProtocolo["download_ext"] = $download_ext;
                                                            $protocolosConsiderados[] = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                                                            $protocoloFormatado = $documento;
                                                            $indexacao = self::consultarIndexacaoSolr($objDocumentoDTO->getDblIdDocumento());
                                                            if (!$indexacao && !$sigiloso) {
                                                                $objIndexacaoRN = new IndexacaoRN();
                                                                $objIndexacaoDTO = new IndexacaoDTO();
                                                                $objIndexacaoDTO->setStrProtocoloFormatadoPesquisa($protocoloFormatado);
                                                                try {
                                                                    $objIndexacaoRN->gerarIndexacaoProcesso($objIndexacaoDTO);
                                                                } catch (\Throwable $t) {
                                                                }
                                                                return ["result" => "false", "mensagem" => mb_convert_encoding("O documento externo é recente e ainda está pendente de indexação interna pelo SEI. Espere de 1 a 2 minutos para poder interagir.", 'UTF-8', 'ISO-8859-1')];
                                                            }
                                                            $idProtocolosConsiderados[] = $idProtocolo;
                                                        }
                                                    } else {
                                                        $idProtocolo["id_documento"] = $objDocumentoDTO->getDblIdDocumento();
                                                        $idProtocolo["id_procedimento"] = $objDocumentoDTO->getDblIdProcedimento();
                                                        $idProtocolo["download_ext"] = false;
                                                        $protocolosConsiderados[] = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                                                        $idProtocolosConsiderados[] = $idProtocolo;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (!$sigiloso) {
                                        try {
                                            $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
                                            $objEntradaConsultarProcedimentoAPI->setProtocoloProcedimento($documento);
                                            $objSaidaConsultarProcedimentoAPI = (new SeiRN())->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

                                            $idProcesso = $objSaidaConsultarProcedimentoAPI->getIdProcedimento();
                                            $linkAcesso = $objSaidaConsultarProcedimentoAPI->getLinkAcesso();
                                        } catch (Exception $e) {
                                        }
                                    } else {
                                        $idProcesso = $objProtocoloDTO->getDblIdProtocolo();
                                        $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProtocolo();
                                    }

                                    if (!is_null($protocolosConsiderados)) {
                                        return array(["result" => "true", "idDocumento" => $idProcesso, "linkAcesso" => $linkAcesso, "relacaoProtocolos" => json_encode($idProtocolosConsiderados), "idProcedimento" => $idProcesso, "citacaoRealizada" => "#" . $documento]);
                                    } else {
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("Unidade [" . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . "] não possui acesso a nenhum documento do processo nº [" . $documento . "] ", 'UTF-8', 'ISO-8859-1')];
                                    }
                                }
                            } else {
                                if (!is_null($paginas)) {
                                    $pag_doc_init = $paginas[0];
                                    $pag_doc_end = isset($paginas[1]) ? $paginas[1] : $paginas[0];
                                }
                                if (!$sigiloso) {
                                    $objEntradaConsultarDocumentoAPI = new EntradaConsultarDocumentoAPI();
                                    $objEntradaConsultarDocumentoAPI->setProtocoloDocumento($documento);
                                    $objSaidaConsultarDocumentoAPI = (new SeiRN())->consultarDocumento($objEntradaConsultarDocumentoAPI);

                                    $idDocumento["id_documento"] = $objSaidaConsultarDocumentoAPI->getIdDocumento();
                                    $linkAcesso = $objSaidaConsultarDocumentoAPI->getLinkAcesso();
                                    $idProcedimento = $objSaidaConsultarDocumentoAPI->getIdProcedimento();
                                    $protocoloFormatado = $objSaidaConsultarDocumentoAPI->getProcedimentoFormatado();
                                    $idDocumento["download_ext"] = $download_ext;
                                    $idDocumento["pag_doc_init"] = $pag_doc_init;
                                    $idDocumento["pag_doc_end"] = $pag_doc_end;
                                } else {
                                    if (!$protocolo["consultaTopico"]) {
                                        if (($protocolo["acao_origem"] != "usuario_validar_acesso" && $protocolo["acao_origem"] != "arvore_visualizar" && $protocolo["acao_origem"] != "procedimento_gerar")
                                            || $protocolo["id_procedimento"] != $objProtocoloDTO->getDblIdProcedimentoDocumento()
                                        ) {
                                            return ["result" => "false", "mensagem" => mb_convert_encoding("Para interagir com o Assistente IA em documentos de Processos com o nível de acesso Sigiloso é necessário que você tenha o acesso e esteja dentro do processo desejado.", 'UTF-8', 'ISO-8859-1')];
                                        }
                                    }

                                    $objDocumentoDTO = self::consultaDocumento($documento);
                                    $retornoPermissaoAcesso = MdIaRecursoRN::verificarSelecaoDocumentoAlvo($objDocumentoDTO);
                                    if (!$retornoPermissaoAcesso) {
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("Unidade [" . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . "] não possui acesso ao documento [" . $documento . "].", 'UTF-8', 'ISO-8859-1')];
                                    }
                                    $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProcedimentoDocumento() . '&amp;id_documento=' . $objProtocoloDTO->getDblIdProtocolo();
                                    $idDocumento["id_documento"] = $objProtocoloDTO->getDblIdProtocolo();
                                    if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_GERADO) {
                                        $idDocumento["download_ext"] = false;
                                    } else {
                                        $idDocumento["download_ext"] = $download_ext;
                                    }
                                    $idDocumento["pag_doc_init"] = $pag_doc_init;
                                    $idDocumento["pag_doc_end"] = $pag_doc_end;
                                    $idProcedimento = $objProtocoloDTO->getDblIdProcedimentoDocumento();
                                }
                                if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_GERADO) {
                                    if (!is_null($paginas)) {
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo indicado se refere a Documento Gerado no SEI, que, por natureza, não aceita indicação de intervalo de páginas para interação sobre seu conteúdo com o Assistente de IA.", 'UTF-8', 'ISO-8859-1')];
                                    }
                                } elseif ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {

                                    $extensaoArquivo = self::capturaExtensaoDocumento($idDocumento["id_documento"]);

                                    if (in_array($extensaoArquivo, $arrExtensoesAceitas)) {
                                        if (!is_null($paginas) && !in_array($extensaoArquivo, ['pdf'])) {
                                            return ["result" => "false", "mensagem" => mb_convert_encoding("A indicação de intervalo de páginas sobre Documento Externo para interação com o Assistente de IA está restrita a documentos do tipo PDF.", 'UTF-8', 'ISO-8859-1')];
                                        }
                                    } else {
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo indicado se refere a Documento Externo de arquivo com extensão não permitida para interação sobre seu conteúdo com o Assistente de IA.", 'UTF-8', 'ISO-8859-1')];
                                    }
                                    $indexacao = self::consultarIndexacaoSolr($idDocumento["id_documento"]);
                                    if (!$indexacao && !$sigiloso && is_null($paginas)) {
                                        $objIndexacaoRN = new IndexacaoRN();
                                        $objIndexacaoDTO = new IndexacaoDTO();
                                        $objIndexacaoDTO->setStrProtocoloFormatadoPesquisa($protocoloFormatado);
                                        try {
                                            $objIndexacaoRN->gerarIndexacaoProcesso($objIndexacaoDTO);
                                        } catch (\Throwable $t) {
                                        }
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("O documento externo é recente e ainda está pendente de indexação interna pelo SEI. Espere de 1 a 2 minutos para poder interagir.", 'UTF-8', 'ISO-8859-1')];
                                    }
                                }
                            }
                            $retornoDocumentos[] = ["result" => "true", "idDocumento" => $idDocumento, "linkAcesso" => $linkAcesso, "idProcedimento" => $idProcedimento, "citacaoRealizada" => "#" . $documento];
                        } else {
                            return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo citado #" . $documento . " não existe no SEI.", 'UTF-8', 'ISO-8859-1')];
                        }
                    }
                } catch (Exception $e) {
                    throw new InfraException('Erro consultando protocolos de citação.', $e);
                }
            }
        }
        return $retornoDocumentos;
    }


    public static function listaProtocolosUtilizados($protocolo)
    {
        foreach ($protocolo['documento'] as $citacao) {
            $retornoProtocoloLimpo = self::retornaProtocoloLimpo($citacao);
            $documento = $retornoProtocoloLimpo["documento"];
            if (!is_null($documento)) {
                try {
                    $objProtocoloDTO = self::consultaProtocoloDocumento($documento);
                    if (!is_null($objProtocoloDTO)) {
                        if (!is_null($objProtocoloDTO)) {
                            if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO) {
                                $idProcesso = $objProtocoloDTO->getDblIdProtocolo();
                                $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProtocolo();
                                return array(["result" => "true", "idDocumento" => $idProcesso, "linkAcesso" => $linkAcesso, "relacaoProtocolos" => "", "idProcedimento" => $idProcesso, "citacaoRealizada" => "#" . $documento]);
                            } else {
                                $idProcedimento = $objProtocoloDTO->getDblIdProcedimentoDocumento();
                                $idDocumento["id_documento"] = $objProtocoloDTO->getDblIdProtocolo();
                                $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProcedimentoDocumento() . '&amp;id_documento=' . $objProtocoloDTO->getDblIdProtocolo();
                            }
                            $retornoDocumentos[] = ["result" => "true", "idDocumento" => $idDocumento, "linkAcesso" => $linkAcesso, "idProcedimento" => $idProcedimento, "citacaoRealizada" => "#" . $documento];
                        } else {
                            return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo citado #" . $documento . " não existe no SEI.", 'UTF-8', 'ISO-8859-1')];
                        }
                    }
                } catch (Exception $e) {
                    throw new InfraException('Erro consultando protocolos de citação.', $e);
                }
            }
        }
        return $retornoDocumentos;
    }
    public static function consultarIndexacaoSolr($idDocumento)
    {
        $queryParams = [];
        if (!empty($idDocumento)) {
            $queryParams[] = "id_doc:" . intval($idDocumento);
        }

        // Monta a query final (caso tenha múltiplos filtros, usa "AND" para combiná-los)
        $queryString = count($queryParams) > 0 ? implode(" AND ", $queryParams) : "*:*";

        $parametros = new stdClass();
        $parametros->q = $queryString;
        $parametros->start = 0;
        $parametros->rows = 1;

        $urlBusca = IaWS::obterUrlSolAuth() . '/' .  ConfiguracaoSEI::getInstance()->getValor('Solr', 'CoreProtocolos') . '/select?' . http_build_query($parametros) . '&hl=true&hl.snippets=2&hl.fl=content&hl.fragsize=100&hl.maxAnalyzedChars=1048576&hl.alternateField=content&hl.maxAlternateFieldLength=100&fl=id_proc,content,content_type';
        // Faz a requisição HTTP ao Solr
        $resultados = file_get_contents($urlBusca);
        $xml = simplexml_load_string($resultados);
        if (!$xml->xpath('/response/result/doc')) {
            return false;
        }
        return true;
    }
    public static function geraLogExcedeuJanelaContexto($dadosEnviados)
    {
        if ($dadosEnviados["protocolo"] == "false") {
            $protocoloIndicado = "";
        } else {
            $protocoloIndicado = $dadosEnviados["protocolo"];
        }

        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();


        $log = "00001 - MENSAGEM AO ASSISTENTE DE IA BLOQUEADA POR ULTRAPASSAR JANELA DE CONTEXTO \n";
        $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
        $log .= "00003 - Endpoint do Recurso: " . $urlEndpoint = $urlApi["urlBase"] . $urlApi["linkEndpoint"] . " \n";
        $log .= "00004 - Tokens Enviados: " . $dadosEnviados["tokensEnviados"] . " \n";
        $log .= "00005 - Quantidade Máxima de Tokens Permitidos: " . $dadosEnviados["janelaContexto"] . " \n";
        $log .= "00006 - Protocolo Indicado: " . $protocoloIndicado . " \n";
        $log .= "00007 - FIM \n";
        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
        return array("result" => "true");
    }

    public static function retornaMensagemAmigavelUsuario($statusRequisicao, $mensagemOriginal)
    {
        if ($statusRequisicao != "200") {
            switch ($statusRequisicao) {
                case '204':
                    $resposta = "O prompt e o conteúdo do documento/processo citado não são compatíveis. Revise o prompt buscando deixar mais objetivo e alinhado ao conteúdo referenciado.";
                    $tipo = "warning";
                    break;

                case '401':
                case '409':
                case '422':
                case '500':
                case '501':
                case '502':
                case '503':
                case '504':
                    $resposta = "Tivemos um erro inesperado. O administrador do sistema já foi informado sobre o erro.";
                    $tipo = "error";
                    break;

                case '400':
                    $resposta = "Tivemos um erro inesperado. O administrador do sistema já foi informado sobre o erro.";
                    $tipo = "error";
                    break;

                case '403':
                    if (strpos($mensagemOriginal, 'bloqueado pela política de uso') !== false) {
                        $resposta = "O seu prompt foi bloqueado por eventual conflito com as diretrizes da IA utilizada. Analise seu prompt e o conteúdo referenciado para remover eventual conteúdo inapropriado, ou entre em contato com o administrador para propor revisão das regras de filtro de conteúdo.";
                        $tipo = "warning";
                    } else {
                        $resposta = "Foi referenciado mais de um documento no prompt com finalidade de tradução ou reescrita integral de texto. Adeque o prompt para referenciar um documento por vez.";
                        $tipo = "warning";
                    }
                    break;

                case '404':
                    if (strpos($mensagemOriginal, 'Documento ou processo nao encontrado') !== false) {
                        $resposta = "Documento não encontrado. O administrador do sistema já foi informado sobre o erro.";
                    } else {
                        $resposta = "Algum recurso interno está indisponível no momento. Tente novamente mais tarde.";
                    }
                    $tipo = "error";
                    break;

                case '408':
                case '0':
                case '100':
                case '411':
                case '412':
                    $resposta = "O limite de tempo de resposta da solução foi excedido. Tente novamente mais tarde.";
                    $tipo = "error";
                    break;

                case '413':
                    if (strpos($mensagemOriginal, 'A resposta do modelo ficou muito longa e foi truncada') !== false || strpos($mensagemOriginal, 'Texto muito longo') !== false) {
                        $resposta = "O tamanho da resposta para seu prompt ultrapassou o limite suportado pela IA. Considere decompor seu prompt ou diminuir a quantidade de conteúdo referenciado.";
                    } else {
                        $resposta = "O tamanho do conteúdo do tópico atual (memória+prompt+conteúdos referenciados) ultrapassou o limite suportado pela IA. Considere abrir novo tópico, decompor o novo prompt em tarefas menores ou diminuir a quantidade de conteúdo referenciado nele.";
                    }

                    $tipo = "warning";
                    break;

                case '415':
                    $resposta = "A solução identificou citação de documento externo com extensão de arquivo não permitida. Adeque o prompt para não referenciar documento externo com extensão não permitida.";
                    $tipo = "warning";
                    break;
            }
        } else {
            $resposta = $mensagemOriginal;
        }
        return array("resposta" => $resposta, "tipoCritica" => $tipo);
    }

    public static function consultarMensagem($dadosEnviados)
    {
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($dadosEnviados["IdMdIaInteracaoChat"]);
        $objMdIaInteracaoChatDTO->retStrResposta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retStrPergunta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retNumStatusRequisicao();
        $objMdIaInteracaoChatDTO->retDthCadastro();
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);
        if (!is_null($interacao)) {
            if ($interacao->getNumStatusRequisicao() == "") {
                return array("result" => "false");
            } else {
                $resposta = self::retornaMensagemAmigavelUsuario($interacao->getNumStatusRequisicao(), $interacao->getStrResposta());
                return array(
                    "result" => "true",
                    "resposta" => mb_convert_encoding($resposta["resposta"], 'UTF-8', 'ISO-8859-1'),
                    "tipo_critica" => $resposta["tipoCritica"],
                    "id_mensagem" => $interacao->getNumIdMessage(),
                    "status_requisicao" => $interacao->getNumStatusRequisicao(),
                    "pergunta" => mb_convert_encoding($interacao->getStrPergunta(), 'UTF-8', 'ISO-8859-1'),
                    "dth_cadastro" => substr($interacao->getDthCadastro(), 0, 19)
                );
            }
        } else {
            return array("result" => "false");
        }
    }
}
