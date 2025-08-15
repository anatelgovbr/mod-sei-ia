<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 07/11/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaPromptsFavoritosDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_prompts_favoritos';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaPromptsFavoritos', 'id_md_ia_prompts_favoritos');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaGrupoPromptsFav', 'id_md_ia_grupo_prompts_fav');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'DescricaoPrompt', 'descricao_prompt');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Prompt', 'prompt');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeGrupoFavorito', 'nome_grupo', 'md_ia_grupo_prompts_fav');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario', 'md_ia_grupo_prompts_fav');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade', 'md_ia_grupo_prompts_fav');

        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'PalavrasPesquisa');

        $this->configurarPK('IdMdIaPromptsFavoritos', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaGrupoPromptsFav', 'md_ia_grupo_prompts_fav', 'id_md_ia_grupo_prompts_fav');
    }
}
