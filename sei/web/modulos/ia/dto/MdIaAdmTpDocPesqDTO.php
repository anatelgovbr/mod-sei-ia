<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmTpDocPesqDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_tp_doc_pesq';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmTpDocPesq', 'id_md_ia_adm_tp_doc_pesq');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmPesqDoc', 'id_md_ia_adm_pesq_doc');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdSerie', 'id_serie');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmPesqDocMdIaAdmPesqDoc', 'id_md_ia_adm_pesq_doc', 'md_ia_adm_pesq_doc');

        $this->configurarPK('IdMdIaAdmTpDocPesq', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmPesqDoc', 'md_ia_adm_pesq_doc', 'id_md_ia_adm_pesq_doc');

        $this->configurarFK('IdSerie', 'serie', 'id_serie', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie','nome','serie');

    }
}
