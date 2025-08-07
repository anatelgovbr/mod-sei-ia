<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 21/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.40.0
 *
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(true);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('md_ia_galeria_prompts_selecionar');

    SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

    PaginaSEI::getInstance()->salvarCamposPost(array('selGrupoGaleriaPrompts', 'txtPalavrasPesquisaGaleriaPrompt', 'SelSinAtivo', 'optMeusPrompts'));

    switch ($_GET['acao']) {
        case 'md_ia_galeria_prompts_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaGaleriaPromptsDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
                    $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($arrStrIds[$i]);
                    $arrObjMdIaGaleriaPromptsDTO[] = $objMdIaGaleriaPromptsDTO;
                }

                $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
                $objMdIaGaleriaPromptsRN->excluir($arrObjMdIaGaleriaPromptsDTO);
                PaginaSEI::getInstance()->setStrMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . '&id_procedimento=' . $_GET['id_procedimento'] . '&resultado=1'));
            die;

        case 'md_ia_galeria_prompts_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                $arrObjMdIaGaleriaPromptsDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
                    $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($arrStrIds[$i]);
                    $objMdIaGaleriaPromptsDTO->setStrSinAtivo("N");
                    $objMdIaGaleriaPromptsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                    $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
                    $objMdIaGaleriaPromptsRN->alterar($objMdIaGaleriaPromptsDTO);
                }

                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_ia_galeria_prompts_reativar':
            $strTitulo = 'Reativar Prompt Publicado';
            if ($_GET['acao_confirmada'] == 'sim') {
                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                    $objMdIaGaleriaPromptsDTO = array();
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
                        $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($arrStrIds[$i]);
                        $objMdIaGaleriaPromptsDTO->setStrSinAtivo("S");
                        $objMdIaGaleriaPromptsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                        $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
                        $objMdIaGaleriaPromptsRN->alterar($objMdIaGaleriaPromptsDTO);
                    }
                    PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
                die;
            }
            break;

        case 'md_ia_galeria_prompts_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Galeria de Prompts', 'Galeria de Prompts');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_ia_galeria_prompts_cadastrar') {
                if (isset($_GET['id_md_ia_galeria_prompts'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_ia_galeria_prompts']);
                }
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" name="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    $arrComandos[] = '<button type="button" accesskey="N" id="btnGrupoGaleriaPromptsNovo" value="Novo Prompt" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';


    $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
    $objMdIaGaleriaPromptsDTO->retNumIdMdIaGaleriaPrompts();
    $objMdIaGaleriaPromptsDTO->retNumIdMdIaGrupoGaleriaPrompt();
    $objMdIaGaleriaPromptsDTO->retStrDescricao();
    $objMdIaGaleriaPromptsDTO->retStrPrompt();
    $objMdIaGaleriaPromptsDTO->retDthAlteracao();
    $objMdIaGaleriaPromptsDTO->retStrNomeGrupo();
    $objMdIaGaleriaPromptsDTO->retStrSiglaUsuario();
    $objMdIaGaleriaPromptsDTO->retStrSiglaUnidade();
    $objMdIaGaleriaPromptsDTO->retStrDescricaoUnidade();
    $objMdIaGaleriaPromptsDTO->retNumIdUnidade();
    $objMdIaGaleriaPromptsDTO->retStrNomeUsuario();
    $objMdIaGaleriaPromptsDTO->retStrSinAtivo();

    if (isset($_GET['id_md_ia_grupo_galeria_prompt'])) {
        $numIdMdIaGrupoGaleriaPrompt = ($_GET['id_md_ia_grupo_galeria_prompt'] == '-1' ? 'null' : $_GET['id_md_ia_grupo_galeria_prompt']);
        PaginaSEI::getInstance()->salvarCampo('selGrupoGaleriaPrompts', $numIdMdIaGrupoGaleriaPrompt);
    } elseif (isset($_POST['selGrupoGaleriaPrompts'])) {
        $numIdMdIaGrupoGaleriaPrompt = $_POST['selGrupoGaleriaPrompts'];
        PaginaSEI::getInstance()->salvarCampo('selGrupoGaleriaPrompts', $numIdMdIaGrupoGaleriaPrompt);
    } else {
        $numIdMdIaGrupoGaleriaPrompt = PaginaSEI::getInstance()->recuperarCampo('selGrupoGaleriaPrompts', '');
    }
    $strPalavrasPesquisa = $_POST['txtPalavrasPesquisaGaleriaPrompt'] ?? PaginaSEI::getInstance()->recuperarCampo('txtPalavrasPesquisaGaleriaPrompt');

    if (isset($_POST['SelSinAtivo'])) {
        $SelSinAtivo = $_POST['SelSinAtivo'];
        PaginaSEI::getInstance()->salvarCampo('SelSinAtivo', $SelSinAtivo);
    } else {
        $SelSinAtivo = PaginaSEI::getInstance()->recuperarCampo('SelSinAtivo', '');
    }

    if (isset($_POST['optMeusPrompts'])) {
        $optMeusPrompts = $_POST['optMeusPrompts'];
        PaginaSEI::getInstance()->salvarCampo('optMeusPrompts', $optMeusPrompts);
    }

    if ($strPalavrasPesquisa != '') {
        $strPesquisa = '%' . $strPalavrasPesquisa . '%';
        $objMdIaGaleriaPromptsDTO->adicionarCriterio(
            array('Descricao', 'Prompt'),
            array(InfraDTO::$OPER_LIKE, InfraDTO::$OPER_LIKE),
            array($strPesquisa, $strPesquisa),
            array(InfraDTO::$OPER_LOGICO_OR)
        );
    }

    if ($numIdMdIaGrupoGaleriaPrompt > 0) {
        $objMdIaGaleriaPromptsDTO->setNumIdMdIaGrupoGaleriaPrompt($numIdMdIaGrupoGaleriaPrompt);
    }
    if ($SelSinAtivo == "") {
        $SelSinAtivo = "S";
    }
    $objMdIaGaleriaPromptsDTO->setStrSinAtivo($SelSinAtivo);

    if ($optMeusPrompts == 'S') {
        $objMdIaGaleriaPromptsDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
    }

    if (PaginaSEI::GET('acao') === 'md_ia_galeria_prompts_reativar') {
        //Lista somente inativos
        $objMdIaGaleriaPromptsDTO->setBolExclusaoLogica(false);
        $objMdIaGaleriaPromptsDTO->setStrSinAtivo('N');
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaGaleriaPromptsDTO, 'IdMdIaGaleriaPrompts', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdIaGaleriaPromptsDTO);

    $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
    $arrObjMdIaGaleriaPromptsDTO = $objMdIaGaleriaPromptsRN->listar($objMdIaGaleriaPromptsDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdIaGaleriaPromptsDTO);
    $numRegistros = count($arrObjMdIaGaleriaPromptsDTO);

    if ($numRegistros > 0) {

        $bolPermiteEdicao = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar');

        $bolAcaoModerador = SessaoSEI::getInstance()->verificarPermissao('md_ia_galeria_prompt_moderador');

        $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_galeria_prompt_excluir');
        $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_galeria_prompt_desativar');
        $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_galeria_prompt_reativar');

        if ($bolAcaoExcluir) {
            //$arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_excluir&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            //$arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_desativar&acao_origem=' . PaginaSEI::GET('acao'));
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
            //*$arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_reativar&acao_origem=' . PaginaSEI::GET('acao') . '&acao_confirmada=sim');
        }

        $strResultado = '';

        $strSumarioTabela = 'Galeria de Prompts';
        if ($SelSinAtivo != 'N') {
            $strCaptionTabela = 'Prompts Publicados';
        } else {
            $strCaptionTabela = 'Prompts Publicados Inativos';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck('', 'Infra') . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaGaleriaPromptsDTO, 'Grupo', 'NomeGrupo', $arrObjMdIaGaleriaPromptsDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaGaleriaPromptsDTO, 'Descrição do Prompt', 'DescricaoPrompt', $arrObjMdIaGaleriaPromptsDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%">Prompt</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="7%">Usuário</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="5%">Unidade</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaGaleriaPromptsDTO, 'Data', 'Alteracao', $arrObjMdIaGaleriaPromptsDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="12%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
    }

    $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="fecharModal();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

    $strItensSelGrupoGaleriaPrompts = MdIaGrupoGaleriaPromptINT::montarSelectGrupoGaleriaPrompt('Todos', 'Todos', $numIdMdIaGrupoGaleriaPrompt);
    $strItensSelAtivos = MdIaGrupoGaleriaPromptINT::montarSelectSinAtivo('S', 'Ativos', $SelSinAtivo);

    for ($i = 0; $i < $numRegistros; $i++) {

        if (($arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdUnidade() == SessaoSEI::getInstance()->getNumIdUnidadeAtual() || $bolAcaoModerador) && $bolPermiteEdicao) {
            $bolPermiteEdicao = true;
        } else {
            $bolPermiteEdicao = false;
        }

        if ($arrObjMdIaGaleriaPromptsDTO[$i]->getStrSinAtivo() == 'S') {
            $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
        } else {
            $strCssTr = '<tr class="trVermelha">';
        }
        $strResultado .= $strCssTr;

        $strResultado .= '<td valign="top" class="tdInteracao">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdMdIaGaleriaPrompts(), $arrObjMdIaGaleriaPromptsDTO[$i]->getStrDescricao()) . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . $arrObjMdIaGaleriaPromptsDTO[$i]->getStrNomeGrupo() . '</td>';
        $strResultado .= '<td align="left" valign="top" class="tdInteracao">' . nl2br(htmlspecialchars(mb_strimwidth($arrObjMdIaGaleriaPromptsDTO[$i]->getStrDescricao(), 0, 500, "...", "ISO-8859-1"), ENT_QUOTES | ENT_SUBSTITUTE, 'ISO-8859-1', false)) . '</td>';
        $strResultado .= '<td align="left" valign="top" class="tdInteracao">' . nl2br(htmlspecialchars(mb_strimwidth($arrObjMdIaGaleriaPromptsDTO[$i]->getStrPrompt(), 0, 500, "...", "ISO-8859-1"), ENT_QUOTES | ENT_SUBSTITUTE, 'ISO-8859-1', false)) . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao"><a alt="' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrNomeUsuario()) . '" title="' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrNomeUsuario()) . '" class="ancoraSigla">' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrSiglaUsuario()) . '</a></td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao"><a alt="' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrDescricaoUnidade()) . '" title="' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrDescricaoUnidade()) . '" class="ancoraSigla">' . PaginaSEI::tratarHTML($arrObjMdIaGaleriaPromptsDTO[$i]->getStrSiglaUnidade()) . '</a></td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . $arrObjMdIaGaleriaPromptsDTO[$i]->getDthAlteracao() . '</td>';


        $strResultado .= '<td align="center" valign="top" class="tdInteracao tdAcompanhamentoUltima">';

        $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdMdIaGaleriaPrompts(), $arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdMdIaGaleriaPrompts());

        if ($bolPermiteEdicao) {
            $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_ia_galeria_prompts=' . $arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdMdIaGaleriaPrompts()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '" ><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Prompt Publicado" alt="Alterar Prompt Publicado" class="infraImg" /></a>&nbsp;';
            $strId = $arrObjMdIaGaleriaPromptsDTO[$i]->getNumIdMdIaGaleriaPrompts();
            $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdIaGaleriaPromptsDTO[$i]->getStrDescricao());
            if ($arrObjMdIaGaleriaPromptsDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeDesativar() . '" title="Desativar Prompt Publicado" alt="Desativar Prompt Publicado" class="infraImg" /></a>&nbsp;';
            } else {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeReativar() . '" title="Reativar Prompt Publicado" alt="Reativar Prompt Publicado" class="infraImg" /></a>&nbsp;';
            }
            $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');"  tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Prompt Publicado" alt="Excluir Prompt Publicado" class="infraImg" /></a>&nbsp;';
        }


        $strResultado .= '</td></tr>' . "\n";
    }
    $strResultado .= '</table>';
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

#lblSelGrupoGaleriaPrompts {position:absolute;left:0%;top:0%;}
#selGrupoGaleriaPrompts {position:absolute;left:0%;top:22%;width:25%;}

#lblSelSinAtivo {position:absolute;left:27%;top:0%;width:14%;}
#SelSinAtivo {position:absolute;left:27%;top:22%;width:14%;}

#lblPalavrasPesquisaGaleriaPrompt {position:absolute;left:43%;top:0%;width:57%;}
#txtPalavrasPesquisaGaleriaPrompt {position:absolute;left:43%;top:22%;width:57%;}

#lblMeusPrompts {position:absolute;left:0%;top:70%;width:34%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmGaleriaPrompts" method="post"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('8em');
    ?>
    <label id="lblSelGrupoGaleriaPrompts" for="selGrupoGaleriaPrompts" accesskey="G"
        class="infraLabelOpcional"><span class="infraTeclaAtalho">G</span>rupo:</label>
    <select id="selGrupoGaleriaPrompts" name="selGrupoGaleriaPrompts" onchange="this.form.submit();"
        class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
        <?= $strItensSelGrupoGaleriaPrompts ?>
    </select>

    <label id="lblSelSinAtivo" for="SelSinAtivo" accesskey="A"
        class="infraLabelOpcional"><span class="infraTeclaAtalho">S</span>ituação:</label>
    <select id="SelSinAtivo" name="SelSinAtivo" onchange="this.form.submit();"
        class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
        <?= $strItensSelAtivos ?>
    </select>

    <label id="lblPalavrasPesquisaGaleriaPrompt" for="txtPalavrasPesquisaGaleriaPrompt" accesskey=""
        class="infraLabelOpcional">Palavras-chave para pesquisa:</label>
    <input type="text" id="txtPalavrasPesquisaGaleriaPrompt" name="txtPalavrasPesquisaGaleriaPrompt"
        class="infraText" value="<?= PaginaSEI::tratarHTML($strPalavrasPesquisa) ?>"
        onkeypress="return tratarDigitacao(event);"
        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />

    <label id="lblMeusPrompts" class="infraLabelOpcional"> <input type="checkbox" id="optMeusPrompts"
            name="optMeusPrompts"
            onchange="this.form.submit();"
            value="S" <?php echo $_POST['optMeusPrompts'] == 'S' ? 'checked="checked"' : '' ?>
            class="infraCheckbox"> Meus Prompts Publicados</label>

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    PaginaSEI::getInstance()->montarAreaDebug();
    //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?php
require_once "md_ia_galeria_prompts_js.php";
require_once "md_ia_chat_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>