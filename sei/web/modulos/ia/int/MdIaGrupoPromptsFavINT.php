<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaGrupoPromptsFavINT extends InfraINT
{

    public static function montarSelectGrupoPromptsFav($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $arrobjMdIaGrupoPromptsFavDTO = new MdIaGrupoPromptsFavDTO();
        $arrobjMdIaGrupoPromptsFavDTO->retNumIdMdIaGrupoPromptsFav();
        $arrobjMdIaGrupoPromptsFavDTO->retStrNomeGrupo();

        $arrobjMdIaGrupoPromptsFavDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $arrobjMdIaGrupoPromptsFavDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());

        $arrobjMdIaGrupoPromptsFavDTO->setOrdStrNomeGrupo(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
        $arrobjMdIaGrupoPromptsFavDTO = $objMdIaGrupoPromptsFavRN->listar($arrobjMdIaGrupoPromptsFavDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrobjMdIaGrupoPromptsFavDTO, 'IdMdIaGrupoPromptsFav', 'NomeGrupo');
    }

}
