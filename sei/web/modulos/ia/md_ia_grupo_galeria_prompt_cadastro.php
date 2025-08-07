<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.45
 **/



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

    PaginaSEI::getInstance()->verificarSelecao('md_ia_grupo_galeria_prompt_selecionar');

    $objMdIaGrupoGaleriaPromptDTO = new MdIaGrupoGaleriaPromptDTO();
    $strDesabilitar = '';
    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_ia_grupo_galeria_prompt_cadastrar':
            $strTitulo = 'Novo Grupo de Galeria de Prompts';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdIaGrupoGaleriaPrompt" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaGrupoGaleriaPromptDTO->setNumIdMdIaGrupoGaleriaPrompt(PaginaSEI::POST('txtIdMdIaGrupoGaleriaPrompt'));
            $objMdIaGrupoGaleriaPromptDTO->setStrNomeGrupo(PaginaSEI::POST('txtNomeGrupo'));

            if (isset($_POST['sbmCadastrarMdIaGrupoGaleriaPrompt'])) {
                try {
                    $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
                    $objMdIaGrupoGaleriaPromptDTO = $objMdIaGrupoGaleriaPromptRN->cadastrar($objMdIaGrupoGaleriaPromptDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Grupo de Galeria de Prompts "' . $objMdIaGrupoGaleriaPromptDTO->getNumIdMdIaGrupoGaleriaPrompt() . '" cadastradO com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_grupo_galeria_prompt_alterar':
            $strTitulo = 'Alterar Grupo de Galeria de Prompts';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaGrupoGaleriaPrompt" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_ia_grupo_galeria_prompt'])) {
                $objMdIaGrupoGaleriaPromptDTO->setNumIdMdIaGrupoGaleriaPrompt($_GET['id_md_ia_grupo_galeria_prompt']);
                $objMdIaGrupoGaleriaPromptDTO->retTodos();
                $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
                $objMdIaGrupoGaleriaPromptDTO = $objMdIaGrupoGaleriaPromptRN->consultar($objMdIaGrupoGaleriaPromptDTO);
                if ($objMdIaGrupoGaleriaPromptDTO === null) {
                    throw new InfraException("Registro não encontrado.");
                }
                $idMdIaGrupoGaleriaPrompt = $objMdIaGrupoGaleriaPromptDTO->getNumIdMdIaGrupoGaleriaPrompt();
            } else {
                $objMdIaGrupoGaleriaPromptDTO->setNumIdMdIaGrupoGaleriaPrompt(PaginaSEI::POST('hdnIdMdIaGrupoGaleriaPrompt'));
                $objMdIaGrupoGaleriaPromptDTO->setStrNomeGrupo(PaginaSEI::POST('txtNomeGrupo'));
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdIaGrupoGaleriaPrompt'])) {
                try {
                    $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
                    $objMdIaGrupoGaleriaPromptRN->alterar($objMdIaGrupoGaleriaPromptDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Grupo de Galeria de Prompts "' . $objMdIaGrupoGaleriaPromptDTO->getNumIdMdIaGrupoGaleriaPrompt() . '" alteradO com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_grupo_galeria_prompt_consultar':
            $strTitulo = 'Consultar Grupo de Galeria de Prompts';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
            $objMdIaGrupoGaleriaPromptDTO->setBolExclusaoLogica(false);
            $objMdIaGrupoGaleriaPromptDTO->retTodos();
            $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
            $objMdIaGrupoGaleriaPromptDTO = $objMdIaGrupoGaleriaPromptRN->consultar($objMdIaGrupoGaleriaPromptDTO);
            if ($objMdIaGrupoGaleriaPromptDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . PaginaSEI::GET('acao') . "' não reconhecida.");
    }
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . ($strTitulo ?? false));
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<?php if (0) { ?><style>
        <?php } ?>#lblIdMdIaGrupoGaleriaPrompt {
            position: absolute;
            left: 0;
            top: 0;
            width: 25%;
        }

        #txtIdMdIaGrupoGaleriaPrompt {
            position: absolute;
            left: 0;
            top: 40%;
            width: 25%;
        }

        #lblNomeGrupo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80%;
        }

        #txtNomeGrupo {
            position: absolute;
            left: 0;
            top: 40%;
            width: 80%;
        }

        <?php if (0) { ?>
    </style><?php } ?>
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<?php if (0) { ?><script type="text/javascript">
        <?php } ?>

        function inicializar() {
            if ('<?= PaginaSEI::GET('acao') ?>' === 'md_ia_grupo_galeria_prompt_cadastrar') {
                document.getElementById('txtNomeGrupo').focus();
            } else if ('<?= PaginaSEI::GET('acao') ?>' === 'txtNomeGrupo') {
                infraDesabilitarCamposAreaDados();
            } else {
                document.getElementById('btnCancelar').focus();
            }
            infraEfeitoTabelas(true);
        }

        function validarCadastro() {
            if (infraTrim(document.getElementById('txtIdMdIaGrupoGaleriaPrompt').value) == '') {
                alert('Informe O Id do Grupo.');
                document.getElementById('txtIdMdIaGrupoGaleriaPrompt').focus();
                return false;
            }

            if (infraTrim(document.getElementById('txtNomeGrupo').value) == '') {
                alert('Informe O Nome.');
                document.getElementById('txtNomeGrupo').focus();
                return false;
            }

            return true;
        }

        function OnSubmitForm() {
            return validarCadastro();
        }

        <?php if (0) { ?>
    </script><?php } ?>
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo ?? false, 'onload="inicializar();"');
?>
<form id="frmMdIaGrupoGaleriaPromptCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao') . '&acao_origem=' . PaginaSEI::GET('acao')) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos ?? false);
    //PaginaSEI::getInstance()->montarAreaValidacao();
    PaginaSEI::getInstance()->abrirAreaDados('5em');
    ?>
    <label id="lblNomeGrupo" accesskey="N" for="txtNomeGrupo" accesskey="" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">N</span>ome:</label>
    <input type="text" id="txtNomeGrupo" name="txtNomeGrupo" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo()) ?>" onkeypress="return infraMascaraTexto(this,event,100);" maxlength="100" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
    <?php
    PaginaSEI::getInstance()->fecharAreaDados();
    //PaginaSEI::getInstance()->montarAreaDebug();
    ?>
    <input type="hidden" id="hdnIdMdIaGrupoGaleriaPrompt" name="hdnIdMdIaGrupoGaleriaPrompt"
        value="<?= $idMdIaGrupoGaleriaPrompt ?>" />
</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
