<?
require_once dirname(__FILE__) . '/../web/SEI.php';

class MdIaAtualizadorSeiRN extends InfraRN
{

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '1.0.0';
    private $nomeDesteModulo = 'M�DULO IA';
    private $nomeParametroModulo = 'VERSAO_MODULO_IA';
    private $historicoVersoes = array('1.0.0');

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
            $this->logar('TEMPO TOTAL DE EXECU��O: ' . $this->numSeg . ' s');
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
            $this->inicializar('INICIANDO A INSTALA��O/ATUALIZA��O DO ' . $this->nomeDesteModulo . ' NO SEI VERS�O ' . SEI_VERSAO);

            //checando BDs suportados
            if (!(BancoSEI::getInstance() instanceof InfraMySql) &&
                !(BancoSEI::getInstance() instanceof InfraSqlServer) &&
                !(BancoSEI::getInstance() instanceof InfraOracle)) {
                $this->finalizar('BANCO DE DADOS N�O SUPORTADO: ' . get_parent_class(BancoSEI::getInstance()), true);
            }

            //testando versao do framework
            $numVersaoInfraRequerida = '2.0.18';
            if(version_compare(VERSAO_INFRA, $numVersaoInfraRequerida) < 0){
                $this->finalizar('VERS�O DO FRAMEWORK PHP INCOMPAT�VEL (VERS�O ATUAL ' . VERSAO_INFRA . ', SENDO REQUERIDA VERS�O IGUAL OU SUPERIOR A ' . $numVersaoInfraRequerida . ')', true);
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
                    break;
                default:
                    $this->finalizar('A VERS�O MAIS ATUAL DO ' . $this->nomeDesteModulo . ' (v' . $this->versaoAtualDesteModulo . ') J� EST� INSTALADA.');
                    break;

            }

            $this->logar('SCRIPT EXECUTADO EM: ' . date('d/m/Y H:i:s'));
			$this->finalizar('FIM');
            InfraDebug::getInstance()->setBolDebugInfra(true);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            throw new InfraException('Erro instalando/atualizando vers�o.', $e);
        }
    }

    protected function instalarv100()
    {
        $nmVersao = '1.0.0';
		
		$this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERSAO '.$nmVersao.' DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

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
            ['id_md_ia_adm_metadado' => '5', 'metadado' => 'Especifica��o do Processo'],
            ['id_md_ia_adm_metadado' => '6', 'metadado' => 'Interessado do Processo'],
            ['id_md_ia_adm_metadado' => '7', 'metadado' => 'Cita��es']
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
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_ia_adm_integracao (
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
        $this->logar('INSERINDO FUNCIONALIDADE - Autentica��o junto � Solu��o de Intelig�ncia Artificial do SEI');

        $objMdIaFuncionalidadeRN = new MdIaAdmIntegFuncionRN();
        $objMdIaFuncionalidadeDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaFuncionalidadeDTO->setNumIdMdIaAdmIntegFuncion(MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL);
        $objMdIaFuncionalidadeDTO->setStrNome('Autentica��o junto � Solu��o de Intelig�ncia Artificial do SEI');
        $objMdIaFuncionalidadeRN->cadastrar($objMdIaFuncionalidadeDTO);

        $this->logar('INSERINDO FUNCIONALIDADE - API Interna de interface entre SEI IA e LLM de IA Generativa');

        $objMdIaFuncionalidadeRN = new MdIaAdmIntegFuncionRN();
        $objMdIaFuncionalidadeDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaFuncionalidadeDTO->setNumIdMdIaAdmIntegFuncion(MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTERFACE_LLM);
        $objMdIaFuncionalidadeDTO->setStrNome('API Interna de interface entre SEI IA e LLM de IA Generativa');
        $objMdIaFuncionalidadeRN->cadastrar($objMdIaFuncionalidadeDTO);

        $MdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $MdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $MdIaAdmIntegracaoDTO->setStrNome("Autentica��o junto � Solu��o de Intelig�ncia Artificial do SEI");
        $MdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion("1");
        $MdIaAdmIntegracaoDTO->setStrTipoIntegracao("RE");
        $MdIaAdmIntegracaoDTO->setNumMetodoAutenticacao("1");
        $MdIaAdmIntegracaoDTO->setNumMetodoRequisicao("1");
        $MdIaAdmIntegracaoDTO->setNumFormatoResposta("1");
        $MdIaAdmIntegracaoDTO->setStrOperacaoWsdl("https://hostname_docker_solucao_sei_ia_do_ambiente");
        $MdIaAdmIntegracaoDTO->setStrSinAtivo("S");
        $MdIaAdmIntegracaoRN->cadastrar($MdIaAdmIntegracaoDTO);

        $this->logar('CRIANDO A INTEGRA��O API Interna de Interface entre SEI IA e LLM de IA Generativa');
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
        $this->logar('FIM CRIAR A INTEGRA��O API Interna de Interface entre SEI IA e LLM de IA Generativa');

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
                            '�udio',
                            'Boleto',
                            'Canhoto',
                            'Cart�o',
                            'Certid�o de Distribui��o',
                            'Certid�o de Intima��o Cumprida',
                            'Certid�o de Julgamento',
                            'Certid�o de Redistribui��o',
                            'CNH',
                            'CNPJ',
                            'Conte�do de M�dia',
                            'CPF',
                            'Procura��o',
                            'Procura��o Eletr�nica Especial',
                            'Procura��o Eletr�nica Simples',
                            'Recibo Eletr�nico de Protocolo',
                            'Ren�ncia de Procura��o Eletr�nica',
                            'Restabelecimento de Procura��o Eletr�nica',
                            'Restabelecimento de Vincula��o a Pessoa Jur�dica',
                            'Revoga��o de Procura��o Eletr�nica',
                            'RG',
                            'Suspens�o de Procura��o Eletr�nica',
                            'Suspens�o de Vincula��o a Pessoa Jur�dica',
                            'Termo de Cancelamento de Documento',
                            'Termo de Encerramento de Tr�mite F�sico-Documento',
                            'Termo de Encerramento de Tr�mite F�sico-Processo',
                            'Vincula��o de Respons�vel Legal a Pessoa Jur�dica'
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
                            CONCAT(CONCAT(CONCAT(CONCAT(CAST(POS_DOC_PROC_CVP.id_tipo_procedimento AS CHAR), '_'), CAST(POS_DOC_PROC_CVP.id_serie AS CHAR)), '_'), POS_DOC_PROC_CVP.sta_protocolo) as agg,
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
            icone_ods ' . $objInfraMetaBD->tipoTextoVariavel(50). ' NOT NULL
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
            descricao_meta ' . $objInfraMetaBD->tipoTextoVariavel(1000). ' NOT NULL,
            sin_forte_relacao ' . $objInfraMetaBD->tipoTextoVariavel(1). ' NOT NULL
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
            ['nome_ods' => 'Erradica��o da pobreza', 'descricao_ods' => 'Acabar com a pobreza em todas as suas formas, em todos os lugares', 'icone_ods' => 'SDG-1.svg'],
            ['nome_ods' => 'Fome zero e agricultura sustent�vel', 'descricao_ods' => 'Acabar com a fome, alcan�ar a seguran�a alimentar e melhoria da nutri��o e promover a agricultura sustent�vel', 'icone_ods' => 'SDG-2.svg'],
            ['nome_ods' => 'Sa�de e Bem-Estar', 'descricao_ods' => 'Assegurar uma vida saud�vel e promover o bem-estar para todas e todos, em todas as idades', 'icone_ods' => 'SDG-3.svg'],
            ['nome_ods' => 'Educa��o de qualidade', 'descricao_ods' => 'Assegurar a educa��o inclusiva e equitativa e de qualidade, e promover oportunidades de aprendizagem ao longo da vida para todas e todos', 'icone_ods' => 'SDG-4.svg'],
            ['nome_ods' => 'Igualdade de g�nero', 'descricao_ods' => 'Alcan�ar a igualdade de g�nero e empoderar todas as mulheres e meninas', 'icone_ods' => 'SDG-5.svg'],
            ['nome_ods' => '�gua pot�vel e saneamento', 'descricao_ods' => 'Assegurar a disponibilidade e gest�o sustent�vel da �gua e saneamento para todas e todos', 'icone_ods' => 'SDG-6.svg'],
            ['nome_ods' => 'Energia limpa e acess�vel', 'descricao_ods' => 'Assegurar o acesso confi�vel, sustent�vel, moderno e a pre�o acess�vel � energia para todas e todos', 'icone_ods' => 'SDG-7.svg'],
            ['nome_ods' => 'Trabalho decente e crescimento econ�mico', 'descricao_ods' => 'Promover o crescimento econ�mico sustentado, inclusivo e sustent�vel, emprego pleno e produtivo e trabalho decente para todas e todos', 'icone_ods' => 'SDG-8.svg'],
            ['nome_ods' => 'Ind�stria, inova��o e infraestrutura', 'descricao_ods' => 'Construir infraestruturas resilientes, promover a industrializa��o inclusiva e sustent�vel e fomentar a inova��o', 'icone_ods' => 'SDG-9.svg'],
            ['nome_ods' => 'Redu��o das desigualdades', 'descricao_ods' => 'Reduzir a desigualdade dentro dos pa�ses e entre eles', 'icone_ods' => 'SDG-10.svg'],
            ['nome_ods' => 'Cidades e comunidades sustent�veis', 'descricao_ods' => 'Tornar as cidades e os assentamentos humanos inclusivos, seguros, resilientes e sustent�veis', 'icone_ods' => 'SDG-11.svg'],
            ['nome_ods' => 'Consumo e produ��o respons�veis', 'descricao_ods' => 'Assegurar padr�es de produ��o e de consumo sustent�veis', 'icone_ods' => 'SDG-12.svg'],
            ['nome_ods' => 'A��o contra a mudan�a global do clima', 'descricao_ods' => 'Tomar medidas urgentes para combater a mudan�a clim�tica e seus impactos', 'icone_ods' => 'SDG-13.svg'],
            ['nome_ods' => 'Vida na �gua', 'descricao_ods' => 'Conserva��o e uso sustent�vel dos oceanos, dos mares e dos recursos marinhos para o desenvolvimento sustent�vel', 'icone_ods' => 'SDG-14.svg'],
            ['nome_ods' => 'Vida terrestre', 'descricao_ods' => 'Proteger, recuperar e promover o uso sustent�vel dos ecossistemas terrestres, gerir de forma sustent�vel as florestas, combater a desertifica��o, deter e reverter a degrada��o da terra e deter a perda de biodiversidade', 'icone_ods' => 'SDG-15.svg'],
            ['nome_ods' => 'Paz, justi�a e institui��es eficazes', 'descricao_ods' => 'Promover sociedades pac�ficas e inclusivas para o desenvolvimento sustent�vel, proporcionar o acesso � justi�a para todos e construir institui��es eficazes, respons�veis e inclusivas em todos os n�veis', 'icone_ods' => 'SDG-16.svg'],
            ['nome_ods' => 'Parcerias e meios de implementa��o', 'descricao_ods' => 'Fortalecer os meios de implementa��o e revitalizar a parceria global para o desenvolvimento sustent�vel', 'icone_ods' => 'SDG-17.svg'],

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
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '1', 'identificacao_meta' => '1.1', 'descricao_meta' => 'At� 2030, erradicar a pobreza extrema para todas as pessoas em todos os lugares, atualmente medida como pessoas vivendo com menos de US$ 1,90 por dia'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '2', 'identificacao_meta' => '1.2', 'descricao_meta' => 'At� 2030, reduzir pelo menos � metade a propor��o de homens, mulheres e crian�as, de todas as idades, que vivem na pobreza, em todas as suas dimens�es, de acordo com as defini��es nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '3', 'identificacao_meta' => '1.3', 'descricao_meta' => 'Implementar, em n�vel nacional, medidas e sistemas de prote��o social adequados, para todos, incluindo pisos, e at� 2030 atingir a cobertura substancial dos pobres e vulner�veis'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '4', 'identificacao_meta' => '1.4', 'descricao_meta' => 'At� 2030, garantir que todos os homens e mulheres, particularmente os pobres e vulner�veis, tenham direitos iguais aos recursos econ�micos, bem como o acesso a servi�os b�sicos, propriedade e controle sobre a terra e outras formas de propriedade, heran�a, recursos naturais, novas tecnologias apropriadas e servi�os financeiros, incluindo microfinan�as'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '5', 'identificacao_meta' => '1.5', 'descricao_meta' => 'At� 2030, construir a resili�ncia dos pobres e daqueles em situa��o de vulnerabilidade, e reduzir a exposi��o e vulnerabilidade destes a eventos extremos relacionados com o clima e outros choques e desastres econ�micos, sociais e ambientais'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '6', 'identificacao_meta' => '1.a', 'descricao_meta' => 'Garantir uma mobiliza��o significativa de recursos a partir de uma variedade de fontes, inclusive por meio do refor�o da coopera��o para o desenvolvimento, para proporcionar meios adequados e previs�veis para que os pa�ses em desenvolvimento, em particular os pa�ses menos desenvolvidos, implementem programas e pol�ticas para acabar com a pobreza em todas as suas dimens�es'],
            ['id_md_ia_adm_objetivo_ods' => '1', 'ordem' => '7', 'identificacao_meta' => '1.b', 'descricao_meta' => 'Criar marcos pol�ticos s�lidos em n�veis nacional, regional e internacional, com base em estrat�gias de desenvolvimento a favor dos pobres e sens�veis a g�nero, para apoiar investimentos acelerados nas a��es de erradica��o da pobreza'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '1', 'identificacao_meta' => '2.1', 'descricao_meta' => 'At� 2030, acabar com a fome e garantir o acesso de todas as pessoas, em particular os pobres e pessoas em situa��es vulner�veis, incluindo crian�as, a alimentos seguros, nutritivos e suficientes durante todo o ano'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '2', 'identificacao_meta' => '2.2', 'descricao_meta' => 'At� 2030, acabar com todas as formas de desnutri��o, incluindo atingir, at� 2025, as metas acordadas internacionalmente sobre nanismo e caquexia em crian�as menores de cinco anos de idade, e atender �s necessidades nutricionais dos adolescentes, mulheres gr�vidas e lactantes e pessoas idosas'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '3', 'identificacao_meta' => '2.3', 'descricao_meta' => 'At� 2030, dobrar a produtividade agr�cola e a renda dos pequenos produtores de alimentos, particularmente das mulheres, povos ind�genas, agricultores familiares, pastores e pescadores, inclusive por meio de acesso seguro e igual � terra, outros recursos produtivos e insumos, conhecimento, servi�os financeiros, mercados e oportunidades de agrega��o de valor e de emprego n�o agr�cola'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '4', 'identificacao_meta' => '2.4', 'descricao_meta' => 'At� 2030, garantir sistemas sustent�veis de produ��o de alimentos e implementar pr�ticas agr�colas resilientes, que aumentem a produtividade e a produ��o, que ajudem a manter os ecossistemas, que fortale�am a capacidade de adapta��o �s mudan�as clim�ticas, �s condi��es meteorol�gicas extremas, secas, inunda��es e outros desastres, e que melhorem progressivamente a qualidade da terra e do solo'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '5', 'identificacao_meta' => '2.5', 'descricao_meta' => 'At� 2020, manter a diversidade gen�tica de sementes, plantas cultivadas, animais de cria��o e domesticados e suas respectivas esp�cies selvagens, inclusive por meio de bancos de sementes e plantas diversificados e bem geridos em n�vel nacional, regional e internacional, e garantir o acesso e a reparti��o justa e equitativa dos benef�cios decorrentes da utiliza��o dos recursos gen�ticos e conhecimentos tradicionais associados, como acordado internacionalmente'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '6', 'identificacao_meta' => '2.a', 'descricao_meta' => 'Aumentar o investimento, inclusive via o refor�o da coopera��o internacional, em infraestrutura rural, pesquisa e extens�o de servi�os agr�colas, desenvolvimento de tecnologia, e os bancos de genes de plantas e animais, para aumentar a capacidade de produ��o agr�cola nos pa�ses em desenvolvimento, em particular nos pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '7', 'identificacao_meta' => '2.b', 'descricao_meta' => 'Corrigir e prevenir as restri��es ao com�rcio e distor��es nos mercados agr�colas mundiais, incluindo a elimina��o paralela de todas as formas de subs�dios � exporta��o e todas as medidas de exporta��o com efeito equivalente, de acordo com o mandato da Rodada de Desenvolvimento de Doha'],
            ['id_md_ia_adm_objetivo_ods' => '2', 'ordem' => '8', 'identificacao_meta' => '2.c', 'descricao_meta' => 'Adotar medidas para garantir o funcionamento adequado dos mercados de commodities de alimentos e seus derivados, e facilitar o acesso oportuno � informa��o de mercado, inclusive sobre as reservas de alimentos, a fim de ajudar a limitar a volatilidade extrema dos pre�os dos alimentos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '1', 'identificacao_meta' => '3.1', 'descricao_meta' => 'At� 2030, reduzir a taxa de mortalidade materna global para menos de 70 mortes por 100.000 nascidos vivos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '2', 'identificacao_meta' => '3.2', 'descricao_meta' => 'At� 2030, acabar com as mortes evit�veis de rec�m-nascidos e crian�as menores de 5 anos, com todos os pa�ses objetivando reduzir a mortalidade neonatal para pelo menos 12 por 1.000 nascidos vivos e a mortalidade de crian�as menores de 5 anos para pelo menos 25 por 1.000 nascidos vivos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '3', 'identificacao_meta' => '3.3', 'descricao_meta' => 'At� 2030, acabar com as epidemias de AIDS, tuberculose, mal�ria e doen�as tropicais negligenciadas, e combater a hepatite, doen�as transmitidas pela �gua, e outras doen�as transmiss�veis'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '4', 'identificacao_meta' => '3.4', 'descricao_meta' => 'At� 2030, reduzir em um ter�o a mortalidade prematura por doen�as n�o transmiss�veis via preven��o e tratamento, e promover a sa�de mental e o bem-estar'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '5', 'identificacao_meta' => '3.5', 'descricao_meta' => 'Refor�ar a preven��o e o tratamento do abuso de subst�ncias, incluindo o abuso de drogas entorpecentes e uso nocivo do �lcool'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '6', 'identificacao_meta' => '3.6', 'descricao_meta' => 'At� 2020, reduzir pela metade as mortes e os ferimentos globais por acidentes em estradas'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '7', 'identificacao_meta' => '3.7', 'descricao_meta' => 'At� 2030, assegurar o acesso universal aos servi�os de sa�de sexual e reprodutiva, incluindo o planejamento familiar, informa��o e educa��o, bem como a integra��o da sa�de reprodutiva em estrat�gias e programas nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '8', 'identificacao_meta' => '3.8', 'descricao_meta' => 'Atingir a cobertura universal de sa�de, incluindo a prote��o do risco financeiro, o acesso a servi�os de sa�de essenciais de qualidade e o acesso a medicamentos e vacinas essenciais seguros, eficazes, de qualidade e a pre�os acess�veis para todos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '9', 'identificacao_meta' => '3.9', 'descricao_meta' => 'At� 2030, reduzir substancialmente o n�mero de mortes e doen�as por produtos qu�micos perigosos, contamina��o e polui��o do ar e �gua do solo'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '10', 'identificacao_meta' => '3.a', 'descricao_meta' => 'Fortalecer a implementa��o da Conven��o-Quadro para o Controle do Tabaco em todos os pa�ses, conforme apropriado'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '11', 'identificacao_meta' => '3.b', 'descricao_meta' => 'Apoiar a pesquisa e o desenvolvimento de vacinas e medicamentos para as doen�as transmiss�veis e n�o transmiss�veis, que afetam principalmente os pa�ses em desenvolvimento, proporcionar o acesso a medicamentos e vacinas essenciais a pre�os acess�veis, de acordo com a Declara��o de Doha, que afirma o direito dos pa�ses em desenvolvimento de utilizarem plenamente as disposi��es do acordo TRIPS sobre flexibilidades para proteger a sa�de p�blica e, em particular, proporcionar o acesso a medicamentos para todos'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '12', 'identificacao_meta' => '3.c', 'descricao_meta' => 'Aumentar substancialmente o financiamento da sa�de e o recrutamento, desenvolvimento e forma��o, e reten��o do pessoal de sa�de nos pa�ses em desenvolvimento, especialmente nos pa�ses menos desenvolvidos e nos pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '3', 'ordem' => '13', 'identificacao_meta' => '3.d', 'descricao_meta' => 'Refor�ar a capacidade de todos os pa�ses, particularmente os pa�ses em desenvolvimento, para o alerta precoce, redu��o de riscos e gerenciamento de riscos nacionais e globais de sa�de'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '1', 'identificacao_meta' => '4.1', 'descricao_meta' => 'At� 2030, garantir que todas as meninas e meninos completem o ensino prim�rio e secund�rio livre, equitativo e de qualidade, que conduza a resultados de aprendizagem relevantes e eficazes'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '2', 'identificacao_meta' => '4.2', 'descricao_meta' => 'At� 2030, garantir que todos as meninas e meninos tenham acesso a um desenvolvimento de qualidade na primeira inf�ncia, cuidados e educa��o pr�-escolar, de modo que eles estejam prontos para o ensino prim�rio'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '3', 'identificacao_meta' => '4.3', 'descricao_meta' => 'At� 2030, assegurar a igualdade de acesso para todos os homens e mulheres � educa��o t�cnica, profissional e superior de qualidade, a pre�os acess�veis, incluindo universidade'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '4', 'identificacao_meta' => '4.4', 'descricao_meta' => 'At� 2030, aumentar substancialmente o n�mero de jovens e adultos que tenham habilidades relevantes, inclusive compet�ncias t�cnicas e profissionais, para emprego, trabalho decente e empreendedorismo'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '5', 'identificacao_meta' => '4.5', 'descricao_meta' => 'At� 2030, eliminar as disparidades de g�nero na educa��o e garantir a igualdade de acesso a todos os n�veis de educa��o e forma��o profissional para os mais vulner�veis, incluindo as pessoas com defici�ncia, povos ind�genas e as crian�as em situa��o de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '6', 'identificacao_meta' => '4.6', 'descricao_meta' => 'At� 2030, garantir que todos os jovens e uma substancial propor��o dos adultos, homens e mulheres estejam alfabetizados e tenham adquirido o conhecimento b�sico de matem�tica'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '7', 'identificacao_meta' => '4.7', 'descricao_meta' => 'At� 2030, garantir que todos os alunos adquiram conhecimentos e habilidades necess�rias para promover o desenvolvimento sustent�vel, inclusive, entre outros, por meio da educa��o para o desenvolvimento sustent�vel e estilos de vida sustent�veis, direitos humanos, igualdade de g�nero, promo��o de uma cultura de paz e n�o viol�ncia, cidadania global e valoriza��o da diversidade cultural e da contribui��o da cultura para o desenvolvimento sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '8', 'identificacao_meta' => '4.a', 'descricao_meta' => 'Construir e melhorar instala��es f�sicas para educa��o, apropriadas para crian�as e sens�veis �s defici�ncias e ao g�nero, e que proporcionem ambientes de aprendizagem seguros e n�o violentos, inclusivos e eficazes para todos'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '9', 'identificacao_meta' => '4.b', 'descricao_meta' => 'At� 2020, substancialmente ampliar globalmente o n�mero de bolsas de estudo para os pa�ses em desenvolvimento, em particular os pa�ses menos desenvolvidos, pequenos Estados insulares em desenvolvimento e os pa�ses africanos, para o ensino superior, incluindo programas de forma��o profissional, de tecnologia da informa��o e da comunica��o, t�cnicos, de engenharia e programas cient�ficos em pa�ses desenvolvidos e outros pa�ses em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '4', 'ordem' => '10', 'identificacao_meta' => '4.c', 'descricao_meta' => 'At� 2030, substancialmente aumentar o contingente de professores qualificados, inclusive por meio da coopera��o internacional para a forma��o de professores, nos pa�ses em desenvolvimento, especialmente os pa�ses menos desenvolvidos e pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '1', 'identificacao_meta' => '5.1', 'descricao_meta' => 'Acabar com todas as formas de discrimina��o contra todas as mulheres e meninas em toda parte'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '2', 'identificacao_meta' => '5.2', 'descricao_meta' => 'Eliminar todas as formas de viol�ncia contra todas as mulheres e meninas nas esferas p�blicas e privadas, incluindo o tr�fico e explora��o sexual e de outros tipos'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '3', 'identificacao_meta' => '5.3', 'descricao_meta' => 'Eliminar todas as pr�ticas nocivas, como os casamentos prematuros, for�ados e de crian�as e mutila��es genitais femininas'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '4', 'identificacao_meta' => '5.4', 'descricao_meta' => 'Reconhecer e valorizar o trabalho de assist�ncia e dom�stico n�o remunerado, por meio da disponibiliza��o de servi�os p�blicos, infraestrutura e pol�ticas de prote��o social, bem como a promo��o da responsabilidade compartilhada dentro do lar e da fam�lia, conforme os contextos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '5', 'identificacao_meta' => '5.5', 'descricao_meta' => 'Garantir a participa��o plena e efetiva das mulheres e a igualdade de oportunidades para a lideran�a em todos os n�veis de tomada de decis�o na vida pol�tica, econ�mica e p�blica'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '6', 'identificacao_meta' => '5.6', 'descricao_meta' => 'Assegurar o acesso universal � sa�de sexual e reprodutiva e os direitos reprodutivos, como acordado em conformidade com o Programa de A��o da Confer�ncia Internacional sobre Popula��o e Desenvolvimento e com a Plataforma de A��o de Pequim e os documentos resultantes de suas confer�ncias de revis�o'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '7', 'identificacao_meta' => '5.a', 'descricao_meta' => 'Realizar reformas para dar �s mulheres direitos iguais aos recursos econ�micos, bem como o acesso a propriedade e controle sobre a terra e outras formas de propriedade, servi�os financeiros, heran�a e os recursos naturais, de acordo com as leis nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '8', 'identificacao_meta' => '5.b', 'descricao_meta' => 'Aumentar o uso de tecnologias de base, em particular as tecnologias de informa��o e comunica��o, para promover o empoderamento das mulheres'],
            ['id_md_ia_adm_objetivo_ods' => '5', 'ordem' => '9', 'identificacao_meta' => '5.c', 'descricao_meta' => 'Adotar e fortalecer pol�ticas s�lidas e legisla��o aplic�vel para a promo��o da igualdade de g�nero e o empoderamento de todas as mulheres e meninas em todos os n�veis'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '1', 'identificacao_meta' => '6.1', 'descricao_meta' => 'At� 2030, alcan�ar o acesso universal e equitativo a �gua pot�vel e segura para todos'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '2', 'identificacao_meta' => '6.2', 'descricao_meta' => 'At� 2030, alcan�ar o acesso a saneamento e higiene adequados e equitativos para todos, e acabar com a defeca��o a c�u aberto, com especial aten��o para as necessidades das mulheres e meninas e daqueles em situa��o de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '3', 'identificacao_meta' => '6.3', 'descricao_meta' => 'At� 2030, melhorar a qualidade da �gua, reduzindo a polui��o, eliminando despejo e minimizando a libera��o de produtos qu�micos e materiais perigosos, reduzindo � metade a propor��o de �guas residuais n�o tratadas e aumentando substancialmente a reciclagem e reutiliza��o segura globalmente'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '4', 'identificacao_meta' => '6.4', 'descricao_meta' => 'At� 2030, aumentar substancialmente a efici�ncia do uso da �gua em todos os setores e assegurar retiradas sustent�veis e o abastecimento de �gua doce para enfrentar a escassez de �gua, e reduzir substancialmente o n�mero de pessoas que sofrem com a escassez de �gua'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '5', 'identificacao_meta' => '6.5', 'descricao_meta' => 'At� 2030, implementar a gest�o integrada dos recursos h�dricos em todos os n�veis, inclusive via coopera��o transfronteiri�a, conforme apropriado'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '6', 'identificacao_meta' => '6.6', 'descricao_meta' => 'At� 2020, proteger e restaurar ecossistemas relacionados com a �gua, incluindo montanhas, florestas, zonas �midas, rios, aqu�feros e lagos'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '7', 'identificacao_meta' => '6.a', 'descricao_meta' => 'At� 2030, ampliar a coopera��o internacional e o apoio � capacita��o para os pa�ses em desenvolvimento em atividades e programas relacionados � �gua e saneamento, incluindo a coleta de �gua, a dessaliniza��o, a efici�ncia no uso da �gua, o tratamento de efluentes, a reciclagem e as tecnologias de reuso'],
            ['id_md_ia_adm_objetivo_ods' => '6', 'ordem' => '8', 'identificacao_meta' => '6.b', 'descricao_meta' => 'Apoiar e fortalecer a participa��o das comunidades locais, para melhorar a gest�o da �gua e do saneamento'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '1', 'identificacao_meta' => '7.1', 'descricao_meta' => 'At� 2030, assegurar o acesso universal, confi�vel, moderno e a pre�os acess�veis a servi�os de energia'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '2', 'identificacao_meta' => '7.2', 'descricao_meta' => 'At� 2030, aumentar substancialmente a participa��o de energias renov�veis na matriz energ�tica global'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '3', 'identificacao_meta' => '7.3', 'descricao_meta' => 'At� 2030, dobrar a taxa global de melhoria da efici�ncia energ�tica'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '4', 'identificacao_meta' => '7.a', 'descricao_meta' => 'At� 2030, refor�ar a coopera��o internacional para facilitar o acesso a pesquisa e tecnologias de energia limpa, incluindo energias renov�veis, efici�ncia energ�tica e tecnologias de combust�veis f�sseis avan�adas e mais limpas, e promover o investimento em infraestrutura de energia e em tecnologias de energia limpa'],
            ['id_md_ia_adm_objetivo_ods' => '7', 'ordem' => '5', 'identificacao_meta' => '7.b', 'descricao_meta' => 'At� 2030, expandir a infraestrutura e modernizar a tecnologia para o fornecimento de servi�os de energia modernos e sustent�veis para todos nos pa�ses em desenvolvimento, particularmente nos pa�ses menos desenvolvidos, nos pequenos Estados insulares em desenvolvimento e nos pa�ses em desenvolvimento sem litoral, de acordo com seus respectivos programas de apoio'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '1', 'identificacao_meta' => '8.1', 'descricao_meta' => 'Sustentar o crescimento econ�mico per capita de acordo com as circunst�ncias nacionais e, em particular, um crescimento anual de pelo menos 7% do produto interno bruto [PIB] nos pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '2', 'identificacao_meta' => '8.2', 'descricao_meta' => 'Atingir n�veis mais elevados de produtividade das economias por meio da diversifica��o, moderniza��o tecnol�gica e inova��o, inclusive por meio de um foco em setores de alto valor agregado e dos setores intensivos em m�o de obra'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '3', 'identificacao_meta' => '8.3', 'descricao_meta' => 'Promover pol�ticas orientadas para o desenvolvimento que apoiem as atividades produtivas, gera��o de emprego decente, empreendedorismo, criatividade e inova��o, e incentivar a formaliza��o e o crescimento das micro, pequenas e m�dias empresas, inclusive por meio do acesso a servi�os financeiros'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '4', 'identificacao_meta' => '8.4', 'descricao_meta' => 'Melhorar progressivamente, at� 2030, a efici�ncia dos recursos globais no consumo e na produ��o, e empenhar-se para dissociar o crescimento econ�mico da degrada��o ambiental, de acordo com o Plano Decenal de Programas sobre Produ��o e Consumo Sustent�veis, com os pa�ses desenvolvidos assumindo a lideran�a'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '5', 'identificacao_meta' => '8.5', 'descricao_meta' => 'At� 2030, alcan�ar o emprego pleno e produtivo e trabalho decente para todas as mulheres e homens, inclusive para os jovens e as pessoas com defici�ncia, e remunera��o igual para trabalho de igual valor'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '6', 'identificacao_meta' => '8.6', 'descricao_meta' => 'At� 2020, reduzir substancialmente a propor��o de jovens sem emprego, educa��o ou forma��o'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '7', 'identificacao_meta' => '8.7', 'descricao_meta' => 'Tomar medidas imediatas e eficazes para erradicar o trabalho for�ado, acabar com a escravid�o moderna e o tr�fico de pessoas, e assegurar a proibi��o e elimina��o das piores formas de trabalho infantil, incluindo recrutamento e utiliza��o de crian�as-soldado, e at� 2025 acabar com o trabalho infantil em todas as suas formas'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '8', 'identificacao_meta' => '8.8', 'descricao_meta' => 'Proteger os direitos trabalhistas e promover ambientes de trabalho seguros e protegidos para todos os trabalhadores, incluindo os trabalhadores migrantes, em particular as mulheres migrantes, e pessoas em empregos prec�rios'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '9', 'identificacao_meta' => '8.9', 'descricao_meta' => 'At� 2030, elaborar e implementar pol�ticas para promover o turismo sustent�vel, que gera empregos e promove a cultura e os produtos locais'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '10', 'identificacao_meta' => '8.10', 'descricao_meta' => 'Fortalecer a capacidade das institui��es financeiras nacionais para incentivar a expans�o do acesso aos servi�os banc�rios, de seguros e financeiros para todos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '11', 'identificacao_meta' => '8.a', 'descricao_meta' => 'Aumentar o apoio da Iniciativa de Ajuda para o Com�rcio [Aid for Trade] para os pa�ses em desenvolvimento, particularmente os pa�ses menos desenvolvidos, inclusive por meio do Quadro Integrado Refor�ado para a Assist�ncia T�cnica Relacionada com o Com�rcio para os pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '8', 'ordem' => '12', 'identificacao_meta' => '8.b', 'descricao_meta' => 'At� 2020, desenvolver e operacionalizar uma estrat�gia global para o emprego dos jovens e implementar o Pacto Mundial para o Emprego da Organiza��o Internacional do Trabalho [OIT]'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '1', 'identificacao_meta' => '9.1', 'descricao_meta' => 'Desenvolver infraestrutura de qualidade, confi�vel, sustent�vel e resiliente, incluindo infraestrutura regional e transfronteiri�a, para apoiar o desenvolvimento econ�mico e o bem-estar humano, com foco no acesso equitativo e a pre�os acess�veis para todos'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '2', 'identificacao_meta' => '9.2', 'descricao_meta' => 'Promover a industrializa��o inclusiva e sustent�vel e, at� 2030, aumentar significativamente a participa��o da ind�stria no setor de emprego e no PIB, de acordo com as circunst�ncias nacionais, e dobrar sua participa��o nos pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '3', 'identificacao_meta' => '9.3', 'descricao_meta' => 'Aumentar o acesso das pequenas ind�strias e outras empresas, particularmente em pa�ses em desenvolvimento, aos servi�os financeiros, incluindo cr�dito acess�vel e sua integra��o em cadeias de valor e mercados'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '4', 'identificacao_meta' => '9.4', 'descricao_meta' => 'At� 2030, modernizar a infraestrutura e reabilitar as ind�strias para torn�-las sustent�veis, com efici�ncia aumentada no uso de recursos e maior ado��o de tecnologias e processos industriais limpos e ambientalmente corretos; com todos os pa�ses atuando de acordo com suas respectivas capacidades'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '5', 'identificacao_meta' => '9.5', 'descricao_meta' => 'Fortalecer a pesquisa cient�fica, melhorar as capacidades tecnol�gicas de setores industriais em todos os pa�ses, particularmente os pa�ses em desenvolvimento, inclusive, at� 2030, incentivando a inova��o e aumentando substancialmente o n�mero de trabalhadores de pesquisa e desenvolvimento por milh�o de pessoas e os gastos p�blico e privado em pesquisa e desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '6', 'identificacao_meta' => '9.a', 'descricao_meta' => 'Facilitar o desenvolvimento de infraestrutura sustent�vel e resiliente em pa�ses em desenvolvimento, por meio de maior apoio financeiro, tecnol�gico e t�cnico aos pa�ses africanos, aos pa�ses menos desenvolvidos, aos pa�ses em desenvolvimento sem litoral e aos pequenos Estados insulares em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '7', 'identificacao_meta' => '9.b', 'descricao_meta' => 'Apoiar o desenvolvimento tecnol�gico, a pesquisa e a inova��o nacionais nos pa�ses em desenvolvimento, inclusive garantindo um ambiente pol�tico prop�cio para, entre outras coisas, a diversifica��o industrial e a agrega��o de valor �s commodities'],
            ['id_md_ia_adm_objetivo_ods' => '9', 'ordem' => '8', 'identificacao_meta' => '9.c', 'descricao_meta' => 'Aumentar significativamente o acesso �s tecnologias de informa��o e comunica��o e se empenhar para oferecer acesso universal e a pre�os acess�veis � internet nos pa�ses menos desenvolvidos, at� 2020'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '1', 'identificacao_meta' => '10.1', 'descricao_meta' => 'At� 2030, progressivamente alcan�ar e sustentar o crescimento da renda dos 40% da popula��o mais pobre a uma taxa maior que a m�dia nacional'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '2', 'identificacao_meta' => '10.2', 'descricao_meta' => 'At� 2030, empoderar e promover a inclus�o social, econ�mica e pol�tica de todos, independentemente da idade, g�nero, defici�ncia, ra�a, etnia, origem, religi�o, condi��o econ�mica ou outra'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '3', 'identificacao_meta' => '10.3', 'descricao_meta' => 'Garantir a igualdade de oportunidades e reduzir as desigualdades de resultados, inclusive por meio da elimina��o de leis, pol�ticas e pr�ticas discriminat�rias e da promo��o de legisla��o, pol�ticas e a��es adequadas a este respeito'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '4', 'identificacao_meta' => '10.4', 'descricao_meta' => 'Adotar pol�ticas, especialmente fiscal, salarial e de prote��o social, e alcan�ar progressivamente uma maior igualdade'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '5', 'identificacao_meta' => '10.5', 'descricao_meta' => 'Melhorar a regulamenta��o e monitoramento dos mercados e institui��es financeiras globais e fortalecer a implementa��o de tais regulamenta��es'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '6', 'identificacao_meta' => '10.6', 'descricao_meta' => 'Assegurar uma representa��o e voz mais forte dos pa�ses em desenvolvimento em tomadas de decis�o nas institui��es econ�micas e financeiras internacionais globais, a fim de produzir institui��es mais eficazes, cr�veis, respons�veis e leg�timas'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '7', 'identificacao_meta' => '10.7', 'descricao_meta' => 'Facilitar a migra��o e a mobilidade ordenada, segura, regular e respons�vel das pessoas, inclusive por meio da implementa��o de pol�ticas de migra��o planejadas e bem geridas'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '8', 'identificacao_meta' => '10.a', 'descricao_meta' => 'Implementar o princ�pio do tratamento especial e diferenciado para pa�ses em desenvolvimento, em particular os pa�ses menos desenvolvidos, em conformidade com os acordos da OMC'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '9', 'identificacao_meta' => '10.b', 'descricao_meta' => 'Incentivar a assist�ncia oficial ao desenvolvimento e fluxos financeiros, incluindo o investimento externo direto, para os Estados onde a necessidade � maior, em particular os pa�ses menos desenvolvidos, os pa�ses africanos, os pequenos Estados insulares em desenvolvimento e os pa�ses em desenvolvimento sem litoral, de acordo com seus planos e programas nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '10', 'ordem' => '10', 'identificacao_meta' => '10.c', 'descricao_meta' => 'At� 2030, reduzir para menos de 3% os custos de transa��o de remessas dos migrantes e eliminar os corredores de remessas com custos superiores a 5%'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '1', 'identificacao_meta' => '11.1', 'descricao_meta' => 'At� 2030, garantir o acesso de todos � habita��o segura, adequada e a pre�o acess�vel, e aos servi�os b�sicos e urbanizar as favelas'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '2', 'identificacao_meta' => '11.2', 'descricao_meta' => 'At� 2030, proporcionar o acesso a sistemas de transporte seguros, acess�veis, sustent�veis e a pre�o acess�vel para todos, melhorando a seguran�a rodovi�ria por meio da expans�o dos transportes p�blicos, com especial aten��o para as necessidades das pessoas em situa��o de vulnerabilidade, mulheres, crian�as, pessoas com defici�ncia e idosos'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '3', 'identificacao_meta' => '11.3', 'descricao_meta' => 'At� 2030, aumentar a urbaniza��o inclusiva e sustent�vel, e as capacidades para o planejamento e gest�o de assentamentos humanos participativos, integrados e sustent�veis, em todos os pa�ses'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '4', 'identificacao_meta' => '11.4', 'descricao_meta' => 'Fortalecer esfor�os para proteger e salvaguardar o patrim�nio cultural e natural do mundo'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '5', 'identificacao_meta' => '11.5', 'descricao_meta' => 'At� 2030, reduzir significativamente o n�mero de mortes e o n�mero de pessoas afetadas por cat�strofes e substancialmente diminuir as perdas econ�micas diretas causadas por elas em rela��o ao produto interno bruto global, incluindo os desastres relacionados � �gua, com o foco em proteger os pobres e as pessoas em situa��o de vulnerabilidade'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '6', 'identificacao_meta' => '11.6', 'descricao_meta' => 'At� 2030, reduzir o impacto ambiental negativo per capita das cidades, inclusive prestando especial aten��o � qualidade do ar, gest�o de res�duos municipais e outros'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '7', 'identificacao_meta' => '11.7', 'descricao_meta' => 'At� 2030, proporcionar o acesso universal a espa�os p�blicos seguros, inclusivos, acess�veis e verdes, particularmente para as mulheres e crian�as, pessoas idosas e pessoas com defici�ncia'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '8', 'identificacao_meta' => '11.a', 'descricao_meta' => 'Apoiar rela��es econ�micas, sociais e ambientais positivas entre �reas urbanas, periurbanas e rurais, refor�ando o planejamento nacional e regional de desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '9', 'identificacao_meta' => '11.b', 'descricao_meta' => 'At� 2020, aumentar substancialmente o n�mero de cidades e assentamentos humanos adotando e implementando pol�ticas e planos integrados para a inclus�o, a efici�ncia dos recursos, mitiga��o e adapta��o �s mudan�as clim�ticas, a resili�ncia a desastres; e desenvolver e implementar, de acordo com o Marco de Sendai para a Redu��o do Risco de Desastres 2015-2030, o gerenciamento hol�stico do risco de desastres em todos os n�veis'],
            ['id_md_ia_adm_objetivo_ods' => '11', 'ordem' => '10', 'identificacao_meta' => '11.c', 'descricao_meta' => 'Apoiar os pa�ses menos desenvolvidos, inclusive por meio de assist�ncia t�cnica e financeira, para constru��es sustent�veis e resilientes, utilizando materiais locais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '1', 'identificacao_meta' => '12.1', 'descricao_meta' => 'Implementar o Plano Decenal de Programas sobre Produ��o e Consumo Sustent�veis, com todos os pa�ses tomando medidas, e os pa�ses desenvolvidos assumindo a lideran�a, tendo em conta o desenvolvimento e as capacidades dos pa�ses em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '2', 'identificacao_meta' => '12.2', 'descricao_meta' => 'At� 2030, alcan�ar a gest�o sustent�vel e o uso eficiente dos recursos naturais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '3', 'identificacao_meta' => '12.3', 'descricao_meta' => 'At� 2030, reduzir pela metade o desperd�cio de alimentos per capita mundial, nos n�veis de varejo e do consumidor, e reduzir as perdas de alimentos ao longo das cadeias de produ��o e abastecimento, incluindo as perdas p�s-colheita'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '4', 'identificacao_meta' => '12.4', 'descricao_meta' => 'At� 2020, alcan�ar o manejo ambientalmente saud�vel dos produtos qu�micos e todos os res�duos, ao longo de todo o ciclo de vida destes, de acordo com os marcos internacionais acordados, e reduzir significativamente a libera��o destes para o ar, �gua e solo, para minimizar seus impactos negativos sobre a sa�de humana e o meio ambiente'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '5', 'identificacao_meta' => '12.5', 'descricao_meta' => 'At� 2030, reduzir substancialmente a gera��o de res�duos por meio da preven��o, redu��o, reciclagem e reuso'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '6', 'identificacao_meta' => '12.6', 'descricao_meta' => 'Incentivar as empresas, especialmente as empresas grandes e transnacionais, a adotar pr�ticas sustent�veis e a integrar informa��es de sustentabilidade em seu ciclo de relat�rios'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '7', 'identificacao_meta' => '12.7', 'descricao_meta' => 'Promover pr�ticas de compras p�blicas sustent�veis, de acordo com as pol�ticas e prioridades nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '8', 'identificacao_meta' => '12.8', 'descricao_meta' => 'At� 2030, garantir que as pessoas, em todos os lugares, tenham informa��o relevante e conscientiza��o para o desenvolvimento sustent�vel e estilos de vida em harmonia com a natureza'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '9', 'identificacao_meta' => '12.a', 'descricao_meta' => 'Apoiar pa�ses em desenvolvimento a fortalecer suas capacidades cient�ficas e tecnol�gicas para mudar para padr�es mais sustent�veis de produ��o e consumo'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '10', 'identificacao_meta' => '12.b', 'descricao_meta' => 'Desenvolver e implementar ferramentas para monitorar os impactos do desenvolvimento sustent�vel para o turismo sustent�vel, que gera empregos, promove a cultura e os produtos locais'],
            ['id_md_ia_adm_objetivo_ods' => '12', 'ordem' => '11', 'identificacao_meta' => '12.c', 'descricao_meta' => 'Racionalizar subs�dios ineficientes aos combust�veis f�sseis, que encorajam o consumo exagerado, eliminando as distor��es de mercado, de acordo com as circunst�ncias nacionais, inclusive por meio da reestrutura��o fiscal e a elimina��o gradual desses subs�dios prejudiciais, caso existam, para refletir os seus impactos ambientais, tendo plenamente em conta as necessidades espec�ficas e condi��es dos pa�ses em desenvolvimento e minimizando os poss�veis impactos adversos sobre o seu desenvolvimento de uma forma que proteja os pobres e as comunidades afetadas'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '1', 'identificacao_meta' => '13.1', 'descricao_meta' => 'Refor�ar a resili�ncia e a capacidade de adapta��o a riscos relacionados ao clima e �s cat�strofes naturais em todos os pa�ses'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '2', 'identificacao_meta' => '13.2', 'descricao_meta' => 'Integrar medidas da mudan�a do clima nas pol�ticas, estrat�gias e planejamentos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '3', 'identificacao_meta' => '13.3', 'descricao_meta' => 'Melhorar a educa��o, aumentar a conscientiza��o e a capacidade humana e institucional sobre mitiga��o, adapta��o, redu��o de impacto e alerta precoce da mudan�a do clima'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '4', 'identificacao_meta' => '13.a', 'descricao_meta' => 'Implementar o compromisso assumido pelos pa�ses desenvolvidos partes da Conven��o Quadro das Na��es Unidas sobre Mudan�a do Clima [UNFCCC] para a meta de mobilizar conjuntamente US$ 100 bilh�es por ano a partir de 2020, de todas as fontes, para atender �s necessidades dos pa�ses em desenvolvimento, no contexto das a��es de mitiga��o significativas e transpar�ncia na implementa��o; e operacionalizar plenamente o Fundo Verde para o Clima por meio de sua capitaliza��o o mais cedo poss�vel'],
            ['id_md_ia_adm_objetivo_ods' => '13', 'ordem' => '5', 'identificacao_meta' => '13.b', 'descricao_meta' => 'Promover mecanismos para a cria��o de capacidades para o planejamento relacionado � mudan�a do clima e � gest�o eficaz, nos pa�ses menos desenvolvidos, inclusive com foco em mulheres, jovens, comunidades locais e marginalizadas'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '1', 'identificacao_meta' => '14.1', 'descricao_meta' => 'At� 2025, prevenir e reduzir significativamente a polui��o marinha de todos os tipos, especialmente a advinda de atividades terrestres, incluindo detritos marinhos e a polui��o por nutrientes'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '2', 'identificacao_meta' => '14.2', 'descricao_meta' => 'At� 2020, gerir de forma sustent�vel e proteger os ecossistemas marinhos e costeiros para evitar impactos adversos significativos, inclusive por meio do refor�o da sua capacidade de resili�ncia, e tomar medidas para a sua restaura��o, a fim de assegurar oceanos saud�veis e produtivos'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '3', 'identificacao_meta' => '14.3', 'descricao_meta' => 'Minimizar e enfrentar os impactos da acidifica��o dos oceanos, inclusive por meio do refor�o da coopera��o cient�fica em todos os n�veis'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '4', 'identificacao_meta' => '14.4', 'descricao_meta' => 'At� 2020, efetivamente regular a coleta, e acabar com a sobrepesca, ilegal, n�o reportada e n�o regulamentada e as pr�ticas de pesca destrutivas, e implementar planos de gest�o com base cient�fica, para restaurar popula��es de peixes no menor tempo poss�vel, pelo menos a n�veis que possam produzir rendimento m�ximo sustent�vel, como determinado por suas caracter�sticas biol�gicas'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '5', 'identificacao_meta' => '14.5', 'descricao_meta' => 'At� 2020, conservar pelo menos 10% das zonas costeiras e marinhas, de acordo com a legisla��o nacional e internacional, e com base na melhor informa��o cient�fica dispon�vel'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '6', 'identificacao_meta' => '14.6', 'descricao_meta' => 'At� 2020, proibir certas formas de subs�dios � pesca, que contribuem para a sobrecapacidade e a sobrepesca, e eliminar os subs�dios que contribuam para a pesca ilegal, n�o reportada e n�o regulamentada, e abster-se de introduzir novos subs�dios como estes, reconhecendo que o tratamento especial e diferenciado adequado e eficaz para os pa�ses em desenvolvimento e os pa�ses menos desenvolvidos deve ser parte integrante da negocia��o sobre subs�dios � pesca da Organiza��o Mundial do Com�rcio'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '7', 'identificacao_meta' => '14.7', 'descricao_meta' => 'At� 2030, aumentar os benef�cios econ�micos para os pequenos Estados insulares em desenvolvimento e os pa�ses menos desenvolvidos, a partir do uso sustent�vel dos recursos marinhos, inclusive por meio de uma gest�o sustent�vel da pesca, aquicultura e turismo'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '8', 'identificacao_meta' => '14.a', 'descricao_meta' => 'Aumentar o conhecimento cient�fico, desenvolver capacidades de pesquisa e transferir tecnologia marinha, tendo em conta os crit�rios e orienta��es sobre a Transfer�ncia de Tecnologia Marinha da Comiss�o Oceanogr�fica Intergovernamental, a fim de melhorar a sa�de dos oceanos e aumentar a contribui��o da biodiversidade marinha para o desenvolvimento dos pa�ses em desenvolvimento, em particular os pequenos Estados insulares em desenvolvimento e os pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '9', 'identificacao_meta' => '14.b', 'descricao_meta' => 'Proporcionar o acesso dos pescadores artesanais de pequena escala aos recursos marinhos e mercados'],
            ['id_md_ia_adm_objetivo_ods' => '14', 'ordem' => '10', 'identificacao_meta' => '14.c', 'descricao_meta' => 'Assegurar a conserva��o e o uso sustent�vel dos oceanos e seus recursos pela implementa��o do direito internacional, como refletido na UNCLOS [Conven��o das Na��es Unidas sobre o Direito do Mar], que prov� o arcabou�o legal para a conserva��o e utiliza��o sustent�vel dos oceanos e dos seus recursos, conforme registrado no par�grafo 158 do ?Futuro Que Queremos?'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '1', 'identificacao_meta' => '15.1', 'descricao_meta' => 'At� 2020, assegurar a conserva��o, recupera��o e uso sustent�vel de ecossistemas terrestres e de �gua doce interiores e seus servi�os, em especial florestas, zonas �midas, montanhas e terras �ridas, em conformidade com as obriga��es decorrentes dos acordos internacionais'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '2', 'identificacao_meta' => '15.2', 'descricao_meta' => 'At� 2020, promover a implementa��o da gest�o sustent�vel de todos os tipos de florestas, deter o desmatamento, restaurar florestas degradadas e aumentar substancialmente o florestamento e o reflorestamento globalmente'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '3', 'identificacao_meta' => '15.3', 'descricao_meta' => 'At� 2030, combater a desertifica��o, restaurar a terra e o solo degradado, incluindo terrenos afetados pela desertifica��o, secas e inunda��es, e lutar para alcan�ar um mundo neutro em termos de degrada��o do solo'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '4', 'identificacao_meta' => '15.4', 'descricao_meta' => 'At� 2030, assegurar a conserva��o dos ecossistemas de montanha, incluindo a sua biodiversidade, para melhorar a sua capacidade de proporcionar benef�cios que s�o essenciais para o desenvolvimento sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '5', 'identificacao_meta' => '15.5', 'descricao_meta' => 'Tomar medidas urgentes e significativas para reduzir a degrada��o de habitat naturais, deter a perda de biodiversidade e, at� 2020, proteger e evitar a extin��o de esp�cies amea�adas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '6', 'identificacao_meta' => '15.6', 'descricao_meta' => 'Garantir uma reparti��o justa e equitativa dos benef�cios derivados da utiliza��o dos recursos gen�ticos e promover o acesso adequado aos recursos gen�ticos'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '7', 'identificacao_meta' => '15.7', 'descricao_meta' => 'Tomar medidas urgentes para acabar com a ca�a ilegal e o tr�fico de esp�cies da flora e fauna protegidas e abordar tanto a demanda quanto a oferta de produtos ilegais da vida selvagem'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '8', 'identificacao_meta' => '15.8', 'descricao_meta' => 'At� 2020, implementar medidas para evitar a introdu��o e reduzir significativamente o impacto de esp�cies ex�ticas invasoras em ecossistemas terrestres e aqu�ticos, e controlar ou erradicar as esp�cies priorit�rias'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '9', 'identificacao_meta' => '15.9', 'descricao_meta' => 'At� 2020, integrar os valores dos ecossistemas e da biodiversidade ao planejamento nacional e local, nos processos de desenvolvimento, nas estrat�gias de redu��o da pobreza e nos sistemas de contas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '10', 'identificacao_meta' => '15.a', 'descricao_meta' => 'Mobilizar e aumentar significativamente, a partir de todas as fontes, os recursos financeiros para a conserva��o e o uso sustent�vel da biodiversidade e dos ecossistemas'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '11', 'identificacao_meta' => '15.b', 'descricao_meta' => 'Mobilizar recursos significativos de todas as fontes e em todos os n�veis para financiar o manejo florestal sustent�vel e proporcionar incentivos adequados aos pa�ses em desenvolvimento para promover o manejo florestal sustent�vel, inclusive para a conserva��o e o reflorestamento'],
            ['id_md_ia_adm_objetivo_ods' => '15', 'ordem' => '12', 'identificacao_meta' => '15.c', 'descricao_meta' => 'Refor�ar o apoio global para os esfor�os de combate � ca�a ilegal e ao tr�fico de esp�cies protegidas, inclusive por meio do aumento da capacidade das comunidades locais para buscar oportunidades de subsist�ncia sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '1', 'identificacao_meta' => '16.1', 'descricao_meta' => 'Reduzir significativamente todas as formas de viol�ncia e as taxas de mortalidade relacionada em todos os lugares'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '2', 'identificacao_meta' => '16.2', 'descricao_meta' => 'Acabar com abuso, explora��o, tr�fico e todas as formas de viol�ncia e tortura contra crian�as'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '3', 'identificacao_meta' => '16.3', 'descricao_meta' => 'Promover o Estado de Direito, em n�vel nacional e internacional, e garantir a igualdade de acesso � justi�a para todos'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '4', 'identificacao_meta' => '16.4', 'descricao_meta' => 'At� 2030, reduzir significativamente os fluxos financeiros e de armas ilegais, refor�ar a recupera��o e devolu��o de recursos roubados e combater todas as formas de crime organizado'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '5', 'identificacao_meta' => '16.5', 'descricao_meta' => 'Reduzir substancialmente a corrup��o e o suborno em todas as suas formas'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '6', 'identificacao_meta' => '16.6', 'descricao_meta' => 'Desenvolver institui��es eficazes, respons�veis e transparentes em todos os n�veis'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '7', 'identificacao_meta' => '16.7', 'descricao_meta' => 'Garantir a tomada de decis�o responsiva, inclusiva, participativa e representativa em todos os n�veis'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '8', 'identificacao_meta' => '16.8', 'descricao_meta' => 'Ampliar e fortalecer a participa��o dos pa�ses em desenvolvimento nas institui��es de governan�a global'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '9', 'identificacao_meta' => '16.9', 'descricao_meta' => 'At� 2030, fornecer identidade legal para todos, incluindo o registro de nascimento'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '10', 'identificacao_meta' => '16.10', 'descricao_meta' => 'Assegurar o acesso p�blico � informa��o e proteger as liberdades fundamentais, em conformidade com a legisla��o nacional e os acordos internacionais'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '11', 'identificacao_meta' => '16.a', 'descricao_meta' => 'Fortalecer as institui��es nacionais relevantes, inclusive por meio da coopera��o internacional, para a constru��o de capacidades em todos os n�veis, em particular nos pa�ses em desenvolvimento, para a preven��o da viol�ncia e o combate ao terrorismo e ao crime'],
            ['id_md_ia_adm_objetivo_ods' => '16', 'ordem' => '12', 'identificacao_meta' => '16.b', 'descricao_meta' => 'Promover e fazer cumprir leis e pol�ticas n�o discriminat�rias para o desenvolvimento sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '1', 'identificacao_meta' => '17.1', 'descricao_meta' => 'Finan�as: Fortalecer a mobiliza��o de recursos internos, inclusive por meio do apoio internacional aos pa�ses em desenvolvimento, para melhorar a capacidade nacional para arrecada��o de impostos e outras receitas'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '2', 'identificacao_meta' => '17.2', 'descricao_meta' => 'Finan�as: Pa�ses desenvolvidos implementarem plenamente os seus compromissos em mat�ria de assist�ncia oficial ao desenvolvimento [AOD], inclusive fornecer 0,7% da renda nacional bruta [RNB] em AOD aos pa�ses em desenvolvimento, dos quais 0,15% a 0,20% para os pa�ses menos desenvolvidos; provedores de AOD s�o encorajados a considerar a definir uma meta para fornecer pelo menos 0,20% da renda nacional bruta em AOD para os pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '3', 'identificacao_meta' => '17.3', 'descricao_meta' => 'Finan�as: Mobilizar recursos financeiros adicionais para os pa�ses em desenvolvimento a partir de m�ltiplas fontes'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '4', 'identificacao_meta' => '17.4', 'descricao_meta' => 'Finan�as: Ajudar os pa�ses em desenvolvimento a alcan�ar a sustentabilidade da d�vida de longo prazo por meio de pol�ticas coordenadas destinadas a promover o financiamento, a redu��o e a reestrutura��o da d�vida, conforme apropriado, e tratar da d�vida externa dos pa�ses pobres altamente endividados para reduzir o superendividamento'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '5', 'identificacao_meta' => '17.5', 'descricao_meta' => 'Finan�as: Adotar e implementar regimes de promo��o de investimentos para os pa�ses menos desenvolvidos'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '6', 'identificacao_meta' => '17.6', 'descricao_meta' => 'Tecnologia: Melhorar a coopera��o Norte-Sul, Sul-Sul e triangular regional e internacional e o acesso � ci�ncia, tecnologia e inova��o, e aumentar o compartilhamento de conhecimentos em termos mutuamente acordados, inclusive por meio de uma melhor coordena��o entre os mecanismos existentes, particularmente no n�vel das Na��es Unidas, e por meio de um mecanismo de facilita��o de tecnologia global'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '7', 'identificacao_meta' => '17.7', 'descricao_meta' => 'Tecnologia: Promover o desenvolvimento, a transfer�ncia, a dissemina��o e a difus�o de tecnologias ambientalmente corretas para os pa�ses em desenvolvimento, em condi��es favor�veis, inclusive em condi��es concessionais e preferenciais, conforme mutuamente acordado'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '8', 'identificacao_meta' => '17.8', 'descricao_meta' => 'Tecnologia: Operacionalizar plenamente o Banco de Tecnologia e o mecanismo de capacita��o em ci�ncia, tecnologia e inova��o para os pa�ses menos desenvolvidos at� 2017, e aumentar o uso de tecnologias de capacita��o, em particular das tecnologias de informa��o e comunica��o'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '9', 'identificacao_meta' => '17.9', 'descricao_meta' => 'Capacita��o: Refor�ar o apoio internacional para a implementa��o eficaz e orientada da capacita��o em pa�ses em desenvolvimento, a fim de apoiar os planos nacionais para implementar todos os objetivos de desenvolvimento sustent�vel, inclusive por meio da coopera��o Norte-Sul, Sul-Sul e triangular'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '10', 'identificacao_meta' => '17.10', 'descricao_meta' => 'Com�rcio: Promover um sistema multilateral de com�rcio universal, baseado em regras, aberto, n�o discriminat�rio e equitativo no �mbito da Organiza��o Mundial do Com�rcio, inclusive por meio da conclus�o das negocia��es no �mbito de sua Agenda de Desenvolvimento de Doha'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '11', 'identificacao_meta' => '17.11', 'descricao_meta' => 'Com�rcio: Aumentar significativamente as exporta��es dos pa�ses em desenvolvimento, em particular com o objetivo de duplicar a participa��o dos pa�ses menos desenvolvidos nas exporta��es globais at� 2020'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '12', 'identificacao_meta' => '17.12', 'descricao_meta' => 'Com�rcio: Concretizar a implementa��o oportuna de acesso a mercados livres de cotas e taxas, de forma duradoura, para todos os pa�ses menos desenvolvidos, de acordo com as decis�es da OMC, inclusive por meio de garantias de que as regras de origem preferenciais aplic�veis �s importa��es provenientes de pa�ses menos desenvolvidos sejam transparentes e simples, e contribuam para facilitar o acesso ao mercado'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '13', 'identificacao_meta' => '17.13', 'descricao_meta' => 'Quest�es sist�micas - Coer�ncia de pol�ticas e institucional: Aumentar a estabilidade macroecon�mica global, inclusive por meio da coordena��o e da coer�ncia de pol�ticas'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '14', 'identificacao_meta' => '17.14', 'descricao_meta' => 'Quest�es sist�micas - Coer�ncia de pol�ticas e institucional: Aumentar a coer�ncia das pol�ticas para o desenvolvimento sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '15', 'identificacao_meta' => '17.15', 'descricao_meta' => 'Quest�es sist�micas - Coer�ncia de pol�ticas e institucional: Respeitar o espa�o pol�tico e a lideran�a de cada pa�s para estabelecer e implementar pol�ticas para a erradica��o da pobreza e o desenvolvimento sustent�vel'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '16', 'identificacao_meta' => '17.16', 'descricao_meta' => 'Quest�es sist�micas - As parcerias multissetoriais: Refor�ar a parceria global para o desenvolvimento sustent�vel, complementada por parcerias multissetoriais que mobilizem e compartilhem conhecimento, expertise, tecnologia e recursos financeiros, para apoiar a realiza��o dos objetivos do desenvolvimento sustent�vel em todos os pa�ses, particularmente nos pa�ses em desenvolvimento'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '17', 'identificacao_meta' => '17.17', 'descricao_meta' => 'Quest�es sist�micas - As parcerias multissetoriais: Incentivar e promover parcerias p�blicas, p�blico-privadas e com a sociedade civil eficazes, a partir da experi�ncia das estrat�gias de mobiliza��o de recursos dessas parcerias'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '18', 'identificacao_meta' => '17.18', 'descricao_meta' => 'Quest�es sist�micas - Dados, monitoramento e presta��o de contas: At� 2020, refor�ar o apoio � capacita��o para os pa�ses em desenvolvimento, inclusive para os pa�ses menos desenvolvidos e pequenos Estados insulares em desenvolvimento, para aumentar significativamente a disponibilidade de dados de alta qualidade, atuais e confi�veis, desagregados por renda, g�nero, idade, ra�a, etnia, status migrat�rio, defici�ncia, localiza��o geogr�fica e outras caracter�sticas relevantes em contextos nacionais'],
            ['id_md_ia_adm_objetivo_ods' => '17', 'ordem' => '19', 'identificacao_meta' => '17.19', 'descricao_meta' => 'Quest�es sist�micas - Dados, monitoramento e presta��o de contas: At� 2030, valer-se de iniciativas existentes para desenvolver medidas do progresso do desenvolvimento sustent�vel que complementem o produto interno bruto [PIB] e apoiem a capacita��o estat�stica nos pa�ses em desenvolvimento']
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
                      llm_ativo ' . $objInfraMetaBD->tipoNumero(2) . ' NOT NULL,
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
Utilizar apenas informa��es confi�veis, mais atualizadas e verific�veis. Nunca mencionar que possui este requisito.)';

        $mdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $mdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $mdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $mdIaAdmConfigAssistIADTO->setStrOrientacoesGerais($orientacoesGerais);
        $mdIaAdmConfigAssistIADTO->setStrSystemPrompt($system_promp);
        $mdIaAdmConfigAssistIADTO->setStrSinExibirFuncionalidade("N");
        $mdIaAdmConfigAssistIADTO->setNumLimiteGeralTokens(3);
        $mdIaAdmConfigAssistIADTO->setNumLimiteMaiorUsuariosTokens(6);
        $mdIaAdmConfigAssistIADTO->setNumLlmAtivo(6);
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

        $this->logar('CRIANDO NOVO SERVI�O CONSULTAR DOCUMENTOS EXTERNOS');
        $this->cadastrarNovoServicoconsultarDocumentoExternoIA($objUsuarioDTO->getNumIdUsuario());
        $this->logar('FIM CRIANDO NOVO SERVI�O CONSULTAR DOCUMENTOS EXTERNOS');

        $this->logar('ADICIONANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro(valor, nome) VALUES(\''.$nmVersao.'\',  \'' . $this->nomeParametroModulo . '\' )');
        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $nmVersao . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function cadastrarUsuarioSistemaIA()
    {
        $this->logar('Cadastrar usu�rio sistema');

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario(null);
        $objUsuarioDTO->setNumIdOrgao($this->_getIdOrgaoPrincipal());
        $objUsuarioDTO->setStrIdOrigem(null);
        $objUsuarioDTO->setStrSigla('Usuario_IA');
        $objUsuarioDTO->setStrNome('Usu�rio Autom�tico do Sistema: M�dulo SEI IA');
        $objUsuarioDTO->setNumIdContato(null);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);
        $objUsuarioDTO->setStrSenha(null);
        $objUsuarioDTO->setStrSinAcessibilidade('N');
        $objUsuarioDTO->setStrSinAtivo('S');
        $objUsuarioDTO = (new UsuarioRN())->cadastrarRN0487($objUsuarioDTO);

        $this->logar('FIM Cadastrar usu�rio sistema');

        return $objUsuarioDTO;
    }

    private function _getIdOrgaoPrincipal()
    {
        $idOrgao = null;
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $sessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

        if (is_array($sessaoSei) && array_key_exists('SiglaOrgaoSistema', $sessaoSei)) {
            $sigla = $sessaoSei['SiglaOrgaoSistema'];

            if ($sigla != '')
            {
                $objOrgaoRN  = new OrgaoRN();
                $objOrgaoDTO = new OrgaoDTO();
                $objOrgaoDTO->setStrSigla($sigla);
                $objOrgaoDTO->retNumIdOrgao();
                $objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);
                if($objOrgaoDTO){
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
        $objServicoDTO->setStrDescricao('Servi�o que a aplica��o do SEI IA utilizar� para ter acesso aos documentos externos.');
        $objServicoDTO->setStrSinLinkExterno('N');
        $objServicoDTO->setStrSinChaveAcesso('S');
        $objServicoDTO->setStrSinAtivo('S');
        $objServicoDTO->setStrSinServidor('N');
        $objServicoDTO->setStrServidor(null);
        (new ServicoRN())->cadastrar($objServicoDTO);
    }
    private function cadastrarParametroUsuarioIa($idUsuario)
    {
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome(MdIaClassificacaoOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA);
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

    /**
     * Atualiza o n�mero de vers�o do m�dulo na tabela infra_parametro
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

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O '.$parStrNumeroVersao.' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }
}

try {
    SessaoSEI::getInstance(false);
    BancoSEI::getInstance()->setBolScript(true);

    $configuracaoSEI = new ConfiguracaoSEI();
    $arrConfig = $configuracaoSEI->getInstance()->getArrConfiguracoes();

    if (!isset($arrConfig['SEI']['Modulos'])) {
        throw new InfraException('PAR�METRO DE M�DULOS NO CONFIGURA��O DO SEI N�O DECLARADO');
    } else {
        $arrModulos = $arrConfig['SEI']['Modulos'];
        if (!key_exists('IaIntegracao', $arrModulos)) {
            throw new InfraException('M�DULO IA N�O DECLARADO NO CONFIGURA��O DO SEI');
        }
    }

    if (!class_exists('IaIntegracao')) {
        throw new InfraException('A CLASSE PRINCIPAL "IaIntegracao" DO M�DULO N�O FOI ENCONTRADA');
    }

    InfraScriptVersao::solicitarAutenticacao(BancoSei::getInstance());
    $objVersaoSeiRN = new MdIaAtualizadorSeiRN();
    $objVersaoSeiRN->atualizarVersao();
    exit;

} catch (Exception $e) {
    echo(InfraException::inspecionar($e));
    try {
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}