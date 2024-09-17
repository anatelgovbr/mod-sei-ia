<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmPercRelevMetDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_perc_relev_met';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmPercRelevMet', 'id_md_ia_adm_perc_relev_met');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmConfigSimilar', 'id_md_ia_adm_config_similar');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetadado', 'id_md_ia_adm_metadado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'PercentualRelevancia', 'percentual_relevancia');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->configurarPK('IdMdIaAdmPercRelevMet', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmMetadado', 'md_ia_adm_metadado', 'id_md_ia_adm_metadado');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Metadado', 'metadado', 'md_ia_adm_metadado');
    }
}
