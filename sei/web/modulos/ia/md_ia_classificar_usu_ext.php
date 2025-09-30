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

    $strTitulo = 'Classificar Processo com os Objetivos de Desenvolvimento Sustentável da ONU';


    //TODO ORGANIZAR DEPOIS

    $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
    $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
    $objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
    $objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
    $objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();
    $arrObjMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->listar($objMdIaAdmObjetivoOdsDTO);

    $mdIaAdmObjetivoOdsINT = new MdIaAdmObjetivoOdsINT();
    $arrIdsObjetivosForteRelacao = $mdIaAdmObjetivoOdsINT->arrIdsObjetivosForteRelacao();
    $arrIdsMetasForteRelacao = $mdIaAdmObjetivoOdsINT->arrIdsMetasForteRelacao();

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
?>
<link rel="stylesheet" type="text/css" href="modulos/ia/css/md_ia_comum.css" />
<?php
PaginaSEI::getInstance()->abrirStyle();
include_once('md_ia_classificar_usu_ext_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('auto');
?>
<div class="row" style="margin-top:15px;">
    <div id="multi-step-form-container" class="col-6" style="margin: 0 auto">
        <ul class="form-stepper form-stepper-horizontal text-center mx-auto pl-0">
            <!-- Step 1 -->
            <li class="form-stepper-active text-center form-stepper-list" step="1" onclick="mudarStep(1)">
                <a class="mx-2">
                    <span class="form-stepper-circle">
                        <span>1</span>
                    </span>
                    <div class="label">Objetivos</div>
                </a>
            </li>
            <!-- Step 2 -->
            <li class="form-stepper-unfinished text-center form-stepper-list" step="2">
                <a class="mx-2">
                    <span class="form-stepper-circle text-muted">
                        <span>2</span>
                    </span>
                    <div class="label text-muted">Classificar Metas</div>
                </a>
            </li>
            <!-- Step 3 -->
            <li class="form-stepper-unfinished text-center form-stepper-list" step="3" onclick="mudarStep(3)">
                <a class="mx-2">
                    <span class="form-stepper-circle text-muted">
                        <span>3</span>
                    </span>
                    <div class="label text-muted">Concluir</div>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-12" style="padding-right: 70px; text-align:right">
        <button type="button" name="btnNovaClassificacao" id="btnNovaClassificacao" style="display: none" onclick="mudarStep(1)" class="infraButton">Classificar Novo Objetivo</button>
        <button type="button" name="btnProsseguir" id="btnProsseguir" style="display: none" onclick="mudarStep(3)" class="infraButton">Prosseguir</button>
        <button type="button" name="btnSalvar" id="btnSalvar" onclick="fecharModal()" class="infraButton">Continuar Peticionamento</button>
    </div>
</div>

<div style="padding: 30px; margin-top: 45px;">
    <!-- Step 1 Content -->
    <div id="step-1">
        <div class="row" style="margin-top: -70px; padding: 20px">
            <div class="col-12">
                <label style="font-size: medium">
                    Os Objetivos de Desenvolvimento Sustentável são um apelo global à ação para acabar com a pobreza, proteger o meio ambiente e o clima e garantir que as pessoas, em todos os lugares, possam desfrutar de paz e de prosperidade (https://brasil.un.org/pt-br/sdgs).</br></br>
                    Acessando os ícones abaixo é possível classificar o Processo com as Metas dos Objetivos de Desenvolvimento Sustentável da ONU.
                </label>
            </div>
        </div>
        <div class="row" style="padding: 20px">
            <div class="col-1">
                <label class="switch">
                    <input id="btn-checkbox" type="checkbox" checked onclick="atualizarListaObjetivos()">
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="col-11" style="margin-top: 7px;">
                <h6>
                    <strong>Exibir os Objetivos de Desenvolvimento Sustentável da ONU com Forte Relação Temática com o Órgão</strong>
                </h6>
            </div>
        </div>
        <div class="row" id="todos-objetivos" style="padding:20px;">
            <?php
            foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {
                $classe = "img-desfoque";
                $exibirObjetivo = '';
                if (!in_array($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(), $arrIdsObjetivosForteRelacao)) {
                    $exibirObjetivo = 'display:none';
                }

            ?>
                <div id="<?= $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(); ?>"
                    class=" col-2"
                    style="margin-bottom: 15px;<?= $exibirObjetivo ?>"
                    onclick="exibirMetas(<?= $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(); ?>)">
                    <a><img src="modulos/ia/imagens/Icones_Oficiais_ONU/<?= $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() ?>" alt="Objetivo ODS: <?= $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() ?>" class="<?= $classe ?>" /></a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <!-- Step 2 Content -->
    <div id="step-2" class="d-none">
    </div>
    <!-- Step 3 Content -->
    <div id="step-3" class="d-none">
        <div class="row" style="padding: 20px">
            <div class="col-12">
                <h5><strong>Este Peticionamento está contribuindo com as seguintes Metas dos ODS:</strong></h5>
                <div id="metas-selecionadas" style="padding-top: 10px">
                </div>
            </div>
        </div>
    </div>
</div>
<input id="arr-objetivos-forte-relacao" type="hidden" value="<?= implode(",", $arrIdsObjetivosForteRelacao) ?>">
<input type='hidden' id='hdnInfraItensSelecionados' name='hdnInfraItensSelecionados' value=''>
<?
require_once "md_ia_classificar_usu_ext_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
