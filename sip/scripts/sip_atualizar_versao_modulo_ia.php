<?
require_once dirname(__FILE__) . '/../web/Sip.php';

class MdIaAtualizadorSipRN extends InfraRN
{

    const PARAMETRO_VERSAO_MODULO = 'VERSAO_MODULO_IA';

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '1.0.0';
    private $nomeDesteModulo = 'MÓDULO DO IA';
    private $nomeParametroModulo = 'VERSAO_MODULO_IA';
    private $historicoVersoes = array('1.0.0', '1.1.0');

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSip::getInstance();
    }

    protected function inicializar($strTitulo)
    {
        session_start();
        SessaoSip::getInstance(false);

        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        @ini_set('implicit_flush', '1');
        ob_implicit_flush();

        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(true);
        InfraDebug::getInstance()->setBolEcho(true);
        InfraDebug::getInstance()->limpar();

        $this->numSeg = InfraUtil::verificarTempoProcessamento();

        $this->logar($strTitulo);
    }

    protected function logar($strMsg)
    {
        InfraDebug::getInstance()->gravar($strMsg);
        flush();
    }

    protected function finalizar($strMsg = null, $bolErro = false)
    {
        if (!$bolErro) {
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $this->logar('TEMPO TOTAL DE EXECUÇÃO: ' . $this->numSeg . ' s');
        } else {
            $strMsg = 'ERRO: ' . $strMsg;
        }

        if ($strMsg != null) {
            $this->logar($strMsg);
        }

        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        $this->numSeg = 0;
        die;
    }

    protected function atualizarVersaoConectado()
    {
        try {
            $this->inicializar('INICIANDO A INSTALAÇÃO/ATUALIZAÇÃO DO ' . $this->nomeDesteModulo . ' NO SIP VERSÃO ' . SIP_VERSAO);

            //checando BDs suportados
            if (
                !(BancoSip::getInstance() instanceof InfraMySql) &&
                !(BancoSip::getInstance() instanceof InfraSqlServer) &&
                !(BancoSip::getInstance() instanceof InfraPostgreSql) &&
                !(BancoSip::getInstance() instanceof InfraOracle)
            ) {

                $this->finalizar('BANCO DE DADOS NÃO SUPORTADO: ' . get_parent_class(BancoSip::getInstance()), true);
            }

            //testando versao do framework
            $numVersaoInfraRequerida = '2.29.0';
            if (version_compare(VERSAO_INFRA, $numVersaoInfraRequerida) < 0) {
                $this->finalizar('VERSÃO DO FRAMEWORK PHP INCOMPATÍVEL (VERSÃO ATUAL ' . VERSAO_INFRA . ', SENDO REQUERIDA VERSÃO IGUAL OU SUPERIOR A ' . $numVersaoInfraRequerida . ')', true);
            }

            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sip_teste')) == 0) {
                BancoSip::getInstance()->executarSql('CREATE TABLE sip_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
            }
            BancoSip::getInstance()->executarSql('DROP TABLE sip_teste');

            $objInfraParametro = new InfraParametro(BancoSip::getInstance());

            $strVersaoModulo = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            switch ($strVersaoModulo) {
                case '':
                    $this->instalarv100();
                case '1.0.0':
                    $this->instalarv110();
                    break;
                case '1.1.0':
                    $this->instalarv120();
                    break;
                default:
                    $this->finalizar('A VERSÃO MAIS ATUAL DO ' . $this->nomeDesteModulo . ' (v' . $this->versaoAtualDesteModulo . ') JÁ ESTÁ INSTALADA.');
                    break;
            }

            $this->logar('SCRIPT EXECUTADO EM: ' . date('d/m/Y H:i:s'));
            $this->finalizar('FIM');
            InfraDebug::getInstance()->setBolDebugInfra(true);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando versão.', $e);
        }
    }

    protected function instalarv100()
    {
        $nmVersao = '1.0.0';

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $objSistemaRN = new SistemaRN();
        $objPerfilRN = new PerfilRN();
        $objMenuRN = new MenuRN();
        $objItemMenuRN = new ItemMenuRN();

        $objSistemaDTO = new SistemaDTO();
        $objSistemaDTO->retNumIdSistema();
        $objSistemaDTO->setStrSigla('SEI');

        $objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI não encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        //Perfil Básico
        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Básico');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Básico do sistema SEI não encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI não encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();


        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administração');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Configurações de Similaridade  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_configuracao_similaridade');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial EM Administrador');
        $objItemMenuDTOInteligênciaArtificial = $this->adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Inteligência Artificial', 0);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Configurações de Similaridade');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'Configurações de Similaridade',
            10
        );


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Configurações de Similaridade  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_config_similar_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_metadado_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_config_similar_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_perc_relev_met_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_perc_relev_met_cadastrar');

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Documentos Relevantes  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_listar');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Documentos Relevantes');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'Documentos Relevantes',
            10
        );

        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_seg_doc_relev_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_seg_doc_relev_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_desativar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_seg_doc_relev_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_pesquisar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_doc_relev_reativar');

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Mapeamento das Integrações  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_listar');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Mapeamento das Integrações');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'Mapeamento das Integrações',
            10
        );

        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_reativar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_integracao_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integracao_desativar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_perc_relev_met_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_recurso');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_perc_relev_met_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_perc_relev_met_alterar');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Pesquisa de Documentos  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_pesq_doc_cadastro');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Pesquisa de Documentos');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'Pesquisa de Documentos',
            10
        );

        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_pesq_doc_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_pesq_doc_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_tp_doc_pesq_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_tp_doc_pesq_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_tp_doc_pesq_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_resultado_pesquisa_documento');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_protocolos_selecionar');

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - ODSs da ONU  EM Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_ods_onu');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->ODSs da ONU');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'ODSs da ONU',
            10
        );

        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_ods_onu_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_ods_onu_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_unidade_alerta_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_unidade_selecionar_todas');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_objetivo_ods_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_meta_ods_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_consultar_objetivo');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_objetivo_ods_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_unidade_alerta_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_unidade_alerta_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_classificacao_ods_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_classificacao_ods_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_classificacao_ods_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_class_meta_ods_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_classificacao_ods_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_class_meta_ods_listar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_class_meta_ods_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_class_meta_ods_alterar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_unidade_alerta_consultar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_hist_class_cadastrar');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_class_meta_ods_excluir');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_hist_class_consultar');

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Configuraes do Assistente IA  Em Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_config_assistente_ia');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Configurações do Assistente IA');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOInteligênciaArtificial->getNumIdItemMenu(),
            $objRecursoPerfil->getNumIdRecurso(),
            'Configurações do Assistente IA',
            10
        );

        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_config_assist_ia_alterar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_adm_config_assist_ia_consultar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_hist_class_listar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_meta_ods_alterar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_cfg_assi_ia_usu_cadastrar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_cfg_assi_ia_usu_excluir');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_consultar_documento_externo_ia');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_integ_funcion_listar');

        $arrAuditoria = array();

        array_push(
            $arrAuditoria,
            '\'md_ia_adm_config_similar_alterar\'',
            '\'md_ia_adm_perc_relev_met_excluir\'',
            '\'md_ia_adm_perc_relev_met_cadastrar\'',
            '\'md_ia_adm_doc_relev_cadastrar\'',
            '\'md_ia_adm_seg_doc_relev_cadastrar\'',
            '\'md_ia_adm_doc_relev_alterar\'',
            '\'md_ia_adm_doc_relev_excluir\'',
            '\'md_ia_adm_doc_relev_desativar\'',
            '\'md_ia_adm_seg_doc_relev_excluir\'',
            '\'md_ia_adm_doc_relev_reativar\'',
            '\'md_ia_adm_integracao_cadastrar\'',
            '\'md_ia_adm_integracao_reativar\'',
            '\'md_ia_adm_integracao_excluir\'',
            '\'md_ia_adm_integracao_alterar\'',
            '\'md_ia_adm_integracao_desativar\'',
            '\'md_ia_adm_perc_relev_met_alterar\'',
            '\'md_ia_adm_pesq_doc_alterar\'',
            '\'md_ia_adm_tp_doc_pesq_excluir\'',
            '\'md_ia_adm_tp_doc_pesq_cadastrar\'',
            '\'md_ia_adm_ods_onu_alterar\'',
            '\'md_ia_adm_unidade_alerta_excluir\'',
            '\'md_ia_adm_unidade_alerta_cadastrar\'',
            '\'md_ia_classificacao_ods_cadastrar\'',
            '\'md_ia_classificacao_ods_alterar\'',
            '\'md_ia_adm_config_assist_ia_alterar\'',
            '\'md_ia_adm_meta_ods_alterar\'',
            '\'md_ia_consultar_documento_externo_ia\''
        );
        $this->_cadastrarAuditoria($numIdSistemaSei, $arrAuditoria);

        $this->logar('ADICIONANDO PARÂMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('INSERT INTO infra_parametro(valor, nome) VALUES(\'' . $nmVersao . '\',  \'' . $this->nomeParametroModulo . '\' )');
        $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv110()
    {
        $nmVersao = '1.1.0';

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $objSistemaRN = new SistemaRN();
        $objPerfilRN = new PerfilRN();
        $objMenuRN = new MenuRN();
        $objItemMenuRN = new ItemMenuRN();

        $objSistemaDTO = new SistemaDTO();
        $objSistemaDTO->retNumIdSistema();
        $objSistemaDTO->setStrSigla('SEI');

        $objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI não encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        //Perfil Básico
        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Básico');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Básico do sistema SEI não encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI não encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();


        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administração');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retTodos();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setNumIdMenu($numIdMenuSei);
        $objItemMenuDTO->setStrRotulo('Configurações do Assistente IA');

        $objItemMenuRN = new ItemMenuRN();
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO != null) {
            $numIdItemMenuSeiInfra = $objItemMenuDTO->getNumIdItemMenu();
            $objItemMenuDTO->setStrRotulo('Assistente IA');
            $objItemMenuDTO->setNumIdRecurso('');
            $objItemMenuRN->alterar($objItemMenuDTO);
        }

        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_config_assistente_ia');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Assistente IA->Configurações do Assistente IA');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $numIdItemMenuSeiInfra,
            $objRecursoPerfil->getNumIdRecurso(),
            'Configurações do Assistente IA',
            10
        );

        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Configuraes do Assistente IA  Em Administrador');
        $objRecursoPerfil = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_adm_grupos_galeria_prompts');

        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administração->Inteligência Artificial->Assistente IA->Grupos de Galeria de Prompts');
        $this->adicionarItemMenu(
            $numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $numIdItemMenuSeiInfra,
            $objRecursoPerfil->getNumIdRecurso(),
            'Grupos de Galeria de Prompts',
            10
        );

        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_grupo_galeria_prompt_cadastrar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_grupo_galeria_prompt_alterar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_grupo_galeria_prompt_excluir');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_ia_galeria_prompt_moderador');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_galeria_prompt_cadastrar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_galeria_prompt_alterar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_galeria_prompt_desativar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_galeria_prompt_reativar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_ia_galeria_prompt_excluir');

        $arrAuditoria = array();

        array_push(
            $arrAuditoria,
            '\'md_ia_grupo_galeria_prompt_cadastrar\'',
            '\'md_ia_grupo_galeria_prompt_alterar\'',
            '\'md_ia_grupo_galeria_prompt_excluir\'',
            '\'md_ia_galeria_prompt_cadastrar\'',
            '\'md_ia_galeria_prompt_alterar\'',
            '\'md_ia_galeria_prompt_desativar\'',
            '\'md_ia_galeria_prompt_reativar\'',
            '\'md_ia_galeria_prompt_excluir\''
        );
        $this->_cadastrarAuditoria($numIdSistemaSei, $arrAuditoria);

        $this->atualizarNumeroVersao($nmVersao);
    }
    
    protected function instalarv120()
    {
        $nmVersao = '1.2.0';
        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $nmVersao .' DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');
        //Atualizando parametro para controlar versao do modulo
        $this->atualizarNumeroVersao($nmVersao);
    }

    private function adicionarRecursoPerfil($numIdSistema, $numIdPerfil, $strNome, $strCaminho = null)
    {

        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNome);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO == null) {
            $objRecursoDTO = new RecursoDTO();
            $objRecursoDTO->setNumIdRecurso(null);
            $objRecursoDTO->setNumIdSistema($numIdSistema);
            $objRecursoDTO->setStrNome($strNome);
            $objRecursoDTO->setStrDescricao(null);

            if ($strCaminho == null) {
                $objRecursoDTO->setStrCaminho('controlador.php?acao=' . $strNome);
            } else {
                $objRecursoDTO->setStrCaminho($strCaminho);
            }
            $objRecursoDTO->setStrSinAtivo('S');
            $objRecursoDTO = $objRecursoRN->cadastrar($objRecursoDTO);
        }

        if ($numIdPerfil != null) {
            $objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
            $objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

            $objRelPerfilRecursoRN = new RelPerfilRecursoRN();

            if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO) == 0) {
                $objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
            }
        }

        return $objRecursoDTO;
    }

    private function adicionarItemMenu($numIdSistema, $numIdPerfil, $numIdMenu, $numIdItemMenuPai, $numIdRecurso, $strRotulo, $numSequencia)
    {

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdMenu($numIdMenu);

        if ($numIdItemMenuPai == null) {
            $objItemMenuDTO->setNumIdMenuPai(null);
            $objItemMenuDTO->setNumIdItemMenuPai(null);
        } else {
            $objItemMenuDTO->setNumIdMenuPai($numIdMenu);
            $objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
        }

        $objItemMenuDTO->setNumIdSistema($numIdSistema);
        $objItemMenuDTO->setNumIdRecurso($numIdRecurso);
        $objItemMenuDTO->setStrRotulo($strRotulo);

        $objItemMenuRN = new ItemMenuRN();
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            $objItemMenuDTO = new ItemMenuDTO();
            $objItemMenuDTO->setNumIdItemMenu(null);
            $objItemMenuDTO->setNumIdMenu($numIdMenu);

            if ($numIdItemMenuPai == null) {
                $objItemMenuDTO->setNumIdMenuPai(null);
                $objItemMenuDTO->setNumIdItemMenuPai(null);
            } else {
                $objItemMenuDTO->setNumIdMenuPai($numIdMenu);
                $objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
            }

            $objItemMenuDTO->setNumIdSistema($numIdSistema);
            $objItemMenuDTO->setNumIdRecurso($numIdRecurso);
            $objItemMenuDTO->setStrRotulo($strRotulo);
            $objItemMenuDTO->setStrDescricao(null);
            $objItemMenuDTO->setNumSequencia($numSequencia);
            $objItemMenuDTO->setStrSinNovaJanela('N');
            $objItemMenuDTO->setStrSinAtivo('S');
            $objItemMenuDTO->setStrIcone(null);

            $objItemMenuDTO = $objItemMenuRN->cadastrar($objItemMenuDTO);
        }

        if ($numIdPerfil != null && $numIdRecurso != null) {
            $objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
            $objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilRecursoDTO->setNumIdRecurso($numIdRecurso);

            $objRelPerfilRecursoRN = new RelPerfilRecursoRN();

            if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO) == 0) {
                $objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
            }

            $objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
            $objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilItemMenuDTO->setNumIdRecurso($numIdRecurso);
            $objRelPerfilItemMenuDTO->setNumIdMenu($numIdMenu);
            $objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

            $objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

            if ($objRelPerfilItemMenuRN->contar($objRelPerfilItemMenuDTO) == 0) {
                $objRelPerfilItemMenuRN->cadastrar($objRelPerfilItemMenuDTO);
            }
        }

        return $objItemMenuDTO;
    }

    private function _cadastrarAuditoria($numIdSistemaSei, $arrAuditoria)
    {
        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');

        //novo grupo de regra de auditoria nova
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Ia');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $countRgAuditoria = $objRegraAuditoriaRN->contar($objRegraAuditoriaDTO);
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        if ($countRgAuditoria == 0) {
            $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS');
            $objRegraAuditoriaDTO2 = new RegraAuditoriaDTO();
            $objRegraAuditoriaDTO2->retNumIdRegraAuditoria();
            $objRegraAuditoriaDTO2->setNumIdRegraAuditoria(null);
            $objRegraAuditoriaDTO2->setStrSinAtivo('S');
            $objRegraAuditoriaDTO2->setNumIdSistema($numIdSistemaSei);
            $objRegraAuditoriaDTO2->setArrObjRelRegraAuditoriaRecursoDTO(array());
            $objRegraAuditoriaDTO2->setStrDescricao('Modulo_Ia');

            $objRegraAuditoriaDTO = $objRegraAuditoriaRN->cadastrar($objRegraAuditoriaDTO2);
        }

        $rs = BancoSip::getInstance()->consultarSql(
            'select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          ' . implode(', ', $arrAuditoria) . ')'
        );

        //CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
    }

    /**
     * Atualiza o número de versão do módulo nas tabelas de parâmetro do sistema
     *
     * @param string $parStrNumeroVersao
     * @return void
     */
    private function atualizarNumeroVersao($parStrNumeroVersao)
    {
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome(self::PARAMETRO_VERSAO_MODULO);
        $objInfraParametroDTO->retTodos();
        $objInfraParametroBD = new InfraParametroBD(BancoSip::getInstance());
        $arrObjInfraParametroDTO = $objInfraParametroBD->listar($objInfraParametroDTO);
        foreach ($arrObjInfraParametroDTO as $objInfraParametroDTO) {
            $objInfraParametroDTO->setStrValor($parStrNumeroVersao);
            $objInfraParametroBD->alterar($objInfraParametroDTO);
        }

        $this->logar('ATUALIZAÇÃO DA VERSÃO ' . $parStrNumeroVersao . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }
}

try {

    SessaoSip::getInstance(false);
    BancoSip::getInstance()->setBolScript(true);

    InfraScriptVersao::solicitarAutenticacao(BancoSip::getInstance());
    $objVersaoSipRN = new MdIaAtualizadorSipRN();
    $objVersaoSipRN->atualizarVersao();
    exit;
} catch (Exception $e) {
    echo (InfraException::inspecionar($e));
    try {
        LogSip::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}
