<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.2
 */

try {
    require_once dirname(__FILE__) . '../../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_ia_adm_config_similar_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();

    $objEditorRN = new EditorRN();
    $objEditorDTO = new EditorDTO();

    $objEditorDTO->setStrNomeCampo('txtOrientacoesGerais');
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO->setStrSinEstilos('N');
    $objEditorDTO->setNumTamanhoEditor(220);
    $retEditor = $objEditorRN->montarSimples($objEditorDTO);

    switch ($_GET['acao']) {
        case 'md_ia_configuracao_similaridade':
            $strTitulo = 'Configura��es de Similaridade ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmConfigSimilar" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';
            $objMdIaAdmConfigSimilarDTO = new MdIaAdmConfigSimilarDTO();
            $objMdIaAdmConfigSimilarDTO->retNumIdMdIaAdmConfigSimilar();
            $objMdIaAdmConfigSimilarDTO->retNumPercRelevContDoc();
            $objMdIaAdmConfigSimilarDTO->retNumPercRelevMetadados();
            $objMdIaAdmConfigSimilarDTO->retNumIdMdIaAdmConfigSimilar();
            $objMdIaAdmConfigSimilarDTO->retNumIdMdIaAdmConfigSimilar();
            $objMdIaAdmConfigSimilarDTO->retStrSinExibirFuncionalidade();
            $objMdIaAdmConfigSimilarDTO->retNumQtdProcessListagem();
            $objMdIaAdmConfigSimilarDTO->retStrOrientacoesGerais();
            $objMdIaAdmConfigSimilarDTO->retNumPercRelevContDoc();
            $objMdIaAdmConfigSimilarDTO->retNumPercRelevMetadados();
            $objMdIaAdmConfigSimilarDTO->retDthAlteracao();
            $objMdIaAdmConfigSimilarRN = new MdIaAdmConfigSimilarRN();
            $objMdIaAdmConfigSimilarDTO = $objMdIaAdmConfigSimilarRN->consultar($objMdIaAdmConfigSimilarDTO);
            if ($objMdIaAdmConfigSimilarDTO == null) {
                throw new InfraException("Registro n�o encontrado.");
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdIaAdmConfigSimilar'])) {
                try {
                    if ($objMdIaAdmConfigSimilarDTO->getNumPercRelevContDoc() != $_POST["txtPercRelevContDoc"] ||
                        $objMdIaAdmConfigSimilarDTO->getNumPercRelevMetadados() != $_POST["txtPercRelevMetadados"]) {
                        $objMdIaAdmConfigSimilarDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                    }
                    $objMdIaAdmConfigSimilarRN = new MdIaAdmConfigSimilarRN();
                    $objMdIaAdmConfigSimilarDTO->setNumQtdProcessListagem($_POST["txtQtdProcessListagem"]);
                    $objMdIaAdmConfigSimilarDTO->setStrOrientacoesGerais($_POST["txtOrientacoesGerais"]);
                    $objMdIaAdmConfigSimilarDTO->setNumPercRelevContDoc($_POST["txtPercRelevContDoc"]);
                    $objMdIaAdmConfigSimilarDTO->setNumPercRelevMetadados($_POST["txtPercRelevMetadados"]);
                    $objMdIaAdmConfigSimilarDTO->setStrSinExibirFuncionalidade($_POST["rdnExibirFuncionalidade"]);
                    $objMdIaAdmConfigSimilarRN->alterar($objMdIaAdmConfigSimilarDTO);

                    $arrTbPercRelevMetadado = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbPercRelevMetadado']);

                    $objMdIaAdmPercRelevMetRN = new MdIaAdmPercRelevMetRN();
                    $objMdIaAdmPercRelevMetRN->alterarPercentuaisMetadados(array($arrTbPercRelevMetadado, $objMdIaAdmConfigSimilarDTO->getNumIdMdIaAdmConfigSimilar()));

                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdIaAdmConfigSimilarDTO->getNumIdMdIaAdmConfigSimilar() . '" alterad com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;
    }

    $objMdIaAdmPercRelevMetDTO = new MdIaAdmPercRelevMetDTO();

    $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmMetadado();
    $objMdIaAdmPercRelevMetDTO->retStrMetadado();
    $objMdIaAdmPercRelevMetDTO->retNumPercentualRelevancia();
    $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmMetadado();
    $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmMetadado();
    $objMdIaAdmPercRelevMetDTO->retStrMetadado();
    $objMdIaAdmPercRelevMetDTO->retNumPercentualRelevancia();
    $objMdIaAdmPercRelevMetDTO->retStrMetadado();
    $objMdIaAdmPercRelevMetDTO->retNumPercentualRelevancia();
    $objMdIaAdmPercRelevMetDTO->retDthAlteracao();
    $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmMetadado();
    $objMdIaAdmPercRelevMetDTO->retNumPercentualRelevancia();

    $objMdIaAdmPercRelevMetDTO->retStrMetadado();
    $objMdIaAdmPercRelevMetRN = new MdIaAdmPercRelevMetRN();
    $numRegistros = $objMdIaAdmPercRelevMetRN->contar($objMdIaAdmPercRelevMetDTO);
    $arrObjMdIaAdmPercRelevMetDTO = $objMdIaAdmPercRelevMetRN->listar($objMdIaAdmPercRelevMetDTO);
    $tabelaPercRelevMet = "";
    $idMetadadosPreenchidosInput = "";
    $somaPesosAdicionados = 0;
    $strSumarioTabela = 'Metadados';
    $strCaptionTabela = 'Metadados';

    foreach ($arrObjMdIaAdmPercRelevMetDTO as $ObjMdIaAdmPercRelevMetDTO) {
        $arrGrid[] = array($ObjMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmMetadado(), $ObjMdIaAdmPercRelevMetDTO->getStrMetadado(), $ObjMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia());
        $idMetadadosPreenchidosInput .= $ObjMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmMetadado() . ",";
        $tabelaPercRelevMet .= "<tr id='" . $ObjMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmMetadado() . "' descricao_metadado='" . $ObjMdIaAdmPercRelevMetDTO->getStrMetadado() . "' peso_metadado='" . $ObjMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia() . "'>";
        $tabelaPercRelevMet .= "<td>" . $ObjMdIaAdmPercRelevMetDTO->getStrMetadado() . "</td>";
        $tabelaPercRelevMet .= "<td>" . $ObjMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia() . "%</td>";
        $tabelaPercRelevMet .= "<td>" . $ObjMdIaAdmPercRelevMetDTO->getDthAlteracao() . "</td>";
        $tabelaPercRelevMet .= "<td class='text-center'>";
        $tabelaPercRelevMet .= "<a onclick='editarPercRelevanciaMetadado(" . $ObjMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmMetadado() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/alterar.svg' title='Alterar Percentual de Relev�ncia do Metadado' alt='Alterar Percentual de Relev�ncia do Metadado' class='infraImg' /></a>";
        $tabelaPercRelevMet .= "</td>";
        $tabelaPercRelevMet .= "</tr>";
        $somaPesosAdicionados += $ObjMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia();
    }
    $strGridMetadados = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrGrid);
    $mdIaMetadados = MdIaAdmMetadadoINT::montarSelectMdIaAdmMetadado(null, null, null);

    if ($objMdIaAdmConfigSimilarDTO->getStrSinExibirFuncionalidade() == "S") {
        $exibirFuncionalidade = "checked='checked'";
        $naoExibirFuncionalidade = "";
    } else {
        $exibirFuncionalidade = "";
        $naoExibirFuncionalidade = "checked='checked'";
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
include_once('md_ia_adm_config_similar_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
    <form id="frmMdIaAdmConfigSimilarCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>
        <div id="divMsg">
            <div class="alert" role="alert">
                <label></label>
            </div>
        </div>
        <div class="row">
            <div class="col-10">
                <label id="lblExibirFuncionalidade" for="txtExibirFuncionalidade" accesskey=""
                       class="infraLabelObrigatorio">Exibir Funcionalidade:</label>
                <img align="top"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('A funcionalidade de "Processos Similares" somente ser� exibida para os usu�rios se selecionada a op��o "Exibir".', 'Ajuda') ?>
                     class="infraImg"/>
                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade"
                           value="S" class="infraRadio" <?= $exibirFuncionalidade ?>
                    <label id="lblTodosProcessos" name="lblTodosProcessos" for="rdnRelevanteTodosProcessos"
                           class="infraLabelOpcional infraLabelRadio">Exibir</label>
                </div>

                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade" value="N"
                           class="infraRadio" <?= $naoExibirFuncionalidade ?>
                    <label id="lblProcessosEspecificos" name="lblProcessosEspecificos"
                           for="rdnRelevanteProcessosEspecificos"
                           class="infraLabelOpcional infraLabelRadio">N�o Exibir</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label id="lblQtdProcessListagem" for="txtQtdProcessListagem" accesskey=""
                           class="infraLabelObrigatorio">Resultados a serem listados:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define a quantidade de processos a serem listados na funcionalidade "Processos Similares" para os usu�rios, sendo o m�nimo 1 e m�ximo 15. O valor padr�o � 5.', 'Ajuda') ?>
                         class="infraImg"/>
                    <input type="number" id="txtQtdProcessListagem" name="txtQtdProcessListagem"
                           onkeypress="return infraMascaraNumero(this, event)"
                           class="infraText form-control campoTamanho70"
                           value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigSimilarDTO->getNumQtdProcessListagem()); ?>"
                           maxlength="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" min="1"
                           max="15"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="form-group">
                    <label id="lblOrientacoesGerais" for="txtOrientacoesGerais" accesskey=""
                           class="infraLabelObrigatorio">Orienta��es
                        Gerais:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orienta��es descritas abaixo ser�o exibidas na tela do SEI IA na se��o da funcionalidade "Processos Similares".', 'Ajuda') ?>
                         class="infraImg"/>
                    <div id="divEditores" style="overflow: auto;">
                        <textarea id="txtOrientacoesGerais" name="txtOrientacoesGerais"
                                  rows="<?= PaginaSEI::getInstance()->isBolNavegadorFirefox() ? '3' : '4' ?>"
                                  class="infraTextarea"
                                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmConfigSimilarDTO->getStrOrientacoesGerais()); ?></textarea>
                        <script type="text/javascript">
                            <?=$retEditor->getStrEditores();?>
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                    <legend class="infraLegend">Percentual de Relev�ncia e Metadados</legend>
                    <div class="row">
                        <div class="col-xl-8 col-lg-10 col-md-10 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label id="lblPercRelevContDoc" for="txtPercRelevContDoc" accesskey=""
                                       class="infraLabelObrigatorio">Percentual
                                    de Relev�ncia do Conte�do dos Documentos:</label>
                                <img align="top"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('O valor deve ser maior que zero e n�o pode exceder 100%. � importante manter uma propor��o de peso para conte�do e metadados, sendo os valores padr�es da instala��o respectivamente 70% e 30%', 'Ajuda') ?>
                                     class="infraImg"/>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="txtPercRelevContDoc" name="txtPercRelevContDoc"
                                           onkeypress="return infraMascara(this,event)"
                                           class="form-control campoTamanho60"
                                           value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigSimilarDTO->getNumPercRelevContDoc()); ?>"
                                           maxlength="3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                           onchange="atualizarPercRelevMetadados()"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8 col-lg-10 col-md-10 col-sm-12 col-xs-12">
                            <label id="lblPercRelevMetadados" for="txtPercRelevMetadados" accesskey=""
                                   class="infraLabelObrigatorio">Percentual
                                de Relev�ncia dos Metadados:</label>
                            <img align="top"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('O valor deste campo � calculado automaticamente de acordo com o valor indicado no campo "Percentual de Relev�ncia do Conte�do dos Documentos". A soma de ambos os campos totalizar� sempre 100%.', 'Ajuda') ?>
                                 class="infraImg"/>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" id="txtPercRelevMetadados" name="txtPercRelevMetadados"
                                       onkeypress="return infraMascaraNumero(this, event)"
                                       class="form-control campoTamanho60"
                                       value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigSimilarDTO->getNumPercRelevMetadados()); ?>"
                                       maxlength="3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                       aria-describedby="basic-addon2" readonly/>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <p><label class="infraLabelObrigatorio">Data/Hora da �ltima
                                    Altera��o:</label> <?= $objMdIaAdmConfigSimilarDTO->getDthAlteracao() ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-5 col-lg-5 col-xl-4">
                            <div class="form-group">
                                <label id="lblMetadado" for="txtMetadado" accesskey="o" class="infraLabelObrigatorio">Metadado:</label>
                                <img align="top"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Somente existem os 7 tipos de Metadados j� cadastrados na instala��o com valores padr�es, que podem ser alterados.

Aten��o que essa distribui��o de percentuais de metadados � sobre o valor do que j� foi definido no campo "Percentual de Relev�ncia dos Metadados" mais acima.', 'Ajuda') ?>
                                     class="infraImg"/>
                                <input type="text" id="txtMetadado" name="txtMetadado"
                                       class="form-control infraText"
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" disabled
                                       style="height: calc(1.5em + 0.4rem + 5px);"/>
                                <input type="hidden" id="hdnMetadado" name="hdnMetadado"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-5 col-lg-5 col-xl-4">
                            <div class="form-group">
                                <label id="lblPercRelevMetadadoAdicionar" for="lblPercRelevMetadadoAdicionar"
                                       accesskey="" class="infraLabelObrigatorio">Percentual
                                    de Relev�ncia:</label>
                                <img align="top"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('O valor deve ser maior que zero e n�o pode exceder 100%. O sistema obriga manter o valor total de 100% nessa distribui��o de percentuais.', 'Ajuda') ?>
                                     class="infraImg"/>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="txtPercRelevMetadadoAdicionar"
                                           name="txtPercRelevMetadadoAdicionar"
                                           onkeypress="return infraMascaraNumero(this, event)" class="form-control"
                                           value=""
                                           maxlength="3" min="1" max="100" disabled
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                           aria-describedby="basic-addon2"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2 col-lg-2 col-xl-4">
                            <button type="button" name="sbmAdicionarPercentualRelevanciaMetadado"
                                    onclick="adicionarPercentualRelevanciaMetadado();"
                                    id="sbmAdicionarPercentualRelevanciaMetadado"
                                    value="Alterar"
                                    class="infraButton"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" disabled
                                    style="margin-top:30px">
                                Alterar
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <table class="infraTable " id="tbPercRelevMetadado">
                                <caption class='infraCaption'> <?= PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) ?> </caption>
                                <thead>
                                <tr>
                                    <th class="infraTh" style="display: none">Id</th>
                                    <th class="infraTh" width="65%">Metadado</th>
                                    <th class="infraTh" width="15%">Percentual de Relev�ncia</th>
                                    <th class="infraTh" width="15%">Data/Hora da �ltima Altera��o</th>
                                    <th class="infraTh" width="5%">A��es</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?= $tabelaPercRelevMet ?>
                                </tbody>
                            </table>
                            <input type="hidden" id="hdnTbPercRelevMetadado" name="hdnTbPercRelevMetadado"
                                   value="<?= $strGridMetadados ?>"/>
                            <input type="hidden" id="hdnPesoAdicionadoTabela" name="hdnPesoAdicionadoTabela"
                                   value="<?= $somaPesosAdicionados ?>"/>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <?
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
require_once "md_ia_adm_config_similar_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
