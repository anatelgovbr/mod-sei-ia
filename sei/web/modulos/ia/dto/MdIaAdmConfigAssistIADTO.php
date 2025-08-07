<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmConfigAssistIADTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_config_assist_ia';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmConfigAssistIA', 'id_md_ia_adm_conf_assist_ia');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OrientacoesGerais', 'orientacoes_gerais');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExibirFuncionalidade', 'sin_exibir_funcionalidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SystemPrompt', 'system_prompt');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'LimiteGeralTokens', 'limite_geral_tokens');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'LimiteMaiorUsuariosTokens', 'limite_maior_usuarios_tokens');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinRefletir', 'sin_refletir');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinBuscarWeb', 'sin_buscar_web');

        $this->configurarPK('IdMdIaAdmConfigAssistIA', InfraDTO::$TIPO_PK_NATIVA);
    }
}
