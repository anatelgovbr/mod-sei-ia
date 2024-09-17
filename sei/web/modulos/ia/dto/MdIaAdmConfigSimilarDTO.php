<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmConfigSimilarDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_config_similar';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmConfigSimilar', 'id_md_ia_adm_config_similar');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'QtdProcessListagem', 'qtd_process_listagem');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OrientacoesGerais', 'orientacoes_gerais');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'PercRelevContDoc', 'perc_relev_cont_doc');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'PercRelevMetadados', 'perc_relev_metadados');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExibirFuncionalidade', 'sin_exibir_funcionalidade');

        $this->configurarPK('IdMdIaAdmConfigSimilar', InfraDTO::$TIPO_PK_NATIVA);
    }
}
