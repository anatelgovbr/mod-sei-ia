<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4Âª REGIÃƒO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versãoo do Gerador de CÃ³digo: 1.43.2
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

    PaginaSEI::getInstance()->verificarSelecao('md_ia_adm_config_assist_ia_alterar');

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

    $strItensSelUsuarios = MdIaAdmCfgAssiIaUsuINT::montarSelectUsuarios();

    $strLinkUsuariosSelecao     = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=usuario_selecionar&tipo_selecao=2&id_object=objLupaUsuario');
    $strLinkAjaxUsuario = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_usuario_auto_completar');

    switch ($_GET['acao']) {
        case 'md_ia_adm_config_assistente_ia':
            $strTitulo = 'Configurações do Assistente IA ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmConfigAssistIA" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';
            $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
            $objMdIaAdmConfigAssistIADTO->retNumIdMdIaAdmConfigAssistIA();
            $objMdIaAdmConfigAssistIADTO->retStrSinExibirFuncionalidade();
            $objMdIaAdmConfigAssistIADTO->retStrOrientacoesGerais();
            $objMdIaAdmConfigAssistIADTO->retStrSystemPrompt();
            $objMdIaAdmConfigAssistIADTO->retNumLimiteGeralTokens();
            $objMdIaAdmConfigAssistIADTO->retNumLimiteMaiorUsuariosTokens();
            $objMdIaAdmConfigAssistIADTO->retNumLlmAtivo();
            $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
            $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

            $idLlmAtivo = $objMdIaAdmConfigAssistIADTO->getNumLlmAtivo();

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdIaAdmConfigAssistIA'])) {
                try {
                    $objMdIaAdmConfigAssistIADTO->setStrOrientacoesGerais($_POST["txtOrientacoesGerais"]);
                    $objMdIaAdmConfigAssistIADTO->setStrSinExibirFuncionalidade($_POST["rdnExibirFuncionalidade"]);
                    $objMdIaAdmConfigAssistIADTO->setStrSystemPrompt($_POST["txaPromptSystem"]);
                    $objMdIaAdmConfigAssistIADTO->setNumLimiteGeralTokens($_POST["txtQtdLimiteGeralTokens"]);
                    $objMdIaAdmConfigAssistIADTO->setNumLimiteMaiorUsuariosTokens($_POST["txtQtdLimiteMaiorUsuarios"]);
                    $objMdIaAdmConfigAssistIADTO->setNumLlmAtivo($_POST["selMetodoRequisicao"]);
                    $objMdIaAdmConfigAssistIARN->alterar($objMdIaAdmConfigAssistIADTO);

                    $arrUsuarios = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnIdUsuarios']);

                    $objMdIaAdmCfgAssiIaUsuRN = new MdIaAdmCfgAssiIaUsuRN();

                    $objMdIaAdmCfgAssiIaUsuRN->excluirRelacionamento($objMdIaAdmConfigAssistIADTO->getNumIdMdIaAdmConfigAssistIA());
                    $objMdIaAdmCfgAssiIaUsuRN->cadastrarRelacionamento(array($arrUsuarios, $objMdIaAdmConfigAssistIADTO->getNumIdMdIaAdmConfigAssistIA()));

                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdIaAdmConfigAssistIADTO->getNumIdMdIaAdmConfigAssistIA() . '" Alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;
    }

    if ($objMdIaAdmConfigAssistIADTO && $objMdIaAdmConfigAssistIADTO->getStrSinExibirFuncionalidade() == "S") {
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
include_once('md_ia_adm_config_assistente_ia_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
    <form id="frmMdIaAdmConfigAsssitIACadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>
        <div id="divMsg" style="display: none">
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
                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Configura a funcionalidade de chat de Inteligência Artificial para exibir ou não', 'Ajuda') ?>
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
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="form-group">
                    <label id="lblOrientacoesGerais" for="txtOrientacoesGerais" accesskey=""
                           class="infraLabelObrigatorio">Orientações
                        Gerais:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orientações descritas abaixo serão exibidas na ajuda do Chat de IA', 'Ajuda') ?>
                         class="infraImg"/>
                    <div id="divEditores" style="overflow: auto;">
                        <textarea id="txtOrientacoesGerais" name="txtOrientacoesGerais"
                                  rows="<?= PaginaSEI::getInstance()->isBolNavegadorFirefox() ? '3' : '4' ?>"
                                  class="infraTextarea"
                                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getStrOrientacoesGerais()); ?></textarea>
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
                    <legend class="infraLegend">Limites de Interação por Usuário</legend>
                    <div class="col-xl-08 col-lg-10 col-md-10 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label id="lblQtdLimiteGeralTokens" for="txtQtdLimiteGeralTokens" accesskey=""
                                           class="infraLabelObrigatorio">Limite Geral de Tokens por Usuário/Dia (milhões de tokens):</label>
                                    <img align="top"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('', 'Ajuda') ?>
                                         class="infraImg"/>
                                    <input type="number" id="txtQtdLimiteGeralTokens" name="txtQtdLimiteGeralTokens"
                                           onkeypress="return infraMascaraNumero(this, event)"
                                           class="infraText form-control campoTamanho70"
                                           value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getNumLimiteGeralTokens()); ?>"
                                           maxlength="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" min="1"
                                           max="15"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label id="lblQtdLimiteMaiorUsuarios" for="txtQtdLimiteMaiorUsuarios" accesskey=""
                                           class="infraLabelObrigatorio">Limite maior para Usuários específicos:</label>
                                    <img align="top"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('', 'Ajuda') ?>
                                         class="infraImg"/>
                                    <input type="number" id="txtQtdLimiteMaiorUsuarios" name="txtQtdLimiteMaiorUsuarios"
                                           onkeypress="return infraMascaraNumero(this, event)"
                                           class="infraText form-control campoTamanho70"
                                           value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getNumLimiteMaiorUsuariosTokens()); ?>"
                                           maxlength="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" min="1"
                                           max="15"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-8 col-md-8 col-lg-6">
                                            <label id="lblUsuario" for="txtUsuario" class="infraLabelOpcional">Usuários que terão limite maior:</label>
                                            <img align="top"
                                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('', 'Ajuda') ?>
                                                 class="infraImg"/>
                                            <input type="text" id="txtUsuario" name="txtUsuario" class="infraText form-control"
                                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                            <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" class="infraText" value=""/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-md-10 col-lg-9">
                                            <div class="input-group">
                                                <select id="selUsuarioTeste" name="selUsuarioTeste" size="10" multiple="multiple"
                                                        class="infraSelect form-control"
                                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados ?>">
                                                    <?= $strItensSelUsuarios ?>
                                                </select>
                                                <div class="botoes ml-1">
                                                    <img id="imgLupaUsuario" onclick="objLupaUsuario.selecionar(700,500);"
                                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                                         alt="Selecionar Usuário"
                                                         title="Selecionar Usuário"
                                                         class="infraImg"
                                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/> <br/>
                                                    <img id="imgExcluirUsuario" onclick="objLupaUsuario.remover();"
                                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                                         alt="Remover Usuário" title="Remover Usuário" class="infraImg"
                                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                                </div>
                                                <input type="hidden" id="hdnIdUsuarios" name="hdnIdUsuarios" class="infraText" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                    <legend class="infraLegend">Ativação do LLM de IA Generativa</legend>
                    <div class="col-xl-08 col-lg-10 col-md-10 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label id="lblMetodoRequisicao" class="infraLabelObrigatorio">LLM Ativo:</label>
                            <img align="top"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('poiuytrewq', 'Ajuda') ?>
                                 class="infraImg"/>
                            <select id="selMetodoRequisicao" name="selMetodoRequisicao" class="infraSelect form-control" onchange="exibirCampos()"
                                    tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                                <?= MdIaAdmConfigAssistIAINT::montarSelectLLMAtivo($idLlmAtivo)?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="form-group">
                    <label id="lblPromptSystem" for="txtPromptSystem" accesskey=""
                           class="infraLabelObrigatorio">Prompt System para o LLM:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('#', 'Ajuda') ?>
                         class="infraImg"/>
                    <textarea class="infraTextArea form-control" name="txaPromptSystem" id="txaPromptSystem"
                              rows="12"
                              cols="300"
                              onkeypress="return infraMascaraTexto(this, event, 2000);"
                              maxlength="2000"
                              tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= $objMdIaAdmConfigAssistIADTO->getStrSystemPrompt() ?></textarea>
                </div>
            </div>
        </div>

        <?
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
require_once "md_ia_adm_config_assistente_ia_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
