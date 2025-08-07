<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 25/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.40.0
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

    SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

    $numIdMdIaGaleriaPrompts = '';
    if (isset($_GET['id_md_ia_galeria_prompts'])) {
        $numIdMdIaGaleriaPrompts = $_GET['id_md_ia_galeria_prompts'];
    } else {
        $numIdMdIaGaleriaPrompts = $_POST['hdnIdMdIaGaleriaPrompts'];
    }

    $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();

    $strDesabilitar = '';

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    switch ($_GET['acao']) {
        case 'md_ia_galeria_prompts_cadastrar':

            $strTitulo = 'Publicar na Galeria de Prompts';
            $arrComandos[] = '<button type="submit" accesskey="P" name="sbmPublicarGaleriaPrompts" value="Publicar" class="infraButton"><span class="infraTeclaAtalho">P</span>ublicar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_selecionar&tipo_selecao=2&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmPublicarGaleriaPrompts'])) {
                try {
                    $objMdIaGaleriaPromptsDTO->setNumIdMdIaGrupoGaleriaPrompt($_POST["selGrupoGaleriaPrompts"]);
                    $objMdIaGaleriaPromptsDTO->setStrDescricao($_POST["txaDescricaoPrompt"]);
                    $objMdIaGaleriaPromptsDTO->setStrPrompt($_POST["txaPrompt"]);
                    $objMdIaGaleriaPromptsDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                    $objMdIaGaleriaPromptsDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                    $objMdIaGaleriaPromptsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                    $objMdIaGaleriaPromptsDTO->setStrSinAtivo("S");

                    $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
                    $arrObjMdIaGaleriaPromptsDTO = $objMdIaGaleriaPromptsRN->cadastrar($objMdIaGaleriaPromptsDTO);
                    PaginaSEI::getInstance()->setStrMensagem('Prompt "' . $objMdIaGaleriaPromptsDTO->getNumIdMdIaGaleriaPrompts() . '" publicado na Galeria de Prompts com sucesso.');

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_selecionar&tipo_selecao=2&id_md_ia_grupo_galeria_prompt=' . $numIdMdIaGrupoGaleriaPrompts));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_galeria_prompts_alterar':

            $strTitulo = 'Alterar Prompt na Galeria de Prompts';

            $arrComandos[] = '<button type="submit" accesskey="A" name="sbmAlterarPromptGaleriaPrompts" value="Alterar" class="infraButton"><span class="infraTeclaAtalho">A</span>lterar</button>';
            $strDesabilitar = 'disabled="disabled"';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_selecionar&tipo_selecao=2&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($numIdMdIaGaleriaPrompts);
            $objMdIaGaleriaPromptsDTO->retNumIdMdIaGaleriaPrompts();
            $objMdIaGaleriaPromptsDTO->retNumIdMdIaGrupoGaleriaPrompt();
            $objMdIaGaleriaPromptsDTO->retStrPrompt();
            $objMdIaGaleriaPromptsDTO->retStrDescricao();
            $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
            $objMdIaGaleriaPromptsDTO = $objMdIaGaleriaPromptsRN->consultar($objMdIaGaleriaPromptsDTO);

            if ($objMdIaGaleriaPromptsDTO) {
                $numIdMdIaGrupoGaleriaPrompts = $objMdIaGaleriaPromptsDTO->getNumIdMdIaGrupoGaleriaPrompt();
                $descricaoPrompt = $objMdIaGaleriaPromptsDTO->getStrDescricao();
                $prompt = $objMdIaGaleriaPromptsDTO->getStrPrompt();
            }

            if (isset($_POST['sbmAlterarPromptGaleriaPrompts'])) {
                try {
                    $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
                    $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($numIdMdIaGaleriaPrompts);
                    $objMdIaGaleriaPromptsDTO->setNumIdMdIaGrupoGaleriaPrompt($_POST["selGrupoGaleriaPrompts"]);
                    $objMdIaGaleriaPromptsDTO->setStrDescricao($_POST["txaDescricaoPrompt"]);
                    $objMdIaGaleriaPromptsDTO->setStrPrompt($_POST["txaPrompt"]);
                    $objMdIaGaleriaPromptsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                    $objMdIaGaleriaPromptsRN->alterar($objMdIaGaleriaPromptsDTO);
                    PaginaSEI::getInstance()->setStrMensagem('Prompt "' . $objMdIaGaleriaPromptsDTO->getNumIdMdIaGaleriaPrompts() . '" alterado com sucesso.');

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_selecionar&tipo_selecao=2&id_md_ia_grupo_galeria_prompt=' . $numIdMdIaGrupoGaleriaPrompts));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


    $strItensSelGrupoGaleriaPrompts = MdIaGrupoGaleriaPromptINT::montarSelectGrupoGaleriaPrompt('&nbsp;', 'Selecione uma opção', $numIdMdIaGrupoGaleriaPrompts);
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

?>
#lblselGrupoGaleriaPrompts {position:absolute;left:0%;top:0%;width:50%;}
#selGrupoGaleriaPrompts {position:absolute;left:0%;top:5%;width:50%;}

#lblDescricaoPrompt {position:absolute;left:0%;top:13%;width:95%;}
#txaDescricaoPrompt {position:absolute;left:0%;top:18%;width:95%;}

#lblPrompt {position:absolute;left:0%;top:39%;width:95%;}
#txaPrompt {position:absolute;left:0%;top:44%;width:95%;}

#frmPublicarGaleriaPrompt {display: none;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
<form id="frmPublicarGaleriaPrompt" method="post" onsubmit="return OnSubmitForm();"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    //PaginaSEI::getInstance()->montarAreaValidacao();
    PaginaSEI::getInstance()->abrirAreaDados('40em');
    ?>
    <div class="form-group">
        <label id="lblselGrupoGaleriaPrompts" for="selGrupoGaleriaPrompts" accesskey="G" class="infraLabelObrigatorio"><span
                class="infraTeclaAtalho">G</span>rupo:</label>
        <select id="selGrupoGaleriaPrompts" name="selGrupoGaleriaPrompts" class="infraSelect"
            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" required>
            <?= $strItensSelGrupoGaleriaPrompts ?>
        </select>
    </div>
    <div class="form-group">
        <label id="lblDescricaoPrompt" for="txaDescricaoPrompt" accesskey="D" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">D</span>escrição Prompt:</label>
        <textarea rows="4" required name="txaDescricaoPrompt" id="txaDescricaoPrompt" class="infraTextarea" maxlength="500" onpaste="return infraLimitarTexto(this,event,500);" onkeypress="return infraLimitarTexto(this,event,500);" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= $descricaoPrompt ?></textarea>
    </div>
    <div class="form-group">
        <label id="lblPrompt" for="txaPrompt" accesskey="P" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">P</span>rompt:</label>
        <textarea rows="18" required name="txaPrompt" id="txaPrompt" class="infraTextarea" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= $prompt ?></textarea>
    </div>
    <input type="hidden" id="hdnIdMdIaGaleriaPrompts" name="hdnIdMdIaGaleriaPrompts" value="<?= $numIdMdIaGaleriaPrompts ?>" />

    <?

    PaginaSEI::getInstance()->fecharAreaDados();
    //PaginaSEI::getInstance()->montarAreaDebug();
    //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?php
require_once "md_ia_galeria_prompts_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>