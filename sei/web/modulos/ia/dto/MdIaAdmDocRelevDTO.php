<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmDocRelevDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_doc_relev';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmDocRelev', 'id_md_ia_adm_doc_relev');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdSerie', 'id_serie');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Aplicabilidade', 'aplicabilidade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->configurarPK('IdMdIaAdmDocRelev', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdSerie', 'serie', 'id_serie', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento', 'id_tipo_procedimento', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie','nome','serie');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento', 'nome', 'tipo_procedimento');
    }
}
