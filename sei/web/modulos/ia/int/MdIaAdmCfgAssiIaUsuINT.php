<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 27/03/2024 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmCfgAssiIaUsuINT extends InfraINT
{

    public static function montarSelectUsuarios()
    {
        $objMdIaAdmCfgAssiIaUsuRN = new MdIaAdmCfgAssiIaUsuRN();
        $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();

        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdUsuario();
        $objMdIaAdmCfgAssiIaUsuDTO->retStrNomeUsuario();
        $objMdIaAdmCfgAssiIaUsuDTO->retStrSiglaUsuario();
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdMdIaAdmConfigAssistIA(1);

        $objRetorno = $objMdIaAdmCfgAssiIaUsuRN->listar($objMdIaAdmCfgAssiIaUsuDTO);

        foreach ($objRetorno as $item) {
            $strTextoUnidade = $item->getStrSiglaUsuario() . ' - '.$item->getStrNomeUsuario();
            $item->setStrSiglaUsuario($strTextoUnidade);
        }

        return parent::montarSelectArrInfraDTO(null, null, null, $objRetorno, 'IdUsuario', 'SiglaUsuario');
    }
}
