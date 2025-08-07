<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmSegDocRelevDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_seg_doc_relev';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmSegDocRelev', 'id_md_ia_adm_seg_doc_relev');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmDocRelev', 'id_md_ia_adm_doc_relev');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SegmentoDocumento', 'segmento_documento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'PercentualRelevancia', 'percentual_relevancia');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdSerie', 'id_serie', 'md_ia_adm_doc_relev');

        $this->configurarPK('IdMdIaAdmSegDocRelev', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmDocRelev', 'md_ia_adm_doc_relev', 'id_md_ia_adm_doc_relev');
    }
}
