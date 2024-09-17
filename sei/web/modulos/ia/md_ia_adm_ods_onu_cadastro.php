<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 21/12/2023 - criado por sabino.colab
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

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();

    $strDesabilitar = '';

    $arrComandos = array();


    $objEditorRN = new EditorRN();
    $objEditorDTO = new EditorDTO();

    $objEditorDTO->setStrNomeCampo('txtOrientacoesGerais');
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO->setStrSinEstilos('N');
    $objEditorDTO->setNumTamanhoEditor(220);
    $retEditor = $objEditorRN->montarSimples($objEditorDTO);

    $strItensUnidadesAlerta = MdIaAdmOdsOnuINT::montarSelectUnidadesAlerta();
    $strLinkAjaxAutocompletarUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_unidade_alerta_auto_completar_ajax');
    $strUrlUnidadeAlerta = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_unidade_selecionar_todas&tipo_selecao=2&id_object=objLupaUnidade');

    switch ($_GET['acao']) {
        case 'md_ia_adm_ods_onu':
            $strTitulo = 'Objetivos de Desenvolvimento Sustentável da ONU';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmOdsOnu" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $objMdIaAdmOdsOnuDTO->setNumIdMdIaAdmOdsOnu(1);
            $objMdIaAdmOdsOnuDTO->retNumIdMdIaAdmOdsOnu();
            $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
            $objMdIaAdmOdsOnuDTO->retStrSinClassificacaoExterno();
            $objMdIaAdmOdsOnuDTO->retStrSinExibirAvaliacao();
            $objMdIaAdmOdsOnuDTO->retStrOrientacoesGerais();
            $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
            $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

            $selectTipoDocumento = MdIaAdmTpDocPesqINT::montarSelectTipoDocumento(0, "Selecione uma opção:", 0);

            if ($objMdIaAdmOdsOnuDTO == null) {
                throw new InfraException("Registro não encontrado.");
            }
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
            $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
            $objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
            $objMdIaAdmObjetivoOdsDTO->retStrDescricaoOds();
            $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
            $objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();
            $numRegistros = $objMdIaAdmObjetivoOdsRN->contar($objMdIaAdmObjetivoOdsDTO);
            $arrObjMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->listar($objMdIaAdmObjetivoOdsDTO);


            foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {
                $objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
                $objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
                $objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds());
                $objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
                $quantidadeMetas = $objMdIaAdmMetaOdsRN->contar($objMdIaAdmMetaOdsDTO);

                $tabelaObjetivoOds .= "<tr>";
                $tabelaObjetivoOds .= "<td class='text-center'>" . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . "</td>";
                $tabelaObjetivoOds .= "<td class='text-left'>" . $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() . "</td>";
                $tabelaObjetivoOds .= "<td class='text-left'>" . $objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds() . "</td>";
                $tabelaObjetivoOds .= "<td class='text-center'>".$quantidadeMetas."</td>";
                $tabelaObjetivoOds .= "<td class='text-center'>";
                $tabelaObjetivoOds .= "<a onclick='consultarObjetivoOds(" . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/consultar.svg' title='Consultar Metas do Objetivo' alt='Consultar Metas do Objetivo' class='infraImg' /></a>";
                $tabelaObjetivoOds .= "</td>";
                $tabelaObjetivoOds .= "</tr>";
            }

            $strCaptionTabela = "Metas";

            if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
                $exibirFuncionalidade = "checked='checked'";
                $naoExibirFuncionalidade = "";
                $disableClassificacaoUsuarioExterno = '';
            } else {
                $exibirFuncionalidade = "";
                $naoExibirFuncionalidade = "checked='checked'";
                $disableClassificacaoUsuarioExterno = 'disabled';
            }

            if(!MdIaAdmOdsOnuINT::verificaSeModPeticionamentoVersaoMinima()) {
                $disableClassificacaoUsuarioExterno = 'disabled';
            }

            if ($objMdIaAdmOdsOnuDTO->getStrSinClassificacaoExterno() == "S") {
                $classificacaoUsuarioExterno = "checked='checked'";
                $naoClassificacaoUsuarioExterno = "";
            } else {
                $classificacaoUsuarioExterno = "";
                $naoClassificacaoUsuarioExterno = "checked='checked'";
            }

            if ($objMdIaAdmOdsOnuDTO->getStrSinExibirAvaliacao() == "S") {
                $exibirAvaliacao = "checked='checked'";
                $naoExibirAvaliacao = "";
            } else {
                $exibirAvaliacao = "";
                $naoExibirAvaliacao = "checked='checked'";
            }

            if (isset($_POST['sbmAlterarMdIaAdmOdsOnu'])) {
                try {
                    $objMdIaAdmOdsOnuDTO->setNumIdMdIaAdmOdsOnu($_POST['hdnIdMdIaAdmOdsOnu']);
                    $objMdIaAdmOdsOnuDTO->setStrOrientacoesGerais($_POST['txtOrientacoesGerais']);
                    $objMdIaAdmOdsOnuDTO->setStrSinExibirFuncionalidade($_POST["rdnExibirFuncionalidade"]);
                    if(MdIaAdmOdsOnuINT::verificaSeModPeticionamentoVersaoMinima()) {
                        $objMdIaAdmOdsOnuDTO->setStrSinClassificacaoExterno($_POST["rdnClassificacaoExterno"] ?? 'N');
                    }
                    $objMdIaAdmOdsOnuDTO->setStrSinExibirAvaliacao($_POST["rdnExibirAvaliacao"]);
                    $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
                    $objMdIaAdmOdsOnuRN->alterar($objMdIaAdmOdsOnuDTO);

                    $arrUnidadesAlerta = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnIdUnidades']);

                    $objMdIaAdmUnidadeAlertaRN = new MdIaAdmUnidadeAlertaRN();
                    $objMdIaAdmUnidadeAlertaRN->excluirRelacionamento($objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu());
                    $objMdIaAdmUnidadeAlertaRN->cadastrarRelacionamento(array($arrUnidadesAlerta, $objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu()));

                    PaginaSEI::getInstance()->adicionarMensagem('Pesquisa de Documentos "' . $objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu() . '" alterada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
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
include_once('md_ia_adm_ods_onu_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdIaAdmOdsOnuCadastro" method="post" onsubmit="return OnSubmitForm();"
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
            <div class="col-10 md-4">
                <label id="lblExibirFuncionalidade" for="txtExibirFuncionalidade" accesskey="" class="infraLabelObrigatorio">Exibir Funcionalidade:</label>
                <img align="top"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('A funcionalidade de "Objetivos de Desenvolvimento Sustentável da ONU" somente será exibida para os usuários se selecionada a opção "Exibir".', 'Ajuda') ?>
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
                           class="infraLabelOpcional infraLabelRadio">Não Exibir</label>
                </div>
            </div>
        </div>
        <div class="row classificacaoUsuarioExterno">
            <div class="col-10 md-4">
                <label id="lblClassificacaoUsuarioExterno" for="txtClassificacaoUsuarioExterno" accesskey=""
                       class="infraLabelObrigatorio">Classificação por Usuário Externo:</label>
                <img align="top"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Esta opção permite que a funcionalidade de classificação de acordo com os "Objetivos de Desenvolvimento Sustentável da ONU" seja exibida para os Usuários Externos ao peticionar Processo Novo, Processo Intercorrente e Resposta da Intimação. \n\nA funcionalidade só estará disponível se o módulo "SEI Peticionamento, Intimação e Procuração" estiver instalado na versão 4.3.0 ou superior e a opção "Exibir Funcionalidade" estiver marcada como "Exibir".', 'Ajuda') ?>
                     class="infraImg"/>
                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnClassificacaoExterno"
                           value="S" class="infraRadio" <?= $classificacaoUsuarioExterno ?> <?= $disableClassificacaoUsuarioExterno ?>
                    <label id="lblRdnClassificacaoUsuarioExternoS" name="lblRdnClassificacaoUsuarioExternoS" for="rdnClassificacaoExterno"
                           class="infraLabelOpcional infraLabelRadio">Ativar</label>
                </div>

                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnClassificacaoExterno" value="N"
                           class="infraRadio" <?= $naoClassificacaoUsuarioExterno ?> <?= $disableClassificacaoUsuarioExterno ?>
                    <label id="lblRdnClassificacaoUsuarioExternoN" name="lblRdnClassificacaoExternoN"
                           for="rdnClassificacaoExternoN"
                           class="infraLabelOpcional infraLabelRadio">Desativar</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10 md-4">
                <label id="lblExibirFuncionalidade" for="txtExibirFuncionalidade" accesskey=""
                       class="infraLabelObrigatorio">Fase de Avaliação Especializada por Racional:</label>
                <img align="top"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Parametrização para exibir ou não a Avaliação Especializada por Racional.', 'Ajuda') ?>
                     class="infraImg"/>
                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnExibirAvaliacao"
                           value="S" class="infraRadio" <?= $exibirAvaliacao ?>
                    <label id="lblExibirAvaliacao" name="lblExibirAvaliacao" for="rdnExibirAvaliacao"
                           class="infraLabelOpcional infraLabelRadio">Ativar</label>
                </div>

                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnExibirAvaliacao" value="N"
                           class="infraRadio" <?= $naoExibirAvaliacao ?>
                    <label id="lblNaoExibirAvaliacao" name="lblNaoExibirAvaliacao"
                           for="rdnNaoExibirAvaliacao"
                           class="infraLabelOpcional infraLabelRadio">Desativar</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="form-group">
                    <label id="lblOrientacoesGerais" for="txtOrientacoesGerais" accesskey=""
                           class="infraLabelObrigatorio">Orientações
                        Gerais:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orientações descritas abaixo serão exibidas na tela do SEI IA na seção da funcionalidade "Objetivos de Desenvolvimento Sustentável da ONU".', 'Ajuda') ?>
                         class="infraImg"/>
                    <div id="divEditores" style="overflow: auto;">
                        <textarea id="txtOrientacoesGerais" name="txtOrientacoesGerais"
                                  rows="<?= PaginaSEI::getInstance()->isBolNavegadorFirefox() ? '3' : '4' ?>"
                                  class="infraTextarea"
                                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmOdsOnuDTO->getStrOrientacoesGerais()); ?></textarea>
                        <script type="text/javascript">
                            <?=$retEditor->getStrEditores();?>
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-lg-6">
                            <label id="lblUnidadeAlerta" for="txtUnidadeAlerta" class="infraLabelObrigatorio">Unidades para alertar
                                pendência ou divergência:</label>
                            <img align="top"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('Apenas nas Unidades aqui listadas apresentará ícone próprio do SEI IA sobre o processo alertando pendência de classificação por pelo menos um Usuário ou divergência de classificação de Usuário frente à nova sugestão feita pela Inteligência Artificial do SEI.', 'Ajuda') ?>
                                 class="infraImg"/>
                            <input type="text" id="txtUnidadeAlerta" name="txtUnidadeAlerta" class="infraText form-control"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" class="infraText" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10 col-md-10 col-lg-9">
                            <div class="input-group">
                                <select id="selUnidadeAlerta" name="selUnidadeAlerta" size="10" multiple="multiple"
                                        class="infraSelect form-control"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados ?>">
                                    <?= $strItensUnidadesAlerta ?>
                                </select>
                                <div class="botoes ml-1">
                                    <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                         alt="Selecionar Unidade para alertar pendência ou divergência"
                                         title="Selecionar Unidade para alertar pendência ou divergência"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/> <br/>
                                    <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                         alt="Remover Unidade" title="Remover Unidade" class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>
                                <input type="hidden" id="hdnIdUnidades" name="hdnIdUnidades" class="infraText" value=""/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                    <legend class="infraLegend">Objetivos de Desenvolvimento Sustentável</legend>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <table class="infraTable" id="tbPercRelevMetadado">
                                <caption class='infraCaption'> <?= PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) ?> </caption>
                                <thead>
                                <tr>
                                    <th class="infraTh text-center" width="5%%">Número</th>
                                    <th class="infraTh text-left" width="30%">Nome do Objetivo</th>
                                    <th class="infraTh text-left" width="55%">Descrição</th>
                                    <th class="infraTh text-center" width="5%">Metas</th>
                                    <th class="infraTh text-center" width="5%">Consultar</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?= $tabelaObjetivoOds ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <input type="hidden" id="hdnIdMdIaAdmOdsOnu" name="hdnIdMdIaAdmOdsOnu"
               value="<?= $objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu(); ?>"/>
        <input type="hidden" id="hdnIdSelecaoObjetivo" name="hdnIdSelecaoObjetivo"/>
        <input type="hidden" id="hdntelaConsulta" name="hdntelaConsulta" value="true"/>
        <?
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
require_once "md_ia_adm_ods_onu_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
