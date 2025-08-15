<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaPromptsFavoritosINT extends InfraINT
{

    public static function consultarPromptFavorito($dados) {
        $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();
        $objMdIaPromptsFavoritosDTO->retNumIdMdIaPromptsFavoritos();
        $objMdIaPromptsFavoritosDTO->retNumIdMdIaGrupoPromptsFav();
        $objMdIaPromptsFavoritosDTO->retStrDescricaoPrompt();
        $objMdIaPromptsFavoritosDTO->retStrPrompt();
        $objMdIaPromptsFavoritosDTO->setNumIdMdIaPromptsFavoritos($dados['IdMdIaPromptsFavoritos']);

        $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
        $prompt = $objMdIaPromptsFavoritosRN->consultar($objMdIaPromptsFavoritosDTO);

        if (!is_null($prompt)) {
            return array(
                "result" => "true",
                "prompt" => mb_convert_encoding($prompt->getStrPrompt(), 'UTF-8', 'ISO-8859-1'),
                "descricao_prompt" => mb_convert_encoding($prompt->getStrDescricaoPrompt(), 'UTF-8', 'ISO-8859-1'),
                "id_grupo_favorito" => $prompt->getNumIdMdIaGrupoPromptsFav(),
                "id_prompt_favorito" => $prompt->getNumIdMdIaPromptsFavoritos()
            );
        } else {
            return array("result" => "false");
        }
    }   
}
