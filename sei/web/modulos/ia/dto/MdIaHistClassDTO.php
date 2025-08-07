<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/01/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaHistClassDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_hist_class';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaHistClass', 'id_md_ia_hist_class');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetaOds', 'id_md_ia_adm_meta_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Operacao', 'operacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Cadastro', 'dth_cadastro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinSugestaoAceita', 'sin_sugestao_aceita');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Racional', 'racional');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaTipoUsuario', 'sta_tipo_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProcedimento', 'id_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaHistClassSugest', 'id_md_ia_hist_class_sugest');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaTipoUsuario', 'sta_tipo', 'usuario');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeUsuario','nome','usuario');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'DescricaoMeta','descricao_meta','md_ia_adm_meta_ods');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'IdentificacaoMeta','identificacao_meta','md_ia_adm_meta_ods');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'SiglaUnidade','sigla','unidade');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'DescricaoUnidade','descricao','unidade');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'StaTipoUsuario','sta_tipo','usuario');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmObjetivoOds', 'id_md_ia_adm_objetivo_ods', 'md_ia_adm_meta_ods');

        $this->configurarPK('IdMdIaHistClass', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmMetaOds', 'md_ia_adm_meta_ods', 'id_md_ia_adm_meta_ods');

        $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');

        $this->configurarFK('IdUnidade', 'unidade', 'id_unidade', InfraDTO::$TIPO_FK_OPCIONAL);

    }
}
