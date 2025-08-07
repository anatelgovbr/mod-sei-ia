<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/05/2023 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmUrlIntegracaoINT extends InfraINT
{
    public static function atualizarCadastroUrls($dados)
    {
        $mdIaAdmUrlIntegracaoRN = new MdIaAdmUrlIntegracaoRN;
        $objMdIaAdmUrlIntegracaoDTO = new MdIaAdmUrlIntegracaoDTO();
        $objMdIaAdmUrlIntegracaoDTO->setNumIdAdmIaAdmIntegracao($dados["hdnIdMdIaAdmIntegracao"]);
        $objMdIaAdmUrlIntegracaoDTO->retNumIdMdIaAdmUrlIntegracao();
        $objMdIaAdmUrlIntegracaoDTO->retNumIdAdmIaAdmIntegracao();
        $objMdIaAdmUrlIntegracaoDTO->retStrReferencia();
        $objMdIaAdmUrlIntegracaoDTO->retStrLabel();
        $objMdIaAdmUrlIntegracaoDTO->retStrUrl();

        $arrObjMdIaAdmUrlIntegracaoDTO = $mdIaAdmUrlIntegracaoRN->listar($objMdIaAdmUrlIntegracaoDTO);

        foreach ($arrObjMdIaAdmUrlIntegracaoDTO as $objMdIaAdmUrlIntegracaoDTO) {
            if($dados[$objMdIaAdmUrlIntegracaoDTO->getStrReferencia()]) {
                $objMdIaAdmUrlIntegracaoDTO->setStrUrl($dados[$objMdIaAdmUrlIntegracaoDTO->getStrReferencia()]);
                $mdIaAdmUrlIntegracaoRN->alterar($objMdIaAdmUrlIntegracaoDTO);
            }
        }
    }
}
