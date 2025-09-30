<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/05/2023 - criado por michaelr.colab
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

    //    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();

    $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
    $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
    $objMdIaAdmOdsOnuDTO->retNumIdMdIaAdmOdsOnu();
    $objMdIaAdmOdsOnuDTO->retStrOrientacoesGerais();
    $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
    $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

    if ($_POST['hdnIdProcedimento'] != "") {
        $idProcedimento = $_POST['hdnIdProcedimento'];
    } else {
        $idProcedimento = $_GET['id_procedimento'];
    }

    $objMdIaOdsOnuNsaDTO = new MdIaOdsOnuNsaDTO();
    $objMdIaOdsOnuNsaRN = new MdIaOdsOnuNsaRN();
    $objMdIaOdsOnuNsaDTO->setDblIdProcedimento($idProcedimento);
    $objMdIaOdsOnuNsaDTO->retDblIdProcedimento();
    $registro = $objMdIaOdsOnuNsaRN->consultar($objMdIaOdsOnuNsaDTO);

    if ($registro) {
        $checked = "checked";
    } else {
        $checked = "";
    }
    $mdIaAdmObjetivoOdsINT = new MdIaAdmObjetivoOdsINT();
    $arrIdsObjetivosForteRelacao = $mdIaAdmObjetivoOdsINT->arrIdsObjetivosForteRelacao();

    $dados['id_procedimento'] = $idProcedimento;
    $dados['filtrar_forte_relacao'] = true;
    $listaOdsOnu = MdIaClassMetaOdsINT::listaOdsOnu($dados);

    if ($listaOdsOnu["possuiClassificacaoQuente"]) {
        $display = "none !important";
    } else {
        $display = "flex !important";
    }

    switch ($_GET['acao']) {
        case 'md_ia_ods':
            $strTitulo = 'Classificação pelos ODS da ONU';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arvore_visualizar&acao_origem=' . $_GET['acao'] . '&arvore=1&id_protocolo=' . $_GET['id_procedimento']) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
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
include_once('md_ia_ods_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
?>
    <div id="divMsg" style="display: none">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-justify">
            <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                <legend class="infraLegend">Objetivos de Desenvolvimento Sustentável da ONU</legend>
                <?= $objMdIaAdmOdsOnuDTO->getStrOrientacoesGerais() ?>
                <!-- CONTROLES: radios (modo exclusivo) -->
                <div class="row" style="padding: 0px 20px">
                    <div class="col-12">
                        <div class="d-flex align-items-center" id="filtroNsa" style="display: <?= $display ?>;">
                            <h6 class="font-weight-bold d-flex align-items-center">
                                <label class="switch mr-3">
                                    <input id="btn-na-checkbox" type="checkbox" <?= $checked ?>>
                                    <span class="slider round"></span>
                                </label>
                                Não se aplica classificação por ODS da ONU
                            </h6>
                            <input type="hidden" id="na_flag" name="na_flag" value="0">
                        </div>

                        <div id="filterWrapper" class="mt-3">
                            <h6 class="font-weight-bold d-flex align-items-center">
                                <label class="switch mr-3">
                                    <input id="btn-checkbox" type="checkbox" checked onclick="atualizarListaObjetivos(this)">
                                    <span class="slider round"></span>
                                </label>
                                Exibir apenas os que possuem forte relação com o órgão
                            </h6>
                        </div>
                    </div>
                </div>

                <!-- GRID / Placeholder -->
                <div class="row">
                    <div class="col-12" id="objetivosOds">
                        <div class="container">
                            <div class="row" id="telaOdsOnu">
                                <?php
                                echo $listaOdsOnu["resultado"]
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="hdnIdSelecaoObjetivo" name="hdnIdSelecaoObjetivo" />
                <input type="hidden" id="hdntelaConsulta" name="hdntelaConsulta" value="false" />
                <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= $_GET['id_procedimento'] ?> " />
                <input type="hidden" id="arr-objetivos-forte-relacao" value="<?= implode(",", $arrIdsObjetivosForteRelacao) ?>">
            </fieldset>
            <br />
        </div>
    </div>
<?php

}

PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
?>
</form>
<?
require_once "md_ia_ods_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
