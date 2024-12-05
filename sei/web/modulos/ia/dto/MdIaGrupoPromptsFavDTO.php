<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaGrupoPromptsFavDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_grupo_prompts_fav';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaGrupoPromptsFav', 'id_md_ia_grupo_prompts_fav');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeGrupo', 'nome_grupo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

        $this->configurarPK('IdMdIaGrupoPromptsFav', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdUsuario', 'usuario', 'id_usuario', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->configurarFK('IdUnidade', 'unidade', 'id_unidade', InfraDTO::$TIPO_FK_OPCIONAL);

    }
}
