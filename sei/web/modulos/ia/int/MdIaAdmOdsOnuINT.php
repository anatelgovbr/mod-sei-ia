<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmOdsOnuINT extends InfraINT
{

    public static function montarSelectUnidadesAlerta()
    {
        $objMdIaAdmUnidadeAlertaRN = new MdIaAdmUnidadeAlertaRN();
        $objMdIaAdmUnidadeAlertaDTO= new MdIaAdmUnidadeAlertaDTO();

        $objMdIaAdmUnidadeAlertaDTO->retNumIdMdIaAdmUnidadeAlerta();
        $objMdIaAdmUnidadeAlertaDTO->retNumIdUnidade();
        $objMdIaAdmUnidadeAlertaDTO->retStrSiglaUnidade();
        $objMdIaAdmUnidadeAlertaDTO->retStrDescricaoUnidade();
        $objMdIaAdmUnidadeAlertaDTO->setNumIdMdIaAdmOdsOnu(1);

        $objRetorno = $objMdIaAdmUnidadeAlertaRN->listar($objMdIaAdmUnidadeAlertaDTO);

        foreach ($objRetorno as $item) {
            $strTextoUnidade = $item->getStrSiglaUnidade() . ' - ' . $item->getStrDescricaoUnidade();
            $item->setStrUnidadeAlerta($strTextoUnidade);
        }

        return parent::montarSelectArrInfraDTO(null, null, null, $objRetorno, 'IdUnidade', 'UnidadeAlerta');
    }

    public function getPeticionamentoMenorVersaoRequerida()
    {
        return '4.3.0';
    }
    public static function verificaSeModPeticionamentoVersaoMinima()
    {
        $bolVersaoValida = false;
        $arrModulos = ConfiguracaoSEI::getInstance()->getValor('SEI','Modulos');

        if(is_array($arrModulos) && array_key_exists('PeticionamentoIntegracao', $arrModulos)){
            $objInfraParametroDTO = new InfraParametroDTO();
            $objInfraParametroDTO->setStrNome('VERSAO_MODULO_PETICIONAMENTO');
            $objInfraParametroDTO->retStrValor();
            $objInfraParametroBD = new InfraParametroBD(BancoSEI::getInstance());
            $arrObjInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTO);
            $strVersaoInstalada = $arrObjInfraParametroDTO->getStrValor();
            if (version_compare($strVersaoInstalada, self::getPeticionamentoMenorVersaoRequerida()) >= 0) {
                $bolVersaoValida = true;
            }
        }
        return $bolVersaoValida;
    }

}
