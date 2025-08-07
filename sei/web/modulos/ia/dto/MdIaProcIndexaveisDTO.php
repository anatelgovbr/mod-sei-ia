<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/03/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaProcIndexaveisDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_proc_indexaveis';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProcedimento', 'id_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Hash', 'hash');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinIndexado', 'sin_indexado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Indexacao', 'dth_indexacao');

        $this->configurarPK('IdProcedimento', InfraDTO::$TIPO_PK_INFORMADO);

    }
}
