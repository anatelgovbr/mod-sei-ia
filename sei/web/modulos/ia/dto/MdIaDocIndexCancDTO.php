<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaDocIndexCancDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_doc_index_canc';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'id_documento');

        $this->configurarPK('IdDocumento', InfraDTO::$TIPO_PK_INFORMADO);
    }
}
