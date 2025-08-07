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

    PaginaSEI::getInstance()->prepararSelecao('md_ia_prompts_favoritos_selecionar');

    SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

    PaginaSEI::getInstance()->salvarCamposPost(array('selGrupoFavorito', 'txtPalavrasPesquisaPromptFavorito'));

    switch ($_GET['acao']) {
        case 'md_ia_prompts_favoritos_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaPromptsFavoritosDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();
                    $objMdIaPromptsFavoritosDTO->setNumIdMdIaPromptsFavoritos($arrStrIds[$i]);
                    $arrObjMdIaPromptsFavoritosDTO[] = $objMdIaPromptsFavoritosDTO;
                }

                $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
                $objMdIaPromptsFavoritosRN->excluir($arrObjMdIaPromptsFavoritosDTO);
                PaginaSEI::getInstance()->setStrMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . '&id_procedimento=' . $_GET['id_procedimento'] . '&resultado=1'));
            die;


        case 'md_ia_prompts_favoritos_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Prompts Favoritos', 'Prompts Favoritos');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_ia_prompts_favoritos_cadastrar') {
                if (isset($_GET['id_md_ia_interacao_chat'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_ia_interacao_chat']);
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

    if (SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar')) {
        $arrComandos[] = '<button type="button" accesskey="L" id="btnGrupoPrompsFavoritosListar" value="Listar Grupos" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_listar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">L</span>istar Grupos</button>';
    }

    $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();
    $objMdIaPromptsFavoritosDTO->retNumIdMdIaPromptsFavoritos();
    $objMdIaPromptsFavoritosDTO->retNumIdMdIaInteracaoChat();
    $objMdIaPromptsFavoritosDTO->retStrDescricaoPrompt();
    $objMdIaPromptsFavoritosDTO->retStrPergunta();
    $objMdIaPromptsFavoritosDTO->retDthAlteracao();
    $objMdIaPromptsFavoritosDTO->retStrNomeGrupoFavorito();

    if (isset($_GET['id_md_ia_grupo_prompts_fav'])) {
        $numIdMdIaGrupoPromptsFav = ($_GET['id_md_ia_grupo_prompts_fav'] == '-1' ? 'null' : $_GET['id_md_ia_grupo_prompts_fav']);
        PaginaSEI::getInstance()->salvarCampo('selGrupoFavorito', $numIdMdIaGrupoPromptsFav);
    } elseif (isset($_POST['selGrupoPromptsFavoritos'])) {
        $numIdMdIaGrupoPromptsFav = $_POST['selGrupoPromptsFavoritos'];
        PaginaSEI::getInstance()->salvarCampo('selGrupoFavorito', $numIdMdIaGrupoPromptsFav);
    } else {
        $numIdMdIaGrupoPromptsFav = PaginaSEI::getInstance()->recuperarCampo('selGrupoFavorito', '');
    }
    $strPalavrasPesquisa = $_POST['txtPalavrasPesquisaPromptsFavoritos'] ?? PaginaSEI::getInstance()->recuperarCampo('txtPalavrasPesquisaPromptFavorito');

    if ($strPalavrasPesquisa != '') {
        $strPesquisa = '%' . $strPalavrasPesquisa . '%';
        $objMdIaPromptsFavoritosDTO->adicionarCriterio(
            array('DescricaoPrompt', 'Pergunta'),
            array(InfraDTO::$OPER_LIKE, InfraDTO::$OPER_LIKE),
            array($strPesquisa, $strPesquisa),
            array(InfraDTO::$OPER_LOGICO_OR)
        );
    }

    if ($numIdMdIaGrupoPromptsFav > 0) {
        $objMdIaPromptsFavoritosDTO->setNumIdMdIaGrupoPromptsFav($numIdMdIaGrupoPromptsFav);
    }

    $objMdIaPromptsFavoritosDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
    $objMdIaPromptsFavoritosDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaPromptsFavoritosDTO, 'IdMdIaInteracaoChat', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdIaPromptsFavoritosDTO);

    $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
    $arrObjMdIaPromptsFavoritosDTO = $objMdIaPromptsFavoritosRN->listar($objMdIaPromptsFavoritosDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdIaPromptsFavoritosDTO);
    $numRegistros = count($arrObjMdIaPromptsFavoritosDTO);

    if ($numRegistros > 0) {

        $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar');
        $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar');

        if ($bolAcaoExcluir) {
            $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_excluir&acao_origem=' . $_GET['acao']);
        }

        $strResultado = '';

        $strSumarioTabela = 'Tabela de Prompts Favoritos.';
        $strCaptionTabela = 'Prompts Favoritos';

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck('', 'Infra') . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaPromptsFavoritosDTO, 'Descrição do Prompt', 'DescricaoPrompt', $arrObjMdIaPromptsFavoritosDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaPromptsFavoritosDTO, 'Grupo', 'NomeGrupoFavorito', $arrObjMdIaPromptsFavoritosDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%">Prompt</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaPromptsFavoritosDTO, 'Data', 'Alteracao', $arrObjMdIaPromptsFavoritosDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
    }

    $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="fecharModal();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

    $strItensSelGrupoFavoritos = MdIaGrupoPromptsFavINT::montarSelectGrupoPromptsFav('Todos', 'Todos', $numIdMdIaGrupoPromptsFav);

    for ($i = 0; $i < $numRegistros; $i++) {

        $strCssTr = ($strCssTr == 'class="infraTrClara"') ? 'class="infraTrEscura"' : 'class="infraTrClara"';
        $strResultado .= '<tr ' . $strCssTr . '>';

        $strResultado .= '<td valign="top" class="tdInteracao">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaPromptsFavoritosDTO[$i]->getNumIdMdIaPromptsFavoritos(), $arrObjMdIaPromptsFavoritosDTO[$i]->getStrDescricaoPrompt()) . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . nl2br(htmlspecialchars(mb_strimwidth($arrObjMdIaPromptsFavoritosDTO[$i]->getStrDescricaoPrompt(), 0, 100, "...", "ISO-8859-1"), ENT_QUOTES | ENT_SUBSTITUTE, 'ISO-8859-1', false)) . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . $arrObjMdIaPromptsFavoritosDTO[$i]->getStrNomeGrupoFavorito() . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . nl2br(htmlspecialchars(mb_strimwidth($arrObjMdIaPromptsFavoritosDTO[$i]->getStrPergunta(), 0, 100, "...", "ISO-8859-1"), ENT_QUOTES | ENT_SUBSTITUTE, 'ISO-8859-1', false)) . '</td>';
        $strResultado .= '<td align="center" valign="top" class="tdInteracao">' . $arrObjMdIaPromptsFavoritosDTO[$i]->getDthAlteracao() . '</td>';


        $strResultado .= '<td align="center" valign="top" class="tdInteracao tdAcompanhamentoUltima">';

        $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($arrObjMdIaPromptsFavoritosDTO[$i]->getNumIdMdIaInteracaoChat(), $arrObjMdIaPromptsFavoritosDTO[$i]->getNumIdMdIaInteracaoChat());

        if ($bolAcaoAlterar) {
            $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_ia_interacao_chat=' . $arrObjMdIaPromptsFavoritosDTO[$i]->getNumIdMdIaInteracaoChat()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '" ><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Prompt Favorito" alt="Alterar Prompt Favorito" class="infraImg" /></a>&nbsp;';
        }

        if ($bolAcaoExcluir) {
            $strId = $arrObjMdIaPromptsFavoritosDTO[$i]->getNumIdMdIaPromptsFavoritos();
            $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdIaPromptsFavoritosDTO[$i]->getStrDescricaoPrompt());
        }

        if ($bolAcaoExcluir) {
            $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');"  tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Prompt Favorito" alt="Excluir Prompt Favorito" class="infraImg" /></a>&nbsp;';
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

#lblSelGrupoPromptsFavoritos {position:absolute;left:0%;top:0%;}
#selGrupoPromptsFavoritos {position:absolute;left:0%;top:18%;width:50%;}

#lblPalavrasPesquisaPromptsFavoritos {position:absolute;left:0%;top:50%;width:65%;}
#txtPalavrasPesquisaPromptsFavoritos {position:absolute;left:0%;top:68%;width:65%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmPromptsFavoritos" method="post"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('10em');
    ?>
    <label id="lblSelGrupoPromptsFavoritos" for="selGrupoPromptsFavoritos" accesskey="G"
        class="infraLabelOpcional"><span class="infraTeclaAtalho">G</span>rupo:</label>
    <select id="selGrupoPromptsFavoritos" name="selGrupoPromptsFavoritos" onchange="this.form.submit();"
        class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
        <?= $strItensSelGrupoFavoritos ?>
    </select>

    <label id="lblPalavrasPesquisaPromptsFavoritos" for="txtPalavrasPesquisaPromptsFavoritos" accesskey=""
        class="infraLabelOpcional">Palavras-chave para pesquisa:</label>
    <input type="text" id="txtPalavrasPesquisaPromptsFavoritos" name="txtPalavrasPesquisaPromptsFavoritos"
        class="infraText" value="<?= PaginaSEI::tratarHTML($strPalavrasPesquisa) ?>"
        onkeypress="return tratarDigitacao(event);"
        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    PaginaSEI::getInstance()->montarAreaDebug();
    //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?php
require_once "md_ia_prompts_favoritos_js.php";
require_once "md_ia_chat_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>