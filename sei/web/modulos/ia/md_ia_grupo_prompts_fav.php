<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 22/10/2024 - criado por sabino.colab
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

    PaginaSEI::getInstance()->salvarCamposPost(array('selUnidade'));

    switch ($_GET['acao']) {
        case 'md_ia_grupo_prompts_fav_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $objMdIaGrupoPromptsFavDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaGrupoPromptsFavDTO = new MdIaGrupoPromptsFavDTO();
                    $objMdIaGrupoPromptsFavDTO->setNumIdMdIaGrupoPromptsFav($arrStrIds[$i]);
                    $arrobjMdIaGrupoPromptsFavDTO[] = $objMdIaGrupoPromptsFavDTO;
                }
                $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
                $objMdIaGrupoPromptsFavRN->excluir($arrobjMdIaGrupoPromptsFavDTO);
                PaginaSEI::getInstance()->setStrMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_listar&acao_origem=md_ia_prompts_favoritos_selecionar&acao_retorno=md_ia_prompts_favoritos_listar&tipo_selecao=2'));
            die;

        case 'md_ia_grupo_prompts_fav_listar':
            $strTitulo = 'Grupos de Prompts Favoritos';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_config_assist_ia_consultar');
    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }

    $objMdIaGrupoPromptsFavDTO = new MdIaGrupoPromptsFavDTO();
    $objMdIaGrupoPromptsFavDTO->retNumIdMdIaGrupoPromptsFav();
    $objMdIaGrupoPromptsFavDTO->retStrNomeGrupo();
    $objMdIaGrupoPromptsFavDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
    $objMdIaGrupoPromptsFavDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
    $numIdUnidade = PaginaSEI::getInstance()->recuperarCampo('selUnidade');
    if ($numIdUnidade !== '') {
        $objMdIaGrupoPromptsFavDTO->setNumIdUnidade($numIdUnidade);
    }


    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaGrupoPromptsFavDTO, 'NomeGrupo', InfraDTO::$TIPO_ORDENACAO_ASC);
    //PaginaSEI::getInstance()->prepararPaginacao($arrobjMdIaGrupoPromptsFavDTO);

    $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
    $arrobjMdIaGrupoPromptsFavDTO = $objMdIaGrupoPromptsFavRN->listar($objMdIaGrupoPromptsFavDTO);

    //PaginaSEI::getInstance()->processarPaginacao($arrobjMdIaGrupoPromptsFavDTO);
    $numRegistros = count($arrobjMdIaGrupoPromptsFavDTO);

    if ($numRegistros > 0) {

        $bolCheck = true;
        $bolAcaoAlterar = true;
        $bolAcaoExcluir = true;

        if ($bolAcaoExcluir) {
            $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_excluir&acao_origem=' . $_GET['acao']);
        }

        $strResultado = '';

        $strSumarioTabela = 'Tabela de Grupos de Prompts Favoritos.';
        $strCaptionTabela = 'Grupos de Prompts Favororitos';

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaGrupoPromptsFavDTO, 'Nome Grupo', 'NomeGrupo', $arrobjMdIaGrupoPromptsFavDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="10%" >Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrobjMdIaGrupoPromptsFavDTO[$i]->getNumIdMdIaGrupoPromptsFav(), $arrobjMdIaGrupoPromptsFavDTO[$i]->getStrNomeGrupo()) . '</td>';
            }
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrobjMdIaGrupoPromptsFavDTO[$i]->getStrNomeGrupo()) . '</td>';
            $strResultado .= '<td align="center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrobjMdIaGrupoPromptsFavDTO[$i]->getNumIdMdIaGrupoPromptsFav());

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_ia_grupo_prompts_fav=' . $arrobjMdIaGrupoPromptsFavDTO[$i]->getNumIdMdIaGrupoPromptsFav()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Grupo de Prompts Favoritos" alt="Alterar Grupo de Prompts Favoritos" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strId = $arrobjMdIaGrupoPromptsFavDTO[$i]->getNumIdMdIaGrupoPromptsFav();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrobjMdIaGrupoPromptsFavDTO[$i]->getStrNomeGrupo());
            }


            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Grupo de Prompts Favoritos" alt="Excluir Grupo de Prompts Favoritos" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

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
    #lblUnidade {position:absolute;left:0%;top:0%;width:25%;}
    #selUnidade {position:absolute;left:0%;top:40%;width:25%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmGrupoPrompsFavoritosLista" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?php
require_once "md_ia_grupo_prompts_fav_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>