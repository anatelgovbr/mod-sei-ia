<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 17/01/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmConfigAssistIAINT extends InfraINT
{
    public static function autoCompletarUsuarios($numIdOrgao, $strPalavrasPesquisa, $bolOutros, $bolExternos, $bolSiglaNome, $bolInativos)
    {

        $objUsuarioDTO = new UsuarioDTO();

        if ($bolInativos) {
            $objUsuarioDTO->setBolExclusaoLogica(false);
        }

        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retStrNome();
        $objUsuarioDTO->setNumMaxRegistrosRetorno(50);
        $objUsuarioDTO->setStrPalavrasPesquisa($strPalavrasPesquisa);

        if (!InfraString::isBolVazia($numIdOrgao)) {
            $objUsuarioDTO->setNumIdOrgao($numIdOrgao);
        }

        if ($bolOutros) {
            $objUsuarioDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario(), InfraDTO::$OPER_DIFERENTE);
        }

        if (!$bolExternos) {
            $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SIP);
        } else {
            $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);
        }

        $objUsuarioDTO->setNumMaxRegistrosRetorno(50);

        $objUsuarioDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objUsuarioRN = new UsuarioRN();
        $arrObjUsuarioDTO = $objUsuarioRN->pesquisar($objUsuarioDTO);

        if ($bolSiglaNome) {
            foreach ($arrObjUsuarioDTO as $objUsuarioDTO) {
                $objUsuarioDTO->setStrSigla($objUsuarioDTO->getStrSigla() . ' - ' . $objUsuarioDTO->getStrNome());
            }
        }

        return $arrObjUsuarioDTO;
    }

    public static function habilitadoRefletir()
    {
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSinRefletir();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        if ($objMdIaAdmConfigAssistIADTO->getStrSinRefletir() != 'S') {
            return "display:none !important;";
        }
    }
    public static function habilitadoBuscarNaWeb()
    {
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSinBuscarWeb();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        if ($objMdIaAdmConfigAssistIADTO->getStrSinBuscarWeb() != 'S') {
            return "display:none !important;";
        }
    }
}
