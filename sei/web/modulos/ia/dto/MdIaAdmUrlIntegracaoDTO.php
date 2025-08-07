<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/05/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmUrlIntegracaoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_url_integracao';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmUrlIntegracao', 'id_adm_url_integracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdAdmIaAdmIntegracao', 'id_md_ia_adm_integracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Referencia', 'referencia');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Label', 'label');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Url', 'url');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmPesqDocMdIaAdmPesqDoc', 'id_md_ia_adm_pesq_doc', 'md_ia_adm_pesq_doc');

        $this->configurarPK('IdMdIaAdmUrlIntegracao', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdAdmIaAdmIntegracao', 'md_ia_adm_integracao', 'id_md_ia_adm_integracao');
    }
}

