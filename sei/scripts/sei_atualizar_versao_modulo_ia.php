<?
require_once dirname(__FILE__) . '/../web/SEI.php';

class MdIaAtualizadorSeiRN extends InfraRN
{

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '1.0.0';
    private $nomeDesteModulo = 'MÓDULO IA';
    private $nomeParametroModulo = 'VERSAO_MODULO_IA';
    private $historicoVersoes = array('1.0.0', '1.1.0');

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function inicializar($strTitulo)
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

        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(true);
        InfraDebug::getInstance()->setBolEcho(true);
        InfraDebug::getInstance()->limpar();

        $this->numSeg = InfraUtil::verificarTempoProcessamento();

        $this->logar($strTitulo);
    }

    private function logar($strMsg)
    {
        InfraDebug::getInstance()->gravar($strMsg);
        flush();
    }

    private function finalizar($strMsg = null, $bolErro = false)
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
            $this->inicializar('INICIANDO A INSTALAÇÃO/ATUALIZAÇÃO DO ' . $this->nomeDesteModulo . ' NO SEI VERSÃO ' . SEI_VERSAO);

            //checando BDs suportados
            if (
                !(BancoSEI::getInstance() instanceof InfraMySql) &&
                !(BancoSEI::getInstance() instanceof InfraSqlServer) &&
                !(BancoSEI::getInstance() instanceof InfraPostgreSql) &&
                !(BancoSEI::getInstance() instanceof InfraOracle)
            ) {
                $this->finalizar('BANCO DE DADOS NÃO SUPORTADO: ' . get_parent_class(BancoSEI::getInstance()), true);
            }

            //testando versao do framework
            $numVersaoInfraRequerida = '2.29.0';
            if (version_compare(VERSAO_INFRA, $numVersaoInfraRequerida) < 0) {
                $this->finalizar('VERSÃO DO FRAMEWORK PHP INCOMPATÍVEL (VERSÃO ATUAL ' . VERSAO_INFRA . ', SENDO REQUERIDA VERSÃO IGUAL OU SUPERIOR A ' . $numVersaoInfraRequerida . ')', true);
            }

            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sei_teste')) == 0) {
                BancoSEI::getInstance()->executarSql('CREATE TABLE sei_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
            }

            BancoSEI::getInstance()->executarSql('DROP TABLE sei_teste');

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            $strVersaoModuloIa = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            switch ($strVersaoModuloIa) {
                case '':
                    $this->instalarv100();
                case '1.0.0':
                    $this->instalarv110();
                    break;
                default:
                    $this->finalizar('A VERSÃO MAIS ATUAL DO ' . $this->nomeDesteModulo . ' (v' . $this->versaoAtualDesteModulo . ') JÁ ESTÁ INSTALADA.');
                    break;
            }

            $this->logar('SCRIPT EXECUTADO EM: ' . date('d/m/Y H:i:s'));
            $this->finalizar('FIM');
            InfraDebug::getInstance()->setBolDebugInfra(true);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            throw new InfraException('Erro instalando/atualizando versão.', $e);
        }
    }

    protected function instalarv100()
    {
        $nmVersao = '1.0.0';

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSAO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('CRIANDO A TABELA md_ia_adm_config_similar');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_config_similar (
                      id_md_ia_adm_config_similar ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      qtd_process_listagem ' . $objInfraMetaBD->tipoNumero(2) . ' NOT NULL,
                      orientacoes_gerais ' . $objInfraMetaBD->tipoTextoVariavel(4000) . ' NOT NULL,
                      perc_relev_cont_doc ' . $objInfraMetaBD->tipoNumero(3) . ' NOT NULL,
                      perc_relev_metadados ' . $objInfraMetaBD->tipoNumero(3) . ' NOT NULL,
                      dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
                      sin_exibir_funcionalidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_config_similar', 'pk_md_ia_adm_config_similar', array('id_md_ia_adm_config_similar'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_config_similar');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_config_similar', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_metadado');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_metadado (
                      id_md_ia_adm_metadado ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      metadado ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NOT NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_metadado', 'pk_md_ia_adm_metadado', array('id_md_ia_adm_metadado'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_metadado');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_metadado', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_perc_relev_met');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_perc_relev_met (
                      id_md_ia_adm_perc_relev_met ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_md_ia_adm_config_similar ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_md_ia_adm_metadado ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      percentual_relevancia ' . $objInfraMetaBD->tipoNumero(3) . ' NOT NULL,
                      dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_perc_relev_met', 'pk_md_ia_adm_perc_relev_met', array('id_md_ia_adm_perc_relev_met'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_perc_relev_met', 'md_ia_adm_perc_relev_met', array('id_md_ia_adm_config_similar'), 'md_ia_adm_config_similar', array('id_md_ia_adm_config_similar'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_adm_perc_relev_met', 'md_ia_adm_perc_relev_met', array('id_md_ia_adm_metadado'), 'md_ia_adm_metadado', array('id_md_ia_adm_metadado'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_perc_relev_met');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_perc_relev_met', 1);

        $this->logar('POPULANDO TABELA md_ia_adm_config_similar');

        $mdIaConfigSimilarRN = new MdIaAdmConfigSimilarRN();

        $orientacoesGerais = '<ol>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">
	<p>Esta funcionalidade utiliza t&eacute;cnicas de intelig&ecirc;ncia artificial para apresentar recomenda&ccedil;&otilde;es de processos similares a partir do conte&uacute;do dos documentos e metadados do processo aberto.&nbsp;</p>
	</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">
	<p>A avalia&ccedil;&atilde;o deve confirmar os processos similares (polegar para cima) e, igualmente importante, marcar os processos n&atilde;o similares (polegar para baixo). Caso a ordem de similaridade dos processos listados possa ser melhorada, ainda devem mudar o Ranking na primeira coluna.&nbsp;</p>
	</li>
</ol>';
        $mdIaAdmConfigSimilarDTO = new MdIaAdmConfigSimilarDTO();
        $mdIaAdmConfigSimilarDTO->setNumIdMdIaAdmConfigSimilar(1);
        $mdIaAdmConfigSimilarDTO->setNumQtdProcessListagem(5);
        $mdIaAdmConfigSimilarDTO->setStrOrientacoesGerais($orientacoesGerais);
        $mdIaAdmConfigSimilarDTO->setNumPercRelevContDoc(70);
        $mdIaAdmConfigSimilarDTO->setNumPercRelevMetadados(30);
        $mdIaAdmConfigSimilarDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        $mdIaAdmConfigSimilarDTO->setStrSinExibirFuncionalidade("N");
        $mdIaConfigSimilarRN->cadastrar($mdIaAdmConfigSimilarDTO);

        $this->logar('POPULANDO TABELA md_ia_adm_metadado');
        $mdIaAdmMetadadoRN = new MdIaAdmMetadadoRN();
        $arrMetadado = [
            ['id_md_ia_adm_metadado' => '1', 'metadado' => 'Tipo de Processo'],
            ['id_md_ia_adm_metadado' => '2', 'metadado' => 'Processos Relacionados'],
            ['id_md_ia_adm_metadado' => '3', 'metadado' => 'Tipos de Documentos'],
            ['id_md_ia_adm_metadado' => '4', 'metadado' => 'Unidade Geradora do Processo'],
            ['id_md_ia_adm_metadado' => '5', 'metadado' => 'Especificação do Processo'],
            ['id_md_ia_adm_metadado' => '6', 'metadado' => 'Interessado do Processo'],
            ['id_md_ia_adm_metadado' => '7', 'metadado' => 'Citações']
        ];

        foreach ($arrMetadado as $chave => $tipo) {
            $mdIaAdmMetadadoDTO = new MdIaAdmMetadadoDTO();
            $mdIaAdmMetadadoDTO->setNumIdMdIaAdmMetadado($chave + 1);
            $mdIaAdmMetadadoDTO->setStrMetadado($tipo['metadado']);
            $mdIaAdmMetadadoRN->cadastrar($mdIaAdmMetadadoDTO);
        }

        $this->logar('POPULANDO TABELA md_ia_adm_perc_relev_met');

        $mdIaAdmPercRelevMetRN = new MdIaAdmPercRelevMetRN();

        $arrMdIaAdmPercRelevMet = [
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '1', 'percentual_relevancia' => '50'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '2', 'percentual_relevancia' => '15'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '3', 'percentual_relevancia' => '15'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '4', 'percentual_relevancia' => '10'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '5', 'percentual_relevancia' => '5'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '6', 'percentual_relevancia' => '3'],
            ['id_md_ia_adm_config_similar' => '1', 'id_md_ia_metadado' => '7', 'percentual_relevancia' => '2']
        ];
        foreach ($arrMdIaAdmPercRelevMet as $chave => $tipo) {
            $mdIaAdmPercRelevMetDTO = new MdIaAdmPercRelevMetDTO();
            $mdIaAdmPercRelevMetDTO->setNumIdMdIaAdmPercRelevMet($chave + 1);
            $mdIaAdmPercRelevMetDTO->setNumIdMdIaAdmConfigSimilar($tipo['id_md_ia_adm_config_similar']);
            $mdIaAdmPercRelevMetDTO->setNumIdMdIaAdmMetadado($tipo['id_md_ia_metadado']);
            $mdIaAdmPercRelevMetDTO->setNumPercentualRelevancia($tipo['percentual_relevancia']);
            $mdIaAdmPercRelevMetDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
            $mdIaAdmPercRelevMetRN->cadastrar($mdIaAdmPercRelevMetDTO);
        }


        $this->logar('CRIANDO A TABELA md_ia_adm_doc_relev');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_doc_relev (
                    id_md_ia_adm_doc_relev ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                    aplicabilidade ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NOT NULL,
                    id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                    id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NULL,
                    sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                    dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
                )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_doc_relev', 'pk_md_ia_adm_doc_relev', array('id_md_ia_adm_doc_relev'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_doc_relev', 'md_ia_adm_doc_relev', array('id_serie'), 'serie', array('id_serie'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_doc_relev');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_doc_relev', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_seg_doc_relev');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_seg_doc_relev (
                      id_md_ia_adm_seg_doc_relev ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_md_ia_adm_doc_relev ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      segmento_documento ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
                      percentual_relevancia ' . $objInfraMetaBD->tipoNumero(3) . ' NOT NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_seg_doc_relev', 'pk_md_ia_adm_seg_doc_relev', array('id_md_ia_adm_seg_doc_relev'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_seg_doc_relev', 'md_ia_adm_seg_doc_relev', array('id_md_ia_adm_doc_relev'), 'md_ia_adm_doc_relev', array('id_md_ia_adm_doc_relev'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_seg_doc_relev');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_seg_doc_relev', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_integ_funcion');

        $sql_tabelas = 'CREATE TABLE md_ia_adm_integ_funcion (
              id_md_ia_adm_integ_funcion ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              nome ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_integ_funcion', 'pk_md_ia_adm_integ_funcion', array('id_md_ia_adm_integ_funcion'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_integ_funcion');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_integ_funcion', 1);

        // CRIA ESTRUTURA DAS TABELAS RELACIONADAS AO MAPEAMENTO DE INTEGRACAO
        # ----------------------------------- INTEGRACAO ---------------------
        $this->logar('CRIANDO A TABELA md_ia_adm_integracao');
        BancoSEI::getInstance()->executarSql(
            'CREATE TABLE md_ia_adm_integracao (
                                id_md_ia_adm_integracao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                                nome ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
                                id_md_ia_adm_integ_funcion ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                                tipo_integracao ' . $objInfraMetaBD->tipoTextoFixo(2) . ' NOT NULL,
                                metodo_autenticacao ' . $objInfraMetaBD->tipoNumero() . ' NULL,
                                metodo_requisicao ' . $objInfraMetaBD->tipoNumero() . ' NULL,
                                formato_resposta ' . $objInfraMetaBD->tipoTextoVariavel(10) . ' NULL,
                                versao_soap ' . $objInfraMetaBD->tipoTextoVariavel(5) . ' NULL,
                                token_autenticacao ' . $objInfraMetaBD->tipoTextoVariavel(76) . ' NULL,
                                url_wsdl ' . $objInfraMetaBD->tipoTextoVariavel(250) . ' NULL,
                                operacao_wsdl ' . $objInfraMetaBD->tipoTextoVariavel(250) . ' NULL,
                                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL) '
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_integracao', 'pk_md_ia_adm_integracao', array('id_md_ia_adm_integracao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_integracao', 'md_ia_adm_integracao', array('id_md_ia_adm_integ_funcion'), 'md_ia_adm_integ_funcion', array('id_md_ia_adm_integ_funcion'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_integracao');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_integracao', 1);

        //-- ==============================================================
        //--  Populando a tabela: md_ia_adm_integ_funcion
        //-- ==============================================================
        $this->logar('INSERINDO FUNCIONALIDADE - Autenticação junto à Solução de Inteligência Artificial do SEI');

        $objMdIaFuncionalidadeRN = new MdIaAdmIntegFuncionRN();
        $objMdIaFuncionalidadeDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaFuncionalidadeDTO->setNumIdMdIaAdmIntegFuncion(MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL);
        $objMdIaFuncionalidadeDTO->setStrNome('Autenticação junto à Solução de Inteligência Artificial do SEI');
        $objMdIaFuncionalidadeRN->cadastrar($objMdIaFuncionalidadeDTO);

        $this->logar('INSERINDO FUNCIONALIDADE - API Interna de interface entre SEI IA e LLM de IA Generativa');

        $objMdIaFuncionalidadeRN = new MdIaAdmIntegFuncionRN();
        $objMdIaFuncionalidadeDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaFuncionalidadeDTO->setNumIdMdIaAdmIntegFuncion(MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTERFACE_LLM);
        $objMdIaFuncionalidadeDTO->setStrNome('API Interna de interface entre SEI IA e LLM de IA Generativa');
        $objMdIaFuncionalidadeRN->cadastrar($objMdIaFuncionalidadeDTO);

        $MdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $MdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $MdIaAdmIntegracaoDTO->setStrNome("Autenticação junto à Solução de Inteligência Artificial do SEI");
        $MdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion("1");
        $MdIaAdmIntegracaoDTO->setStrTipoIntegracao("RE");
        $MdIaAdmIntegracaoDTO->setNumMetodoAutenticacao("1");
        $MdIaAdmIntegracaoDTO->setNumMetodoRequisicao("1");
        $MdIaAdmIntegracaoDTO->setNumFormatoResposta("1");
        $MdIaAdmIntegracaoDTO->setStrOperacaoWsdl("https://hostname_docker_solucao_sei_ia_do_ambiente");
        $MdIaAdmIntegracaoDTO->setStrSinAtivo("S");
        $MdIaAdmIntegracaoRN->cadastrar($MdIaAdmIntegracaoDTO);

        $this->logar('CRIANDO A INTEGRAÇÃO API Interna de Interface entre SEI IA e LLM de IA Generativa');
        $MdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $MdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $MdIaAdmIntegracaoDTO->setStrNome("API Interna de interface entre SEI IA e LLM de IA Generativa");
        $MdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion("2");
        $MdIaAdmIntegracaoDTO->setStrTipoIntegracao("RE");
        $MdIaAdmIntegracaoDTO->setNumMetodoAutenticacao("1");
        $MdIaAdmIntegracaoDTO->setNumMetodoRequisicao("1");
        $MdIaAdmIntegracaoDTO->setNumFormatoResposta("1");
        $MdIaAdmIntegracaoDTO->setStrOperacaoWsdl("https://hostname_docker_solucao_api_interna_llm_do_ambiente");
        $MdIaAdmIntegracaoDTO->setStrSinAtivo("S");
        $MdIaAdmIntegracaoRN->cadastrar($MdIaAdmIntegracaoDTO);
        $this->logar('FIM CRIAR A INTEGRAÇÃO API Interna de Interface entre SEI IA e LLM de IA Generativa');

        $this->logar('CRIANDO A TABELA md_ia_adm_pesq_doc');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_pesq_doc (
                      id_md_ia_adm_pesq_doc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      qtd_process_listagem ' . $objInfraMetaBD->tipoNumero(2) . ' NOT NULL,
                      orientacoes_gerais ' . $objInfraMetaBD->tipoTextoVariavel(4000) . ' NOT NULL,
                      nome_secao ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
                      sin_exibir_funcionalidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_pesq_doc', 'pk_md_ia_adm_pesq_doc', array('id_md_ia_adm_pesq_doc'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_pesq_doc');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_pesq_doc', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_tp_doc_pesq');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_tp_doc_pesq (
                      id_md_ia_adm_tp_doc_pesq ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_md_ia_adm_pesq_doc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
                      sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_tp_doc_pesq', 'pk_md_ia_adm_tp_doc_pesq', array('id_md_ia_adm_tp_doc_pesq'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_tp_doc_pesq', 'md_ia_adm_tp_doc_pesq', array('id_md_ia_adm_pesq_doc'), 'md_ia_adm_pesq_doc', array('id_md_ia_adm_pesq_doc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_adm_tp_doc_pesq', 'md_ia_adm_tp_doc_pesq', array('id_serie'), 'serie', array('id_serie'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_tp_doc_pesq');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_tp_doc_pesq', 1);


        $MdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();

        $orientacoesGerais = '<ol>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Esta funcionalidade viabiliza a pesquisa por confronto do conte&uacute;do de documentos com documentos, com ou sem a inser&ccedil;&atilde;o de texto complementar para a pesquisa. Utiliza t&eacute;cnicas de intelig&ecirc;ncia artificial para que a pesquisa de conte&uacute;do seja mais assertiva comparado com t&eacute;cnicas tradicionais de pesquisa.</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Para contribuir com o aprendizado ativo do SEI IA, os usu&aacute;rios devem realizar a avalia&ccedil;&atilde;o sobre os documentos apresentados na modal de resultado da pesquisa, confirmando sua relev&acirc;ncia sobre o conte&uacute;do pesquisado clicando no &iacute;cone de polegar para cima ou no &iacute;cone de polegar para baixo caso o resultado n&atilde;o tenha sido relevante.</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Caso a ordem dos documentos listados na modal de resultado da pesquisa possa ser melhorada, antes de mudar o Ranking na primeira coluna deve primeiro realizar a avalia&ccedil;&atilde;o de todos os documentos listados.</li>
</ol>';
        $MdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
        $MdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc(1);
        $MdIaAdmPesqDocDTO->setNumQtdProcessListagem(5);
        $MdIaAdmPesqDocDTO->setStrOrientacoesGerais($orientacoesGerais);
        $MdIaAdmPesqDocDTO->setStrNomeSecao("Pesquisa de Documentos");
        $MdIaAdmPesqDocDTO->setStrSinExibirFuncionalidade("N");
        $MdIaAdmPesqDocRN->cadastrar($MdIaAdmPesqDocDTO);


        $queryDocumentosRelevantes = "WITH
                    TIPO_DOC_NAO_CONSIDERADOS AS (
                        SELECT
                            id_serie
                        FROM serie
                        WHERE nome IN (
                            'AR',
                            'Áudio',
                            'Boleto',
                            'Canhoto',
                            'Cartão',
                            'Certidão de Distribuição',
                            'Certidão de Intimação Cumprida',
                            'Certidão de Julgamento',
                            'Certidão de Redistribuição',
                            'CNH',
                            'CNPJ',
                            'Conteúdo de Mídia',
                            'CPF',
                            'Procuração',
                            'Procuração Eletrônica Especial',
                            'Procuração Eletrônica Simples',
                            'Recibo Eletrônico de Protocolo',
                            'Renúncia de Procuração Eletrônica',
                            'Restabelecimento de Procuração Eletrônica',
                            'Restabelecimento de Vinculação a Pessoa Jurídica',
                            'Revogação de Procuração Eletrônica',
                            'RG',
                            'Suspensão de Procuração Eletrônica',
                            'Suspensão de Vinculação a Pessoa Jurídica',
                            'Termo de Cancelamento de Documento',
                            'Termo de Encerramento de Trâmite Físico-Documento',
                            'Termo de Encerramento de Trâmite Físico-Processo',
                            'Vinculação de Responsável Legal a Pessoa Jurídica'
                        )
                    ),
                    PROC_CONSIDERADOS AS (
                    SELECT
                        id_procedimento
                    FROM procedimento
                    ),
                    AGG_PROC AS (
                        SELECT
                            documento.id_procedimento,
                            tipo_procedimento.id_tipo_procedimento,
                            COUNT(documento.id_documento) as qtd_doc,
                            COUNT(DISTINCT(documento.id_serie)) as qtd_tipo_doc
                        FROM documento
                            LEFT JOIN procedimento ON documento.id_procedimento = procedimento.id_procedimento
                            LEFT JOIN tipo_procedimento ON procedimento.id_tipo_procedimento = tipo_procedimento.id_tipo_procedimento
                        WHERE documento.id_procedimento IN (SELECT id_procedimento FROM PROC_CONSIDERADOS)
                        GROUP BY documento.id_procedimento, tipo_procedimento.id_tipo_procedimento
                    ),
                    BASE_PTILES_6_1_DOC AS(
                    SELECT
                        id_tipo_procedimento,
                        qtd_doc,
                        ntile(100) over (partition by id_tipo_procedimento order by qtd_doc asc) percentile
                    FROM AGG_PROC
                    ),
                    PTILES_6_1_DOC AS(
                        SELECT
                            id_tipo_procedimento,
                            MAX(qtd_doc) as qtd_doc
                        FROM BASE_PTILES_6_1_DOC
                        WHERE percentile IN (50)
                        GROUP BY id_tipo_procedimento
                    ),
                    BASE_PTILES_6_1_TIPO_DOC AS(
                    SELECT
                        id_tipo_procedimento,
                        qtd_tipo_doc,
                        ntile(100) over (partition by id_tipo_procedimento order by qtd_tipo_doc asc) percentile
                    FROM AGG_PROC
                    ),
                    PTILES_6_1_TIPO_DOC AS(
                    SELECT
                        id_tipo_procedimento,
                        MAX(qtd_tipo_doc) as qtd_tipo_doc
                    FROM BASE_PTILES_6_1_TIPO_DOC
                    WHERE percentile IN (50)
                    GROUP BY id_tipo_procedimento
                    ),
                    AGG_PROC_MEDIAN AS(
                        SELECT
                            AGG_PROC.id_procedimento,
                            AGG_PROC.id_tipo_procedimento
                        FROM AGG_PROC
                        LEFT JOIN PTILES_6_1_DOC ON AGG_PROC.id_tipo_procedimento = PTILES_6_1_DOC.id_tipo_procedimento
                        LEFT JOIN PTILES_6_1_TIPO_DOC ON AGG_PROC.id_tipo_procedimento = PTILES_6_1_TIPO_DOC.id_tipo_procedimento
                        WHERE	AGG_PROC.qtd_doc >= PTILES_6_1_DOC.qtd_doc * 0.5 OR AGG_PROC.qtd_tipo_doc >= PTILES_6_1_TIPO_DOC.qtd_tipo_doc * 0.5
                        GROUP BY id_procedimento, AGG_PROC.id_tipo_procedimento, AGG_PROC.qtd_tipo_doc, AGG_PROC.qtd_doc
                    ),
                    QTD_PROC_TIPO_MEDIAN AS(
                        SELECT
                            id_tipo_procedimento,
                            COUNT(DISTINCT id_procedimento) as qtd_proc_median
                        FROM AGG_PROC_MEDIAN
                        GROUP BY id_tipo_procedimento
                    ),
                    QTD_DOC_TIPO__PROC_MEDIAN AS(
                        SELECT
                            documento.id_serie,
                            AGG_PROC_MEDIAN.id_tipo_procedimento,
                            COUNT(documento.id_documento) as qtd_doc_proc_median
                        FROM AGG_PROC_MEDIAN
                            LEFT JOIN documento ON AGG_PROC_MEDIAN.id_procedimento = documento.id_procedimento
                        GROUP BY documento.id_serie, AGG_PROC_MEDIAN.id_tipo_procedimento
                    ),
                    DOCS_PROC_MEDIAN AS(
                        SELECT
                            QTD_DOC_TIPO__PROC_MEDIAN.id_serie,
                            QTD_DOC_TIPO__PROC_MEDIAN.id_tipo_procedimento,
                            QTD_DOC_TIPO__PROC_MEDIAN.qtd_doc_proc_median,
                            QTD_PROC_TIPO_MEDIAN.qtd_proc_median,
                            QTD_DOC_TIPO__PROC_MEDIAN.qtd_doc_proc_median / QTD_PROC_TIPO_MEDIAN.qtd_proc_median as avg_doc_proc_median
                        FROM QTD_DOC_TIPO__PROC_MEDIAN
                            LEFT JOIN QTD_PROC_TIPO_MEDIAN ON QTD_DOC_TIPO__PROC_MEDIAN.id_tipo_procedimento = QTD_PROC_TIPO_MEDIAN.id_tipo_procedimento
                    ),
                    DOCS_FREQUENTES AS (
                        SELECT
                            id_tipo_procedimento,
                            id_serie
                        FROM DOCS_PROC_MEDIAN
                        WHERE avg_doc_proc_median >= 0.1
                    ),
                    QTD_TIPO_DOC_DOCS_FREQUENTES AS (
                        SELECT
                            id_tipo_procedimento,
                            COUNT(DISTINCT id_serie) as qtd_tipo_doc_frequente
                        FROM DOCS_FREQUENTES
                        GROUP BY  id_tipo_procedimento
                    ),
                    PREP_PROC_DOCS_FREQUENTES AS (
                        SELECT
                            documento.id_procedimento,
                            DOCS_FREQUENTES.id_tipo_procedimento,
                            COUNT(DISTINCT documento.id_serie) as qtd_tipo_doc
                        FROM DOCS_FREQUENTES
                            LEFT JOIN procedimento ON DOCS_FREQUENTES.id_tipo_procedimento = procedimento.id_tipo_procedimento
                            LEFT JOIN documento ON
                                procedimento.id_procedimento = documento.id_procedimento
                                AND DOCS_FREQUENTES.id_serie = documento.id_serie
                        WHERE documento.id_procedimento IN (SELECT id_procedimento FROM PROC_CONSIDERADOS)
                        GROUP BY documento.id_procedimento, DOCS_FREQUENTES.id_tipo_procedimento
                    ),
                    PROC_CVP AS (
                        SELECT
                            PREP_PROC_DOCS_FREQUENTES.*
                        FROM PREP_PROC_DOCS_FREQUENTES
                        LEFT JOIN QTD_TIPO_DOC_DOCS_FREQUENTES ON PREP_PROC_DOCS_FREQUENTES.id_tipo_procedimento = QTD_TIPO_DOC_DOCS_FREQUENTES.id_tipo_procedimento
                        WHERE qtd_tipo_doc >= qtd_tipo_doc_frequente / 2
                    ),
                    PREP_DOC_PROC_CVP AS(
                        SELECT
                            DISTINCT PROC_CVP.id_procedimento,
                            PROC_CVP.id_tipo_procedimento,
                            DOCS_FREQUENTES.id_serie,
                            protocolo.sta_protocolo,
                            documento.id_documento
                        FROM PROC_CVP
                        LEFT JOIN DOCS_FREQUENTES ON PROC_CVP.id_tipo_procedimento = DOCS_FREQUENTES.id_tipo_procedimento
                        LEFT JOIN documento ON PROC_CVP.id_procedimento = documento.id_procedimento AND DOCS_FREQUENTES.id_serie = documento.id_serie
                        LEFT JOIN protocolo ON documento.id_documento = protocolo.id_protocolo
                    ),
                    DOC_PROC_CVP AS(
                        SELECT
                            *
                        FROM PREP_DOC_PROC_CVP
                        WHERE id_documento IS NOT NULL
                    ),
                    POS_DOC_PROC_CVP AS(
                        SELECT
                            DOC_PROC_CVP.*,
                            ROW_NUMBER() OVER(PARTITION BY id_procedimento order by id_documento) AS ordem_doc
                        FROM DOC_PROC_CVP
                    ),
                    MAX_POS_DOC_PROC_CVP AS(
                        SELECT
                            id_tipo_procedimento,
                            id_procedimento,
                            MAX(ordem_doc) as max_ordem_doc
                        FROM POS_DOC_PROC_CVP
                        GROUP BY id_tipo_procedimento, id_procedimento
                    ),
                    PERC_POS_DOC_PROC_CVP AS(
                        SELECT
                            CONCAT(CONCAT(CONCAT(CONCAT(CAST(POS_DOC_PROC_CVP.id_tipo_procedimento AS CHAR(255)), '_'), CAST(POS_DOC_PROC_CVP.id_serie AS CHAR(255))), '_'), POS_DOC_PROC_CVP.sta_protocolo) as agg,
                            POS_DOC_PROC_CVP.id_tipo_procedimento,
                            POS_DOC_PROC_CVP.id_procedimento,
                            MAX_POS_DOC_PROC_CVP.max_ordem_doc,
                            POS_DOC_PROC_CVP.ordem_doc,
                            POS_DOC_PROC_CVP.id_serie,
                            POS_DOC_PROC_CVP.id_documento,
                            POS_DOC_PROC_CVP.sta_protocolo,
                            POS_DOC_PROC_CVP.ordem_doc / MAX_POS_DOC_PROC_CVP.max_ordem_doc as p_ordem_doc
                        FROM POS_DOC_PROC_CVP
                        LEFT JOIN MAX_POS_DOC_PROC_CVP ON POS_DOC_PROC_CVP.id_procedimento = MAX_POS_DOC_PROC_CVP.id_procedimento
                    ),
                    BASE_PTILES_6_5_4 AS(
                        SELECT
                            id_tipo_procedimento,
                            id_serie,
                            sta_protocolo,
                            ntile(100) over (partition by agg order by p_ordem_doc asc) percentile
                        FROM PERC_POS_DOC_PROC_CVP
                    ),
                    POS_EST_TP_DOC_TP_PROC_CVP  AS(
                        SELECT
                            id_tipo_procedimento,
                            id_serie,
                            sta_protocolo
                        FROM BASE_PTILES_6_5_4
                        WHERE percentile = 50
                        GROUP BY id_tipo_procedimento, id_serie, sta_protocolo
                    ),
                        BASE_FILTRADA_CVA AS(
                        SELECT
                            id_tipo_procedimento,
                            id_serie,
                            sta_protocolo
                        FROM POS_EST_TP_DOC_TP_PROC_CVP
                        WHERE id_serie NOT IN (SELECT id_serie FROM TIPO_DOC_NAO_CONSIDERADOS)
                    )
                SELECT * FROM BASE_FILTRADA_CVA";
        $documentosRelevantes = BancoSEI::getInstance()->consultarSql($queryDocumentosRelevantes);

        $mdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();

        foreach ($documentosRelevantes as $documentoRelevante) {
            $mdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
            $mdIaAdmDocRelevDTO->setNumIdSerie($documentoRelevante['id_serie']);
            if ($documentoRelevante['sta_protocolo'] == "R") {
                $aplicabilidade = "E";
            } else {
                $aplicabilidade = "I";
            }
            $mdIaAdmDocRelevDTO->setStrAplicabilidade($aplicabilidade);
            $mdIaAdmDocRelevDTO->setNumIdTipoProcedimento($documentoRelevante['id_tipo_procedimento']);
            $mdIaAdmDocRelevDTO->setStrSinAtivo("S");
            $mdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
            $mdIaAdmDocRelevRN->cadastrar($mdIaAdmDocRelevDTO);
        }

        $this->logar('CRIANDO A TABELA md_ia_adm_ods_onu');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_ods_onu (
            id_md_ia_adm_ods_onu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            sin_exibir_funcionalidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            sin_classificacao_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            orientacoes_gerais ' . $objInfraMetaBD->tipoTextoVariavel(4000) . ' NOT NULL,
            sin_exibir_avaliacao ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_ods_onu', 'pk_md_ia_adm_ods_onu', array('id_md_ia_adm_ods_onu'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_ods_onu');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_ods_onu', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_unidade_alerta');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_unidade_alerta (
            id_md_ia_adm_unidade_alerta ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_ods_onu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_unidade_alerta', 'pk_md_ia_adm_unidade_alerta', array('id_md_ia_adm_unidade_alerta'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_unidade_alerta', 'md_ia_adm_unidade_alerta', array('id_md_ia_adm_ods_onu'), 'md_ia_adm_ods_onu', array('id_md_ia_adm_ods_onu'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_adm_unidade_alerta', 'md_ia_adm_unidade_alerta', array('id_unidade'), 'unidade', array('id_unidade'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_unidade_alerta');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_unidade_alerta', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_objetivo_ods');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_objetivo_ods (
            id_md_ia_adm_objetivo_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_ods_onu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            nome_ods ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
            descricao_ods ' . $objInfraMetaBD->tipoTextoVariavel(250) . ' NOT NULL,
            icone_ods ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_objetivo_ods', 'pk_md_ia_adm_objetivo_ods', array('id_md_ia_adm_objetivo_ods'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_objetivo_ods', 'md_ia_adm_objetivo_ods', array('id_md_ia_adm_ods_onu'), 'md_ia_adm_ods_onu', array('id_md_ia_adm_ods_onu'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_objetivo_ods');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_objetivo_ods', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_meta');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_meta_ods (
            id_md_ia_adm_meta_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_objetivo_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            ordem ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            identificacao_meta ' . $objInfraMetaBD->tipoTextoVariavel(25) . ' NOT NULL,
            descricao_meta ' . $objInfraMetaBD->tipoTextoVariavel(1000) . ' NOT NULL,
            sin_forte_relacao ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_meta_ods', 'pk_md_ia_adm_meta_ods', array('id_md_ia_adm_meta_ods'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_meta_ods', 'md_ia_adm_meta_ods', array('id_md_ia_adm_objetivo_ods'), 'md_ia_adm_objetivo_ods', array('id_md_ia_adm_objetivo_ods'));

        $this->logar('CRIANDO A SEQUENCE md_ia_adm_meta_ods');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_meta_ods', 1);

        $mdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();

        $orientacoesGerais = '<ol>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Esta funcionalidade do SEI IA apoia a classifica&ccedil;&atilde;o dos processos segundo os Objetivos de Desenvolvimento Sustent&aacute;vel (ODS) definidos pela Organiza&ccedil;&atilde;o das Na&ccedil;&otilde;es Unidas (ONU) para a Agenda 2030.&nbsp;Os Objetivos de Desenvolvimento Sustent&aacute;vel da ONU s&atilde;o um apelo global &agrave; a&ccedil;&atilde;o para acabar com a pobreza, proteger o meio ambiente e o clima e garantir que as pessoas, em todos os lugares, possam desfrutar de paz e de prosperidade (<a href="https://brasil.un.org/pt-br/sdgs" target="_blank">https://brasil.un.org/pt-br/sdgs</a>).</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Acessando os &iacute;cones de cada ODS &eacute; poss&iacute;vel visualizar a &uacute;ltima classifica&ccedil;&atilde;o efetiva realizada por algum usu&aacute;rio (<strong>&iacute;cone colorido</strong>), classifica&ccedil;&atilde;o sugerida pelo SEI IA com pend&ecirc;ncia de confirma&ccedil;&atilde;o (<strong>&iacute;cone colorido com destaque &quot;IA&quot;</strong>) e classifica&ccedil;&atilde;o sugerida por Usu&aacute;rio Externo igualmente pendente de confirma&ccedil;&atilde;o por usu&aacute;rio interno (<strong>&iacute;cone colorido com destaque de Usu&aacute;rio Externo</strong>).</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">A avalia&ccedil;&atilde;o deve confirmar ou n&atilde;o as sugest&otilde;es de classifica&ccedil;&atilde;o realizadas.</li>
	<li style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Quando o &iacute;cone estiver cinza sem destaque significa que n&atilde;o existe hist&oacute;rico de classifica&ccedil;&atilde;o. &Iacute;cone cinza com destaque que simboliza hist&oacute;rico significa que atualmente n&atilde;o possui classifica&ccedil;&atilde;o efetiva ou sugerida, mas tem hist&oacute;rico de classifica&ccedil;&otilde;es anteriores. Na modal de classifica&ccedil;&atilde;o aberta sobre cada Objetivo, caso dispon&iacute;vel, &eacute; poss&iacute;vel acessar o hist&oacute;rico.</li>
</ol>';
        $mdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
        $mdIaAdmOdsOnuDTO->setNumIdMdIaAdmOdsOnu(1);
        $mdIaAdmOdsOnuDTO->setStrOrientacoesGerais($orientacoesGerais);
        $mdIaAdmOdsOnuDTO->setStrSinExibirFuncionalidade("N");
        $mdIaAdmOdsOnuDTO->setStrSinExibirAvaliacao("N");
        $mdIaAdmOdsOnuDTO->setStrSinClassificacaoExterno("N");
        $mdIaAdmOdsOnuRN->cadastrar($mdIaAdmOdsOnuDTO);


        $this->logar('POPULANDO TABELA md_ia_adm_objetivo_ods');

        $mdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();

        $arrMdIaAdmObjetivoOds = [
            ['nome_ods' => 'Erradicação da pobreza', 'descricao_ods' => 'Acabar com a pobreza em todas as suas formas, em todos os lugares', 'icone_ods' => 'SDG-1.svg'],
            ['nome_ods' => 'Fome zero e agricultura sustentável', 'descricao_ods' => 'Acabar com a fome, alcançar a segurança alimentar e melhoria da nutrição e promover a agricultura sustentável', 'icone_ods' => 'SDG-2.svg'],
            ['nome_ods' => 'Saúde e Bem-Estar', 'descricao_ods' => 'Assegurar uma vida saudável e promover o bem-estar para todas e todos, em todas as idades', 'icone_ods' => 'SDG-3.svg'],
            ['nome_ods' => 'Educação de qualidade', 'descricao_ods' => 'Assegurar a educação inclusiva e equitativa e de qualidade, e promover oportunidades de aprendizagem ao longo da vida para todas e todos', 'icone_ods' => 'SDG-4.svg'],
            ['nome_ods' => 'Igualdade de gênero', 'descricao_ods' => 'Alcançar a igualdade de gênero e empoderar todas as mulheres e meninas', 'icone_ods' => 'SDG-5.svg'],
            ['nome_ods' => 'Água potável e saneamento', 'descricao_ods' => 'Assegurar a disponibilidade e gestão sustentável da água e saneamento para todas e todos', 'icone_ods' => 'SDG-6.svg'],
            ['nome_ods' => 'Energia limpa e acessível', 'descricao_ods' => 'Assegurar o acesso confiável, sustentável, moderno e a preço acessível à energia para todas e todos', 'icone_ods' => 'SDG-7.svg'],
            ['nome_ods' => 'Trabalho decente e crescimento económico', 'descricao_ods' => 'Promover o crescimento econômico sustentado, inclusivo e sustentável, emprego pleno e produtivo e trabalho decente para todas e todos', 'icone_ods' => 'SDG-8.svg'],
            ['nome_ods' => 'Indústria, inovação e infraestrutura', 'descricao_ods' => 'Construir infraestruturas resilientes, promover a industrialização inclusiva e sustentável e fomentar a inovação', 'icone_ods' => 'SDG-9.svg'],
            ['nome_ods' => 'Redução das desigualdades', 'descricao_ods' => 'Reduzir a desigualdade dentro dos países e entre eles', 'icone_ods' => 'SDG-10.svg'],
            ['nome_ods' => 'Cidades e comunidades sustentáveis', 'descricao_ods' => 'Tornar as cidades e os assentamentos humanos inclusivos, seguros, resilientes e sustentáveis', 'icone_ods' => 'SDG-11.svg'],
            ['nome_ods' => 'Consumo e produção responsáveis', 'descricao_ods' => 'Assegurar padrões de produção e de consumo sustentáveis', 'icone_ods' => 'SDG-12.svg'],
            ['nome_ods' => 'Ação contra a mudança global do clima', 'descricao_ods' => 'Tomar medidas urgentes para combater a mudança climática e seus impactos', 'icone_ods' => 'SDG-13.svg'],
            ['nome_ods' => 'Vida na água', 'descricao_ods' => 'Conservação e uso sustentável dos oceanos, dos mares e dos recursos marinhos para o desenvolvimento sustentável', 'icone_ods' => 'SDG-14.svg'],
            ['nome_ods' => 'Vida terrestre', 'descricao_ods' => 'Proteger, recuperar e promover o uso sustentável dos ecossistemas terrestres, gerir de forma sustentável as florestas, combater a desertificação, deter e reverter a degradação da terra e deter a perda de biodiversidade', 'icone_ods' => 'SDG-15.svg'],
            ['nome_ods' => 'Paz, justiça e instituições eficazes', 'descricao_ods' => 'Promover sociedades pacíficas e inclusivas para o desenvolvimento sustentável, proporcionar o acesso à justiça para todos e construir instituições eficazes, responsáveis e inclusivas em todos os níveis', 'icone_ods' => 'SDG-16.svg'],
            ['nome_ods' => 'Parcerias e meios de implementação', 'descricao_ods' => 'Fortalecer os meios de implementação e revitalizar a parceria global para o desenvolvimento sustentável', 'icone_ods' => 'SDG-17.svg'],

        ];
        foreach ($arrMdIaAdmObjetivoOds as $chave => $tipo) {
            $mdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
            $mdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmOdsOnu(1);
            $mdIaAdmObjetivoOdsDTO->setStrNomeOds($tipo['nome_ods']);
            $mdIaAdmObjetivoOdsDTO->setStrDescricaoOds($tipo['descricao_ods']);
            $mdIaAdmObjetivoOdsDTO->setStrIconeOds($tipo['icone_ods']);
            $mdIaAdmObjetivoOdsRN->cadastrar($mdIaAdmObjetivoOdsDTO);
        }

        $this->logar('POPULANDO TABELA md_ia_adm_meta_ods');

        $mdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();

        $arrMdIaAdmMetaOds = [
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '1', 'identificacao_meta' => '1.1', 'descricao_meta' => 'Até 2030, erradicar a pobreza extrema para todas as pessoas em todos os lugares, atualmente medida como pessoas vivendo com menos de US$ 1,90 por dia'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '2', 'identificacao_meta' => '1.2', 'descricao_meta' => 'Até 2030, reduzir pelo menos à metade a proporção de homens, mulheres e crianças, de todas as idades, que vivem na pobreza, em todas as suas dimensões, de acordo com as definições nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '3', 'identificacao_meta' => '1.3', 'descricao_meta' => 'Implementar, em nível nacional, medidas e sistemas de proteção social adequados, para todos, incluindo pisos, e até 2030 atingir a cobertura substancial dos pobres e vulneráveis'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '4', 'identificacao_meta' => '1.4', 'descricao_meta' => 'Até 2030, garantir que todos os homens e mulheres, particularmente os pobres e vulneráveis, tenham direitos iguais aos recursos econômicos, bem como o acesso a serviços básicos, propriedade e controle sobre a terra e outras formas de propriedade, herança, recursos naturais, novas tecnologias apropriadas e serviços financeiros, incluindo microfinanças'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '5', 'identificacao_meta' => '1.5', 'descricao_meta' => 'Até 2030, construir a resiliência dos pobres e daqueles em situação de vulnerabilidade, e reduzir a exposição e vulnerabilidade destes a eventos extremos relacionados com o clima e outros choques e desastres econômicos, sociais e ambientais'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '6', 'identificacao_meta' => '1.a', 'descricao_meta' => 'Garantir uma mobilização significativa de recursos a partir de uma variedade de fontes, inclusive por meio do reforço da cooperação para o desenvolvimento, para proporcionar meios adequados e previsíveis para que os países em desenvolvimento, em particular os países menos desenvolvidos, implementem programas e políticas para acabar com a pobreza em todas as suas dimensões'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '7', 'identificacao_meta' => '1.b', 'descricao_meta' => 'Criar marcos políticos sólidos em níveis nacional, regional e internacional, com base em estratégias de desenvolvimento a favor dos pobres e sensíveis a gênero, para apoiar investimentos acelerados nas ações de erradicação da pobreza'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '1', 'identificacao_meta' => '2.1', 'descricao_meta' => 'Até 2030, acabar com a fome e garantir o acesso de todas as pessoas, em particular os pobres e pessoas em situações vulneráveis, incluindo crianças, a alimentos seguros, nutritivos e suficientes durante todo o ano'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '2', 'identificacao_meta' => '2.2', 'descricao_meta' => 'Até 2030, acabar com todas as formas de desnutrição, incluindo atingir, até 2025, as metas acordadas internacionalmente sobre nanismo e caquexia em crianças menores de cinco anos de idade, e atender às necessidades nutricionais dos adolescentes, mulheres grávidas e lactantes e pessoas idosas'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '3', 'identificacao_meta' => '2.3', 'descricao_meta' => 'Até 2030, dobrar a produtividade agrícola e a renda dos pequenos produtores de alimentos, particularmente das mulheres, povos indígenas, agricultores familiares, pastores e pescadores, inclusive por meio de acesso seguro e igual à terra, outros recursos produtivos e insumos, conhecimento, serviços financeiros, mercados e oportunidades de agregação de valor e de emprego não agrícola'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '4', 'identificacao_meta' => '2.4', 'descricao_meta' => 'Até 2030, garantir sistemas sustentáveis de produção de alimentos e implementar práticas agrícolas resilientes, que aumentem a produtividade e a produção, que ajudem a manter os ecossistemas, que fortaleçam a capacidade de adaptação às mudanças climáticas, às condições meteorológicas extremas, secas, inundações e outros desastres, e que melhorem progressivamente a qualidade da terra e do solo'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '5', 'identificacao_meta' => '2.5', 'descricao_meta' => 'Até 2020, manter a diversidade genética de sementes, plantas cultivadas, animais de criação e domesticados e suas respectivas espécies selvagens, inclusive por meio de bancos de sementes e plantas diversificados e bem geridos em nível nacional, regional e internacional, e garantir o acesso e a repartição justa e equitativa dos benefícios decorrentes da utilização dos recursos genéticos e conhecimentos tradicionais associados, como acordado internacionalmente'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '6', 'identificacao_meta' => '2.a', 'descricao_meta' => 'Aumentar o investimento, inclusive via o reforço da cooperação internacional, em infraestrutura rural, pesquisa e extensão de serviços agrícolas, desenvolvimento de tecnologia, e os bancos de genes de plantas e animais, para aumentar a capacidade de produção agrícola nos países em desenvolvimento, em particular nos países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '7', 'identificacao_meta' => '2.b', 'descricao_meta' => 'Corrigir e prevenir as restrições ao comércio e distorções nos mercados agrícolas mundiais, incluindo a eliminação paralela de todas as formas de subsídios à exportação e todas as medidas de exportação com efeito equivalente, de acordo com o mandato da Rodada de Desenvolvimento de Doha'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '8', 'identificacao_meta' => '2.c', 'descricao_meta' => 'Adotar medidas para garantir o funcionamento adequado dos mercados de commodities de alimentos e seus derivados, e facilitar o acesso oportuno à informação de mercado, inclusive sobre as reservas de alimentos, a fim de ajudar a limitar a volatilidade extrema dos preços dos alimentos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '1', 'identificacao_meta' => '3.1', 'descricao_meta' => 'Até 2030, reduzir a taxa de mortalidade materna global para menos de 70 mortes por 100.000 nascidos vivos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '2', 'identificacao_meta' => '3.2', 'descricao_meta' => 'Até 2030, acabar com as mortes evitáveis de recém-nascidos e crianças menores de 5 anos, com todos os países objetivando reduzir a mortalidade neonatal para pelo menos 12 por 1.000 nascidos vivos e a mortalidade de crianças menores de 5 anos para pelo menos 25 por 1.000 nascidos vivos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '3', 'identificacao_meta' => '3.3', 'descricao_meta' => 'Até 2030, acabar com as epidemias de AIDS, tuberculose, malária e doenças tropicais negligenciadas, e combater a hepatite, doenças transmitidas pela água, e outras doenças transmissíveis'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '4', 'identificacao_meta' => '3.4', 'descricao_meta' => 'Até 2030, reduzir em um terço a mortalidade prematura por doenças não transmissíveis via prevenção e tratamento, e promover a saúde mental e o bem-estar'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '5', 'identificacao_meta' => '3.5', 'descricao_meta' => 'Reforçar a prevenção e o tratamento do abuso de substâncias, incluindo o abuso de drogas entorpecentes e uso nocivo do álcool'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '6', 'identificacao_meta' => '3.6', 'descricao_meta' => 'Até 2020, reduzir pela metade as mortes e os ferimentos globais por acidentes em estradas'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '7', 'identificacao_meta' => '3.7', 'descricao_meta' => 'Até 2030, assegurar o acesso universal aos serviços de saúde sexual e reprodutiva, incluindo o planejamento familiar, informação e educação, bem como a integração da saúde reprodutiva em estratégias e programas nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '8', 'identificacao_meta' => '3.8', 'descricao_meta' => 'Atingir a cobertura universal de saúde, incluindo a proteção do risco financeiro, o acesso a serviços de saúde essenciais de qualidade e o acesso a medicamentos e vacinas essenciais seguros, eficazes, de qualidade e a preços acessíveis para todos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '9', 'identificacao_meta' => '3.9', 'descricao_meta' => 'Até 2030, reduzir substancialmente o número de mortes e doenças por produtos químicos perigosos, contaminação e poluição do ar e água do solo'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '10', 'identificacao_meta' => '3.a', 'descricao_meta' => 'Fortalecer a implementação da Convenção-Quadro para o Controle do Tabaco em todos os países, conforme apropriado'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '11', 'identificacao_meta' => '3.b', 'descricao_meta' => 'Apoiar a pesquisa e o desenvolvimento de vacinas e medicamentos para as doenças transmissíveis e não transmissíveis, que afetam principalmente os países em desenvolvimento, proporcionar o acesso a medicamentos e vacinas essenciais a preços acessíveis, de acordo com a Declaração de Doha, que afirma o direito dos países em desenvolvimento de utilizarem plenamente as disposições do acordo TRIPS sobre flexibilidades para proteger a saúde pública e, em particular, proporcionar o acesso a medicamentos para todos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '12', 'identificacao_meta' => '3.c', 'descricao_meta' => 'Aumentar substancialmente o financiamento da saúde e o recrutamento, desenvolvimento e formação, e retenção do pessoal de saúde nos países em desenvolvimento, especialmente nos países menos desenvolvidos e nos pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '13', 'identificacao_meta' => '3.d', 'descricao_meta' => 'Reforçar a capacidade de todos os países, particularmente os países em desenvolvimento, para o alerta precoce, redução de riscos e gerenciamento de riscos nacionais e globais de saúde'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '1', 'identificacao_meta' => '4.1', 'descricao_meta' => 'Até 2030, garantir que todas as meninas e meninos completem o ensino primário e secundário livre, equitativo e de qualidade, que conduza a resultados de aprendizagem relevantes e eficazes'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '2', 'identificacao_meta' => '4.2', 'descricao_meta' => 'Até 2030, garantir que todos as meninas e meninos tenham acesso a um desenvolvimento de qualidade na primeira infância, cuidados e educação pré-escolar, de modo que eles estejam prontos para o ensino primário'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '3', 'identificacao_meta' => '4.3', 'descricao_meta' => 'Até 2030, assegurar a igualdade de acesso para todos os homens e mulheres à educação técnica, profissional e superior de qualidade, a preços acessíveis, incluindo universidade'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '4', 'identificacao_meta' => '4.4', 'descricao_meta' => 'Até 2030, aumentar substancialmente o número de jovens e adultos que tenham habilidades relevantes, inclusive competências técnicas e profissionais, para emprego, trabalho decente e empreendedorismo'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '5', 'identificacao_meta' => '4.5', 'descricao_meta' => 'Até 2030, eliminar as disparidades de gênero na educação e garantir a igualdade de acesso a todos os níveis de educação e formação profissional para os mais vulneráveis, incluindo as pessoas com deficiência, povos indígenas e as crianças em situação de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '6', 'identificacao_meta' => '4.6', 'descricao_meta' => 'Até 2030, garantir que todos os jovens e uma substancial proporção dos adultos, homens e mulheres estejam alfabetizados e tenham adquirido o conhecimento básico de matemática'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '7', 'identificacao_meta' => '4.7', 'descricao_meta' => 'Até 2030, garantir que todos os alunos adquiram conhecimentos e habilidades necessárias para promover o desenvolvimento sustentável, inclusive, entre outros, por meio da educação para o desenvolvimento sustentável e estilos de vida sustentáveis, direitos humanos, igualdade de gênero, promoção de uma cultura de paz e não violência, cidadania global e valorização da diversidade cultural e da contribuição da cultura para o desenvolvimento sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '8', 'identificacao_meta' => '4.a', 'descricao_meta' => 'Construir e melhorar instalações físicas para educação, apropriadas para crianças e sensíveis às deficiências e ao gênero, e que proporcionem ambientes de aprendizagem seguros e não violentos, inclusivos e eficazes para todos'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '9', 'identificacao_meta' => '4.b', 'descricao_meta' => 'Até 2020, substancialmente ampliar globalmente o número de bolsas de estudo para os países em desenvolvimento, em particular os países menos desenvolvidos, pequenos Estados insulares em desenvolvimento e os países africanos, para o ensino superior, incluindo programas de formação profissional, de tecnologia da informação e da comunicação, técnicos, de engenharia e programas científicos em países desenvolvidos e outros países em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '10', 'identificacao_meta' => '4.c', 'descricao_meta' => 'Até 2030, substancialmente aumentar o contingente de professores qualificados, inclusive por meio da cooperação internacional para a formação de professores, nos países em desenvolvimento, especialmente os países menos desenvolvidos e pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '1', 'identificacao_meta' => '5.1', 'descricao_meta' => 'Acabar com todas as formas de discriminação contra todas as mulheres e meninas em toda parte'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '2', 'identificacao_meta' => '5.2', 'descricao_meta' => 'Eliminar todas as formas de violência contra todas as mulheres e meninas nas esferas públicas e privadas, incluindo o tráfico e exploração sexual e de outros tipos'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '3', 'identificacao_meta' => '5.3', 'descricao_meta' => 'Eliminar todas as práticas nocivas, como os casamentos prematuros, forçados e de crianças e mutilações genitais femininas'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '4', 'identificacao_meta' => '5.4', 'descricao_meta' => 'Reconhecer e valorizar o trabalho de assistência e doméstico não remunerado, por meio da disponibilização de serviços públicos, infraestrutura e políticas de proteção social, bem como a promoção da responsabilidade compartilhada dentro do lar e da família, conforme os contextos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '5', 'identificacao_meta' => '5.5', 'descricao_meta' => 'Garantir a participação plena e efetiva das mulheres e a igualdade de oportunidades para a liderança em todos os níveis de tomada de decisão na vida política, econômica e pública'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '6', 'identificacao_meta' => '5.6', 'descricao_meta' => 'Assegurar o acesso universal à saúde sexual e reprodutiva e os direitos reprodutivos, como acordado em conformidade com o Programa de Ação da Conferência Internacional sobre População e Desenvolvimento e com a Plataforma de Ação de Pequim e os documentos resultantes de suas conferências de revisão'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '7', 'identificacao_meta' => '5.a', 'descricao_meta' => 'Realizar reformas para dar às mulheres direitos iguais aos recursos econômicos, bem como o acesso a propriedade e controle sobre a terra e outras formas de propriedade, serviços financeiros, herança e os recursos naturais, de acordo com as leis nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '8', 'identificacao_meta' => '5.b', 'descricao_meta' => 'Aumentar o uso de tecnologias de base, em particular as tecnologias de informação e comunicação, para promover o empoderamento das mulheres'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '9', 'identificacao_meta' => '5.c', 'descricao_meta' => 'Adotar e fortalecer políticas sólidas e legislação aplicável para a promoção da igualdade de gênero e o empoderamento de todas as mulheres e meninas em todos os níveis'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '1', 'identificacao_meta' => '6.1', 'descricao_meta' => 'Até 2030, alcançar o acesso universal e equitativo a água potável e segura para todos'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '2', 'identificacao_meta' => '6.2', 'descricao_meta' => 'Até 2030, alcançar o acesso a saneamento e higiene adequados e equitativos para todos, e acabar com a defecação a céu aberto, com especial atenção para as necessidades das mulheres e meninas e daqueles em situação de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '3', 'identificacao_meta' => '6.3', 'descricao_meta' => 'Até 2030, melhorar a qualidade da água, reduzindo a poluição, eliminando despejo e minimizando a liberação de produtos químicos e materiais perigosos, reduzindo à metade a proporção de águas residuais não tratadas e aumentando substancialmente a reciclagem e reutilização segura globalmente'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '4', 'identificacao_meta' => '6.4', 'descricao_meta' => 'Até 2030, aumentar substancialmente a eficiência do uso da água em todos os setores e assegurar retiradas sustentáveis e o abastecimento de água doce para enfrentar a escassez de água, e reduzir substancialmente o número de pessoas que sofrem com a escassez de água'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '5', 'identificacao_meta' => '6.5', 'descricao_meta' => 'Até 2030, implementar a gestão integrada dos recursos hídricos em todos os níveis, inclusive via cooperação transfronteiriça, conforme apropriado'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '6', 'identificacao_meta' => '6.6', 'descricao_meta' => 'Até 2020, proteger e restaurar ecossistemas relacionados com a água, incluindo montanhas, florestas, zonas úmidas, rios, aquíferos e lagos'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '7', 'identificacao_meta' => '6.a', 'descricao_meta' => 'Até 2030, ampliar a cooperação internacional e o apoio à capacitação para os países em desenvolvimento em atividades e programas relacionados à água e saneamento, incluindo a coleta de água, a dessalinização, a eficiência no uso da água, o tratamento de efluentes, a reciclagem e as tecnologias de reuso'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '8', 'identificacao_meta' => '6.b', 'descricao_meta' => 'Apoiar e fortalecer a participação das comunidades locais, para melhorar a gestão da água e do saneamento'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '1', 'identificacao_meta' => '7.1', 'descricao_meta' => 'Até 2030, assegurar o acesso universal, confiável, moderno e a preços acessíveis a serviços de energia'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '2', 'identificacao_meta' => '7.2', 'descricao_meta' => 'Até 2030, aumentar substancialmente a participação de energias renováveis na matriz energética global'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '3', 'identificacao_meta' => '7.3', 'descricao_meta' => 'Até 2030, dobrar a taxa global de melhoria da eficiência energética'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '4', 'identificacao_meta' => '7.a', 'descricao_meta' => 'Até 2030, reforçar a cooperação internacional para facilitar o acesso a pesquisa e tecnologias de energia limpa, incluindo energias renováveis, eficiência energética e tecnologias de combustíveis fósseis avançadas e mais limpas, e promover o investimento em infraestrutura de energia e em tecnologias de energia limpa'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '5', 'identificacao_meta' => '7.b', 'descricao_meta' => 'Até 2030, expandir a infraestrutura e modernizar a tecnologia para o fornecimento de serviços de energia modernos e sustentáveis para todos nos países em desenvolvimento, particularmente nos países menos desenvolvidos, nos pequenos Estados insulares em desenvolvimento e nos países em desenvolvimento sem litoral, de acordo com seus respectivos programas de apoio'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '1', 'identificacao_meta' => '8.1', 'descricao_meta' => 'Sustentar o crescimento econômico per capita de acordo com as circunstâncias nacionais e, em particular, um crescimento anual de pelo menos 7% do produto interno bruto [PIB] nos países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '2', 'identificacao_meta' => '8.2', 'descricao_meta' => 'Atingir níveis mais elevados de produtividade das economias por meio da diversificação, modernização tecnológica e inovação, inclusive por meio de um foco em setores de alto valor agregado e dos setores intensivos em mão de obra'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '3', 'identificacao_meta' => '8.3', 'descricao_meta' => 'Promover políticas orientadas para o desenvolvimento que apoiem as atividades produtivas, geração de emprego decente, empreendedorismo, criatividade e inovação, e incentivar a formalização e o crescimento das micro, pequenas e médias empresas, inclusive por meio do acesso a serviços financeiros'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '4', 'identificacao_meta' => '8.4', 'descricao_meta' => 'Melhorar progressivamente, até 2030, a eficiência dos recursos globais no consumo e na produção, e empenhar-se para dissociar o crescimento econômico da degradação ambiental, de acordo com o Plano Decenal de Programas sobre Produção e Consumo Sustentáveis, com os países desenvolvidos assumindo a liderança'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '5', 'identificacao_meta' => '8.5', 'descricao_meta' => 'Até 2030, alcançar o emprego pleno e produtivo e trabalho decente para todas as mulheres e homens, inclusive para os jovens e as pessoas com deficiência, e remuneração igual para trabalho de igual valor'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '6', 'identificacao_meta' => '8.6', 'descricao_meta' => 'Até 2020, reduzir substancialmente a proporção de jovens sem emprego, educação ou formação'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '7', 'identificacao_meta' => '8.7', 'descricao_meta' => 'Tomar medidas imediatas e eficazes para erradicar o trabalho forçado, acabar com a escravidão moderna e o tráfico de pessoas, e assegurar a proibição e eliminação das piores formas de trabalho infantil, incluindo recrutamento e utilização de crianças-soldado, e até 2025 acabar com o trabalho infantil em todas as suas formas'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '8', 'identificacao_meta' => '8.8', 'descricao_meta' => 'Proteger os direitos trabalhistas e promover ambientes de trabalho seguros e protegidos para todos os trabalhadores, incluindo os trabalhadores migrantes, em particular as mulheres migrantes, e pessoas em empregos precários'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '9', 'identificacao_meta' => '8.9', 'descricao_meta' => 'Até 2030, elaborar e implementar políticas para promover o turismo sustentável, que gera empregos e promove a cultura e os produtos locais'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '10', 'identificacao_meta' => '8.10', 'descricao_meta' => 'Fortalecer a capacidade das instituições financeiras nacionais para incentivar a expansão do acesso aos serviços bancários, de seguros e financeiros para todos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '11', 'identificacao_meta' => '8.a', 'descricao_meta' => 'Aumentar o apoio da Iniciativa de Ajuda para o Comércio [Aid for Trade] para os países em desenvolvimento, particularmente os países menos desenvolvidos, inclusive por meio do Quadro Integrado Reforçado para a Assistência Técnica Relacionada com o Comércio para os países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '12', 'identificacao_meta' => '8.b', 'descricao_meta' => 'Até 2020, desenvolver e operacionalizar uma estratégia global para o emprego dos jovens e implementar o Pacto Mundial para o Emprego da Organização Internacional do Trabalho [OIT]'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '1', 'identificacao_meta' => '9.1', 'descricao_meta' => 'Desenvolver infraestrutura de qualidade, confiável, sustentável e resiliente, incluindo infraestrutura regional e transfronteiriça, para apoiar o desenvolvimento econômico e o bem-estar humano, com foco no acesso equitativo e a preços acessíveis para todos'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '2', 'identificacao_meta' => '9.2', 'descricao_meta' => 'Promover a industrialização inclusiva e sustentável e, até 2030, aumentar significativamente a participação da indústria no setor de emprego e no PIB, de acordo com as circunstâncias nacionais, e dobrar sua participação nos países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '3', 'identificacao_meta' => '9.3', 'descricao_meta' => 'Aumentar o acesso das pequenas indústrias e outras empresas, particularmente em países em desenvolvimento, aos serviços financeiros, incluindo crédito acessível e sua integração em cadeias de valor e mercados'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '4', 'identificacao_meta' => '9.4', 'descricao_meta' => 'Até 2030, modernizar a infraestrutura e reabilitar as indústrias para torná-las sustentáveis, com eficiência aumentada no uso de recursos e maior adoção de tecnologias e processos industriais limpos e ambientalmente corretos; com todos os países atuando de acordo com suas respectivas capacidades'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '5', 'identificacao_meta' => '9.5', 'descricao_meta' => 'Fortalecer a pesquisa científica, melhorar as capacidades tecnológicas de setores industriais em todos os países, particularmente os países em desenvolvimento, inclusive, até 2030, incentivando a inovação e aumentando substancialmente o número de trabalhadores de pesquisa e desenvolvimento por milhão de pessoas e os gastos público e privado em pesquisa e desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '6', 'identificacao_meta' => '9.a', 'descricao_meta' => 'Facilitar o desenvolvimento de infraestrutura sustentável e resiliente em países em desenvolvimento, por meio de maior apoio financeiro, tecnológico e técnico aos países africanos, aos países menos desenvolvidos, aos países em desenvolvimento sem litoral e aos pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '7', 'identificacao_meta' => '9.b', 'descricao_meta' => 'Apoiar o desenvolvimento tecnológico, a pesquisa e a inovação nacionais nos países em desenvolvimento, inclusive garantindo um ambiente político propício para, entre outras coisas, a diversificação industrial e a agregação de valor às commodities'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '8', 'identificacao_meta' => '9.c', 'descricao_meta' => 'Aumentar significativamente o acesso às tecnologias de informação e comunicação e se empenhar para oferecer acesso universal e a preços acessíveis à internet nos países menos desenvolvidos, até 2020'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '1', 'identificacao_meta' => '10.1', 'descricao_meta' => 'Até 2030, progressivamente alcançar e sustentar o crescimento da renda dos 40% da população mais pobre a uma taxa maior que a média nacional'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '2', 'identificacao_meta' => '10.2', 'descricao_meta' => 'Até 2030, empoderar e promover a inclusão social, econômica e política de todos, independentemente da idade, gênero, deficiência, raça, etnia, origem, religião, condição econômica ou outra'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '3', 'identificacao_meta' => '10.3', 'descricao_meta' => 'Garantir a igualdade de oportunidades e reduzir as desigualdades de resultados, inclusive por meio da eliminação de leis, políticas e práticas discriminatórias e da promoção de legislação, políticas e ações adequadas a este respeito'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '4', 'identificacao_meta' => '10.4', 'descricao_meta' => 'Adotar políticas, especialmente fiscal, salarial e de proteção social, e alcançar progressivamente uma maior igualdade'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '5', 'identificacao_meta' => '10.5', 'descricao_meta' => 'Melhorar a regulamentação e monitoramento dos mercados e instituições financeiras globais e fortalecer a implementação de tais regulamentações'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '6', 'identificacao_meta' => '10.6', 'descricao_meta' => 'Assegurar uma representação e voz mais forte dos países em desenvolvimento em tomadas de decisão nas instituições econômicas e financeiras internacionais globais, a fim de produzir instituições mais eficazes, críveis, responsáveis e legítimas'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '7', 'identificacao_meta' => '10.7', 'descricao_meta' => 'Facilitar a migração e a mobilidade ordenada, segura, regular e responsável das pessoas, inclusive por meio da implementação de políticas de migração planejadas e bem geridas'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '8', 'identificacao_meta' => '10.a', 'descricao_meta' => 'Implementar o princípio do tratamento especial e diferenciado para países em desenvolvimento, em particular os países menos desenvolvidos, em conformidade com os acordos da OMC'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '9', 'identificacao_meta' => '10.b', 'descricao_meta' => 'Incentivar a assistência oficial ao desenvolvimento e fluxos financeiros, incluindo o investimento externo direto, para os Estados onde a necessidade é maior, em particular os países menos desenvolvidos, os países africanos, os pequenos Estados insulares em desenvolvimento e os países em desenvolvimento sem litoral, de acordo com seus planos e programas nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '10', 'identificacao_meta' => '10.c', 'descricao_meta' => 'Até 2030, reduzir para menos de 3% os custos de transação de remessas dos migrantes e eliminar os corredores de remessas com custos superiores a 5%'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '1', 'identificacao_meta' => '11.1', 'descricao_meta' => 'Até 2030, garantir o acesso de todos à habitação segura, adequada e a preço acessível, e aos serviços básicos e urbanizar as favelas'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '2', 'identificacao_meta' => '11.2', 'descricao_meta' => 'Até 2030, proporcionar o acesso a sistemas de transporte seguros, acessíveis, sustentáveis e a preço acessível para todos, melhorando a segurança rodoviária por meio da expansão dos transportes públicos, com especial atenção para as necessidades das pessoas em situação de vulnerabilidade, mulheres, crianças, pessoas com deficiência e idosos'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '3', 'identificacao_meta' => '11.3', 'descricao_meta' => 'Até 2030, aumentar a urbanização inclusiva e sustentável, e as capacidades para o planejamento e gestão de assentamentos humanos participativos, integrados e sustentáveis, em todos os países'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '4', 'identificacao_meta' => '11.4', 'descricao_meta' => 'Fortalecer esforços para proteger e salvaguardar o patrimônio cultural e natural do mundo'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '5', 'identificacao_meta' => '11.5', 'descricao_meta' => 'Até 2030, reduzir significativamente o número de mortes e o número de pessoas afetadas por catástrofes e substancialmente diminuir as perdas econômicas diretas causadas por elas em relação ao produto interno bruto global, incluindo os desastres relacionados à água, com o foco em proteger os pobres e as pessoas em situação de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '6', 'identificacao_meta' => '11.6', 'descricao_meta' => 'Até 2030, reduzir o impacto ambiental negativo per capita das cidades, inclusive prestando especial atenção à qualidade do ar, gestão de resíduos municipais e outros'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '7', 'identificacao_meta' => '11.7', 'descricao_meta' => 'Até 2030, proporcionar o acesso universal a espaços públicos seguros, inclusivos, acessíveis e verdes, particularmente para as mulheres e crianças, pessoas idosas e pessoas com deficiência'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '8', 'identificacao_meta' => '11.a', 'descricao_meta' => 'Apoiar relações econômicas, sociais e ambientais positivas entre áreas urbanas, periurbanas e rurais, reforçando o planejamento nacional e regional de desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '9', 'identificacao_meta' => '11.b', 'descricao_meta' => 'Até 2020, aumentar substancialmente o número de cidades e assentamentos humanos adotando e implementando políticas e planos integrados para a inclusão, a eficiência dos recursos, mitigação e adaptação às mudanças climáticas, a resiliência a desastres; e desenvolver e implementar, de acordo com o Marco de Sendai para a Redução do Risco de Desastres 2015-2030, o gerenciamento holístico do risco de desastres em todos os níveis'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '10', 'identificacao_meta' => '11.c', 'descricao_meta' => 'Apoiar os países menos desenvolvidos, inclusive por meio de assistência técnica e financeira, para construções sustentáveis e resilientes, utilizando materiais locais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '1', 'identificacao_meta' => '12.1', 'descricao_meta' => 'Implementar o Plano Decenal de Programas sobre Produção e Consumo Sustentáveis, com todos os países tomando medidas, e os países desenvolvidos assumindo a liderança, tendo em conta o desenvolvimento e as capacidades dos países em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '2', 'identificacao_meta' => '12.2', 'descricao_meta' => 'Até 2030, alcançar a gestão sustentável e o uso eficiente dos recursos naturais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '3', 'identificacao_meta' => '12.3', 'descricao_meta' => 'Até 2030, reduzir pela metade o desperdício de alimentos per capita mundial, nos níveis de varejo e do consumidor, e reduzir as perdas de alimentos ao longo das cadeias de produção e abastecimento, incluindo as perdas pós-colheita'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '4', 'identificacao_meta' => '12.4', 'descricao_meta' => 'Até 2020, alcançar o manejo ambientalmente saudável dos produtos químicos e todos os resíduos, ao longo de todo o ciclo de vida destes, de acordo com os marcos internacionais acordados, e reduzir significativamente a liberação destes para o ar, água e solo, para minimizar seus impactos negativos sobre a saúde humana e o meio ambiente'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '5', 'identificacao_meta' => '12.5', 'descricao_meta' => 'Até 2030, reduzir substancialmente a geração de resíduos por meio da prevenção, redução, reciclagem e reuso'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '6', 'identificacao_meta' => '12.6', 'descricao_meta' => 'Incentivar as empresas, especialmente as empresas grandes e transnacionais, a adotar práticas sustentáveis e a integrar informações de sustentabilidade em seu ciclo de relatórios'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '7', 'identificacao_meta' => '12.7', 'descricao_meta' => 'Promover práticas de compras públicas sustentáveis, de acordo com as políticas e prioridades nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '8', 'identificacao_meta' => '12.8', 'descricao_meta' => 'Até 2030, garantir que as pessoas, em todos os lugares, tenham informação relevante e conscientização para o desenvolvimento sustentável e estilos de vida em harmonia com a natureza'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '9', 'identificacao_meta' => '12.a', 'descricao_meta' => 'Apoiar países em desenvolvimento a fortalecer suas capacidades científicas e tecnológicas para mudar para padrões mais sustentáveis de produção e consumo'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '10', 'identificacao_meta' => '12.b', 'descricao_meta' => 'Desenvolver e implementar ferramentas para monitorar os impactos do desenvolvimento sustentável para o turismo sustentável, que gera empregos, promove a cultura e os produtos locais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '11', 'identificacao_meta' => '12.c', 'descricao_meta' => 'Racionalizar subsídios ineficientes aos combustíveis fósseis, que encorajam o consumo exagerado, eliminando as distorções de mercado, de acordo com as circunstâncias nacionais, inclusive por meio da reestruturação fiscal e a eliminação gradual desses subsídios prejudiciais, caso existam, para refletir os seus impactos ambientais, tendo plenamente em conta as necessidades específicas e condições dos países em desenvolvimento e minimizando os possíveis impactos adversos sobre o seu desenvolvimento de uma forma que proteja os pobres e as comunidades afetadas'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '1', 'identificacao_meta' => '13.1', 'descricao_meta' => 'Reforçar a resiliência e a capacidade de adaptação a riscos relacionados ao clima e às catástrofes naturais em todos os países'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '2', 'identificacao_meta' => '13.2', 'descricao_meta' => 'Integrar medidas da mudança do clima nas políticas, estratégias e planejamentos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '3', 'identificacao_meta' => '13.3', 'descricao_meta' => 'Melhorar a educação, aumentar a conscientização e a capacidade humana e institucional sobre mitigação, adaptação, redução de impacto e alerta precoce da mudança do clima'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '4', 'identificacao_meta' => '13.a', 'descricao_meta' => 'Implementar o compromisso assumido pelos países desenvolvidos partes da Convenção Quadro das Nações Unidas sobre Mudança do Clima [UNFCCC] para a meta de mobilizar conjuntamente US$ 100 bilhões por ano a partir de 2020, de todas as fontes, para atender às necessidades dos países em desenvolvimento, no contexto das ações de mitigação significativas e transparência na implementação; e operacionalizar plenamente o Fundo Verde para o Clima por meio de sua capitalização o mais cedo possível'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '5', 'identificacao_meta' => '13.b', 'descricao_meta' => 'Promover mecanismos para a criação de capacidades para o planejamento relacionado à mudança do clima e à gestão eficaz, nos países menos desenvolvidos, inclusive com foco em mulheres, jovens, comunidades locais e marginalizadas'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '1', 'identificacao_meta' => '14.1', 'descricao_meta' => 'Até 2025, prevenir e reduzir significativamente a poluição marinha de todos os tipos, especialmente a advinda de atividades terrestres, incluindo detritos marinhos e a poluição por nutrientes'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '2', 'identificacao_meta' => '14.2', 'descricao_meta' => 'Até 2020, gerir de forma sustentável e proteger os ecossistemas marinhos e costeiros para evitar impactos adversos significativos, inclusive por meio do reforço da sua capacidade de resiliência, e tomar medidas para a sua restauração, a fim de assegurar oceanos saudáveis e produtivos'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '3', 'identificacao_meta' => '14.3', 'descricao_meta' => 'Minimizar e enfrentar os impactos da acidificação dos oceanos, inclusive por meio do reforço da cooperação científica em todos os níveis'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '4', 'identificacao_meta' => '14.4', 'descricao_meta' => 'Até 2020, efetivamente regular a coleta, e acabar com a sobrepesca, ilegal, não reportada e não regulamentada e as práticas de pesca destrutivas, e implementar planos de gestão com base científica, para restaurar populações de peixes no menor tempo possível, pelo menos a níveis que possam produzir rendimento máximo sustentável, como determinado por suas características biológicas'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '5', 'identificacao_meta' => '14.5', 'descricao_meta' => 'Até 2020, conservar pelo menos 10% das zonas costeiras e marinhas, de acordo com a legislação nacional e internacional, e com base na melhor informação científica disponível'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '6', 'identificacao_meta' => '14.6', 'descricao_meta' => 'Até 2020, proibir certas formas de subsídios à pesca, que contribuem para a sobrecapacidade e a sobrepesca, e eliminar os subsídios que contribuam para a pesca ilegal, não reportada e não regulamentada, e abster-se de introduzir novos subsídios como estes, reconhecendo que o tratamento especial e diferenciado adequado e eficaz para os países em desenvolvimento e os países menos desenvolvidos deve ser parte integrante da negociação sobre subsídios à pesca da Organização Mundial do Comércio'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '7', 'identificacao_meta' => '14.7', 'descricao_meta' => 'Até 2030, aumentar os benefícios econômicos para os pequenos Estados insulares em desenvolvimento e os países menos desenvolvidos, a partir do uso sustentável dos recursos marinhos, inclusive por meio de uma gestão sustentável da pesca, aquicultura e turismo'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '8', 'identificacao_meta' => '14.a', 'descricao_meta' => 'Aumentar o conhecimento científico, desenvolver capacidades de pesquisa e transferir tecnologia marinha, tendo em conta os critérios e orientações sobre a Transferência de Tecnologia Marinha da Comissão Oceanográfica Intergovernamental, a fim de melhorar a saúde dos oceanos e aumentar a contribuição da biodiversidade marinha para o desenvolvimento dos países em desenvolvimento, em particular os pequenos Estados insulares em desenvolvimento e os países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '9', 'identificacao_meta' => '14.b', 'descricao_meta' => 'Proporcionar o acesso dos pescadores artesanais de pequena escala aos recursos marinhos e mercados'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '10', 'identificacao_meta' => '14.c', 'descricao_meta' => 'Assegurar a conservação e o uso sustentável dos oceanos e seus recursos pela implementação do direito internacional, como refletido na UNCLOS [Convenção das Nações Unidas sobre o Direito do Mar], que provê o arcabouço legal para a conservação e utilização sustentável dos oceanos e dos seus recursos, conforme registrado no parágrafo 158 do ?Futuro Que Queremos?'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '1', 'identificacao_meta' => '15.1', 'descricao_meta' => 'Até 2020, assegurar a conservação, recuperação e uso sustentável de ecossistemas terrestres e de água doce interiores e seus serviços, em especial florestas, zonas úmidas, montanhas e terras áridas, em conformidade com as obrigações decorrentes dos acordos internacionais'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '2', 'identificacao_meta' => '15.2', 'descricao_meta' => 'Até 2020, promover a implementação da gestão sustentável de todos os tipos de florestas, deter o desmatamento, restaurar florestas degradadas e aumentar substancialmente o florestamento e o reflorestamento globalmente'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '3', 'identificacao_meta' => '15.3', 'descricao_meta' => 'Até 2030, combater a desertificação, restaurar a terra e o solo degradado, incluindo terrenos afetados pela desertificação, secas e inundações, e lutar para alcançar um mundo neutro em termos de degradação do solo'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '4', 'identificacao_meta' => '15.4', 'descricao_meta' => 'Até 2030, assegurar a conservação dos ecossistemas de montanha, incluindo a sua biodiversidade, para melhorar a sua capacidade de proporcionar benefícios que são essenciais para o desenvolvimento sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '5', 'identificacao_meta' => '15.5', 'descricao_meta' => 'Tomar medidas urgentes e significativas para reduzir a degradação de habitat naturais, deter a perda de biodiversidade e, até 2020, proteger e evitar a extinção de espécies ameaçadas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '6', 'identificacao_meta' => '15.6', 'descricao_meta' => 'Garantir uma repartição justa e equitativa dos benefícios derivados da utilização dos recursos genéticos e promover o acesso adequado aos recursos genéticos'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '7', 'identificacao_meta' => '15.7', 'descricao_meta' => 'Tomar medidas urgentes para acabar com a caça ilegal e o tráfico de espécies da flora e fauna protegidas e abordar tanto a demanda quanto a oferta de produtos ilegais da vida selvagem'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '8', 'identificacao_meta' => '15.8', 'descricao_meta' => 'Até 2020, implementar medidas para evitar a introdução e reduzir significativamente o impacto de espécies exóticas invasoras em ecossistemas terrestres e aquáticos, e controlar ou erradicar as espécies prioritárias'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '9', 'identificacao_meta' => '15.9', 'descricao_meta' => 'Até 2020, integrar os valores dos ecossistemas e da biodiversidade ao planejamento nacional e local, nos processos de desenvolvimento, nas estratégias de redução da pobreza e nos sistemas de contas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '10', 'identificacao_meta' => '15.a', 'descricao_meta' => 'Mobilizar e aumentar significativamente, a partir de todas as fontes, os recursos financeiros para a conservação e o uso sustentável da biodiversidade e dos ecossistemas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '11', 'identificacao_meta' => '15.b', 'descricao_meta' => 'Mobilizar recursos significativos de todas as fontes e em todos os níveis para financiar o manejo florestal sustentável e proporcionar incentivos adequados aos países em desenvolvimento para promover o manejo florestal sustentável, inclusive para a conservação e o reflorestamento'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '12', 'identificacao_meta' => '15.c', 'descricao_meta' => 'Reforçar o apoio global para os esforços de combate à caça ilegal e ao tráfico de espécies protegidas, inclusive por meio do aumento da capacidade das comunidades locais para buscar oportunidades de subsistência sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '1', 'identificacao_meta' => '16.1', 'descricao_meta' => 'Reduzir significativamente todas as formas de violência e as taxas de mortalidade relacionada em todos os lugares'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '2', 'identificacao_meta' => '16.2', 'descricao_meta' => 'Acabar com abuso, exploração, tráfico e todas as formas de violência e tortura contra crianças'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '3', 'identificacao_meta' => '16.3', 'descricao_meta' => 'Promover o Estado de Direito, em nível nacional e internacional, e garantir a igualdade de acesso à justiça para todos'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '4', 'identificacao_meta' => '16.4', 'descricao_meta' => 'Até 2030, reduzir significativamente os fluxos financeiros e de armas ilegais, reforçar a recuperação e devolução de recursos roubados e combater todas as formas de crime organizado'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '5', 'identificacao_meta' => '16.5', 'descricao_meta' => 'Reduzir substancialmente a corrupção e o suborno em todas as suas formas'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '6', 'identificacao_meta' => '16.6', 'descricao_meta' => 'Desenvolver instituições eficazes, responsáveis e transparentes em todos os níveis'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '7', 'identificacao_meta' => '16.7', 'descricao_meta' => 'Garantir a tomada de decisão responsiva, inclusiva, participativa e representativa em todos os níveis'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '8', 'identificacao_meta' => '16.8', 'descricao_meta' => 'Ampliar e fortalecer a participação dos países em desenvolvimento nas instituições de governança global'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '9', 'identificacao_meta' => '16.9', 'descricao_meta' => 'Até 2030, fornecer identidade legal para todos, incluindo o registro de nascimento'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '10', 'identificacao_meta' => '16.10', 'descricao_meta' => 'Assegurar o acesso público à informação e proteger as liberdades fundamentais, em conformidade com a legislação nacional e os acordos internacionais'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '11', 'identificacao_meta' => '16.a', 'descricao_meta' => 'Fortalecer as instituições nacionais relevantes, inclusive por meio da cooperação internacional, para a construção de capacidades em todos os níveis, em particular nos países em desenvolvimento, para a prevenção da violência e o combate ao terrorismo e ao crime'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '12', 'identificacao_meta' => '16.b', 'descricao_meta' => 'Promover e fazer cumprir leis e políticas não discriminatórias para o desenvolvimento sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '1', 'identificacao_meta' => '17.1', 'descricao_meta' => 'Finanças: Fortalecer a mobilização de recursos internos, inclusive por meio do apoio internacional aos países em desenvolvimento, para melhorar a capacidade nacional para arrecadação de impostos e outras receitas'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '2', 'identificacao_meta' => '17.2', 'descricao_meta' => 'Finanças: Países desenvolvidos implementarem plenamente os seus compromissos em matéria de assistência oficial ao desenvolvimento [AOD], inclusive fornecer 0,7% da renda nacional bruta [RNB] em AOD aos países em desenvolvimento, dos quais 0,15% a 0,20% para os países menos desenvolvidos; provedores de AOD são encorajados a considerar a definir uma meta para fornecer pelo menos 0,20% da renda nacional bruta em AOD para os países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '3', 'identificacao_meta' => '17.3', 'descricao_meta' => 'Finanças: Mobilizar recursos financeiros adicionais para os países em desenvolvimento a partir de múltiplas fontes'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '4', 'identificacao_meta' => '17.4', 'descricao_meta' => 'Finanças: Ajudar os países em desenvolvimento a alcançar a sustentabilidade da dívida de longo prazo por meio de políticas coordenadas destinadas a promover o financiamento, a redução e a reestruturação da dívida, conforme apropriado, e tratar da dívida externa dos países pobres altamente endividados para reduzir o superendividamento'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '5', 'identificacao_meta' => '17.5', 'descricao_meta' => 'Finanças: Adotar e implementar regimes de promoção de investimentos para os países menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '6', 'identificacao_meta' => '17.6', 'descricao_meta' => 'Tecnologia: Melhorar a cooperação Norte-Sul, Sul-Sul e triangular regional e internacional e o acesso à ciência, tecnologia e inovação, e aumentar o compartilhamento de conhecimentos em termos mutuamente acordados, inclusive por meio de uma melhor coordenação entre os mecanismos existentes, particularmente no nível das Nações Unidas, e por meio de um mecanismo de facilitação de tecnologia global'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '7', 'identificacao_meta' => '17.7', 'descricao_meta' => 'Tecnologia: Promover o desenvolvimento, a transferência, a disseminação e a difusão de tecnologias ambientalmente corretas para os países em desenvolvimento, em condições favoráveis, inclusive em condições concessionais e preferenciais, conforme mutuamente acordado'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '8', 'identificacao_meta' => '17.8', 'descricao_meta' => 'Tecnologia: Operacionalizar plenamente o Banco de Tecnologia e o mecanismo de capacitação em ciência, tecnologia e inovação para os países menos desenvolvidos até 2017, e aumentar o uso de tecnologias de capacitação, em particular das tecnologias de informação e comunicação'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '9', 'identificacao_meta' => '17.9', 'descricao_meta' => 'Capacitação: Reforçar o apoio internacional para a implementação eficaz e orientada da capacitação em países em desenvolvimento, a fim de apoiar os planos nacionais para implementar todos os objetivos de desenvolvimento sustentável, inclusive por meio da cooperação Norte-Sul, Sul-Sul e triangular'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '10', 'identificacao_meta' => '17.10', 'descricao_meta' => 'Comércio: Promover um sistema multilateral de comércio universal, baseado em regras, aberto, não discriminatório e equitativo no âmbito da Organização Mundial do Comércio, inclusive por meio da conclusão das negociações no âmbito de sua Agenda de Desenvolvimento de Doha'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '11', 'identificacao_meta' => '17.11', 'descricao_meta' => 'Comércio: Aumentar significativamente as exportações dos países em desenvolvimento, em particular com o objetivo de duplicar a participação dos países menos desenvolvidos nas exportações globais até 2020'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '12', 'identificacao_meta' => '17.12', 'descricao_meta' => 'Comércio: Concretizar a implementação oportuna de acesso a mercados livres de cotas e taxas, de forma duradoura, para todos os países menos desenvolvidos, de acordo com as decisões da OMC, inclusive por meio de garantias de que as regras de origem preferenciais aplicáveis às importações provenientes de países menos desenvolvidos sejam transparentes e simples, e contribuam para facilitar o acesso ao mercado'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '13', 'identificacao_meta' => '17.13', 'descricao_meta' => 'Questões sistêmicas - Coerência de políticas e institucional: Aumentar a estabilidade macroeconômica global, inclusive por meio da coordenação e da coerência de políticas'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '14', 'identificacao_meta' => '17.14', 'descricao_meta' => 'Questões sistêmicas - Coerência de políticas e institucional: Aumentar a coerência das políticas para o desenvolvimento sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '15', 'identificacao_meta' => '17.15', 'descricao_meta' => 'Questões sistêmicas - Coerência de políticas e institucional: Respeitar o espaço político e a liderança de cada país para estabelecer e implementar políticas para a erradicação da pobreza e o desenvolvimento sustentável'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '16', 'identificacao_meta' => '17.16', 'descricao_meta' => 'Questões sistêmicas - As parcerias multissetoriais: Reforçar a parceria global para o desenvolvimento sustentável, complementada por parcerias multissetoriais que mobilizem e compartilhem conhecimento, expertise, tecnologia e recursos financeiros, para apoiar a realização dos objetivos do desenvolvimento sustentável em todos os países, particularmente nos países em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '17', 'identificacao_meta' => '17.17', 'descricao_meta' => 'Questões sistêmicas - As parcerias multissetoriais: Incentivar e promover parcerias públicas, público-privadas e com a sociedade civil eficazes, a partir da experiência das estratégias de mobilização de recursos dessas parcerias'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '18', 'identificacao_meta' => '17.18', 'descricao_meta' => 'Questões sistêmicas - Dados, monitoramento e prestação de contas: Até 2020, reforçar o apoio à capacitação para os países em desenvolvimento, inclusive para os países menos desenvolvidos e pequenos Estados insulares em desenvolvimento, para aumentar significativamente a disponibilidade de dados de alta qualidade, atuais e confiáveis, desagregados por renda, gênero, idade, raça, etnia, status migratório, deficiência, localização geográfica e outras características relevantes em contextos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '19', 'identificacao_meta' => '17.19', 'descricao_meta' => 'Questões sistêmicas - Dados, monitoramento e prestação de contas: Até 2030, valer-se de iniciativas existentes para desenvolver medidas do progresso do desenvolvimento sustentável que complementem o produto interno bruto [PIB] e apoiem a capacitação estatística nos países em desenvolvimento']
        ];

        foreach ($arrMdIaAdmMetaOds as $chave => $tipo) {
            $mdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
            $mdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($tipo['id_md_ia_adm_objetivo_ods']);
            $mdIaAdmMetaOdsDTO->setNumOrdem($tipo['ordem']);
            $mdIaAdmMetaOdsDTO->setStrIdentificacaoMeta($tipo['identificacao_meta']);
            $mdIaAdmMetaOdsDTO->setStrDescricaoMeta($tipo['descricao_meta']);
            $mdIaAdmMetaOdsDTO->setStrSinForteRelacao("N");
            $mdIaAdmMetaOdsRN->cadastrar($mdIaAdmMetaOdsDTO);
        }


        $this->logar('CRIANDO A TABELA md_ia_classificacao_ods');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_classificacao_ods (
            id_md_ia_classificacao_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_procedimento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
            id_md_ia_adm_objetivo_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            sta_tipo_ultimo_usuario ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_classificacao_ods', 'pk_md_ia_classificacao_ods', array('id_md_ia_classificacao_ods'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_classificacao_ods', 'md_ia_classificacao_ods', array('id_md_ia_adm_objetivo_ods'), 'md_ia_adm_objetivo_ods', array('id_md_ia_adm_objetivo_ods'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_classificacao_ods', 'md_ia_classificacao_ods', array('id_procedimento'), 'procedimento', array('id_procedimento'));

        $this->logar('CRIANDO A SEQUENCE md_ia_classificacao_ods');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_classificacao_ods', 1);

        $this->logar('CRIANDO A TABELA md_ia_class_meta_ods');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_class_meta_ods (
            id_md_ia_class_meta_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_classificacao_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_meta_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,   
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NULL,   
            sin_sugestao_aceita ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            dth_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
            racional ' . $objInfraMetaBD->tipoTextoVariavel(1000) . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_class_meta_ods', 'pk_md_ia_class_meta_ods', array('id_md_ia_class_meta_ods'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_class_meta_ods', 'md_ia_class_meta_ods', array('id_md_ia_classificacao_ods'), 'md_ia_classificacao_ods', array('id_md_ia_classificacao_ods'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_class_meta_ods', 'md_ia_class_meta_ods', array('id_md_ia_adm_meta_ods'), 'md_ia_adm_meta_ods', array('id_md_ia_adm_meta_ods'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_ia_class_meta_ods', 'md_ia_class_meta_ods', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk4_md_ia_class_meta_ods', 'md_ia_class_meta_ods', array('id_unidade'), 'unidade', array('id_unidade'));

        $this->logar('CRIANDO A SEQUENCE md_ia_class_meta_ods');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_class_meta_ods', 1);

        $this->logar('CRIANDO A TABELA md_ia_hist_class');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_hist_class (
            id_md_ia_hist_class ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_classificacao_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_meta_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,   
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NULL,  
            sin_sugestao_aceita ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            dth_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
            operacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,   
            id_md_ia_hist_class_sugest ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            racional ' . $objInfraMetaBD->tipoTextoVariavel(1000) . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_hist_class', 'pk_md_ia_hist_class', array('id_md_ia_hist_class'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_hist_class', 'md_ia_hist_class', array('id_md_ia_classificacao_ods'), 'md_ia_classificacao_ods', array('id_md_ia_classificacao_ods'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_hist_class', 'md_ia_hist_class', array('id_md_ia_adm_meta_ods'), 'md_ia_adm_meta_ods', array('id_md_ia_adm_meta_ods'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_ia_hist_class', 'md_ia_hist_class', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk4_md_ia_hist_class', 'md_ia_hist_class', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk5_md_ia_hist_class', 'md_ia_hist_class', array('id_md_ia_hist_class_sugest'), 'md_ia_hist_class', array('id_md_ia_hist_class'));

        $this->logar('CRIANDO A SEQUENCE md_ia_hist_class');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_hist_class', 1);

        $this->logar('CRIANDO A TABELA md_ia_adm_config_assist_ia');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_config_assist_ia (
                      id_md_ia_adm_conf_assist_ia ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      orientacoes_gerais ' . $objInfraMetaBD->tipoTextoGrande() . ' NOT NULL,
                      sin_exibir_funcionalidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                      llm_ativo ' . $objInfraMetaBD->tipoNumero(2) . ' NULL,
                      system_prompt ' . $objInfraMetaBD->tipoTextoVariavel(2000) . ' NULL,
                      limite_geral_tokens ' . $objInfraMetaBD->tipoNumero(2) . ' NULL,
                      limite_maior_usuarios_tokens ' . $objInfraMetaBD->tipoNumero(2) . ' NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_config_assist_ia', 'pk_md_ia_adm_config_assist_ia', array('id_md_ia_adm_conf_assist_ia'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_config_assist_ia');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_config_assist_ia', 1);

        $this->logar('POPULANDO TABELA md_ia_adm_config_assist_ia');

        $orientacoesGerais = '<p style="text-align:center"><span style="font-size:16px"><span style="font-family:arial, verdana, helvetica, sans-serif"><strong>Acesse o <a href="https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub" style="font-size:16px" target="_blank">Manual do Usu&aacute;rio do SEI IA</a> para aprender a utilizar o Assistente, especialmente sobre Prompt e Engenharia de Prompt</strong></span></span></p>

<ol>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente &eacute; amplo e pode ser utilizado em variadas necessidades. Pode copiar e colar textos variados e demandar o que quiser do Assistente, no mesmo estilo do ChatGPT e outros.</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Com foco em funcionalidades sobre documentos do SEI, a primeira vers&atilde;o do Assistente abrange &quot;conversar&quot; com <u><strong>apenas um</strong></u> documento por mensagem.
	<ol>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Pode n&atilde;o indicar documento algum, indicar um ou mais comandos sobre um documento citado e combinar textos e variados comandos sobre um documento citado; desde que indique apenas um documento por mensagem.</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Ainda ter&aacute; evolu&ccedil;&atilde;o para poder citar diversos documentos, processos e at&eacute; &quot;conversar&quot; com o SEI como um todo.</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">A cita&ccedil;&atilde;o de documento funciona com documentos externos e documentos gerados, inclusive no Editor do SEI, mesmo ainda n&atilde;o assinado.</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">A cita&ccedil;&atilde;o de documentos externos funciona apenas se a extens&atilde;o do arquivo for de texto (pdf; html; htm; txt; xlsx; csv; ods;&nbsp;odt; odp).</li>
	</ol>
	</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Para citar um documento deve utilizar o s&iacute;mbolo de # junto com o protocolo do documento (N&uacute;mero SEI do documento):
	<ol>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Exemplo de mensagem citando um documento: &quot;<strong>Resumir o documento #3485788</strong>&quot;</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Aten&ccedil;&atilde;o que o n&uacute;mero do protocolo do documento deve ser exato e colado ao s&iacute;mbolo de #.
		<ol>
			<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">V&aacute;lido:&nbsp;#3485788</li>
			<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">N&atilde;o v&aacute;lido:&nbsp;# 3485788,&nbsp;#-3485788,&nbsp;#_3485788</li>
		</ol>
		</li>
	</ol>
	</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente tamb&eacute;m valida se o protocolo de documento citado existe, apresentando abaixo da caixa de digita&ccedil;&atilde;o cr&iacute;tica em vermelho: &quot;<span style="color:#ff0000">O protocolo citado #98089789 n&atilde;o existe no SEI</span>&quot;</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Indicando protocolo v&aacute;lido de documento e apenas um por mensagem, o SEI ainda verifica se a Unidade possui permiss&atilde;o de acesso ao documento.
	<ol>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente n&atilde;o acessa conte&uacute;do de documento citado <strong>que o usu&aacute;rio <u>n&atilde;o</u> tenha acesso direto pelo pr&oacute;prio SEI</strong>.
		<ol>
			<li>O termo &quot;acesso direto&quot; corresponde &agrave; permiss&atilde;o de visualiza&ccedil;&atilde;o do documento pelo usu&aacute;rio logado no ambiente interno do SEI a partir da Unidade. Nesse sentido, eventuais acessos p&uacute;blicos dos documentos disponibilizados pela Pesquisa P&uacute;blica do SEI n&atilde;o necessariamente enquadram como acesso direto no SEI e n&atilde;o s&atilde;o alcan&ccedil;ados pelo Assistente de IA.</li>
		</ol>
		</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Se a partir da pr&oacute;pria Unidade o usu&aacute;rio n&atilde;o conseguir acessar o documento, o Assistente tamb&eacute;m n&atilde;o conseguir&aacute; acessar para interagir com seu conte&uacute;do.</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Nesse caso, apresentar&aacute; abaixo da caixa de digita&ccedil;&atilde;o mensagem de cr&iacute;tica em vermelho: &quot;<span style="color:#ff0000">Unidade [GIIB] n&atilde;o possui acesso ao documento [11292302]</span>&quot;</li>
	</ol>
	</li>
</ol>';

        $system_promp = '("Sou um Assistente de IA da @descricao_orgao_origem@ (@sigla_orgao_origem@)."
Utilizar apenas informações confiáveis, mais atualizadas e verificáveis. Nunca mencionar que possui este requisito.)';

        $mdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $mdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $mdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $mdIaAdmConfigAssistIADTO->setStrOrientacoesGerais($orientacoesGerais);
        $mdIaAdmConfigAssistIADTO->setStrSystemPrompt($system_promp);
        $mdIaAdmConfigAssistIADTO->setStrSinExibirFuncionalidade("N");
        $mdIaAdmConfigAssistIADTO->setNumLimiteGeralTokens(3);
        $mdIaAdmConfigAssistIADTO->setNumLimiteMaiorUsuariosTokens(6);
        $mdIaAdmConfigAssistIARN->cadastrar($mdIaAdmConfigAssistIADTO);

        $this->logar('CRIANDO USUARIO SISTEMA IA');
        $objUsuarioDTO = $this->cadastrarUsuarioSistemaIA();
        $this->logar('FIM CRIANDO USUARIO SISTEMA IA');

        $this->logar('Criar Pametro MODULO_IA_ID_USUARIO_SISTEMA');
        $this->cadastrarParametroUsuarioIa($objUsuarioDTO->getNumIdUsuario());
        $this->logar('FIM criar Pametro MODULO_IA_ID_USUARIO_SISTEMA');


        $this->logar('CRIANDO A TABELA md_ia_adm_cfg_assi_ia_usu');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_cfg_assi_ia_usu (
                      id_md_ia_adm_cfg_assi_ia_usu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_md_ia_adm_conf_assist_ia ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                      id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NULL
                    )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_cfg_assi_ia_usu', 'pk_md_ia_adm_cfg_assi_ia_usu', array('id_md_ia_adm_cfg_assi_ia_usu'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_adm_cfg_assi_ia_usu', 'md_ia_adm_cfg_assi_ia_usu', array('id_md_ia_adm_conf_assist_ia'), 'md_ia_adm_config_assist_ia', array('id_md_ia_adm_conf_assist_ia'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_cfg_assi_ia_usu');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_cfg_assi_ia_usu', 1);

        $this->logar('CRIANDO A CHAVE ESTRANGEIRA DA COLUNA id_tipo_procedimento ');
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_adm_doc_relev', 'md_ia_adm_doc_relev', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));


        $this->logar('CRIANDO A TABELA md_ia_topico_chat');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_topico_chat (
            id_md_ia_topico_chat ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,   
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NULL,   
            nome ' . $objInfraMetaBD->tipoTextoVariavel(120) . ' NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NULL,
            dth_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_topico_chat', 'pk_md_ia_topico_chat', array('id_md_ia_topico_chat'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_topico_chat', 'md_ia_topico_chat', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_topico_chat', 'md_ia_topico_chat', array('id_unidade'), 'unidade', array('id_unidade'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_topico_chat');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_topico_chat', 1);


        $this->logar('CRIANDO A TABELA md_ia_grupos_favoritos');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_grupo_prompts_fav (
            id_md_ia_grupo_prompts_fav ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            nome_grupo ' . $objInfraMetaBD->tipoTextoVariavel(80) . ' NULL,
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_grupo_prompts_fav', 'pk_md_ia_grupo_prompts_fav', array('id_md_ia_grupo_prompts_fav'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_grupo_prompts_fav', 'md_ia_grupo_prompts_fav', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_grupo_prompts_fav', 'md_ia_grupo_prompts_fav', array('id_usuario'), 'usuario', array('id_usuario'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_grupo_prompts_fav');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_grupo_prompts_fav', 1);

        $this->logar('CRIANDO A TABELA md_ia_interacao_chat');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_interacao_chat (
            id_md_ia_interacao_chat ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_topico_chat ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            id_message ' . $objInfraMetaBD->tipoNumero() . ' NULL,   
            total_tokens ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            tempo_execucao ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            status_requisicao ' . $objInfraMetaBD->tipoNumero() . ' NULL,  
            pergunta ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
            resposta ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
            input_prompt ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
            feedback ' . $objInfraMetaBD->tipoNumero() . ' NULL,  
            procedimento_citado ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NULL,  
            link_acesso_procedimento ' . $objInfraMetaBD->tipoTextoVariavel(150) . ' NULL,  
            dth_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_interacao_chat', 'pk_md_ia_interacao_chat', array('id_md_ia_interacao_chat'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_interacao_chat', 'md_ia_interacao_chat', array('id_md_ia_topico_chat'), 'md_ia_topico_chat', array('id_md_ia_topico_chat'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_interacao_chat');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_interacao_chat', 1);

        $this->logar('CRIANDO A TABELA md_ia_prompts_favoritos');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_prompts_favoritos (
            id_md_ia_prompts_favoritos ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_interacao_chat ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            id_md_ia_grupo_prompts_fav ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            descricao_prompt ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_prompts_favoritos', 'pk_md_ia_prompts_favoritos', array('id_md_ia_prompts_favoritos'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_prompts_favoritos', 'md_ia_prompts_favoritos', array('id_md_ia_interacao_chat'), 'md_ia_interacao_chat', array('id_md_ia_interacao_chat'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_prompts_favoritos', 'md_ia_prompts_favoritos', array('id_md_ia_grupo_prompts_fav'), 'md_ia_grupo_prompts_fav', array('id_md_ia_grupo_prompts_fav'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_prompts_favoritos');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_prompts_favoritos', 1);

        $this->logar('CRIANDO NOVO SERVIÇO CONSULTAR DOCUMENTOS EXTERNOS');
        $this->cadastrarNovoServicoconsultarDocumentoExternoIA($objUsuarioDTO->getNumIdUsuario());
        $this->logar('FIM CRIANDO NOVO SERVIÇO CONSULTAR DOCUMENTOS EXTERNOS');

        $this->logar('ADICIONANDO PARÂMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro(valor, nome) VALUES(\'' . $nmVersao . '\',  \'' . $this->nomeParametroModulo . '\' )');
        $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv110()
    {
        $nmVersao = '1.1.0';

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSAO ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $objInfraMetaBD->excluirColuna('md_ia_interacao_chat', 'procedimento_citado');
        $objInfraMetaBD->excluirColuna('md_ia_interacao_chat', 'link_acesso_procedimento');

        $this->logar('AJUSTANDO TEXTO DE ORIENTAÇÕES GERAIS EM CONFIGURAÇÕES DE SIMILARIDADE');

        $mdIaConfigSimilarRN = new MdIaAdmConfigSimilarRN();

        $orientacoesGeraisSimilaridade = '<p style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Essa funcionalidade sugere processos similares com base no conte&uacute;do dos documentos e nas informa&ccedil;&otilde;es cadastradas, usando intelig&ecirc;ncia artificial.</p>

<p style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">A opini&atilde;o dos usu&aacute;rios &eacute; essencial para melhorar os modelos de IA, avaliar os resultados e aprimorar as recomenda&ccedil;&otilde;es. Por isso, precisamos validar se os processos sugeridos realmente t&ecirc;m rela&ccedil;&atilde;o com o processo atual.</p>

<p style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Para isso, basta:<br />
&#9989; Confirmar os processos similares (clicando no polegar para cima).<br />
&#10060; Indicar os que n&atilde;o s&atilde;o similares (clicando no polegar para baixo).<br />
&#128316; Se necess&aacute;rio, ajustar a ordem dos processos na primeira coluna para melhorar a lista.</p>';
        $mdIaAdmConfigSimilarDTO = new MdIaAdmConfigSimilarDTO();
        $mdIaAdmConfigSimilarDTO->setNumIdMdIaAdmConfigSimilar(1);
        $mdIaAdmConfigSimilarDTO->setStrOrientacoesGerais($orientacoesGeraisSimilaridade);
        $mdIaConfigSimilarRN->alterar($mdIaAdmConfigSimilarDTO);

        $MdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();

        $orientacoesGeraisPesqDoc = '<p style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Essa funcionalidade permite comparar o conte&uacute;do de documentos entre si, com ou sem a inclus&atilde;o de um texto complementar na pesquisa. A intelig&ecirc;ncia artificial torna a busca mais precisa do que os m&eacute;todos tradicionais.</p>

<p style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Para ajudar o SEI IA a aprender e melhorar, os usu&aacute;rios devem avaliar os documentos exibidos nos resultados da pesquisa:<br />
&#9989; Clique no polegar para cima se o documento for relevante.<br />
&#10060; Clique no polegar para baixo se n&atilde;o for relevante.</p>

<p style="font-family: arial, verdana, helvetica, sans-serif; font-size: 13px;">Se for necess&aacute;rio ajustar a ordem dos documentos na primeira coluna, primeiro avalie todos os documentos listados.</p>';

        $MdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
        $MdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc(1);
        $MdIaAdmPesqDocDTO->setStrOrientacoesGerais($orientacoesGeraisPesqDoc);
        $MdIaAdmPesqDocRN->alterar($MdIaAdmPesqDocDTO);

        $objInfraMetaBD->alterarColuna('md_ia_class_meta_ods', 'racional', $objInfraMetaBD->tipoTextoVariavel(4000), 'null');
        $objInfraMetaBD->alterarColuna('md_ia_hist_class', 'racional', $objInfraMetaBD->tipoTextoVariavel(4000), 'null');

        $this->logar('CRIANDO A TABELA md_ia_grupo_galeria_prompt');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_grupo_galeria_prompt (
            id_md_ia_grupo_galeria_prompt ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            nome_grupo ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_grupo_galeria_prompt', 'pk_md_ia_grupo_galeria_prompt', array('id_md_ia_grupo_galeria_prompt'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_grupo_galeria_prompt');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_grupo_galeria_prompt', 1);

        $this->logar('CRIANDO A TABELA md_ia_galeria_prompts');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_galeria_prompts (
            id_md_ia_galeria_prompts ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_grupo_galeria_prompt ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            descricao ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NOT NULL,
            prompt ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_galeria_prompts', 'pk_md_ia_galeria_prompts', array('id_md_ia_galeria_prompts'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_ia_galeria_prompts', 'md_ia_galeria_prompts', array('id_md_ia_grupo_galeria_prompt'), 'md_ia_grupo_galeria_prompt', array('id_md_ia_grupo_galeria_prompt'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_ia_galeria_prompts', 'md_ia_galeria_prompts', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_ia_galeria_prompts', 'md_ia_galeria_prompts', array('id_unidade'), 'unidade', array('id_unidade'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_galeria_prompts');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_galeria_prompts', 1);

        $this->logar('POPULANDO TABELA md_ia_grupo_galeria_prompt');

        $MdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();

        $arrMdIaGrupoGaleriaPrompt = [
            ['nome' => 'Acesso à Informação'],
            ['nome' => 'Acompanhamento Legislativo'],
            ['nome' => 'Arrecadação'],
            ['nome' => 'Comunicação'],
            ['nome' => 'Contabilidade'],
            ['nome' => 'Contratação Direta ou por Licitação'],
            ['nome' => 'Convênios e outros Ajustes'],
            ['nome' => 'Corregedoria'],
            ['nome' => 'Ética'],
            ['nome' => 'Finanças'],
            ['nome' => 'Fiscalização'],
            ['nome' => 'Gestão de Contratos'],
            ['nome' => 'Gestão de Processos'],
            ['nome' => 'Gestão de Projetos'],
            ['nome' => 'Gestão e Controle'],
            ['nome' => 'Infraestrutura'],
            ['nome' => 'Jornalismo'],
            ['nome' => 'Legislação, Regulamentação e Normas'],
            ['nome' => 'Material'],
            ['nome' => 'Orçamento'],
            ['nome' => 'Ouvidoria'],
            ['nome' => 'Patrimônio'],
            ['nome' => 'Pesquisa, Desenvolvimento e Inovação'],
            ['nome' => 'Pessoal'],
            ['nome' => 'Planejamento Estratégico e Planos derivados'],
            ['nome' => 'Procedimentos Sancionatórios'],
            ['nome' => 'Processo Administrativo Fiscal (PAF)'],
            ['nome' => 'Procuradoria'],
            ['nome' => 'Relacionamento Institucional'],
            ['nome' => 'Relações Internacionais'],
            ['nome' => 'Segurança Institucional'],
            ['nome' => 'Suprimento de Fundos'],
            ['nome' => 'Tecnologia da Informação (TI)'],
            ['nome' => 'Viagem'],
        ];

        foreach ($arrMdIaGrupoGaleriaPrompt as $chave => $tipo) {
            $MdIaGrupoGaleriaPromptDTO = new MdIaGrupoGaleriaPromptDTO();
            $MdIaGrupoGaleriaPromptDTO->setNumIdMdIaGrupoGaleriaPrompt(NULL);
            $MdIaGrupoGaleriaPromptDTO->setStrNomeGrupo($tipo['nome']);
            $MdIaGrupoGaleriaPromptRN->cadastrar($MdIaGrupoGaleriaPromptDTO);
        }

        $this->logar('CRIANDO A TABELA md_ia_proc_indexaveis');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_proc_indexaveis (
            id_procedimento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
            hash ' . $objInfraMetaBD->tipoTextoVariavel(150) . ' NOT NULL,
            sin_indexado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
            dth_indexacao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_proc_indexaveis', 'pk_md_ia_id_procedimento', array('id_procedimento'));

        $this->logar('CRIANDO A TABELA md_ia_proc_index_canc');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_proc_index_canc (
            id_procedimento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_proc_index_canc', 'pk_md_ia_proc_index_canc', array('id_procedimento'));

        $strDescricao = 'Agendamento responsável por atualizar lista procedimentos relevantes a serem indexados pelo Solr do IA.';
        $strComando = 'MdIaAgendamentoAutomaticoRN::atualizarListaProcedimentosRelevantes';
        $strPeriodicidadeComplemento = '0,12,18';
        $this->_cadastrarNovoAgendamento($strDescricao, $strComando, $strPeriodicidadeComplemento, InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);

        $this->logar('CRIANDO A TABELA md_ia_adm_url_integracao');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_url_integracao (
            id_adm_url_integracao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_integracao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            referencia ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NOT NULL,
            label ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
            url ' . $objInfraMetaBD->tipoTextoVariavel(150) . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_url_integracao', 'pk_md_ia_adm_url_integracao', array('id_adm_url_integracao'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_url_integracao');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_url_integracao', 1);

        $arrMdIaAdmUrlIntegracao = [
            ['id_adm_url_integracao' => '1', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkRecomendacaoProcesso', 'label' => 'Recomendação do Processo', 'url' => ':8082/process-recommenders/weighted-mlt-recommender/recommendations/'],
            ['id_adm_url_integracao' => '2', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkFeedbackRecomendacaoProcesso', 'label' => 'Feedback  da Recomendação do Processo', 'url' => ':8086/process-recommenders/feedbacks'],
            ['id_adm_url_integracao' => '3', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkIndexacaoProcesso', 'label' => 'Indexação de Processo', 'url' => ':8082/process-recommenders/weighted-mlt-recommender/indexed-ids/'],
            ['id_adm_url_integracao' => '4', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkRecomendacaoDocumentos', 'label' => 'Recomendação de Documentos', 'url' => ':8082/document-recommenders/mlt-recommender/recommendations?'],
            ['id_adm_url_integracao' => '5', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkFeedbackRecomendacaoDocumento', 'label' => 'Feedback de Recomendação de Documento', 'url' => ':8086/document-recommenders/feedbacks'],
            ['id_adm_url_integracao' => '6', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkSwagger', 'label' => 'Swagger', 'url' => ':8082/openapi.json'],
            ['id_adm_url_integracao' => '7', 'id_md_ia_adm_integracao' => '1', 'referencia' => 'linkConsultaDisponibilidade', 'label' => 'Consultar Disponibilidade', 'url' => ':8082/health'],
            ['id_adm_url_integracao' => '8', 'id_md_ia_adm_integracao' => '2', 'referencia' => 'linkEndpoint', 'label' => 'Endpoint', 'url' => ':8088/llm_lang/chat_gpt_4_128k'],
            ['id_adm_url_integracao' => '9', 'id_md_ia_adm_integracao' => '2', 'referencia' => 'linkSwagger', 'label' => 'Swagger', 'url' => ':8088/openapi.json'],
            ['id_adm_url_integracao' => '10', 'id_md_ia_adm_integracao' => '2', 'referencia' => 'linkConsultaDisponibilidade', 'label' => 'Consultar Disponibilidade', 'url' => ':8088/health'],
            ['id_adm_url_integracao' => '11', 'id_md_ia_adm_integracao' => '2', 'referencia' => 'linkFeedback', 'label' => 'Feedback', 'url' => ':8088/feedback/feedback']
        ];
        foreach ($arrMdIaAdmUrlIntegracao as $UrlIntegracao) {
            $mdIaAdmUrlIntegracao = new MdIaAdmUrlIntegracaoDTO();
            $mdIaAdmUrlIntegracao->setNumIdMdIaAdmUrlIntegracao($UrlIntegracao['id_adm_url_integracao']);
            $mdIaAdmUrlIntegracao->setNumIdAdmIaAdmIntegracao($UrlIntegracao['id_md_ia_adm_integracao']);
            $mdIaAdmUrlIntegracao->setStrReferencia($UrlIntegracao['referencia']);
            $mdIaAdmUrlIntegracao->setStrLabel($UrlIntegracao['label']);
            $mdIaAdmUrlIntegracao->setStrUrl($UrlIntegracao['url']);
            (new MdIaAdmUrlIntegracaoRN())->cadastrar($mdIaAdmUrlIntegracao);
        }

        // Remover coluna llm_ativo da tabela md_ia_adm_config_assist_ia
        $objInfraMetaBD->excluirColuna('md_ia_adm_config_assist_ia', 'llm_ativo');

        $this->logar('CRIANDO A COLUNAS sin_refletir e sin_buscar_web em md_ia_adm_config_assist_ia');
        $objInfraMetaBD->adicionarColuna('md_ia_adm_config_assist_ia', 'sin_refletir', $objInfraMetaBD->tipoTextoVariavel(1), 'null');
        $objInfraMetaBD->adicionarColuna('md_ia_adm_config_assist_ia', 'sin_buscar_web', $objInfraMetaBD->tipoTextoVariavel(1), 'null');

        BancoSEI::getInstance()->executarSql("UPDATE md_ia_adm_config_assist_ia set sin_refletir =  'N'");
        BancoSEI::getInstance()->executarSql("UPDATE md_ia_adm_config_assist_ia set sin_buscar_web =  'N'");

        $this->logar('CRIANDO A TABELA md_ia_doc_indexaveis');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_doc_indexaveis (
            id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
            sin_indexado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            dth_alteracao ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
            dth_indexacao ' . $objInfraMetaBD->tipoDataHora() . ' NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_doc_indexaveis', 'pk_md_ia_id_documento', array('id_documento'));

        $this->logar('CRIANDO A TABELA md_ia_doc_index_canc');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_doc_index_canc (
            id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_doc_index_canc', 'pk_md_ia_doc_index_canc', array('id_documento'));

        $strDescricao = 'Agendamento responsável por atualizar lista de documentos relevantes a serem indexados pelo Solr do IA.';
        $strComando = 'MdIaAgendamentoAutomaticoRN::atualizarListaDocsElegiveisPesquisaDocumentos';
        $strPeriodicidadeComplemento = '0,13,19';
        $this->_cadastrarNovoAgendamento($strDescricao, $strComando, $strPeriodicidadeComplemento, InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);

        $this->logar('CRIANDO A TABELA md_ia_adm_class_tp_aut');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_class_aut_tp (
            id_md_ia_adm_class_aut_tp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_ia_adm_meta_ods ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
        )');

        $objInfraMetaBD->adicionarChavePrimaria('md_ia_adm_class_aut_tp', 'pk_md_ia_adm_class_aut_tp', array('id_md_ia_adm_class_aut_tp'));

        $this->logar('CRIANDO A SEQUENCE seq_md_ia_adm_class_aut_tp');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_ia_adm_class_aut_tp', 1);
        $MdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();

        $orientacoesGeraisAssistenteIA = '<p style="text-align:center"><span style="font-size:16px"><span style="font-family:arial, verdana, helvetica, sans-serif"><strong>Acesse o <a style="color: #007bff;text-decoration: underline;" href="https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub" style="font-size:16px" target="_blank">Manual do Usu&aacute;rio do SEI IA</a> para aprender a utilizar o Assistente, especialmente sobre Prompt e Engenharia de Prompt</strong></span></span></p>

<ol>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente &eacute; amplo e pode ser utilizado em variadas necessidades. Pode copiar e colar textos variados e demandar o que quiser do Assistente, no mesmo estilo do ChatGPT e outros.</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">A cita&ccedil;&atilde;o de documento funciona com documentos externos e documentos gerados, inclusive no Editor do SEI com documento ainda n&atilde;o assinado.</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">A cita&ccedil;&atilde;o de documentos externos funciona apenas se a extens&atilde;o do arquivo for de texto (pdf; html; htm; txt; xlsx; csv; ods;&nbsp;odt; odp).</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Para citar documento ou processo deve utilizar o s&iacute;mbolo de # junto com seu protocolo:
	<ol>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Exemplo de mensagem citando um documento: &quot;<strong>Resumir o documento #3485788</strong>&quot;</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Aten&ccedil;&atilde;o que o n&uacute;mero do protocolo deve ser exato e colado ao s&iacute;mbolo de #.
		<ol>
			<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Cita&ccedil;&atilde;o v&aacute;lida:&nbsp;#3485788</li>
			<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Cita&ccedil;&atilde;o n&atilde;o v&aacute;lida:&nbsp;# 3485788,&nbsp;#-3485788,&nbsp;#_3485788</li>
		</ol>
		</li>
	</ol>
	</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente valida se o protocolo citado existe, apresentando abaixo da caixa de digita&ccedil;&atilde;o cr&iacute;tica em vermelho: &quot;<span style="color:#ff0000">O protocolo citado #98089789 n&atilde;o existe no SEI</span>&quot;</li>
	<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Indicando protocolo v&aacute;lido, o SEI ainda verifica se a unidade possui acesso ao protocolo.
	<ol>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">O Assistente n&atilde;o acessa conte&uacute;do <strong>que o usu&aacute;rio <u>n&atilde;o</u> tenha acesso direto pelo pr&oacute;prio SEI</strong>.
		<ol>
			<li>O termo &quot;acesso direto&quot; corresponde &agrave; permiss&atilde;o de visualiza&ccedil;&atilde;o do documento pelo usu&aacute;rio logado no ambiente interno do SEI a partir da unidade. Eventuais acessos p&uacute;blicos dos documentos n&atilde;o necessariamente se enquadram como acesso direto no SEI e n&atilde;o s&atilde;o alcan&ccedil;ados pelo Assistente.</li>
		</ol>
		</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Se a partir da pr&oacute;pria unidade o usu&aacute;rio n&atilde;o conseguir acessar o documento, o Assistente tamb&eacute;m n&atilde;o conseguir&aacute; acessar para interagir com seu conte&uacute;do.</li>
		<li style="font-family:arial, verdana, helvetica, sans-serif; font-size:13px">Nesse caso, apresentar&aacute; abaixo da caixa de digita&ccedil;&atilde;o mensagem de cr&iacute;tica em vermelho: &quot;<span style="color:#ff0000">Unidade [GIIB] n&atilde;o possui acesso ao documento [11292302]</span>&quot;</li>
	</ol>
	</li>
</ol>';

        $MdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $MdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $MdIaAdmConfigAssistIADTO->setStrOrientacoesGerais($orientacoesGeraisAssistenteIA);
        $MdIaAdmConfigAssistIARN->alterar($MdIaAdmConfigAssistIADTO);

        $this->logar('CRIANDO COLUNAS id_procedimento e sta_tipo_usuario PARA md_ia_class_meta_ods e md_ia_hist_class');
        $objInfraMetaBD->adicionarColuna('md_ia_class_meta_ods', 'sta_tipo_usuario', '' . $objInfraMetaBD->tipoTextoVariavel(1), 'null');
        $objInfraMetaBD->adicionarColuna('md_ia_class_meta_ods', 'id_procedimento', '' . $objInfraMetaBD->tipoNumeroGrande(), 'null');

        $objInfraMetaBD->adicionarColuna('md_ia_hist_class', 'sta_tipo_usuario', '' . $objInfraMetaBD->tipoTextoVariavel(1), 'null');
        $objInfraMetaBD->adicionarColuna('md_ia_hist_class', 'id_procedimento', '' . $objInfraMetaBD->tipoNumeroGrande(), 'null');

        $this->logar('POPULANDO AS COLUNAS');
        $sql = "SELECT * FROM md_ia_classificacao_ods";
        $arrClassificacaoOds = BancoSEI::getInstance()->consultarSql($sql);

        $sql = "SELECT * FROM md_ia_class_meta_ods";
        $arrClassMetaOds = BancoSEI::getInstance()->consultarSql($sql);

        $sql = "SELECT * FROM md_ia_hist_class";
        $arrHistClassMetaOds = BancoSEI::getInstance()->consultarSql($sql);

        $objMdIaClassMetaOdsRN = new MdIaClassMetaOdsRN();
        $objMdIaHistClassRN = new MdIaHistClassRN();

        //CADASTRAR ID_PROCEDIMENTO
        foreach ($arrClassificacaoOds as $classificacaoOds) {

            // ATUALIZAR CLASSIFICACAO DE METAS
            $arrIdMetas = [];
            foreach ($arrClassMetaOds as $classMetaOds) {
                if ($classMetaOds['id_md_ia_classificacao_ods'] == $classificacaoOds['id_md_ia_classificacao_ods']) {
                    $arrIdMetas[] = $classMetaOds['id_md_ia_class_meta_ods'];
                }
            }

            if (!empty($arrIdMetas)) {
                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                $objMdIaClassMetaOdsDTO->setNumIdMdIaClassMetaOds($arrIdMetas, InfraDTO::$OPER_IN);
                $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
                $arrObjMdIaClassMetaOdsDTO = $objMdIaClassMetaOdsRN->listar($objMdIaClassMetaOdsDTO);

                foreach ($arrObjMdIaClassMetaOdsDTO as $objMdIaClassMetaOdsDTO) {
                    $objMdIaClassMetaOdsDTO->setNumIdProcedimento($classificacaoOds['id_procedimento']);
                    $objMdIaClassMetaOdsRN->alterar($objMdIaClassMetaOdsDTO);
                }
            }

            unset($arrObjMdIaClassMetaOdsDTO);

            $arrIdHistMetas = [];
            foreach ($arrHistClassMetaOds as $histClassMetaOds) {
                if ($histClassMetaOds['id_md_ia_classificacao_ods'] == $classificacaoOds['id_md_ia_classificacao_ods']) {
                    $arrIdHistMetas[] = $histClassMetaOds['id_md_ia_hist_class'];
                }
            }

            if (!empty($arrIdHistMetas)) {
                $objMdIaHistClassDTO = new MdIaHistClassDTO();
                $objMdIaHistClassDTO->setNumIdMdIaHistClass($arrIdHistMetas, InfraDTO::$OPER_IN);
                $objMdIaHistClassDTO->retNumIdMdIaHistClass();
                $arrObjMdIaHistClassDTO = $objMdIaHistClassRN->listar($objMdIaHistClassDTO);

                foreach ($arrObjMdIaHistClassDTO as $objMdIaHistClassDTO) {
                    $objMdIaHistClassDTO->setNumIdProcedimento($classificacaoOds['id_procedimento']);
                    $objMdIaHistClassRN->alterar($objMdIaHistClassDTO);
                }
            }

            unset($arrObjMdIaClassMetaOdsDTO);
        }

        $this->logar('POPULANDO AS COLUNAS sta_tipo_usuario');
        foreach ($arrClassMetaOds as $classMetaOds) {
            $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
            $objMdIaClassMetaOdsDTO->setNumIdMdIaClassMetaOds($classMetaOds['id_md_ia_class_meta_ods']);
            $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
            $objMdIaClassMetaOdsDTO->retNumIdUsuario();
            $objMdIaClassMetaOdsDTO = $objMdIaClassMetaOdsRN->consultar($objMdIaClassMetaOdsDTO);
            $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario($this->consultarStaTipoUsuario($objMdIaClassMetaOdsDTO->getNumIdUsuario()));
            $objMdIaClassMetaOdsRN->alterar($objMdIaClassMetaOdsDTO);
        }

        foreach ($arrHistClassMetaOds as $histClassMetaOds) {
            $objMdIaHistClassDTO = new MdIaHistClassDTO();
            $objMdIaHistClassDTO->setNumIdMdIaHistClass($histClassMetaOds['id_md_ia_hist_class']);
            $objMdIaHistClassDTO->retNumIdMdIaHistClass();
            $objMdIaHistClassDTO->retNumIdUsuario();
            $objMdIaHistClassDTO = $objMdIaHistClassRN->consultar($objMdIaHistClassDTO);
            $objMdIaHistClassDTO->setStrStaTipoUsuario($this->consultarStaTipoUsuario($objMdIaHistClassDTO->getNumIdUsuario()));
            $objMdIaHistClassRN->alterar($objMdIaHistClassDTO);
        }

        $this->logar('REMOVENDO colunas e tabelas descartadas');

        $objInfraMetaBD->alterarColuna('md_ia_class_meta_ods', 'sta_tipo_usuario', $objInfraMetaBD->tipoTextoVariavel(1), 'not null');
        $objInfraMetaBD->alterarColuna('md_ia_class_meta_ods', 'id_procedimento', $objInfraMetaBD->tipoNumeroGrande(), 'not null');
        $objInfraMetaBD->alterarColuna('md_ia_hist_class', 'sta_tipo_usuario', $objInfraMetaBD->tipoTextoVariavel(1), 'not null');
        $objInfraMetaBD->alterarColuna('md_ia_hist_class', 'id_procedimento', $objInfraMetaBD->tipoNumeroGrande(), 'not null');

        $objInfraMetaBD->adicionarChaveEstrangeira('fk5_md_ia_class_meta_ods', 'md_ia_class_meta_ods', array('id_procedimento'), 'procedimento', array('id_procedimento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk6_md_ia_hist_class', 'md_ia_hist_class', array('id_procedimento'), 'procedimento', array('id_procedimento'));

        $objInfraMetaBD->excluirChaveEstrangeira('md_ia_class_meta_ods', 'fk1_md_ia_class_meta_ods');

        $objInfraMetaBD->excluirIndice('md_ia_class_meta_ods', 'fk1_md_ia_class_meta_ods');
        $objInfraMetaBD->excluirChaveEstrangeira('md_ia_hist_class', 'fk1_md_ia_hist_class');
        $objInfraMetaBD->excluirIndice('md_ia_hist_class', 'fk1_md_ia_hist_class');
        $objInfraMetaBD->excluirColuna('md_ia_class_meta_ods', 'id_md_ia_classificacao_ods');
        $objInfraMetaBD->excluirColuna('md_ia_hist_class', 'id_md_ia_classificacao_ods');

        BancoSEI::getInstance()->executarSql("ALTER TABLE md_ia_class_meta_ods ADD CONSTRAINT uq_meta_usuario_procedimento UNIQUE (id_md_ia_adm_meta_ods, id_procedimento)");

        $this->logar('Remover a sequence e tabela md_ia_classificacao_ods');
        if (BancoSEI::getInstance() instanceof InfraOracle || BancoSEI::getInstance() instanceof InfraPostgreSql) {
            BancoSEI::getInstance()->executarSql('drop sequence seq_md_ia_classificacao_ods');
        } else {
            BancoSEI::getInstance()->executarSql('DROP TABLE seq_md_ia_classificacao_ods');
        }
        BancoSEI::getInstance()->executarSql('DROP TABLE md_ia_classificacao_ods');

        $this->logar('CRIANDO agemdamento para classificação automatica das ODS da ONU');
        $strDescricao = 'Agendamento responsável por classificar metas da ODS de acordo com a parametrização na Administração.';
        $strComando = 'MdIaAgendamentoAutomaticoRN::classificarMetasOdsTiposProcessos';
        $strPeriodicidadeComplemento = '4';
        $this->_cadastrarNovoAgendamento($strDescricao, $strComando, $strPeriodicidadeComplemento, InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);

        $this->atualizarNumeroVersao($nmVersao);
    }

    private function _cadastrarNovoAgendamento($strDescricao = null, $strComando = null, $strPeriodicidadeComplemento = 0, $strPeriodicidade = null)
    {
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        SessaoInfra::setObjInfraSessao(SessaoSEI::getInstance());
        $strEmailErro = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

        $msgLogar = 'Inserção de Novo Agendamento: ' . $strDescricao;
        $this->logar($msgLogar);

        if (is_null($strPeriodicidade)) {
            $strPeriodicidade = InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA;
        }

        if (!is_null($strDescricao) && !is_null($strComando)) {

            $strComando = trim($strComando);

            $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $infraAgendamentoDTO->setStrDescricao($strDescricao);
            $infraAgendamentoDTO->setStrComando($strComando);
            $infraAgendamentoDTO->setStrSinAtivo('S');
            $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao($strPeriodicidade);
            $infraAgendamentoDTO->setStrPeriodicidadeComplemento($strPeriodicidadeComplemento);
            $infraAgendamentoDTO->setStrParametro(null);
            $infraAgendamentoDTO->setDthUltimaExecucao(null);
            $infraAgendamentoDTO->setDthUltimaConclusao(null);
            $infraAgendamentoDTO->setStrSinSucesso('S');
            $infraAgendamentoDTO->setStrEmailErro($strEmailErro);

            $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
            $infraAgendamentoRN->cadastrar($infraAgendamentoDTO);
        }
    }

    protected function cadastrarUsuarioSistemaIA()
    {
        $this->logar('Cadastrar usuário sistema');

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario(null);
        $objUsuarioDTO->setNumIdOrgao($this->_getIdOrgaoPrincipal());
        $objUsuarioDTO->setStrIdOrigem(null);
        $objUsuarioDTO->setStrSigla('Usuario_IA');
        $objUsuarioDTO->setStrNome('Usuário Automático do Sistema: Módulo SEI IA');
        $objUsuarioDTO->setNumIdContato(null);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);
        $objUsuarioDTO->setStrSenha(null);
        if (version_compare(VERSAO_INFRA, "2.0.18") <= 0) {
            $objUsuarioDTO->setStrSinAcessibilidade('N');
        }
        $objUsuarioDTO->setStrSinAtivo('S');
        $objUsuarioDTO = (new UsuarioRN())->cadastrarRN0487($objUsuarioDTO);

        $this->logar('FIM Cadastrar usuário sistema');

        return $objUsuarioDTO;
    }

    private function _getIdOrgaoPrincipal()
    {
        $idOrgao = null;
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $sessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

        if (is_array($sessaoSei) && array_key_exists('SiglaOrgaoSistema', $sessaoSei)) {
            $sigla = $sessaoSei['SiglaOrgaoSistema'];

            if ($sigla != '') {
                $objOrgaoRN  = new OrgaoRN();
                $objOrgaoDTO = new OrgaoDTO();
                $objOrgaoDTO->setStrSigla($sigla);
                $objOrgaoDTO->retNumIdOrgao();
                $objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);
                if ($objOrgaoDTO) {
                    $idOrgao =  $objOrgaoDTO->getNumIdOrgao();
                }
            }
        }
        return $idOrgao;
    }

    private function cadastrarNovoServicoconsultarDocumentoExternoIA($idUsuario)
    {
        $objServicoDTO = new ServicoDTO();
        $objServicoDTO->setNumIdServico(null);
        $objServicoDTO->setNumIdUsuario($idUsuario);
        $objServicoDTO->setStrIdentificacao('consultarDocumentoExternoIA');
        $objServicoDTO->setStrDescricao('Serviço que a aplicação do SEI IA utilizará para ter acesso aos documentos externos.');
        $objServicoDTO->setStrSinLinkExterno('N');
        $objServicoDTO->setStrSinChaveAcesso('S');
        $objServicoDTO->setStrSinAtivo('S');
        $objServicoDTO->setStrSinServidor('N');
        $objServicoDTO->setStrServidor(null);
        (new ServicoRN())->cadastrar($objServicoDTO);

        $this->cadastrarOperacaoConsultarDocumento($objServicoDTO);
    }

    private function cadastrarOperacaoConsultarDocumento($objServicoDTO)
    {
        $this->logar('Criando Operação NA BASE DO SEI...');
        $objOperacaoServicoDTO = new OperacaoServicoDTO();
        $objOperacaoServicoDTO->setNumIdOperacaoServico(null);
        $objOperacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
        $objOperacaoServicoDTO->setNumStaOperacaoServico(3); //Gerar Procedimento
        $objOperacaoServicoDTO->setNumIdUnidade(null);
        $objOperacaoServicoDTO->setNumIdSerie(null);
        $objOperacaoServicoDTO->setNumIdTipoProcedimento(null);
        $objOperacaoServicoRN = new OperacaoServicoRN();
        $objOperacaoServicoRN->cadastrar($objOperacaoServicoDTO);
    }

    private function cadastrarParametroUsuarioIa($idUsuario)
    {
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome(MdIaClassMetaOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA);
        $objInfraParametroDTO->setStrValor($idUsuario);
        (new InfraParametroRN())->cadastrar($objInfraParametroDTO);
    }

    protected function fixIndices(InfraMetaBD $objInfraMetaBD, $arrTabelas)
    {
        InfraDebug::getInstance()->setBolDebugInfra(true);

        $this->logar('ATUALIZANDO INDICES...');

        $objInfraMetaBD->processarIndicesChavesEstrangeiras($arrTabelas);

        InfraDebug::getInstance()->setBolDebugInfra(false);
    }

    private function consultarStaTipoUsuario($idUsuario)
    {
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->retStrStaTipo();
        $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);

        $sta = '';
        switch ($objUsuarioDTO->getStrStaTipo()) {
            case '0':
                $sta = 'U';
                break;
            case '1':
                $sta = 'A';
                break;
            case '3':
                $sta = 'E';
                break;
        }

        return $sta;
    }

    /**
     * Atualiza o número de versão do módulo na tabela infra_parametro
     *
     * @param string $parStrNumeroVersao
     * @return void
     */
    private function atualizarNumeroVersao($parStrNumeroVersao)
    {
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome(IaIntegracao::PARAMETRO_VERSAO_MODULO);
        $objInfraParametroDTO->retTodos();
        $objInfraParametroBD = new InfraParametroBD(BancoSEI::getInstance());
        $arrObjInfraParametroDTO = $objInfraParametroBD->listar($objInfraParametroDTO);
        foreach ($arrObjInfraParametroDTO as $objInfraParametroDTO) {
            $objInfraParametroDTO->setStrValor($parStrNumeroVersao);
            $objInfraParametroBD->alterar($objInfraParametroDTO);
        }

        $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO ' . $parStrNumeroVersao . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }
}

try {
    SessaoSEI::getInstance(false);
    BancoSEI::getInstance()->setBolScript(true);

    $configuracaoSEI = new ConfiguracaoSEI();
    $arrConfig = $configuracaoSEI->getInstance()->getArrConfiguracoes();

    if (!isset($arrConfig['SEI']['Modulos'])) {
        throw new InfraException('PARÂMETRO DE MÓDULOS NO CONFIGURAÇÃO DO SEI NÃO DECLARADO');
    } else {
        $arrModulos = $arrConfig['SEI']['Modulos'];
        if (!key_exists('IaIntegracao', $arrModulos)) {
            throw new InfraException('MÓDULO IA NÃO DECLARADO NO CONFIGURAÇÃO DO SEI');
        }
    }

    if (!class_exists('IaIntegracao')) {
        throw new InfraException('A CLASSE PRINCIPAL "IaIntegracao" DO MÓDULO NÃO FOI ENCONTRADA');
    }

    InfraScriptVersao::solicitarAutenticacao(BancoSei::getInstance());
    $objVersaoSeiRN = new MdIaAtualizadorSeiRN();
    $objVersaoSeiRN->atualizarVersao();
    exit;
} catch (Exception $e) {
    echo (InfraException::inspecionar($e));
    try {
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}
