<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 03/09/2024 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmIntegFuncionDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_integ_funcion';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmIntegFuncion', 'id_md_ia_adm_integ_funcion');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

        $this->configurarPK('IdMdIaAdmIntegFuncion', InfraDTO::$TIPO_PK_NATIVA);

    }
}
