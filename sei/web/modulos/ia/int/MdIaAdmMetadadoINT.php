<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 06/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmMetadadoINT extends InfraINT
{

    public static function montarSelectMdIaAdmMetadado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdIaAdmMetadadoDTO = new MdIaAdmMetadadoDTO();
        $objMdIaAdmMetadadoDTO->retNumIdMdIaAdmMetadado();
        $objMdIaAdmMetadadoDTO->retStrMetadado();
        $objMdIaAdmMetadadoDTO->setOrdNumIdMdIaAdmMetadado(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdIaAdmMetadadoRN = new MdIaAdmMetadadoRN();
        $arrObjMdIaAdmMetadadoDTO = $objMdIaAdmMetadadoRN->listar($objMdIaAdmMetadadoDTO);
        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdIaAdmMetadadoDTO, 'IdMdIaAdmMetadado', 'Metadado');
    }
}
