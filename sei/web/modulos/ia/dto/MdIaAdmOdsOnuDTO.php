<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmOdsOnuDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_ods_onu';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmOdsOnu', 'id_md_ia_adm_ods_onu');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExibirFuncionalidade', 'sin_exibir_funcionalidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinClassificacaoExterno', 'sin_classificacao_externo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExibirAvaliacao', 'sin_exibir_avaliacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OrientacoesGerais', 'orientacoes_gerais');

        $this->configurarPK('IdMdIaAdmOdsOnu', InfraDTO::$TIPO_PK_NATIVA);
    }
}
