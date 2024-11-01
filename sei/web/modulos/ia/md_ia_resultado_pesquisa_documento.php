<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
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

    $objProcedimentoRN = new ProcedimentoRN();

    $objMdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
    $objMdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc(1);
    $objMdIaAdmPesqDocDTO->retStrNomeSecao();
    $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
    $objMdIaAdmPesqDocDTO = $objMdIaAdmPesqDocRN->consultar($objMdIaAdmPesqDocDTO);

    $parametrosUrl = str_replace("*", "&", $_GET["parametrosUrl"]);

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
include_once('md_ia_recurso_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <div id="divTituloModal" class="infraBarraLocalizacao"><?= $objMdIaAdmPesqDocDTO->getStrNomeSecao() ?></div>
    <form id="frmMdIaPesquisaDocumento" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        //PaginaSEI::getInstance()->montarAreaValidacao();
        ?>
        <div id="divMsg" style="display:none;">
            <div class="alert" role="alert">
                <label></label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-justify">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" id="resultado_processamento">
                        <div class="d-flex justify-content-center align-items-center">
                            <img src="../../../infra_css/svg/aguarde.svg" alt="" style="d-inline-block">
                            <span>Pesquisando...</span>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <?
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
    </div>
<?
require_once "md_ia_resultado_pesquisa_documento_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
