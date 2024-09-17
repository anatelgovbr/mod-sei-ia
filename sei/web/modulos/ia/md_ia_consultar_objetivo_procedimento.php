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

//    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

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

    <div id="divTituloModal" class="infraBarraLocalizacao mb-3">
        <div class="row">
            <div class="col-8">
                <h4 class="font-weight-bold">Metas dos Objetivos de Desenvolvimento Sustentável da ONU</h4>
            </div>
            <div class="col-4 text-right botoes" style="display: none">
                <button type="button" accesskey="H" name="btnHistorico" id="btnHistorico" value="Histórico" onclick="exibirHistorico()" class="infraButton"><span class="infraTeclaAtalho">H</span>istórico</button>
                <button type="button" accesskey="M" name="btnMetas" id="btnMetas" value="Métas" onclick="exibirMetas()" class="infraButton" style="display: none"><span class="infraTeclaAtalho">V</span>oltar</button>
                <button type="button" accesskey="S" name="btnSalvar" id="btnSalvar" value="Salvar"  onclick="salvarClassificacaoOds()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>
                <button type="button" accesskey="C" name="btnFechar" id="btnFechar"   onclick="fecharModal()" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>
            </div>
        </div>
    </div>
    <div id="divMsg" style="display: none">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row" id="conteudoObjetivo">
        <div class="col-12">
            <p>Carregando dados do objetivo...</p>
        </div>
    </div>
<?
require_once "md_ia_consultar_objetivo_procedimento_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
