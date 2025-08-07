<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 03/06/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaDocIndexaveisDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_doc_indexaveis';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'id_documento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinIndexado', 'sin_indexado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Indexacao', 'dth_indexacao');

        $this->configurarPK('IdDocumento', InfraDTO::$TIPO_PK_INFORMADO);
    }
}
