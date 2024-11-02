<?php

class IaIntegracao extends SeiIntegracao
{

    const PARAMETRO_VERSAO_MODULO = 'VERSAO_MODULO_IA';

    public function __construct()
    {

    }

    public function getNome()
    {
        return 'SEI IA';
    }

    public function getVersao()
    {
        return '1.0.0';
    }

    public function getInstituicao()
    {
        return 'Anatel - Agência Nacional de Telecomunicações';
    }

    public function montarBotaoProcesso(ProcedimentoAPI $objProcedimentoAPI)
    {
        $arrBotoes = array();

        if ($this->verificaAcesso($objProcedimentoAPI)) {
            $strBotaoAvaliacaoSeiIa = $this->retornaBotao();
            $arrBotoes[] = $strBotaoAvaliacaoSeiIa;
        }
        return $arrBotoes;
    }

        public function montarBotaoDocumento(ProcedimentoAPI $objProcedimentoAPI, $arrObjDocumentoAPI)
        {
            $arrBotoes = array();

            if ($this->verificaAcesso($objProcedimentoAPI)) {
                foreach ($arrObjDocumentoAPI as $documentoAPI) {
                    $strBotaoAvaliacaoSeiIa = $this->retornaBotao();
                    $idDocumento = $documentoAPI->getIdDocumento();
                    $arrBotoes[$idDocumento][] = $strBotaoAvaliacaoSeiIa;
                }
            }
            return $arrBotoes;
        }

        public function retornaBotao()
        {
            $strLink = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']);
            $imgIcone = "modulos/ia/imagens/md_ia_icone.svg?" . Icone::VERSAO;
            $title = "Inteligência Artificial";

            $strBotaoAvaliacaoSeiIa = '<a href="' . $strLink . '"class="botaoSEI">';
            $strBotaoAvaliacaoSeiIa .= '<img class="infraCorBarraSistema" src="' . $imgIcone . '" alt="' . $title . '" title="' . $title . '">';
            $strBotaoAvaliacaoSeiIa .= '</a>';
            return $strBotaoAvaliacaoSeiIa;
        }

        public function verificaAcesso($objProcedimentoAPI, $bolConsideraChatIa= false)
        {
            if (!is_null($objProcedimentoAPI)) {
                $bolPermissaoAcesso = $objProcedimentoAPI->getCodigoAcesso() > 0;
            } else {
                $bolPermissaoAcesso = true;
            }
            $bolAcaoRecursoIa = SessaoSEI::getInstance()->verificarPermissao('md_ia_recurso');
            $mdIaRecursoRN = new MdIaRecursoRN();
            $bolExibirFuncionalidade = $mdIaRecursoRN->exibeFuncionalidade($bolConsideraChatIa);
            if ($bolAcaoRecursoIa && $bolExibirFuncionalidade && $bolPermissaoAcesso) {
                return true;
            }
        }

        public function exibeTooltip()
        {

            $bolExibirFuncionalidade = false;

            $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
            $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
            $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
            $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

            if ($objMdIaAdmOdsOnuDTO) {
                if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
                    $bolExibirFuncionalidade = true;
                    return $bolExibirFuncionalidade;
                }
            }
            return $bolExibirFuncionalidade;
        }

        public function consultaUnidadeAlerta()
        {
            $objMdIaAdmUnidadeAlertaDTO = new MdIaAdmUnidadeAlertaDTO();
            $objMdIaAdmUnidadeAlertaDTO->setNumIdMdIaAdmOdsOnu(1);
            $objMdIaAdmUnidadeAlertaDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objMdIaAdmUnidadeAlertaDTO->retNumIdMdIaAdmUnidadeAlerta();
            $objMdIaAdmUnidadeAlertaRN = new MdIaAdmUnidadeAlertaRN();
            return $objMdIaAdmUnidadeAlertaRN->consultar($objMdIaAdmUnidadeAlertaDTO);
        }

        public function listaObjetivosOdsOnu()
        {
            $objetivosdaOnu = NULL;
            $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
            $objMdIaAdmOdsOnuDTO->retNumIdMdIaAdmOdsOnu();
            $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
            $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

            if ($objMdIaAdmOdsOnuDTO) {
                $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
                $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
                $objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmOdsOnu($objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu());
                $objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();

                $objetivosdaOnu = $objMdIaAdmObjetivoOdsRN->listar($objMdIaAdmObjetivoOdsDTO);
            }
            return $objetivosdaOnu;
        }

        public function retornaClassificacao($dados)
        {
            $idProcedimento = $dados[0];
            $objetivoOds = $dados[1];
            $objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
            $objMdIaClassificacaoOdsDTO->setNumIdProcedimento($idProcedimento);
            $objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($objetivoOds);
            $objMdIaClassificacaoOdsDTO->retStrStaTipoUltimoUsuario();
            $objMdIaClassificacaoOdsRN = new MdIaClassificacaoOdsRN();
            return $objMdIaClassificacaoOdsRN->consultar($objMdIaClassificacaoOdsDTO);
        }

        public function retornaIconePendencia($objProcedimentoAPI)
        {
            $tipo = 'IA';
            $id = 'IA_' . $objProcedimentoAPI->getIdProcedimento();
            $title = 'IA - Alerta \nPendência de classificação segundo os Objetivos de Desenvolvimento Sustentável da ONU ou divergência em sugestão mais recente feita pelo SEI IA ou por Usuário Externo.';
            $icone = "modulos/ia/imagens/md_ia_icone_alerta.svg?" . Icone::VERSAO;

            $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
            $objArvoreAcaoItemAPI->setTipo($tipo);
            $objArvoreAcaoItemAPI->setId($id);
            $objArvoreAcaoItemAPI->setIdPai($objProcedimentoAPI->getIdProcedimento());
            $objArvoreAcaoItemAPI->setTitle($title);
            $objArvoreAcaoItemAPI->setIcone($icone);
            $objArvoreAcaoItemAPI->setTarget('ifrVisualizacao');
            $objArvoreAcaoItemAPI->setHref(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&id_procedimento=' . $objProcedimentoAPI->getIdProcedimento()));
            $objArvoreAcaoItemAPI->setSinHabilitado('S');
            $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;
            return $arrObjArvoreAcaoItemAPI;
        }

        public function montarIconeProcesso(ProcedimentoAPI $objProcedimentoAPI)
        {
            if ($this->verificaAcesso($objProcedimentoAPI)) {

                if($this->exibeTooltip()) {
                    if (!is_null($this->consultaUnidadeAlerta())) {

                        $existeClassificacao = false;
                        $arrObjMdIaAdmObjetivoOdsDTO = $this->listaObjetivosOdsOnu();

                        if (!is_null($arrObjMdIaAdmObjetivoOdsDTO)) {

                            foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {

                                $objMdIaClassificacaoOdsDTO = $this->retornaClassificacao(array($objProcedimentoAPI->getIdProcedimento(), $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds()));

                                if (!is_null($objMdIaClassificacaoOdsDTO)) {

                                    $existeClassificacao = true;

                                    if (in_array($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario() , ['I', 'E'])) {
                                        return $this->retornaIconePendencia($objProcedimentoAPI);
                                    }
                                }
                            }
                            if (!$existeClassificacao) {
                                return $this->retornaIconePendencia($objProcedimentoAPI);
                            }
                        }
                    }
                }
            }
        }

        public function montaTooltip()
        {
            $title = "IA - Alerta";
            $descricao = "Pendência de classificação segundo os Objetivos de Desenvolvimento Sustentável da ONU ou divergência em sugestão mais recente feita pelo SEI IA ou por Usuário Externo.";

            $icone = "modulos/ia/imagens/md_ia_icone_alerta.svg?" . Icone::VERSAO;

            $img = "<a href='javascript:void(0);' " . PaginaSEI::montarTitleTooltip($descricao, $title) . " ><img src='" . $icone . "' class='imagemStatus' style='padding-top: 1px' /></a>";
            return $img;
        }

        public function montarIconeControleProcessos($arrObjProcedimentoDTO)
        {
            if ($this->verificaAcesso(NULL)) {

                if($this->exibeTooltip()) {
                    if (!is_null($this->consultaUnidadeAlerta())) {

                        $arrObjMdIaAdmObjetivoOdsDTO = $this->listaObjetivosOdsOnu();
                        if (!is_null($arrObjMdIaAdmObjetivoOdsDTO)) {
                            foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {
                                $existeClassificacao = false;
                                foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {

                                    $objMdIaClassificacaoOdsDTO = $this->retornaClassificacao(array($objProcedimentoDTO->getIdProcedimento(), $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds()));

                                    if (!is_null($objMdIaClassificacaoOdsDTO)) {

                                        $existeClassificacao = true;

                                        if (in_array($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario(), ['I', 'E'])) {
                                            $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip();
                                            break;
                                        }
                                    }
                                }

                                if (!$existeClassificacao) {
                                    $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip();
                                }
                            }
                        }
                        return $arrIcone;
                    }
                }
            }

        }

        public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO)
        {

            if ($this->verificaAcesso(NULL)) {

                if($this->exibeTooltip()) {
                    if (!is_null($this->consultaUnidadeAlerta())) {

                        $arrObjMdIaAdmObjetivoOdsDTO = $this->listaObjetivosOdsOnu();
                        if (!is_null($arrObjMdIaAdmObjetivoOdsDTO)) {
                            foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {

                                $existeClassificacao = false;

                                foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {

                                    $objMdIaClassificacaoOdsDTO = $this->retornaClassificacao(array($objProcedimentoDTO->getIdProcedimento(), $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds()));

                                    if (!is_null($objMdIaClassificacaoOdsDTO)) {

                                        $existeClassificacao = true;

                                        if ($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario() == "I") {
                                            $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip();;
                                            break;
                                        }
                                    }
                                }
                                if (!$existeClassificacao) {
                                    $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip();
                                }
                            }
                        }
                        return $arrIcone;
                    }
                }
            }
        }

        public function processarControlador($strAcao)
        {
            switch ($strAcao) {
                case 'md_ia_recurso' :
                    require_once dirname(__FILE__) . '/md_ia_recurso_cadastro.php';
                    return true;
                case 'md_ia_configuracao_similaridade' :
                    require_once dirname(__FILE__) . '/md_ia_adm_config_similar_cadastro.php';
                    return true;
                case 'md_ia_adm_doc_relev_listar' :
                case 'md_ia_adm_doc_relev_excluir' :
                case 'md_ia_adm_doc_relev_desativar':
                case 'md_ia_adm_doc_relev_reativar':
                    require_once dirname(__FILE__) . '/md_ia_adm_doc_relev_lista.php';
                    return true;
                case 'md_ia_adm_doc_relev_cadastrar' :
                case 'md_ia_adm_doc_relev_consultar' :
                case 'md_ia_adm_doc_relev_alterar' :
                    require_once dirname(__FILE__) . '/md_ia_adm_doc_relev_cadastro.php';
                    return true;

                case 'md_ia_adm_integracao_cadastrar':
                case 'md_ia_adm_integracao_alterar':
                case 'md_ia_adm_integracao_consultar':
                    require_once dirname(__FILE__) . '/md_ia_adm_integracao_cadastro.php';
                    return true;

                case 'md_ia_adm_integracao_listar':
                case 'md_ia_adm_integracao_excluir':
                case 'md_ia_adm_integracao_desativar':
                case 'md_ia_adm_integracao_reativar':
                    require_once dirname(__FILE__) . '/md_ia_adm_integracao_lista.php';
                    return true;
                case 'md_ia_adm_pesq_doc_cadastro' :
                    require_once dirname(__FILE__) . '/md_ia_adm_pesq_doc_cadastro.php';
                    return true;

                case 'md_ia_resultado_pesquisa_documento':
                    require_once dirname(__FILE__) . '/md_ia_resultado_pesquisa_documento.php';
                    return true;

                case 'md_ia_protocolos_selecionar':
                    require_once dirname(__FILE__) . '/md_ia_protocolos_selecionar.php';
                    return true;

                case 'md_ia_adm_ods_onu' :
                case 'md_ia_adm_ods_onu_consultar':
                    require_once dirname(__FILE__) . '/md_ia_adm_ods_onu_cadastro.php';
                    return true;

                case 'md_ia_unidade_selecionar_todas':
                    require_once dirname(__FILE__) . '/md_ia_unidade_lista.php';
                    return true;

                case 'md_ia_consultar_objetivo_procedimento':
                    require_once dirname(__FILE__) . '/md_ia_consultar_objetivo_procedimento.php';
                    return true;

                case 'md_ia_consultar_objetivo':
                    require_once dirname(__FILE__) . '/md_ia_consultar_objetivo.php';
                    return true;

                case 'md_ia_adm_config_assistente_ia' :
                    require_once dirname(__FILE__) . '/md_ia_adm_config_assistente_ia.php';
                    return true;

                case 'md_ia_modal_orientacoes_gerais' :
                    require_once dirname(__FILE__) . '/md_ia_modal_orientacoes_gerais.php';
                    return true;

                case 'md_ia_modal_configuracoes_assistente_ia' :
                    require_once dirname(__FILE__) . '/md_ia_modal_configuracoes_assistente_ia.php';
                    return true;

                case 'md_ia_classificar_usu_ext':
                    require_once dirname(__FILE__) . '/md_ia_classificar_usu_ext.php';
                    return true;

                case 'md_ia_modal_chats_arquivados' :
                    require_once dirname(__FILE__) . '/md_ia_modal_chats_arquivados.php';
                    return true;

            }

            return false;
        }

        public function processarControladorAjax($strAcao)
        {
            $xml = null;

            switch ($_GET['acao_ajax']) {

                case 'md_ia_similaridade_cadastrar_ajax':
                    $json = MdIaRecursoINT::enviarFeedbackProcessos($_POST);
                    $json = json_encode($json[1]->message);
                    InfraAjax::enviarJSON($json);
                    exit(0);

                case 'md_ia_adm_doc_relev_tipo_documento_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaSelectTipoDocumento($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_documento_relevante_validar_ajax':
                    $itemAdicionado = MdIaAdmDocRelevINT::verificarItemAdicionado($_POST);
                    $json = json_encode($itemAdicionado);
                    InfraAjax::enviarJSON($json);
                    exit();

                case 'md_ia_adm_doc_relev_tipo_documento_cadastrado_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaSelectTipoDocumentoCadastrado($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_adm_doc_relev_tipo_procedimento_cadastrado_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaSelectTipoProcessoCadastrado($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_integracao_busca_operacao_ajax':
                    if ($_POST['tipoWs'] == 'SOAP')
                        $xml = MdIaAdmIntegracaoINT::montarOperacaoSOAP($_POST);
                    else
                        $xml = MdIaAdmIntegracaoINT::montarOperacaoREST($_POST);
                    break;

                case 'md_ia_pesquisa_documentos_ajax':
                    $json = MdIaRecursoINT::retornaUrlModalPesquisaDocumentos($_POST);
                    InfraAjax::enviarJSON($json);
                    exit(0);

                case 'md_ia_pesquisa_documentos_api_ajax':
                    $json = MdIaRecursoINT::consultaPesquisaDocumento($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit();

                case 'md_ia_adm_doc_relev_combobox_tipo_documento_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaComboboxTipoDocumento($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_adm_doc_relev_combobox_tipo_processo_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaComboboxTipoProcessos($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_adm_doc_relev_aplicabilidade_ajax':
                    $objetoSerie = MdIaAdmDocRelevINT::retornaSelectAplicabilidadeCadastrada($_POST);
                    $xml = InfraAjax::gerarXMLSelect($objetoSerie);
                    break;

                case 'md_ia_documento_relevante_validar_reativacao_ajax':
                    $itemAdicionado = MdIaAdmDocRelevINT::validarReativacao($_POST);
                    $json = json_encode($itemAdicionado);
                    InfraAjax::enviarJSON($json);
                    exit();

                case 'md_ia_documento_relevante_validar_desativados_ajax':
                    $itemAdicionado = MdIaAdmDocRelevINT::verificarItemAdicionadoDesativado($_POST);
                    $json = json_encode($itemAdicionado);
                    InfraAjax::enviarJSON($json);
                    exit();

                case 'md_ia_unidade_alerta_auto_completar_ajax':
                    $arrObjUnidadeDTO = UnidadeINT::autoCompletarUnidades($_POST['palavras_pesquisa'], true, '');
                    $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO, 'IdUnidade', 'Sigla');
                    break;

                case 'md_ia_ods_consultar_objetivo_procedimento_ajax':
                    $json = MdIaAdmObjetivoOdsINT::consultarObjetivoProcedimento($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit();

                case 'md_ia_ods_consultar_objetivo_ajax':
                    $json = MdIaAdmObjetivoOdsINT::consultarObjetivo($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit();

                case 'md_ia_ods_consultar_metas_usu_ext_ajax':
                    $json = MdIaAdmObjetivoOdsINT::consultarObjetivoParaClassificacaoUsuExt($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit();

                case 'md_ia_cadastrar_classificacao_ods_ajax':
                    $json = MdIaClassificacaoOdsINT::salvarClassificacaoOds($_POST);
                    InfraAjax::enviarJSON($json);
                    exit(0);
	
	            case 'md_ia_ods_consultar_objetivos_selecionados_ajax':
		            $json = MdIaAdmObjetivoOdsINT::consultarObjetivoSelecionados($_POST);
		            InfraAjax::enviarJSON($json);
		            exit(0);
	
	            case 'md_ia_ods_salvar_metas_selecionadas_sessao_ajax':
		            $json = MdIaAdmObjetivoOdsINT::salvarMetasSelecionadasSessao($_POST);
		            InfraAjax::enviarJSON($json);
		            exit(0);

                case 'md_ia_adm_configurar_metas_ajax':
                    $json = MdIaAdmMetaOdsINT::salvarConfiguracaoMetas($_POST);
                    InfraAjax::enviarJSON($json);
                    exit(0);

                case 'md_ia_assistente_envia_mensagem_ajax':
                    $json = MdIaConfigAssistenteINT::enviarMensagemAssistenteIa($_POST);
                    InfraAjax::enviarJSON($json);
                    exit(0);

                case 'md_ia_pesquisa_documento_cadastrar_ajax':
                    $json = MdIaRecursoINT::enviarFeedbackDocumentos($_POST);
                    $json = json_encode($json[1]->message);
                    InfraAjax::enviarJSON($json);
                    exit(0);

                case 'md_ia_assistente_consulta_disponibilidade_ajax':
                    $json = MdIaConfigAssistenteINT::consultarDisponibilidadeApi($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_assistente_enviar_feedback_ajax':
                    $json = MdIaConfigAssistenteINT::enviarFeedbackProcessos($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_consulta_protocolo_assistente_ia_ajax':
                    $json = MdIaConfigAssistenteINT::consultaProtocolo($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_gera_log_excedeu_limite_janela_contexto_ajax':
                    $json = MdIaConfigAssistenteINT::geraLogExcedeuJanelaContexto($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_usuario_auto_completar':
                    $arrObjUsuarioDTO = MdIaAdmConfigAssistIAINT::autoCompletarUsuarios($_POST['id_orgao'], $_POST['palavras_pesquisa'], false, false, true, false);
                    $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUsuarioDTO, 'IdUsuario', 'Sigla');
                    break;

                case 'md_ia_consultar_mensagem_ajax':
                    $json = MdIaConfigAssistenteINT::consultarMensagem($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_adicionar_topico_ajax':
                    $json = MdIaTopicoChatINT::adicionarTopico();
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_listar_topicos':
                    $json = MdIaTopicoChatINT::listarTopicos();
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_selecionar_topico':
                    $json = MdIaTopicoChatINT::selecionarTopico($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_renomear_topico':
                    $json = MdIaTopicoChatINT::renomearTopico($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_arquivar_topico':
                    $json = MdIaTopicoChatINT::arquivarTopico($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);

                case 'md_ia_desarquivar_topico':
                    $json = MdIaTopicoChatINT::desarquivarTopico($_POST);
                    InfraAjax::enviarJSON(json_encode($json));
                    exit(0);
            }
            return $xml;
        }

        public function processarControladorWebServices($strServico)
        {

            $strArq = null;

            switch ($strServico) {

                case 'wsia':
                    $strArq = 'wsia.wsdl';
                    break;

                default:
                    break;
            }

            if ($strArq != null) {
                $strArq = dirname(__FILE__) . '/ws/' . $strArq;
            }

            return $strArq;
        }

        public function excluirTipoDocumento($arrObjTpDocumento)
        {
            $mdIaRecursoRN = new MdIaRecursoRN();
            $msg = $mdIaRecursoRN->verificarExistenciaTipoDocumento($arrObjTpDocumento);
            if ($msg != '') {
                $objInfraException = new InfraException();
                $objInfraException->lancarValidacao($msg);
            } else {
                return $arrObjTpDocumento;
            }
        }

        public function excluirTipoProcesso($arrObjTipoProcessoDTO)
        {

            $mdIaRecursoRN = new MdIaRecursoRN();
            $msg = $mdIaRecursoRN->verificarExistenciaTipoProcesso($arrObjTipoProcessoDTO);

            //verifica se existe um processo sendo utilizado
            if ($msg != '') {
                $objInfraException = new InfraException();
                $objInfraException->lancarValidacao($msg);
            } else {
                return $arrObjTipoProcessoDTO;
            }
        }
        public function montarBotaoChatIA()
        {
            if ($this->verificaAcesso(NULL, true)) {
                $acao = $_REQUEST['acao'];
                if(SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar')) {
                    // Consulta as configuraes da funcionalidade para saber se deve ou no exibir o chat
                    $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
                    $objMdIaAdmConfigAssistIADTO->retStrSinExibirFuncionalidade();
                    $objMdIaAdmConfigAssistIADTO->setNumMaxRegistrosRetorno(1);
                    $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
                    $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);
                    if($objMdIaAdmConfigAssistIADTO) {
                        if ($objMdIaAdmConfigAssistIADTO->getStrSinExibirFuncionalidade() == 'S' && in_array($acao, $this->getTelasApareceChat())) {
                            require_once('../../sei/web/modulos/ia/md_ia_chat_js.php');
                            return MdIaConfigAssistenteINT::montarChat();
                        }
                    }
                }
                return null;
            }
        }

        private function getTelasApareceChat()
        {
            $arr = [
                'procedimento_controlar',
                'procedimento_trabalhar',
                'editor_montar'
            ];

            return $arr;
        }
}