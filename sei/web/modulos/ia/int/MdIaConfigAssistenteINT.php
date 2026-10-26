<?php

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

    const TAMANHO_MAXIMO_UPLOAD_TOTAL_MB = 500;
    // Regra unica de citacao de protocolo, reutilizada no JavaScript do chat.
    const REGEX_POSSIVEL_CITACAO_PROTOCOLO = '#[^\s#]+';
    const REGEX_CITACAO_PROTOCOLO = '#[0-9]+(?:\[[0-9]+(?::[0-9]+)?\]|(?:[-./][0-9]+)*)(?![0-9A-Za-z_\[]|[-./][0-9A-Za-z])';

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

    private static function normalizarNomeArquivoParaBanco($strNomeArquivo)
    {
        if ($strNomeArquivo === null) {
            return '';
        }

        if (mb_check_encoding($strNomeArquivo, 'UTF-8')) {
            return InfraString::fromUTF8($strNomeArquivo);
        }

        return $strNomeArquivo;
    }

    private static function normalizarNomeArquivoParaExibicao($strNomeArquivo)
    {
        if ($strNomeArquivo === null) {
            return '';
        }

        if (mb_check_encoding($strNomeArquivo, 'UTF-8')) {
            return $strNomeArquivo;
        }

        return InfraString::toUTF8($strNomeArquivo);
    }

    private static function codificarRespostaJson($dados)
    {
        array_walk_recursive($dados, function (&$valor) {
            if (is_string($valor) && !mb_check_encoding($valor, 'UTF-8')) {
                $valor = mb_convert_encoding($valor, 'UTF-8', 'ISO-8859-1');
            }
        });

        $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json !== false) {
            return $json;
        }

        return json_encode([
            'error' => true,
            'mensagem' => mb_convert_encoding(
                'Não foi possível processar a resposta do servidor. Tente novamente.',
                'UTF-8',
                'ISO-8859-1'
            )
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private static function validarCitacoesMensagem(&$mensagem)
    {
        preg_match_all('~' . self::REGEX_POSSIVEL_CITACAO_PROTOCOLO . '~', $mensagem['text_normalizado'], $arrPossiveisCitacoes);
        $arrPossiveisCitacoes = array_values(array_unique($arrPossiveisCitacoes[0]));
        $arrCitacoes = [];

        foreach ($arrPossiveisCitacoes as $strPossivelCitacao) {
            if (strlen($strPossivelCitacao) < 8) {
                return [
                    'error' => true,
                    'erroCitacao' => true,
                    'mensagem' => 'A mensagem possui citação de protocolo em formato irregular.'
                ];
            }

            $bolCitacaoValida = preg_match('~^' . self::REGEX_CITACAO_PROTOCOLO . '~', $strPossivelCitacao, $arrCorrespondencia) === 1;
            if (strpos($strPossivelCitacao, '[') !== false) {
                if (!$bolCitacaoValida) {
                    return [
                        'error' => true,
                        'erroCitacao' => true,
                        'mensagem' => 'A indicação de intervalo de páginas sobre o Documento Externo está em formato irregular.'
                    ];
                }
            } elseif (!$bolCitacaoValida) {
                return [
                    'error' => true,
                    'erroCitacao' => true,
                    'mensagem' => 'A mensagem possui citação de protocolo em formato irregular.'
                ];
            }

            $arrCitacoes[] = $arrCorrespondencia[0];
        }

        $arrCitacoes = array_values(array_unique($arrCitacoes));

        if (empty($arrCitacoes)) {
            $mensagem['dadosCitacoes'] = '';
            return null;
        }

        $arrContexto = isset($mensagem['contextoProtocolo']) && is_array($mensagem['contextoProtocolo'])
            ? $mensagem['contextoProtocolo']
            : [];
        $arrProtocolo = [
            'documento' => $arrCitacoes,
            'acao_origem' => isset($arrContexto['acao_origem']) ? (string)$arrContexto['acao_origem'] : '',
            'acesso' => isset($arrContexto['acesso']) ? (string)$arrContexto['acesso'] : '',
            'id_procedimento' => isset($arrContexto['id_procedimento']) && is_numeric($arrContexto['id_procedimento'])
                ? (int)$arrContexto['id_procedimento']
                : null,
            'consultaTopico' => false
        ];

        $arrDadosCitacoes = self::consultaProtocolo($arrProtocolo);
        if (
            !is_array($arrDadosCitacoes)
            || (isset($arrDadosCitacoes['result']) && $arrDadosCitacoes['result'] == 'false')
            || count($arrDadosCitacoes) !== count($arrCitacoes)
        ) {
            return [
                'error' => true,
                'erroCitacao' => true,
                'mensagem' => isset($arrDadosCitacoes['mensagem'])
                    ? $arrDadosCitacoes['mensagem']
                    : 'Não foi possível validar os protocolos citados. Tente novamente.'
            ];
        }

        $mensagem['dadosCitacoes'] = $arrDadosCitacoes;
        return null;
    }

    public static function enviarMensagemAssistenteIa()
    {
        $mensagem = [];
        if (!isset($_POST['mensagemUsuario'])) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = mb_convert_encoding("Mensagem inválida. Informe um texto para enviar.", 'UTF-8', 'ISO-8859-1');
            return self::codificarRespostaJson($retorno);
        }

        $mensagem = json_decode($_POST['mensagemUsuario'], true);
        if (!is_array($mensagem) || !array_key_exists("text", $mensagem)) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = mb_convert_encoding("Mensagem inválida. Informe um texto para enviar.", 'UTF-8', 'ISO-8859-1');
            return self::codificarRespostaJson($retorno);
        }

        $textoNormalizado = trim(urldecode((string) $mensagem["text"]));
        if ($textoNormalizado === '') {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = mb_convert_encoding("Não é permitido enviar anexo sem texto.", 'UTF-8', 'ISO-8859-1');
            return self::codificarRespostaJson($retorno);
        }
        $mensagem["text_normalizado"] = $textoNormalizado;

        $arrCriticaCitacoes = self::validarCitacoesMensagem($mensagem);
        if ($arrCriticaCitacoes !== null) {
            return self::codificarRespostaJson($arrCriticaCitacoes);
        }

        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSystemPrompt();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $budget = MdIaConfigAssistenteINT::calcularConsumoDiarioToken();

        if ($budget["extrapolouLimiteTokens"]) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = mb_convert_encoding("O volume de conteúdo permitido nas interações diárias foi excedido. Tente novamente amanhã, quando o volume de conteúdo permitido para interação terá sido renovado.", 'UTF-8', 'ISO-8859-1');
            return self::codificarRespostaJson($retorno);
        }
        $systemPrompt = $objMdIaAdmConfigAssistIADTO->getStrSystemPrompt();

        $systemPrompt = str_replace('@descricao_orgao_origem@', SessaoSEI::getInstance()->getStrDescricaoOrgaoUsuario(), $systemPrompt);
        $systemPrompt = str_replace('@sigla_orgao_origem@', SessaoSEI::getInstance()->getStrSiglaOrgaoSistema(), $systemPrompt);

        $dadosMensagem = array();
        $dadosMensagem["text"] = addslashes($mensagem["text_normalizado"]);
        $dadosMensagem["id_usuario"] = SessaoSEI::getInstance()->getNumIdUsuario();
        $dadosMensagem["system_prompt"] = addslashes(mb_convert_encoding($systemPrompt, 'UTF-8', 'ISO-8859-1'));
        $dadosMensagem["use_thinking"] = $mensagem["refletir"];
        $dadosMensagem["use_websearch"] = $mensagem["buscarWeb"];
        $dadosMensagem["skip_memory"] = $mensagem["skipMemory"];
        if ($mensagem["dadosCitacoes"] != "") {
            $procedimentosAgrupados = [];

            foreach ($mensagem["dadosCitacoes"] as $dadosCitacoes) {
                if (!empty($dadosCitacoes["relacaoProtocolos"])) {
                    $protocolos = json_decode($dadosCitacoes["relacaoProtocolos"], true);
                    if (!is_array($protocolos)) {
                        continue;
                    }

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
                            'download_ext'    => isset($proto['download_ext']) ? $proto['download_ext'] : true,
                            'precisa_ocr'     => isset($proto['precisa_ocr']) ? $proto['precisa_ocr'] : (isset($proto['download_ext']) ? $proto['download_ext'] : true)
                        ];

                        $procedimentosAgrupados[$idProc]['id_documentos'][] = $doc;
                    }
                    continue;
                }

                if (empty($dadosCitacoes["idProcedimento"]) || empty($dadosCitacoes["idDocumento"]["id_documento"])) {
                    continue;
                }

                $idProc = $dadosCitacoes["idProcedimento"];

                if (!isset($procedimentosAgrupados[$idProc])) {
                    $procedimentosAgrupados[$idProc] = [
                        "id_procedimento"  => $idProc,
                        "id_documentos"    => []
                    ];
                }

                $doc = [
                    "id_documento" => $dadosCitacoes["idDocumento"]["id_documento"],
                    "download_ext" => $dadosCitacoes["idDocumento"]["download_ext"],
                    "precisa_ocr" => isset($dadosCitacoes["idDocumento"]["precisa_ocr"]) ? $dadosCitacoes["idDocumento"]["precisa_ocr"] : $dadosCitacoes["idDocumento"]["download_ext"],
                    'pag_doc_init'    => !empty($dadosCitacoes["idDocumento"]['pag_doc_init']) ? $dadosCitacoes["idDocumento"]['pag_doc_init'] : 0,
                    'pag_doc_end'    => !empty($dadosCitacoes["idDocumento"]['pag_doc_end']) ? $dadosCitacoes["idDocumento"]['pag_doc_end'] : 0
                ];

                $procedimentosAgrupados[$idProc]["id_documentos"][] = $doc;
            }

            $dadosMensagem["id_procedimentos"] = array_values($procedimentosAgrupados);
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

        if (empty($dadosMensagem["id_topico"])) {
            $dadosMensagem["id_topico"] = $objMdIaTopicoChatINT->adicionarTopico();
        }

        $arrCriticaUpload = self::validarArquivosUpload();
        if ($arrCriticaUpload !== null) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = $arrCriticaUpload["mensagem"];
            return self::codificarRespostaJson($retorno);
        }

        $objMdIaInteracaoChatDTO = self::salvarInteracao($dadosMensagem, $mensagem);
        self::salvarArquivosUpload($objMdIaInteracaoChatDTO->getNumIdMdIaInteracaoChat());
        self::atualizarInteracaoUploads($objMdIaInteracaoChatDTO, $dadosMensagem);
        self::executarConsultaWebService($objMdIaInteracaoChatDTO->getNumIdMdIaInteracaoChat());

        return $objMdIaInteracaoChatDTO->getNumIdMdIaInteracaoChat();
    }

    private static function salvarInteracao($dadosMensagem, $mensagem)
    {
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($dadosMensagem["id_topico"]);
        $pergunta = mb_convert_encoding($mensagem["text_normalizado"], 'HTML-ENTITIES', 'UTF-8');
        $pergunta = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $pergunta);
        $objMdIaInteracaoChatDTO->setStrPergunta($pergunta);
        $objMdIaInteracaoChatDTO->setStrInputPrompt(json_encode($dadosMensagem));
        $objMdIaInteracaoChatDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        return $objMdIaInteracaoChatRN->cadastrar($objMdIaInteracaoChatDTO);
    }

    private static function validarArquivosUpload()
    {
        if (isset($_FILES['arquivos']) && is_array($_FILES['arquivos']['name'])) {
            $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
            $totalArquivos = count($_FILES['arquivos']['name']);
            $numTamanhoTotalArquivos = 0;

            for ($i = 0; $i < $totalArquivos; $i++) {

                if ($_FILES['arquivos']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $strNomeOriginal = $_FILES['arquivos']['name'][$i];
                $numTamanhoArquivo = $_FILES['arquivos']['size'][$i];
                $numTamanhoTotalArquivos += $numTamanhoArquivo;
                $strExtensao = pathinfo($strNomeOriginal, PATHINFO_EXTENSION);
                $arrRegraExtensao = $objMdIaConfigAssistenteRN->consultarRegraExtensaoPermitida($strExtensao, MdIaConfigAssistenteRN::CONTEXTO_AVULSO);

                if ($arrRegraExtensao === null) {
                    $arrExtensoesPermitidas = $objMdIaConfigAssistenteRN->listarExtensoesPermitidasAvulso();
                    $strMensagemErro = 'A extensão "' . strtoupper($strExtensao) . '" não é permitida. As extensões permitidas são: ' . implode(', ', $arrExtensoesPermitidas) . '.';
                    return ["result" => "false", "mensagem" => mb_convert_encoding($strMensagemErro, 'UTF-8', 'ISO-8859-1')];
                }

                $numTamanhoMaximoMb = $arrRegraExtensao['tamanhoMaximoMb'];
                if (!InfraString::isBolVazia($numTamanhoMaximoMb)) {
                    $numTamanhoMaximoBytes = $numTamanhoMaximoMb * 1024 * 1024;

                    if ($numTamanhoArquivo > $numTamanhoMaximoBytes) {
                        $strMensagemErro = 'Arquivo "' . $strNomeOriginal . '" com tamanho acima do permitido. O limite de tamanho para a extensão ' . strtoupper($arrRegraExtensao['extensao']) . ' é de ' . $numTamanhoMaximoMb . ' MB.';
                        return ["result" => "false", "mensagem" => mb_convert_encoding($strMensagemErro, 'UTF-8', 'ISO-8859-1')];
                    }
                }
            }

            if ($numTamanhoTotalArquivos > self::TAMANHO_MAXIMO_UPLOAD_TOTAL_MB * 1024 * 1024) {
                $strMensagemErro = 'O tamanho total dos arquivos excede o limite de ' . self::TAMANHO_MAXIMO_UPLOAD_TOTAL_MB . ' MB por mensagem.';
                return ["result" => "false", "mensagem" => mb_convert_encoding($strMensagemErro, 'UTF-8', 'ISO-8859-1')];
            }
        }

        return null;
    }

    private static function salvarArquivosUpload($numIdMdIaInteracaoChat)
    {
        $AnexoRN = new AnexoRN();

        if (isset($_FILES['arquivos']) && is_array($_FILES['arquivos']['name'])) {
            $totalArquivos = count($_FILES['arquivos']['name']);

            for ($i = 0; $i < $totalArquivos; $i++) {

                // Ignora erros
                if ($_FILES['arquivos']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $strNomeTemporario = $AnexoRN->gerarNomeArquivoTemporario();
                $strNomeOriginal = $_FILES['arquivos']['name'][$i];

                $strCaminhoFinal = DIR_SEI_TEMP . '/' . $strNomeTemporario;
                $strCaminhoTemporarioPHP = $_FILES['arquivos']['tmp_name'][$i];


                if (move_uploaded_file($strCaminhoTemporarioPHP, $strCaminhoFinal)) {
                    $objUploadDTO = new MdIaInteracaoArqAvulsoDTO();
                    $objUploadDTO->setNumIdMdIaInteracaoChat($numIdMdIaInteracaoChat);
                    $objUploadDTO->setStrNomeOriginal(self::normalizarNomeArquivoParaBanco($strNomeOriginal));
                    $objUploadDTO->setStrNomeTemporario($strNomeTemporario);
                    $objUploadDTO->setDthInclusao(InfraData::getStrDataHoraAtual());
                    (new MdIaInteracaoArqAvulsoRN())->cadastrar($objUploadDTO);
                }
            }
        }
    }

    private static function atualizarInteracaoUploads($objMdIaInteracaoChatDTO, $dadosMensagem)
    {
        $arrObjMdIaInteracaoArqAvulsoDTO = self::consultarArquivosInteracao($objMdIaInteracaoChatDTO->getNumIdMdIaInteracaoChat());

        if (!empty($arrObjMdIaInteracaoArqAvulsoDTO)) {

            $uploads = [];

            foreach ($arrObjMdIaInteracaoArqAvulsoDTO as $objMdIaInteracaoArqAvulsoDTO) {
                $obj = [];
                $obj['id_arquivo_avulso'] = $objMdIaInteracaoArqAvulsoDTO->getNumIdMdIaInteracaoArqAvulso();
                $obj['nome_arquivo_avulso'] = self::normalizarNomeArquivoParaExibicao($objMdIaInteracaoArqAvulsoDTO->getStrNomeOriginal());
                $partes = explode('.', $obj['nome_arquivo_avulso']);
                $extensaoAnexo = strtolower(end($partes));
                $obj['extensao_arquivo_avulso'] = $extensaoAnexo;
                $uploads[] = $obj;
            }

            $dadosMensagem['arquivos_avulsos'] = $uploads;

            $objMdIaInteracaoChatDTO->setStrInputPrompt(json_encode($dadosMensagem));
            (new MdIaInteracaoChatRN())->alterar($objMdIaInteracaoChatDTO);
        }
    }

    private static function consultarArquivosInteracao($numIdMdIaInteracaoChat)
    {
        $objUploadDTO = new MdIaInteracaoArqAvulsoDTO();
        $objUploadDTO->setNumIdMdIaInteracaoChat($numIdMdIaInteracaoChat);
        $objUploadDTO->retNumIdMdIaInteracaoArqAvulso();
        $objUploadDTO->retStrNomeOriginal();
        // $objUploadDTO->retStrExtensao();
        return (new MdIaInteracaoArqAvulsoRN())->listar($objUploadDTO);
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
        $objMdIaAdmConfigAssistIADTO->setNumMaxRegistrosRetorno(1);
        $configAssistIa = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $objMdIaAdmCfgAssiIaUsuRN = new MdIaAdmCfgAssiIaUsuRN();
        $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();
        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdMdIaAdmConfigAssistIA();
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $usuarioLimiteMaior = $objMdIaAdmCfgAssiIaUsuRN->consultar($objMdIaAdmCfgAssiIaUsuDTO);

        $total_token_utilizado = (new MdIaInteracaoChatRN())->calcularConsumoTokenDiario(SessaoSEI::getInstance()->getNumIdUsuario());

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
        $objMdIaInteracaoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $objMdIaInteracaoChatDTO->setNumIdUnidadeTopico(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $objMdIaInteracaoChatDTO->setOrdNumIdMdIaInteracaoChat(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdIaInteracaoChatDTO->setNumMaxRegistrosRetorno(1);
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

        if ($interacao !== null) {
            $interacao->setNumFeedback($dadosFeedback["stars"]);
            $objMdIaInteracaoChatRN->alterar($interacao);
        }

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
        $objDocumentoDTO->retDblIdProcedimento();
        $objDocumentoDTO->retStrNomeArvore();
        $objDocumentoDTO->setStrProtocoloDocumentoFormatado($documento);
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
        $objProcedimentoDTO->setStrSinDocTodos('S');
        $objProcedimentoDTO->setArrDblIdProtocoloAssociado(array($objDocumentoDTO->getDblIdDocumento()));

        $objProcedimentoRN = new ProcedimentoRN();
        $arrObjProcedimentoDTO = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

        $objProcedimentoDTO = $arrObjProcedimentoDTO[0];

        $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();

        $objDocumentoInfoAdicionais = $arrObjRelProtocoloProtocoloDTO[0]->getObjProtocoloDTO2();

        $objDocumentoDTO->setStrSinPublicado($objDocumentoInfoAdicionais->getStrSinPublicado());
        $objDocumentoDTO->setStrSinAssinado($objDocumentoInfoAdicionais->getStrSinAssinado());

        return $objDocumentoDTO;
    }

    public static function retornaProtocoloLimpo($citacao)
    {
        $paginas = null;

        // Verifica se quer pesquisar em um intervalo de paginas do documento
        if (preg_match('/#[0-9]+\[[0-9]+(:[0-9]+)?]/', $citacao)) {
            $documento = substr(explode('[', $citacao)[0], 1);
            $paginas = explode(':', substr(explode('[', $citacao)[1], 0, -1));
        } else {
            $documento = substr($citacao, 1);
        }
        return array("documento" => $documento, "paginas" => $paginas);
    }

    public static function verificaCriticaNCitacoes($protocolo)
    {
        $processosCitados = [];

        foreach ($protocolo['documento'] as $citacoes) {
            $retornoProtocoloLimpo = self::retornaProtocoloLimpo($citacoes);
            $documentoCritica = $retornoProtocoloLimpo["documento"];
            if (!is_null($documentoCritica)) {
                $objProtocoloDocumentoCriticaDTO = self::consultaProtocoloDocumento($documentoCritica);
                if (!is_null($objProtocoloDocumentoCriticaDTO)) {
                    if ($objProtocoloDocumentoCriticaDTO->getStrStaProtocolo() != ProtocoloRN::$TP_PROCEDIMENTO) {
                        continue;
                    }
                    $processosCitados[$objProtocoloDocumentoCriticaDTO->getDblIdProtocolo()] = true;
                }
            }
        }

        if (count($processosCitados) > 2) {
            return ["result" => "false", "mensagem" => mb_convert_encoding("É permitido citar no máximo dois processos por prompt. Remova uma ou mais citações de processo para continuar.", 'UTF-8', 'ISO-8859-1')];
        }

        return ["result" => "true"];
    }

    public static function capturaExtensaoDocumento($documento)
    {
        $objAnexoDTO = (new MdIaConfigAssistenteRN())->consultarAnexo($documento);
        if ($objAnexoDTO == null) {
            throw new InfraException('O Documento não contém anexo.');
        }
        $extensaoArquivo = pathinfo($objAnexoDTO->getStrNome(), PATHINFO_EXTENSION);
        $extensaoArquivo = str_replace(' ', '', InfraString::transformarCaixaBaixa($extensaoArquivo));
        return $extensaoArquivo;
    }

    private static function logOcr($mensagem, $tipo = null)
    {
        if ($tipo === null) {
            $tipo = InfraLog::$INFORMACAO;
        }

        // Logs de OCR so sao gravados se MODULO_IA_LOGAR_OCR_DEBUG estiver ativo no banco.
        try {
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $bolDebugAtivo = $objInfraParametro->getValor('MODULO_IA_LOGAR_OCR_DEBUG', false);
            if (!$bolDebugAtivo) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        try {
            LogSEI::getInstance()->gravar('[SEI_IA_OCR] ' . $mensagem, $tipo);
        } catch (\Throwable $e) {
            // Nao interromper fluxo de negocio por falha de log.
        }
    }

    public static function verificarNecessidadeOCR($idDocumento, $extensao)
    {
        try {
            // Only check PDFs
            if (strtolower($extensao) !== 'pdf') {
                self::logOcr('Documento ' . $idDocumento . ' ignorado. Extensao=' . $extensao);
                return false;
            }

            // Get AnexoDTO using the same method as capturaExtensaoDocumento
            $objAnexoDTO = (new MdIaConfigAssistenteRN())->consultarAnexo($idDocumento);

            if ($objAnexoDTO == null) {
                self::logOcr('Documento ' . $idDocumento . ' sem anexo para OCR. Retornando precisa_ocr=true', InfraLog::$AVISO);
                return true;
            }

            // Get file path using AnexoRN::obterLocalizacao
            $objAnexoRN = new AnexoRN();
            $filePath = $objAnexoRN->obterLocalizacao($objAnexoDTO);

            if (!file_exists($filePath) || !is_readable($filePath)) {
                self::logOcr('Arquivo inexistente ou sem leitura. Documento=' . $idDocumento . ' caminho=' . $filePath, InfraLog::$AVISO);
                return true;
            }

            // Call Python detection script
            $pythonScript = dirname(__FILE__) . '/../scripts/detect_pdf_digitalizado_assinado_digital.py';

            if (!file_exists($pythonScript)) {
                self::logOcr('Script Python nao encontrado: ' . $pythonScript, InfraLog::$ERRO);
                return true;
            }

            $python = '/usr/bin/python3.12';
            if (!is_executable($python)) {
                self::logOcr('Runtime OCR indisponivel: ' . $python, InfraLog::$ERRO);
                return true;
            }

            $command = escapeshellarg($python) . ' ' . escapeshellarg($pythonScript) . ' ' . escapeshellarg($filePath) . ' --ocr --assinatura 2>&1';
            self::logOcr('Executando OCR. Documento=' . $idDocumento . ' comando=' . $command);
            $output = shell_exec($command);

            if ($output === null) {
                self::logOcr('Falha shell_exec (output null). Documento=' . $idDocumento, InfraLog::$ERRO);
                return true;
            }

            $outputTrim = trim($output);
            self::logOcr('Saida bruta OCR documento=' . $idDocumento . ' output=' . $outputTrim);

            $result = json_decode($outputTrim, true);

            if (is_array($result) && isset($result['precisa_ocr'])) {
                try {
                    $log = "ASSISTENTE IA OCR - id_documento=" . $idDocumento .
                        " precisa_ocr=" . ((bool)$result['precisa_ocr'] ? 'true' : 'false') .
                        " cobertura=" . (isset($result['cobertura']) ? $result['cobertura'] : 'n/a') .
                        " paginas_analisadas=" . (isset($result['paginas_analisadas']) ? $result['paginas_analisadas'] : 'n/a') .
                        " paginas_digitais=" . (isset($result['paginas_digitais']) ? $result['paginas_digitais'] : 'n/a') .
                        " tem_assinatura_digital=" . (!empty($result['tem_assinatura_digital']) ? 'true' : 'false');
                    self::logOcr($log, InfraLog::$INFORMACAO);
                } catch (\Throwable $e) {
                    // Nao interromper fluxo por falha de log.
                }

                return (bool)$result['precisa_ocr'];
            }

            try {
                self::logOcr(
                    "ASSISTENTE IA OCR - id_documento=" . $idDocumento . " retorno_json_invalido",
                    InfraLog::$AVISO
                );
            } catch (\Throwable $e) {
                // Nao interromper fluxo por falha de log.
            }

            return true;
        } catch (\Throwable $e) {
            try {
                self::logOcr(
                    "ASSISTENTE IA OCR - id_documento=" . $idDocumento . " erro=" . $e->getMessage(),
                    InfraLog::$ERRO
                );
            } catch (\Throwable $logEx) {
                // Nao interromper fluxo por falha de log.
            }
            return true;
        }
    }

    public static function consultaProtocolo($protocolo)
    {

        $documento = $paginas = null;
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();

        if (!is_array($protocolo) || empty($protocolo['documento']) || !is_array($protocolo['documento'])) {
            return ["result" => "false", "mensagem" => "Nenhum documento foi informado para consulta."];
        }

        if (count($protocolo['documento']) > 1) {
            $criticaCitacoes = self::verificaCriticaNCitacoes($protocolo);
            if ($criticaCitacoes["result"] == "false") {
                return $criticaCitacoes;
            }
        }

        $retornoDocumentos = [];

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
                        if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO) {
                            $protocolosConsiderados = [];
                            $idProtocolosConsiderados = [];
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

                                                if ($objMdIaConfigAssistenteRN->consultarAnexo($objDocumentoDTO->getDblIdDocumento()) == null) {
                                                    continue;
                                                }

                                                $extensaoArquivo = self::capturaExtensaoDocumento($objDocumentoDTO->getDblIdDocumento());

                                                $arrRegraExtensao = $objMdIaConfigAssistenteRN->consultarRegraExtensaoPermitida($extensaoArquivo, MdIaConfigAssistenteRN::CONTEXTO_PROTOCOLO);
                                                if ($arrRegraExtensao !== null) {
                                                    $idProtocolo["id_documento"] = $objDocumentoDTO->getDblIdDocumento();
                                                    $idProtocolo["id_procedimento"] = $objDocumentoDTO->getDblIdProcedimento();

                                                    // Verify if external PDF needs OCR
                                                    if (strtolower($extensaoArquivo) === 'pdf' && !$sigiloso) {
                                                        $download_ext = self::verificarNecessidadeOCR($objDocumentoDTO->getDblIdDocumento(), $extensaoArquivo);
                                                    }

                                                    $idProtocolo["download_ext"] = $download_ext;
                                                    $idProtocolo["precisa_ocr"] = $download_ext;
                                                    $protocolosConsiderados[] = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                                                    $protocoloFormatado = $documento;
                                                    if ($arrRegraExtensao['forma_entrega_arquivo'] != 1 && !$download_ext) {
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
                                                    } else {
                                                        $idProtocolo["download_ext"] = true;
                                                    }
                                                    $idProtocolosConsiderados[] = $idProtocolo;
                                                }
                                            } else {
                                                $idProtocolo["id_documento"] = $objDocumentoDTO->getDblIdDocumento();
                                                $idProtocolo["id_procedimento"] = $objDocumentoDTO->getDblIdProcedimento();
                                                $idProtocolo["download_ext"] = false;
                                                $idProtocolo["precisa_ocr"] = false;
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

                            if (!empty($protocolosConsiderados)) {
                                $retornoDocumentos[] = ["result" => "true", "idDocumento" => $idProcesso, "linkAcesso" => $linkAcesso, "relacaoProtocolos" => json_encode($idProtocolosConsiderados), "idProcedimento" => $idProcesso, "citacaoRealizada" => "#" . $documento];
                                continue;
                            } else {
                                return ["result" => "false", "mensagem" => mb_convert_encoding("Unidade [" . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . "] não possui acesso a nenhum documento do processo nº [" . $documento . "] ", 'UTF-8', 'ISO-8859-1')];
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
                                $idDocumento["precisa_ocr"] = $download_ext;
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
                                    $idDocumento["precisa_ocr"] = false;
                                } else {
                                    // Capture extension for OCR verification
                                    $extensaoArquivo = self::capturaExtensaoDocumento($idDocumento["id_documento"]);

                                    // Verify if external PDF needs OCR
                                    if (strtolower($extensaoArquivo) === 'pdf' && !$sigiloso) {
                                        $download_ext = self::verificarNecessidadeOCR($idDocumento["id_documento"], $extensaoArquivo);
                                    }

                                    $idDocumento["download_ext"] = $download_ext;
                                    $idDocumento["precisa_ocr"] = $download_ext;
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

                                // Verify if external PDF needs OCR
                                if (strtolower($extensaoArquivo) === 'pdf' && !$sigiloso) {
                                    $download_ext = self::verificarNecessidadeOCR($idDocumento["id_documento"], $extensaoArquivo);
                                    $idDocumento["precisa_ocr"] = $download_ext;
                                    if ($idDocumento["precisa_ocr"]) {
                                        $idDocumento["download_ext"] = true;
                                    }
                                }

                                $arrRegraExtensao = $objMdIaConfigAssistenteRN->consultarRegraExtensaoPermitida($extensaoArquivo, MdIaConfigAssistenteRN::CONTEXTO_PROTOCOLO);

                                if ($arrRegraExtensao !== null) {
                                    if (!is_null($paginas) && !in_array($extensaoArquivo, ['pdf'])) {
                                        return ["result" => "false", "mensagem" => mb_convert_encoding("A indicação de intervalo de páginas sobre Documento Externo para interação com o Assistente de IA está restrita a documentos do tipo PDF.", 'UTF-8', 'ISO-8859-1')];
                                    }
                                } else {
                                    return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo indicado se refere a Documento Externo de arquivo com extensão não permitida para interação sobre seu conteúdo com o Assistente de IA.", 'UTF-8', 'ISO-8859-1')];
                                }
                                if ($arrRegraExtensao['forma_entrega_arquivo'] != 1 && !$download_ext) {
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
                                } else {
                                    $idDocumento["download_ext"] = true;
                                }
                            }
                        }
                        $retornoDocumentos[] = ["result" => "true", "idDocumento" => $idDocumento, "linkAcesso" => $linkAcesso, "idProcedimento" => $idProcedimento, "citacaoRealizada" => "#" . $documento];
                    } else {
                        return ["result" => "false", "mensagem" => mb_convert_encoding("O protocolo citado #" . $documento . " não existe no SEI.", 'UTF-8', 'ISO-8859-1')];
                    }
                } catch (Exception $e) {
                    $mensagem = ($e instanceof InfraException) ? (string) $e : $e->getMessage();
                    return ["result" => "false", "mensagem" => mb_convert_encoding($mensagem, 'UTF-8', 'ISO-8859-1')];
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
                                $retornoDocumentos[] = ["result" => "true", "idDocumento" => $idProcesso, "linkAcesso" => $linkAcesso, "relacaoProtocolos" => "", "idProcedimento" => $idProcesso, "citacaoRealizada" => "#" . $documento];
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
        if (strpos($resultados, '<doc') == false) {
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
        $log .= "00003 - Endpoint do Recurso: " . $urlApi["linkEndpoint"] . " \n";
        $log .= "00004 - Tokens Enviados: " . $dadosEnviados["tokensEnviados"] . " \n";
        $log .= "00005 - Quantidade Máxima de Tokens Permitidos: " . $dadosEnviados["janelaContexto"] . " \n";
        $log .= "00006 - Protocolo Indicado: " . $protocoloIndicado . " \n";
        $log .= "00007 - FIM \n";
        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
        return array("result" => "true");
    }

    public static function geraLogBloqueioArquivoPrejudicial($dadosEnviados)
    {
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();

        $quantidadeArquivos = isset($dadosEnviados["quantidadeArquivos"]) ? $dadosEnviados["quantidadeArquivos"] : "";

        $log = "00001 - ASSISTENTE IA: BLOQUEIO DE ARQUIVO AVULSO PELA CAMADA DE SEGURANÇA DE REDE DO ORGÃO \n";
        $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
        $log .= "00003 - Endpoint do Recurso: " . $urlApi["linkEndpoint"] . " \n";
        $log .= "00004 - Quantidade de arquivos no envio: " . $quantidadeArquivos . " \n";
        $log .= "00005 - Mensagem exibida ao usuario: Os arquivos avulsos não foram enviados. Dentro deles pode ter conteúdo potencialmente prejudicial e foi bloqueado pela camada de segurança da rede do seu órgão. \n";
        $log .= "00006 - FIM \n";

        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
        return array("result" => "true");
    }

    public static function retornaMensagemAmigavelUsuario($statusRequisicao, $mensagemOriginal)
    {
        $tipo = "success";
        if ($statusRequisicao != "200" && !is_null($statusRequisicao)) {
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

                case '0':
                    if (stripos($mensagemOriginal, 'cURL(28)') !== false || stripos($mensagemOriginal, 'timed out') !== false || stripos($mensagemOriginal, 'timeout') !== false) {
                        $resposta = "O limite de tempo de resposta da solução foi excedido. Tente novamente mais tarde.";
                    } else {
                        $resposta = "Não foi possível conectar à solução de IA no momento. Tente novamente mais tarde.";
                    }
                    $tipo = "error";
                    break;

                case '408':
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

                case '429':
                    $resposta = "A solução atingiu o limite de requisições no momento. Tente novamente em alguns instantes.";
                    $tipo = "warning";
                    break;

                default:
                    $resposta = "Tivemos um erro inesperado. O administrador do sistema já foi informado sobre o erro.";
                    $tipo = "error";
                    break;
            }
        } else {
            $resposta = $mensagemOriginal;
        }
        return array("resposta" => $resposta, "tipoCritica" => $tipo);
    }

    public static function consultarMensagem($dadosEnviados)
    {
        $retorno = array(
            "result" => "false",
            "mensagem" => null,
            "status_execucao" => null,
            "status_execucao_dth" => null,
            "reasoning" => null,
            "reasoning_dth" => null,
            "em_processamento" => false
        );

        if (!isset($dadosEnviados["IdMdIaInteracaoChat"]) || !is_numeric($dadosEnviados["IdMdIaInteracaoChat"])) {
            $retorno["mensagem"] = mb_convert_encoding('Interação não informada.', 'UTF-8', 'ISO-8859-1');
            return $retorno;
        }

        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat((int)$dadosEnviados["IdMdIaInteracaoChat"]);
        $objMdIaInteracaoChatDTO->retNumIdUsuario();
        $objMdIaInteracaoChatDTO->retNumIdUnidadeTopico();
        $objMdIaInteracaoChatDTO->retStrResposta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retStrPergunta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retNumStatusRequisicao();
        $objMdIaInteracaoChatDTO->retDthCadastro();
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

        if (is_null($interacao)) {
            $retorno["mensagem"] = mb_convert_encoding('Interação não encontrada.', 'UTF-8', 'ISO-8859-1');
            return $retorno;
        }

        if (
            $interacao->getNumIdUsuario() != SessaoSEI::getInstance()->getNumIdUsuario()
            || $interacao->getNumIdUnidadeTopico() != SessaoSEI::getInstance()->getNumIdUnidadeAtual()
        ) {
            $retorno["mensagem"] = mb_convert_encoding('A interação informada não pertence à sessão logada.', 'UTF-8', 'ISO-8859-1');
            return $retorno;
        }

        $resposta = mb_convert_encoding(self::retornaMensagemAmigavelUsuario($interacao->getNumStatusRequisicao(), $interacao->getStrResposta()), 'UTF-8', 'ISO-8859-1');
        $objMdIaInteracaoChatEventRN = new MdIaInteracaoChatEventRN();
        $ultimoEventoReasoning = $objMdIaInteracaoChatEventRN->consultarUltimoEventoReasoning((int)$dadosEnviados["IdMdIaInteracaoChat"]);

        if ($interacao->getNumStatusRequisicao() == '') {
            $ultimoEventoStatus = $objMdIaInteracaoChatEventRN->consultarUltimoEventoStatus((int)$dadosEnviados["IdMdIaInteracaoChat"]);

            $retorno['resposta'] = $resposta;
            $retorno['em_processamento'] = true;
            $retorno['status_execucao'] = $ultimoEventoStatus ? mb_convert_encoding($ultimoEventoStatus->getStrConteudo(), 'UTF-8', 'ISO-8859-1') : null;
            $retorno['status_execucao_dth'] = $ultimoEventoStatus ? substr($ultimoEventoStatus->getDthEvento(), 0, 19) : null;
            $retorno['reasoning'] = $ultimoEventoReasoning ? mb_convert_encoding($ultimoEventoReasoning->getStrConteudo(), 'UTF-8', 'ISO-8859-1') : null;
            $retorno['reasoning_dth'] = $ultimoEventoReasoning ? substr($ultimoEventoReasoning->getDthEvento(), 0, 19) : null;
        } else {
            $retorno = array(
                "result" => "true",
                "resposta" => $resposta,
                "tipo_critica" => $resposta["tipoCritica"],
                "id_mensagem" => $interacao->getNumIdMessage(),
                "status_requisicao" => $interacao->getNumStatusRequisicao(),
                "pergunta" => mb_convert_encoding($interacao->getStrPergunta(), 'UTF-8', 'ISO-8859-1'),
                "dth_cadastro" => substr($interacao->getDthCadastro(), 0, 19),
                "status_execucao" => null,
                "status_execucao_dth" => null,
                "reasoning" => $ultimoEventoReasoning ? mb_convert_encoding($ultimoEventoReasoning->getStrConteudo(), 'UTF-8', 'ISO-8859-1') : null,
                "reasoning_dth" => $ultimoEventoReasoning ? substr($ultimoEventoReasoning->getDthEvento(), 0, 19) : null,
                "em_processamento" => false
            );
        }

        return $retorno;
    }
}
