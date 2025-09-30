<?php

class IaIntegracao extends SeiIntegracao
{

    const PARAMETRO_VERSAO_MODULO = 'VERSAO_MODULO_IA';

    public function __construct() {}

    public function getNome()
    {
        return 'SEI IA';
    }

    public function getVersao()
    {
        return '1.3.0';
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
        $strLinkIA = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']);
        $imgIconeIA = "modulos/ia/imagens/md_ia_icone_1.svg?" . Icone::VERSAO;
        $titleIA = "Inteligência Artificial";
        $strBotaoAvaliacaoSeiIa = '<a href="' . $strLinkIA . '"class="botaoSEI">';
        $strBotaoAvaliacaoSeiIa .= '    <img class="infraCorBarraSistema" src="' . $imgIconeIA . '" alt="' . $titleIA . '" title="' . $titleIA . '">';
        $strBotaoAvaliacaoSeiIa .= '</a>';

        $strBotaoAvaliacaoSeiIa .= $this->retornarBotaoOds();
        return $strBotaoAvaliacaoSeiIa;
    }

    private function retornarBotaoOds()
    {
        $strBotaoOds = '';
        $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
        $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
        $objMdIaAdmOdsOnuDTO->retNumIdMdIaAdmOdsOnu();
        $objMdIaAdmOdsOnuDTO->retStrOrientacoesGerais();
        $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
        $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

        if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
            $strLinkODS = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_ods&arvore=1&id_procedimento=' . $_GET['id_procedimento']);
            $imgIconeODS = "modulos/ia/imagens/md_ia_icone_ods.png?" . Icone::VERSAO;
            $titleODS = "Classificação pelos ODS da ONU";
            $strBotaoOds .= '<a href="' . $strLinkODS . '"class="botaoSEI">';
            $strBotaoOds .= '    <img class="infraCorBarraSistema" src="' . $imgIconeODS . '" alt="' . $titleODS . '" title="' . $titleODS . '">';
            $strBotaoOds .= '</a>';
        }

        return $strBotaoOds;
    }

    public function verificaAcesso($objProcedimentoAPI, $bolConsideraChatIa = false)
    {
        if (!is_null($objProcedimentoAPI)) {
            $bolPermissaoAcesso = $objProcedimentoAPI->getCodigoAcesso() > 0;
        } else {
            $bolPermissaoAcesso = true;
        }

        $mdIaRecursoRN = new MdIaRecursoRN();
        $bolExibirFuncionalidade = $mdIaRecursoRN->exibeFuncionalidade($bolConsideraChatIa);
        if ($bolExibirFuncionalidade && $bolPermissaoAcesso) {
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

    private function montarArrIcone($arrObjProcedimentoDTO)
    {
        if ($this->verificaAcesso(NULL) && $this->exibeTooltip()) {

            foreach ($arrObjProcedimentoDTO as $objProcedimentoDTO) {
                if ($this->sugeridoPorUsuarioExtIntArtificial($objProcedimentoDTO->getIdProcedimento())) {
                    $descricao = "Pendência de validação de sugestão feita pelo SEI IA ou por Usuário Externo de classificação do processo segundo os Objetivos de Desenvolvimento Sustentável da ONU.";
                    $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip($descricao);;
                    break;
                }
                if (!$this->verificarSeJaFoiClassificadoAlgumaVez($objProcedimentoDTO->getIdProcedimento()) && !is_null($this->consultaUnidadeAlerta())) {
                    $descricao = "Pendência de classificação do processo segundo os Objetivos de Desenvolvimento Sustentável da ONU.";
                    $arrIcone[$objProcedimentoDTO->getIdProcedimento()][] = $this->montaTooltip($descricao);
                }
            }

            return $arrIcone;
        }
    }

    private function sugeridoPorUsuarioExtIntArtificial($idProcedimento)
    {
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(array(MdIaClassMetaOdsRN::$USUARIO_IA, MdIaClassMetaOdsRN::$USUARIO_EXTERNO), InfraDTO::$OPER_IN);
        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
        $objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
        return (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
    }

    private function verificarSeJaFoiClassificadoAlgumaVez($idProcedimento)
    {
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(array(MdIaClassMetaOdsRN::$USUARIO_PADRAO, MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO), InfraDTO::$OPER_IN);
        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
        $objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
        $objMdIaClassMetaOdsDTO =  (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
        return $objMdIaClassMetaOdsDTO ? true : false;
    }

    public function retornaIconePendencia($objProcedimentoAPI, $title)
    {
        $tipo = 'IA';
        $id = 'IA_' . $objProcedimentoAPI->getIdProcedimento();
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

            if ($this->exibeTooltip()) {

                if ($this->sugeridoPorUsuarioExtIntArtificial($objProcedimentoAPI->getIdProcedimento())) {
                    $title = 'IA - Alerta \nPendência de validação de sugestão feita pelo SEI IA ou por Usuário Externo de classificação do processo segundo os Objetivos de Desenvolvimento Sustentável da ONU.';
                    return $this->retornaIconePendencia($objProcedimentoAPI, $title);
                }

                if (!$this->verificarSeJaFoiClassificadoAlgumaVez($objProcedimentoAPI->getIdProcedimento()) && !is_null($this->consultaUnidadeAlerta())) {
                    $title = 'IA - Alerta \nPendência de classificação do processo segundo os Objetivos de Desenvolvimento Sustentável da ONU.';
                    return $this->retornaIconePendencia($objProcedimentoAPI, $title);
                }
            }
        }
    }

    public function montaTooltip($descricao)
    {
        $title = "IA - Alerta";
        $icone = "modulos/ia/imagens/md_ia_icone_alerta.svg?" . Icone::VERSAO;

        $img = "<a href='javascript:void(0);' " . PaginaSEI::montarTitleTooltip($descricao, $title) . " ><img src='" . $icone . "' class='imagemStatus' style='padding-top: 1px' /></a>";
        return $img;
    }

    public function montarIconeControleProcessos($arrObjProcedimentoDTO)
    {
        return $this->montarArrIcone($arrObjProcedimentoDTO);
    }

    public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO)
    {
        return $this->montarArrIcone($arrObjProcedimentoDTO);
    }

    public function processarControlador($strAcao)
    {
        switch ($strAcao) {
            case 'md_ia_recurso':
                require_once dirname(__FILE__) . '/md_ia_recurso_cadastro.php';
                return true;
            case 'md_ia_ods':
                require_once dirname(__FILE__) . '/md_ia_ods_cadastro.php';
                return true;
            case 'md_ia_configuracao_similaridade':
                require_once dirname(__FILE__) . '/md_ia_adm_config_similar_cadastro.php';
                return true;
            case 'md_ia_adm_doc_relev_listar':
            case 'md_ia_adm_doc_relev_excluir':
            case 'md_ia_adm_doc_relev_desativar':
            case 'md_ia_adm_doc_relev_reativar':
                require_once dirname(__FILE__) . '/md_ia_adm_doc_relev_lista.php';
                return true;
            case 'md_ia_adm_doc_relev_cadastrar':
            case 'md_ia_adm_doc_relev_consultar':
            case 'md_ia_adm_doc_relev_alterar':
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
            case 'md_ia_adm_pesq_doc_cadastro':
                require_once dirname(__FILE__) . '/md_ia_adm_pesq_doc_cadastro.php';
                return true;

            case 'md_ia_resultado_pesquisa_documento':
                require_once dirname(__FILE__) . '/md_ia_resultado_pesquisa_documento.php';
                return true;

            case 'md_ia_protocolos_selecionar':
                require_once dirname(__FILE__) . '/md_ia_protocolos_selecionar.php';
                return true;

            case 'md_ia_adm_ods_onu':
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

            case 'md_ia_adm_config_assistente_ia':
                require_once dirname(__FILE__) . '/md_ia_adm_config_assistente_ia.php';
                return true;

            case 'md_ia_modal_orientacoes_gerais':
                require_once dirname(__FILE__) . '/md_ia_modal_orientacoes_gerais.php';
                return true;

            case 'md_ia_modal_configuracoes_assistente_ia':
                require_once dirname(__FILE__) . '/md_ia_modal_configuracoes_assistente_ia.php';
                return true;

            case 'md_ia_classificar_usu_ext':
                require_once dirname(__FILE__) . '/md_ia_classificar_usu_ext.php';
                return true;

            case 'md_ia_modal_chats_arquivados':
                require_once dirname(__FILE__) . '/md_ia_modal_chats_arquivados.php';
                return true;

            case 'md_ia_grupo_prompts_fav_listar':
            case 'md_ia_grupo_prompts_fav_excluir':
                require_once dirname(__FILE__) . '/md_ia_grupo_prompts_fav.php';
                return true;

            case 'md_ia_grupo_prompts_fav_cadastrar':
            case 'md_ia_grupo_prompts_fav_alterar':
                require_once dirname(__FILE__) . '/md_ia_grupo_prompts_fav_cadastro.php';
                return true;

            case 'md_ia_prompts_favoritos_cadastrar':
            case 'md_ia_prompts_favoritos_alterar':
                require_once dirname(__FILE__) . '/md_ia_prompts_favoritos_cadastro.php';
                return true;

            case 'md_ia_prompts_favoritos_excluir':
            case 'md_ia_prompts_favoritos_selecionar':
                require_once dirname(__FILE__) . '/md_ia_prompts_favoritos.php';
                return true;

            case 'md_ia_adm_grupos_galeria_prompts':
            case 'md_ia_grupo_galeria_prompt_excluir':
                require_once dirname(__FILE__) . '/md_ia_grupo_galeria_prompt_lista.php';
                return true;

            case 'md_ia_grupo_galeria_prompt_cadastrar':
            case 'md_ia_grupo_galeria_prompt_alterar':
                require_once dirname(__FILE__) . '/md_ia_grupo_galeria_prompt_cadastro.php';
                return true;

            case 'md_ia_galeria_prompts_cadastrar':
            case 'md_ia_galeria_prompts_alterar':
                require_once dirname(__FILE__) . '/md_ia_galeria_prompts_cadastro.php';
                return true;

            case 'md_ia_galeria_prompts_excluir':
            case 'md_ia_galeria_prompts_selecionar':
            case 'md_ia_galeria_prompts_desativar':
            case 'md_ia_galeria_prompts_reativar':
                require_once dirname(__FILE__) . '/md_ia_galeria_prompts.php';
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
                $xml = MdIaAdmIntegracaoINT::montarOperacaoREST($_POST);
                break;

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

            case 'md_ia_tipo_procedimento_auto_completar':
                $arrObjTipoProcedimentoDTO = MdIaAdmObjetivoOdsINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa']);
                $xml                       = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcedimentoDTO, 'IdTipoProcedimento', 'Nome');
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
                $json = MdIaClassMetaOdsINT::salvarClassificacaoOds($_POST);
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
                // lê corpo raw e tenta decodificar JSON
                $raw = file_get_contents('php://input');
                $input = json_decode($raw, true);

                if (is_array($input) && isset($input['mensagemUsuario'])) {
                    $mensagemUsuario = $input['mensagemUsuario']; // o array que você enviou do JS
                } else {
                    // fallback para compatibilidade com envio form-urlencoded
                    $mensagemUsuario = $_POST;
                }

                $json = MdIaConfigAssistenteINT::enviarMensagemAssistenteIa($mensagemUsuario);
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
                $json = MdIaConfigAssistenteINT::enviarFeedbackResposta($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_consulta_protocolo_assistente_ia_ajax':
                // lê corpo raw e tenta decodificar JSON
                $raw = file_get_contents('php://input');
                $input = json_decode($raw, true);

                if (is_array($input) && isset($input['protocolo'])) {
                    $protocolo = $input['protocolo']; // o array que você enviou do JS
                } else {
                    // fallback para compatibilidade com envio form-urlencoded
                    $protocolo = $_POST;
                }

                $json = MdIaConfigAssistenteINT::consultaProtocolo($protocolo);
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

            case 'md_ia_busca_dados_integracao':
                $json = MdIaAdmIntegracaoINT::buscaDadosIntegracao($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_consultar_grupo_prompt_favorito_ajax':
                $json = MdIaGrupoPromptsFavINT::consultarGrupoPromptFavorito($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_consultar_prompt_galeria_prompts_ajax':
                $json = MdIaGaleriaPromptsRN::consultarPromptGaleriaPrompts($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_lista_ods_onu_ajax':
                $json = MdIaClassMetaOdsINT::listaOdsOnu($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_consultar_prompt_favorito_ajax':
                $json = MdIaPromptsFavoritosINT::consultarPromptFavorito($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);

            case 'md_ia_atualiza_nao_se_aplica_ods_onu_ajax':
                $json = MdIaOdsOnuNsaINT::atualizaNaoSeAplicaOdsOnu($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);
        }
        return $xml;
    }

    public function processarControladorWebServices($strServico)
    {
        if ($strServico != 'md_ia_documentacao' && $strServico != 'wsia') (new IaWS())->validarPermissao();

        $strArq = null;

        switch ($strServico) {

            case 'wsia':
                $strArq = 'wsia.wsdl';
                $strArq = dirname(__FILE__) . '/ws/' . $strArq;
                break;

            case 'md_ia_documentacao':
                if ($this->verificarPermitirSwagger()) {
                    readfile(dirname(__FILE__) . '/ws/wsia.html');
                    die;
                }
                break;

            case 'md_ia_download_arquivo_documento_externo':
                MdIaControladorWS::downloadArquivoDocumentoExterno();
                die;

            case 'md_ia_consulta_documento':
                MdIaControladorWS::consultarDocumento();
                die;

            case 'md_ia_consulta_processo':
                MdIaControladorWS::consultarProcesso();
                die;

            case 'md_ia_gera_hash_conteudo_documento':
                MdIaControladorWS::gerarHashConteudoDocumento();
                die;

            case 'md_ia_lista_tipo_documento':
                MdIaControladorWS::listarTipoDocumento();
                die;

            case 'md_ia_lista_segmentos_documentos_relevantes':
                MdIaControladorWS::listarSegmentosDocRelevantes();
                die;

            case 'md_ia_lista_percentual_relevancia_metadados':
                MdIaControladorWS::listarPercentualRelevanciaMetadados();
                die;

            case 'md_ia_lista_documentos_indexaveis':
            case 'md_ia_atualiza_documentos_indexaveis':
                MdIaControladorWS::documentosIndexaveis();
                die;

            case 'md_ia_consulta_conteudo_documento':
                MdIaControladorWS::consultarConteudoDocumento();
                die;

            case 'md_ia_lista_processos_indexaveis':
            case 'md_ia_atualiza_processos_indexaveis':
                MdIaControladorWS::processosIndexaveis();
                die;

            case 'md_ia_lista_processos_indexaveis_cancelados':
            case 'md_ia_remove_processos_indexaveis_cancelados':
                MdIaControladorWS::processosIndexadosCancelados();
                die;

            case 'md_ia_lista_documentos_indexaveis_cancelados':
            case 'md_ia_remove_documentos_indexaveis_cancelados':
                MdIaControladorWS::documentosIndexadosCancelados();
                die;

            case 'md_ia_lista_documentos_elegiveis_processos_similares':
                MdIaControladorWS::listarDocumentosRelevantesProcesso();
                die;

            case 'md_ia_consulta_historico_topico':
                MdIaControladorWS::consultarHistoricoTopico();
                die;

            case 'md_ia_consulta_ultimo_id_message':
                MdIaControladorWS::consultarUltimoIdMessage();
                die;

            default:
                break;
        }

        return $strArq;
    }

    private function verificarPermitirSwagger()
    {
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $paramLiberarAutoAvaliacao = $objInfraParametro->getValor('MODULO_IA_AUTORIZAR_SWAGGER', false);
        $pertirAcessoViaParametro = false;
        if (isset($paramLiberarAutoAvaliacao) && $paramLiberarAutoAvaliacao == 1) {
            $pertirAcessoViaParametro = true;
        }

        return $pertirAcessoViaParametro;
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
            if (SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar')) {
                // Consulta as configuraes da funcionalidade para saber se deve ou no exibir o chat
                $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
                $objMdIaAdmConfigAssistIADTO->retStrSinExibirFuncionalidade();
                $objMdIaAdmConfigAssistIADTO->setNumMaxRegistrosRetorno(1);
                $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
                $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);
                if ($objMdIaAdmConfigAssistIADTO) {
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

    public function excluirProcesso(ProcedimentoAPI $objProcedimentoAPI)
    {
        $MdIaHistClassDTO = new MdIaHistClassDTO();
        $MdIaHistClassRN = new MdIaHistClassRN();
        $MdIaHistClassDTO->setDblIdProcedimento($objProcedimentoAPI->getIdProcedimento());
        $MdIaHistClassDTO->retTodos();
        $arrMdIaHistClassDTO = $MdIaHistClassRN->listar($MdIaHistClassDTO);
        if ($arrMdIaHistClassDTO) {
            $MdIaHistClassRN->excluir($arrMdIaHistClassDTO);
        }

        $MdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $MdIaClassMetaOdsRN = new MdIaClassMetaOdsRN();
        $MdIaClassMetaOdsDTO->setDblIdProcedimento($objProcedimentoAPI->getIdProcedimento());
        $MdIaClassMetaOdsDTO->retTodos();
        $arrMdIaClassMetaOdsDTO = $MdIaClassMetaOdsRN->listar($MdIaClassMetaOdsDTO);
        if ($arrMdIaClassMetaOdsDTO) {
            $MdIaClassMetaOdsRN->excluir($arrMdIaClassMetaOdsDTO);
        }
    }
}
