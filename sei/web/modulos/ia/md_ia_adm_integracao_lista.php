<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    #PaginaSEI::getInstance()->prepararSelecao('md_ia_adm_integracao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    //Links dos botos
    $btnLinkNovo = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_integracao_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_ia_adm_integracao_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Integração', 'Selecionar Integrações');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_ia_adm_integracao_cadastrar') {
                if (isset($_GET['id_md_ia_adm_integracao'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_ia_adm_integracao']);
                }
            }
            break;

        case 'md_ia_adm_integracao_listar':
            $strTitulo = 'Mapeamento de Integrações';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    if ($_GET['acao'] == 'md_ia_adm_integracao_listar' || $_GET['acao'] == 'md_ia_adm_integracao_selecionar') {
        $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_integracao_cadastrar');
        if ($bolAcaoCadastrar) {
            $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="acionarNovo()" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
        }
    }

    $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
    $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegracao();
    $objMdIaAdmIntegracaoDTO->retStrNome();
    $objMdIaAdmIntegracaoDTO->retStrNomeFuncionalidade();
    $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegFuncion();
    $objMdIaAdmIntegracaoDTO->retStrSinAtivo();

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaAdmIntegracaoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdIaAdmIntegracaoDTO);

    $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
    $arrObjMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->listar($objMdIaAdmIntegracaoDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdIaAdmIntegracaoDTO);

    /** @var MdIaAdmIntegracaoDTO[] $arrObjMdIaAdmIntegracaoDTO */

    $numRegistros = count($arrObjMdIaAdmIntegracaoDTO);
    $strResultado = '';

    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'md_ia_adm_integracao_selecionar') {
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_integracao_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_integracao_alterar');
            $bolAcaoImprimir = false;
            $bolCheck = true;
        } else {
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_integracao_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_integracao_alterar');
            $bolCheck = true;
        }

        $strSumarioTabela = 'Tabela de Integrações.';
        $strCaptionTabela = 'Integrações';

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmIntegracaoDTO, 'Nome', 'Nome', $arrObjMdIaAdmIntegracaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmIntegracaoDTO, 'Funcionalidade', 'Funcionalidade', $arrObjMdIaAdmIntegracaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {
            if ($arrObjMdIaAdmIntegracaoDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaAdmIntegracaoDTO[$i]->getNumIdMdIaAdmIntegracao(), $arrObjMdIaAdmIntegracaoDTO[$i]->getStrNome()) . '</td>';
            }
            $strResultado .= '<td class="txt-col-center">' . PaginaSEI::tratarHTML($arrObjMdIaAdmIntegracaoDTO[$i]->getStrNome()) . '</td>';
            $strResultado .= '<td class="txt-col-center">' . PaginaSEI::tratarHTML($arrObjMdIaAdmIntegracaoDTO[$i]->getStrNomeFuncionalidade()) . '</td>';
            $strResultado .= '<td align="right">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdIaAdmIntegracaoDTO[$i]->getNumIdMdIaAdmIntegracao());

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_integracao_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_ia_adm_integracao=' . $arrObjMdIaAdmIntegracaoDTO[$i]->getNumIdMdIaAdmIntegracao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeConsultar() . '" title="Consultar Integração" alt="Consultar Integração" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_integracao_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_ia_adm_integracao=' . $arrObjMdIaAdmIntegracaoDTO[$i]->getNumIdMdIaAdmIntegracao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Integração" alt="Alterar Integração" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if ($_GET['acao'] == 'md_ia_adm_integracao_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
    }
    $funcionalidadesDisponiveis = MdIaAdmIntegracaoINT::montarSelectFuncionalidade('null', '&nbsp;', '');;
    if (empty($funcionalidadesDisponiveis)) {
        $strBloquearNovoCadastro = true;
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
//require 'md_ia_geral_css.php';
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdIaAdmIntegracaoLista" method="post"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados();
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>

<?
require_once "md_ia_adm_integracao_lista_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
