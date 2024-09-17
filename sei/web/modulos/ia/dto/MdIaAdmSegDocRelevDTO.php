<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.2
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

        $this->configurarPK('IdMdIaAdmSegDocRelev', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmDocRelev', 'md_ia_adm_doc_relev', 'id_md_ia_adm_doc_relev');
    }
}
