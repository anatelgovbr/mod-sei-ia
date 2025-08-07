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
        case 'md_ia_modal_chats_arquivados':
            $strTitulo = 'Tópicos Arquivados';

            $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
            $objMdIaTopicoChatDTO->retNumIdMdIaTopicoChat();
            $objMdIaTopicoChatDTO->retStrNome();
            $objMdIaTopicoChatDTO->retDthCadastro();
            $objMdIaTopicoChatDTO->setStrSinAtivo("N");
            $objMdIaTopicoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
            $objMdIaTopicoChatDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objMdIaTopicoChatRN = new MdIaTopicoChatRN();
            $numRegistros = $objMdIaTopicoChatRN->contar($objMdIaTopicoChatDTO);

            $arrTopicosArquivados = $objMdIaTopicoChatRN->listar($objMdIaTopicoChatDTO);

            foreach ($arrTopicosArquivados as $topicoArquivado) {
                $tabelaTopicosArquivados .= "<tr>";
                $tabelaTopicosArquivados .= "<td class='text-left'>" . $topicoArquivado->getStrNome() . "</td>";
                $tabelaTopicosArquivados .= "<td class='text-center'>" . $topicoArquivado->getDthCadastro() . "</td>";
                $tabelaTopicosArquivados .= "<td class='text-center'>";
                $tabelaTopicosArquivados .= "<a class='arquivo' title='Desarquivar Tópico' onclick='desarquivarTopico(" . $topicoArquivado->getNumIdMdIaTopicoChat() . ")'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 512 512'><path d='M121 32C91.6 32 66 52 58.9 80.5L1.9 308.4C.6 313.5 0 318.7 0 323.9V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V323.9c0-5.2-.6-10.4-1.9-15.5l-57-227.9C446 52 420.4 32 391 32H121zm0 64H391l48 192H387.8c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7H195.8c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7H73L121 96z'/></svg></a>";
                $tabelaTopicosArquivados .= "</td>";
                $tabelaTopicosArquivados .= "</tr>";
            }
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
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo);

?>
<div id="conteudoModalChatsArquivados" style="margin-top: 35px;">
    <table class="infraTable" width="100%" aria-describedby="Tabela de Tópicos Arquivados">
        <thead>
            <tr>
                <th class="infraTh" width="70%">Nome</th>
                <th class="infraTh" width="20%">Data de Criação</th>
                <th class="infraTh" width="10%"></th>
            </tr>
        </thead>
        <tbody>
            <?= $tabelaTopicosArquivados ?>
        </tbody>
    </table>
</div>
<?
require_once "md_ia_modal_chats_arquivados_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>