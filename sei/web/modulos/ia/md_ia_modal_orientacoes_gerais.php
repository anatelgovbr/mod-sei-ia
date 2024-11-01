<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 03/09/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.40.0
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
//    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    //////////////////////////////////////////////////////////////////////////////
//    InfraDebug::getInstance()->setBolLigado(false);
//    InfraDebug::getInstance()->setBolDebugInfra(false);
//    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    $strParametros = '';
    if (isset($_GET['arvore'])) {
        PaginaSEI::getInstance()->setBolArvore($_GET['arvore']);
        $strParametros .= '&arvore=' . $_GET['arvore'];
    }

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    switch ($_GET['acao']) {
        case 'md_ia_modal_orientacoes_gerais':
            $strTitulo = 'Orientações Gerais sobre o Assistente';

           // Consulta as configurações da funcionalidade para saber se deve ou não exibir o chat
            $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
            $objMdIaAdmConfigAssistIADTO->retStrOrientacoesGerais();
            $objMdIaAdmConfigAssistIADTO->setNumMaxRegistrosRetorno(1);
            $objMdIaAdmConfigAssistIADTO = (new MdIaAdmConfigAssistIARN())->consultar($objMdIaAdmConfigAssistIADTO);
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}
PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();


PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();


PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo);

?>
<div id="conteudoModalOrientacoes" style="margin-top: 35px;">
    <? echo $objMdIaAdmConfigAssistIADTO->getStrOrientacoesGerais(); ?>
</div>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
