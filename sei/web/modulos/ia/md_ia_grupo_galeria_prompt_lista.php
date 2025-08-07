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

    PaginaSEI::getInstance()->prepararSelecao('md_ia_grupo_galeria_prompt_selecionar');

    switch ($_GET['acao']) {
        case 'md_ia_grupo_galeria_prompt_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaGrupoGaleriaPromptDTO = array();
                foreach ($arrStrIds as $strId) {
                    $objMdIaGrupoGaleriaPromptDTO = new MdIaGrupoGaleriaPromptDTO();
                    $objMdIaGrupoGaleriaPromptDTO->setNumIdMdIaGrupoGaleriaPrompt($strId);
                    $arrObjMdIaGrupoGaleriaPromptDTO[] = $objMdIaGrupoGaleriaPromptDTO;
                }
                $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
                $objMdIaGrupoGaleriaPromptRN->excluir($arrObjMdIaGrupoGaleriaPromptDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao_origem') . '&acao_origem=' . PaginaSEI::GET('acao')));
            die;

        case 'md_ia_grupo_galeria_prompt_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Grupo de Galeria de Prompts', 'Selecionar Grupos de Galeria de Prompts');

            //Se cadastrou alguem
            if (PaginaSEI::GET('acao_origem') === 'md_ia_grupo_galeria_prompt_cadastrar') {
                if (isset($_GET['id_md_ia_grupo_galeria_prompt'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_ia_grupo_galeria_prompt']);
                }
            }
            break;

        case 'md_ia_adm_grupos_galeria_prompts':
            $strTitulo = 'Grupos de Galeria de Prompts';
            break;

        default:
            throw new InfraException("Ação '" . PaginaSEI::GET('acao') . "' não reconhecida.");
    }

    $arrComandos = array();
    if (PaginaSEI::GET('acao') === 'md_ia_grupo_galeria_prompt_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_ia_grupo_galeria_prompt_cadastrar');
    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_galeria_prompt_cadastrar&acao_origem=' . PaginaSEI::GET('acao') . '&acao_retorno=' . PaginaSEI::GET('acao')) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }


    $objMdIaGrupoGaleriaPromptDTO = new MdIaGrupoGaleriaPromptDTO();
    $objMdIaGrupoGaleriaPromptDTO->retNumIdMdIaGrupoGaleriaPrompt();
    $objMdIaGrupoGaleriaPromptDTO->retStrNomeGrupo();

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaGrupoGaleriaPromptDTO, 'IdMdIaGrupoGaleriaPrompt', InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdIaGrupoGaleriaPromptRN = new MdIaGrupoGaleriaPromptRN();
    $arrObjMdIaGrupoGaleriaPromptDTO = $objMdIaGrupoGaleriaPromptRN->listar($objMdIaGrupoGaleriaPromptDTO);

    /** @var MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO */

    $numRegistros = count($arrObjMdIaGrupoGaleriaPromptDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;

        if (PaginaSEI::GET('acao') === 'md_ia_grupo_galeria_prompt_selecionar') {
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_grupo_galeria_prompt_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolCheck = true;
        } else {
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_grupo_galeria_prompt_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_grupo_galeria_prompt_excluir');
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_galeria_prompt_excluir&acao_origem=' . PaginaSEI::GET('acao'));
        }

        if ($bolAcaoImprimir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }

        $strResultado = '';

        $strCaptionTabela = 'Grupos de Galeria de Prompts';

        $strResultado .= '<table style="width: 99%" class="infraTable">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<thead><tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" style="width: 1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }

        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaGrupoGaleriaPromptDTO, 'Nome', 'NomeGrupo', $arrObjMdIaGrupoGaleriaPromptDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">Ações</th>' . "\n";
        $strResultado .= '</tr></thead><tbody>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            $strCssTr = ($strCssTr === '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaGrupoGaleriaPromptDTO[$i]->getNumIdMdIaGrupoGaleriaPrompt(), $arrObjMdIaGrupoGaleriaPromptDTO[$i]->getStrNomeGrupo()) . '</td>';
            }

            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaGrupoGaleriaPromptDTO[$i]->getStrNomeGrupo()) . '</td>';
            $strResultado .= '<td style="text-align: center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdIaGrupoGaleriaPromptDTO[$i]->getNumIdMdIaGrupoGaleriaPrompt());

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_galeria_prompt_alterar&acao_origem=' . PaginaSEI::GET('acao') . '&acao_retorno=' . PaginaSEI::GET('acao') . '&id_md_ia_grupo_galeria_prompt=' . $arrObjMdIaGrupoGaleriaPromptDTO[$i]->getNumIdMdIaGrupoGaleriaPrompt()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Grupo de Galeria de Prompts" alt="Alterar Grupo de Galeria de Prompts" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strId = $arrObjMdIaGrupoGaleriaPromptDTO[$i]->getNumIdMdIaGrupoGaleriaPrompt();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdIaGrupoGaleriaPromptDTO[$i]->getStrNomeGrupo());
            }


            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Grupo de Galeria de Prompts" alt="Excluir Grupo de Galeria de Prompts" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr></tbody>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if (PaginaSEI::GET('acao') === 'md_ia_grupo_galeria_prompt_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
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
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<?php if (0) { ?><script type="text/javascript">
        <?php } ?>

        function inicializar() {
            if ('<?= PaginaSEI::GET('acao') ?>' === 'md_ia_grupo_galeria_prompt_selecionar') {
                infraReceberSelecao();
                document.getElementById('btnFecharSelecao').focus();
            } else {
                document.getElementById('btnFechar').focus();
            }
            infraEfeitoTabelas(true);
        }
        <?php if ($bolAcaoExcluir ?? false) { ?>

            function acaoExcluir(id, desc) {
                if (confirm("Confirma exclusão do Grupo de Galeria de Prompts \"" + desc + "\"?")) {
                    document.getElementById('hdnInfraItemId').value = id;
                    document.getElementById('frmMdIaGrupoGaleriaPromptLista').action = '<?= $strLinkExcluir ?? false ?>';
                    document.getElementById('frmMdIaGrupoGaleriaPromptLista').submit();
                }
            }

            function acaoExclusaoMultipla() {
                if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                    alert('Nenhuma Grupo de Galeria de Prompts selecionada.');
                    return;
                }
                if (confirm("Confirma exclusão dos Grupos de Galeria de Prompts selecionadOs?")) {
                    document.getElementById('hdnInfraItemId').value = '';
                    document.getElementById('frmMdIaGrupoGaleriaPromptLista').action = '<?= $strLinkExcluir ?? false ?>';
                    document.getElementById('frmMdIaGrupoGaleriaPromptLista').submit();
                }
            }
        <?php } ?>

        <?php if (0) { ?>
    </script><?php } ?>
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo ?? false, 'onload="inicializar();"');
?>
<form id="frmMdIaGrupoGaleriaPromptLista" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao') . '&acao_origem=' . PaginaSEI::GET('acao')) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos ?? false);
    //PaginaSEI::getInstance()->abrirAreaDados('5em');
    //PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarAreaTabela($strResultado ?? false, $numRegistros ?? false);
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos ?? false);
    ?>
</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
