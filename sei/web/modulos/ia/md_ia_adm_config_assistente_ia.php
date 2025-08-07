<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4Âª REGIÃO
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

    // Links para consulta Ajax
    $strLinkValidarWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_integracao_busca_operacao_ajax');

    $strDesabilitar = '';
    $msgRefletir = 'Ativar ou desativar a função Refletir, que serve para que a IA pensa antes de responder.\n \n Depende de ter indicado API de LLM com recurso de reflexão no Servidor de Soluções de IA.';
    $msgBuscarNaWeb = 'Ativar ou desativar a função Buscar na Web em tempo real para compor contexto de seu prompt com informações atualizadas. \n \n Depende de ter indicado API de busca na web no Servidor de Soluções de IA. \n \n DESATIVADO! Ainda não funciona na versão 1.1 do servidor de soluções de IA. Previsto para versão futura.';

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
            $objMdIaAdmConfigAssistIADTO->retStrSinRefletir();
            $objMdIaAdmConfigAssistIADTO->retStrSinBuscarWeb();
            $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
            $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdIaAdmConfigAssistIA'])) {
                try {
                    $objMdIaAdmConfigAssistIADTO->setStrOrientacoesGerais($_POST["txtOrientacoesGerais"]);
                    $objMdIaAdmConfigAssistIADTO->setStrSinExibirFuncionalidade($_POST["rdnExibirFuncionalidade"]);
                    $objMdIaAdmConfigAssistIADTO->setNumLimiteGeralTokens($_POST["txtQtdLimiteGeralTokens"]);
                    $objMdIaAdmConfigAssistIADTO->setNumLimiteMaiorUsuariosTokens($_POST["txtQtdLimiteMaiorUsuarios"]);
                    $objMdIaAdmConfigAssistIADTO->setStrSinRefletir($_POST["rdnRefletir"]);
                    $objMdIaAdmConfigAssistIADTO->setStrSinBuscarWeb($_POST["rdnBuscarWeb"]);
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

    if ($objMdIaAdmConfigAssistIADTO && $objMdIaAdmConfigAssistIADTO->getStrSinRefletir() == "S") {
        $refletirAtivar = "checked='checked'";
        $refletirDesativar = "";
    } else {
        $refletirAtivar = "";
        $refletirDesativar = "checked='checked'";
    }

    if ($objMdIaAdmConfigAssistIADTO && $objMdIaAdmConfigAssistIADTO->getStrSinBuscarWeb() == "S") {
        $buscarWebAtivar = "checked='checked'";
        $buscarWebDesativar = "";
    } else {
        $buscarWebAtivar = "";
        $buscarWebDesativar = "checked='checked'";
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
                name="ajuda" <?= PaginaSEI::montarTitleTooltip('Selecione a opção Exibir para que o Assistente IA seja ativado nas telas principais do SEI e no seu Editor.

No SIP, o recurso associado ao Assistente é o "md_ia_adm_config_assist_ia_consultar" e a instalação do módulo inclui ele apenas no Perfil Básico do SEI.', 'Ajuda') ?>
                class="infraImg" alt="Ícone de Ajuda" />
            <div class="infraDivRadio">
                <input type="radio" onChange="exibirFuncionalidade()" id="exibirFuncionalidade" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade"
                    value="S" class="infraRadio" <?= $exibirFuncionalidade ?>>
                <label id="lblTodosProcessos" name="lblTodosProcessos" for="exibirFuncionalidade"
                    class="infraLabelOpcional infraLabelRadio">Exibir</label>
            </div>

            <div class="infraDivRadio">
                <input type="radio" onChange="exibirFuncionalidade()" id="naoExibirFuncionalidade" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade" value="N"
                    class="infraRadio" <?= $naoExibirFuncionalidade ?>>
                <label id="lblProcessosEspecificos" name="lblProcessosEspecificos"
                    for="naoExibirFuncionalidade"
                    class="infraLabelOpcional infraLabelRadio">Não Exibir</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-10">
            <label id="lblRefletir" for="txtRefletir" accesskey=""
                class="infraLabelObrigatorio">Refletir:</label>
            <img align="top"
                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                name="ajuda" <?= PaginaSEI::montarTitleTooltip($msgRefletir, 'Ajuda') ?>
                class="infraImg" alt="Ícone de Ajuda" />
            <div class="infraDivRadio">
                <input type="radio" id="refletirAtivar" utlCampoObrigatorio="o" name="rdnRefletir"
                    value="S" class="infraRadio" <?= $refletirAtivar ?>>
                <label id="lblRefletirAtivar" name="lblRefletirAtivar" for="refletirAtivar"
                    class="infraLabelOpcional infraLabelRadio">Ativar</label>
            </div>

            <div class="infraDivRadio">
                <input type="radio" id="refletirDesativar" utlCampoObrigatorio="o" name="rdnRefletir" value="N"
                    class="infraRadio" <?= $refletirDesativar ?>>
                <label id="lblRefletirDesativar" name="lblRefletirDesativar"
                    for="refletirDesativar"
                    class="infraLabelOpcional infraLabelRadio">Desativar</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-10">
            <label id="lblBuscarWeb" for="txtBuscarWeb" accesskey=""
                class="infraLabelObrigatorio">Buscar na Web:</label>
            <img align="top"
                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                name="ajuda" <?= PaginaSEI::montarTitleTooltip($msgBuscarNaWeb, 'Ajuda') ?>
                class="infraImg" alt="Ícone de Ajuda" />
            <div class="infraDivRadio">
                <input type="radio" id="ativarBuscarWeb" disabled utlCampoObrigatorio="o" name="rdnBuscarWeb"
                    value="S" class="infraRadio" <?= $buscarWebAtivar ?>>
                <label id="lblAtivarBuscarWeb" name="lblAtivarBuscarWeb" for="ativarBuscarWeb"
                    class="infraLabelOpcional infraLabelRadio">Ativar</label>
            </div>

            <div class="infraDivRadio">
                <input type="radio" disabled id="desativarBuscarWeb" utlCampoObrigatorio="o" name="rdnBuscarWeb" value="N"
                    class="infraRadio" <?= $buscarWebDesativar ?>>
                <label id="lblDesativarBuscarWeb" name="lblDesativarBuscarWeb"
                    for="desativarBuscarWeb"
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
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('O texto abaixo é exibido na tela de Orientações Gerais do Assistente IA.', 'Ajuda') ?>
                    class="infraImg" />
                <div id="divEditores" style="overflow: auto;">
                    <textarea id="txtOrientacoesGerais" name="txtOrientacoesGerais"
                        rows="<?= PaginaSEI::getInstance()->isBolNavegadorFirefox() ? '3' : '4' ?>"
                        class="infraTextarea"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getStrOrientacoesGerais()); ?></textarea>
                    <script type="text/javascript">
                        <?= $retEditor->getStrEditores(); ?>
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
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define o limite geral de tokens que os usuários poderão utilizar diariamente. O valor é definido por milhões de tokens. O valor padrão deste campo é 3.', 'Ajuda') ?>
                                    class="infraImg" alt="Ícone de Ajuda" />
                                <input type="number" id="txtQtdLimiteGeralTokens" name="txtQtdLimiteGeralTokens"
                                    onkeypress="return infraMascaraNumero(this, event)"
                                    class="infraText form-control campoTamanho70"
                                    value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getNumLimiteGeralTokens()); ?>"
                                    maxlength="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" min="1"
                                    max="15" />
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
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define o limite a maior de tokens que os usuários específicos listados abaixo poderão utilizar diariamente. O valor é definido por milhões de tokens. O valor padrão deste campo é 6.

Somente será aplicado limite a maior sobre os usuários indicados no campo mais abaixo, o que é opcional.', 'Ajuda') ?>
                                    class="infraImg" alt="Ícone de Ajuda" />
                                <input type="number" id="txtQtdLimiteMaiorUsuarios" name="txtQtdLimiteMaiorUsuarios"
                                    onkeypress="return infraMascaraNumero(this, event)"
                                    class="infraText form-control campoTamanho70"
                                    value="<?= PaginaSEI::tratarHTML($objMdIaAdmConfigAssistIADTO->getNumLimiteMaiorUsuariosTokens()); ?>"
                                    maxlength="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" min="1"
                                    max="15" />
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
                                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define a lista, opcional, de usuários que terão limite diário a maior de consumo de tokens.', 'Ajuda') ?>
                                            class="infraImg" alt="Ícone de Ajuda" />
                                        <input type="text" id="txtUsuario" name="txtUsuario" class="infraText form-control"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                                        <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" class="infraText" value="" />
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
                                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" /> <br />
                                                <img id="imgExcluirUsuario" onclick="objLupaUsuario.remover();"
                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                                    alt="Remover Usuário" title="Remover Usuário" class="infraImg"
                                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                                            </div>
                                            <input type="hidden" id="hdnIdUsuarios" name="hdnIdUsuarios" class="infraText" value="" />
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
            <div class="form-group">
                <label id="lblPromptSystem" for="txtPromptSystem" accesskey=""
                    class="infraLabelObrigatorio">Prompt System para o LLM:</label>
                <img align="top"
                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Campo somente leitura. Não alterar!

Define as instruções internas de como o LLM de IA Generativa deve se comportar minimamente nas interações com os usuários.', 'Ajuda') ?>
                    class="infraImg" alt="Ícone de Ajuda" />
                <textarea class="infraTextArea form-control" name="txaPromptSystem" id="txaPromptSystem"
                    rows="12"
                    cols="300" disabled
                    onkeypress="return infraMascaraTexto(this, event, 2000);"
                    maxlength="2000"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= $objMdIaAdmConfigAssistIADTO->getStrSystemPrompt() ?></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" id="hdnConectadoAPI" value="0">
    <?
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?
require_once "md_ia_adm_config_assistente_ia_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
