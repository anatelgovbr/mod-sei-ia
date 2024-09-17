<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 14/03/2017 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.40.0
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
        case 'md_ia_modal_configuracoes_assistente_ia':
            $strTitulo = 'Configura��es do Assistente de IA';

            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
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

$strLinkChatsArquivados = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_modal_chats_arquivados');

?>
<div id="conteudoModalConfiguracoes" style="margin-top: 35px;">
    <div class="card">
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><div class="float-left" style="padding: 7px 0 0 0;"><h6 style=" margin: 0;">T�picos Arquivados</h6></div><div class="text-right float-right"><a onclick="infraAbrirJanelaModal('<?= $strLinkChatsArquivados ?> ', 1024, 800)" class="btn btn-sm btn-outline-secondary">Gerenciar</a></div></li>
        </ul>
    </div>
</div>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
