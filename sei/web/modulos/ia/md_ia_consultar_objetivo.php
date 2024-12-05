<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 27/12/2023 - criado por sabino.colab
 *
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

    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';
    $arrComandos = array();

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
include_once('md_ia_consultar_objetivo_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdIaClassificacaoOds" method="post">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('auto');
    ?>

    <div id="divTituloModal" class="infraBarraLocalizacao">
        <div class="row">
            <div class="col-6">
                <h5 class="font-weight-bold">Metas</h5>
            </div>
            <div class="col-6 text-right botoes">
                <button type="button" accesskey="S" name="btnSalvar" id="btnSalvar" value="Salvar"  onclick="salvarConfiguracaoMetas()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>
                <button type="button" accesskey="C" name="btnFechar" id="btnFechar"   onclick="fecharModal()" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>
            </div>
        </div>
    </div>
    <div id="divMsg" style="display: none">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row" id="conteudoObjetivo"></div>
<?
require_once "md_ia_consultar_objetivo_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
