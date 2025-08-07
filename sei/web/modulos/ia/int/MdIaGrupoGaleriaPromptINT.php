<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 18/03/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaGrupoGaleriaPromptINT extends InfraINT
{

    public static function montarSelectGrupoGaleriaPrompt($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $arrobjMdIaGrupoGaleriaPromptDTO = new MdIaGrupoGaleriaPromptDTO();
        $arrobjMdIaGrupoGaleriaPromptDTO->retNumIdMdIaGrupoGaleriaPrompt();
        $arrobjMdIaGrupoGaleriaPromptDTO->retStrNomeGrupo();

        $arrobjMdIaGrupoGaleriaPromptDTO->setOrdStrNomeGrupo(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
        $arrobjMdIaGrupoGaleriaPromptDTO = $objMdIaGrupoGaleriaPromptRN->listar($arrobjMdIaGrupoGaleriaPromptDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrobjMdIaGrupoGaleriaPromptDTO, 'IdMdIaGrupoGaleriaPrompt', 'NomeGrupo');
    }

    public static function montarSelectSinAtivo($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $arraySituacao = array("S" => "Ativos", "N" => "Inativos");
        return parent::montarSelectArray($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arraySituacao, true);
    }
}
