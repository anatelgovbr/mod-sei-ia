<?
    /**
     * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
     *
     * 29/12/2023 - criado por sabino.colab
     *
     * Versão do Gerador de Código: 1.43.3
     */

    require_once dirname(__FILE__) . '../../../../SEI.php';

    class MdIaClassMetaOdsDTO extends InfraDTO
    {

        public function getStrNomeTabela()
        {
            return 'md_ia_class_meta_ods';
        }

        public function montar()
        {

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaClassMetaOds', 'id_md_ia_class_meta_ods');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaClassificacaoOds', 'id_md_ia_classificacao_ods');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetaOds', 'id_md_ia_adm_meta_ods');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinSugestaoAceita', 'sin_sugestao_aceita');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Racional', 'racional');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

            $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Cadastro', 'dth_cadastro');

            $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmObjetivoOds', 'id_md_ia_adm_objetivo_ods', 'md_ia_adm_meta_ods');

            $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaTipoUsuario', 'sta_tipo', 'usuario');

            $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaTipoUltimoUsuario', 'sta_tipo_ultimo_usuario', 'md_ia_classificacao_ods');

            $this->configurarPK('IdMdIaClassMetaOds', InfraDTO::$TIPO_PK_NATIVA);

            $this->configurarFK('IdMdIaClassificacaoOds', 'md_ia_classificacao_ods', 'id_md_ia_classificacao_ods');

            $this->configurarFK('IdMdIaAdmMetaOds', 'md_ia_adm_meta_ods', 'id_md_ia_adm_meta_ods');

            $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');

            $this->configurarFK('IdUnidade', 'unidade', 'id_unidade');
        }
    }
