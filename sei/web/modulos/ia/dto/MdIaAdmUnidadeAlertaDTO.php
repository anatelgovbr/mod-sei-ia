<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmUnidadeAlertaDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_unidade_alerta';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmUnidadeAlerta', 'id_md_ia_adm_unidade_alerta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmOdsOnu', 'id_md_ia_adm_ods_onu');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidade', 'sigla', 'unidade');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoUnidade', 'descricao', 'unidade');

        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, "UnidadeAlerta");

        $this->configurarPK('IdMdIaAdmUnidadeAlerta', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmOdsOnu', 'md_ia_adm_ods_onu', 'id_md_ia_adm_ods_onu');
        $this->configurarFK('IdUnidade', 'unidade', 'id_unidade');
    }
}
