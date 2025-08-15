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

    $numIdMdIaPromptsFavoritos = '';
    if (isset($_GET['id_md_ia_prompts_favoritos'])) {
        $numIdMdIaPromptsFavoritos = $_GET['id_md_ia_prompts_favoritos'];
    } else {
        $numIdMdIaPromptsFavoritos = $_POST['hdnIdMdIaPromptsFavoritos'];
    }

    $numIdMdIaGrupoPromptsFav = '';
    if (isset($_GET['id_md_ia_grupo_prompts_fav'])) {
        $numIdMdIaGrupoPromptsFav = $_GET['id_md_ia_grupo_prompts_fav'];
    } else {
        $numIdMdIaGrupoPromptsFav = $_POST['selGrupoPromptsFav'];
    }

    $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();

    $strDesabilitar = '';

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    switch ($_GET['acao']) {
        case 'md_ia_prompts_favoritos_cadastrar':

            $strTitulo = 'Novo Prompt Favorito';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarPromptFavorito" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_selecionar&tipo_selecao=2&acao_origem=' . $_GET['acao']). '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmCadastrarPromptFavorito'])) {
                try {
                    $objMdIaPromptsFavoritosDTO->setNumIdMdIaGrupoPromptsFav($_POST["selGrupoPromptsFav"]);
                    $objMdIaPromptsFavoritosDTO->setStrDescricaoPrompt($_POST["txaDescricaoPrompt"]);
                    $objMdIaPromptsFavoritosDTO->setStrPrompt($_POST["txaPrompt"]);
                    $objMdIaPromptsFavoritosDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                    $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
                    $arrObjMdIaInteracaoChatDTO = $objMdIaPromptsFavoritosRN->cadastrar($objMdIaPromptsFavoritosDTO);
                    PaginaSEI::getInstance()->setStrMensagem('Prompt Favorito "' . $objMdIaPromptsFavoritosDTO->getNumIdMdIaPromptsFavoritos() . '" cadastrado com sucesso.');

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_selecionar&tipo_selecao=2&id_md_ia_grupo_prompts_fav=' . $numIdMdIaGrupoPromptsFav));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_prompts_favoritos_alterar':

            $strTitulo = 'Alterar Prompt Favorito';

            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarPromptFavorito" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_selecionar&tipo_selecao=2&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaPromptsFavoritosDTO->setNumIdMdIaPromptsFavoritos($numIdMdIaPromptsFavoritos);
            $objMdIaPromptsFavoritosDTO->retNumIdMdIaGrupoPromptsFav();
            $objMdIaPromptsFavoritosDTO->retStrDescricaoPrompt();
            $objMdIaPromptsFavoritosDTO->retStrPrompt();
            $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
            $objMdIaPromptsFavoritosDTO = $objMdIaPromptsFavoritosRN->consultar($objMdIaPromptsFavoritosDTO);

            if ($objMdIaPromptsFavoritosDTO) {
                $numIdMdIaGrupoPromptsFav = $objMdIaPromptsFavoritosDTO->getNumIdMdIaGrupoPromptsFav();
                $descricaoPrompt = $objMdIaPromptsFavoritosDTO->getStrDescricaoPrompt();
                $prompt = $objMdIaPromptsFavoritosDTO->getStrPrompt();
            }

            if (isset($_POST['sbmAlterarPromptFavorito'])) {
                try {
                    $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();
                    $objMdIaPromptsFavoritosDTO->setNumIdMdIaPromptsFavoritos($numIdMdIaPromptsFavoritos);
                    $objMdIaPromptsFavoritosDTO->setNumIdMdIaGrupoPromptsFav($_POST["selGrupoPromptsFav"]);
                    $objMdIaPromptsFavoritosDTO->setStrDescricaoPrompt($_POST["txaDescricaoPrompt"]);
                    $objMdIaPromptsFavoritosDTO->setStrPrompt($_POST["txaPrompt"]);
                    $objMdIaPromptsFavoritosDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                    $objMdIaPromptsFavoritosRN->alterar($objMdIaPromptsFavoritosDTO);
                    PaginaSEI::getInstance()->setStrMensagem('Prompt Favorito "' . $objMdIaPromptsFavoritosDTO->getNumIdMdIaPromptsFavoritos() . '" alterado com sucesso.');

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_selecionar&tipo_selecao=2&id_md_ia_grupo_prompts_fav=' . $numIdMdIaGrupoPromptsFav));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


    $strItensSelGrupoFavoritos = MdIaGrupoPromptsFavINT::montarSelectGrupoPromptsFav('&nbsp;', 'Selecione uma opção', $numIdMdIaGrupoPromptsFav, SessaoSEI::getInstance()->getNumIdUnidadeAtual());
    $strImgNovoGrupoPromptsFav = '<img id="imgNovoGrupoPromptsFav" onclick="cadastrarGrupoPromptsFav();" src="' . PaginaSEI::getInstance()->getIconeMais() . '" alt="Novo Grupo de Prompts Favoritos" title="Novo Grupo de Prompts Favoritos" class="infraImg" tabindex="' . PaginaSEI::getInstance()->getProxTabDados() . '"/>';
    $strLinkNovoGrupoPromptsFav = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_grupo_prompts_fav_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&pagina_simples=1');

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
    #lblselGrupoPromptsFav {position:absolute;left:0%;top:0%;width:50%;}
    #selGrupoPromptsFav {position:absolute;left:0%;top:10%;width:50%;}
    #imgNovoGrupoPromptsFav {position:absolute;left:50.5%;top:11%;}

    #lblDescricaoPrompt {position:absolute;left:0%;top:25%;width:95%;}
    #txaDescricaoPrompt {position:absolute;left:0%;top:35%;width:95%;}
    #frmNovoPromptFavorito {display: none;}
    
    #lblPrompt {position:absolute;left:0%;top:77%;width:95%;}
    #txaPrompt {position:absolute;left:0%;top:87%;width:95%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
    <form id="frmNovoPromptFavorito" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        //PaginaSEI::getInstance()->montarAreaValidacao();
        PaginaSEI::getInstance()->abrirAreaDados('20em');
        ?>
        <div class="form-group">
            <label id="lblselGrupoPromptsFav" for="selGrupoPromptsFav" accesskey="G" class="infraLabelObrigatorio"><span
                        class="infraTeclaAtalho">G</span>rupo:</label>
            <select id="selGrupoPromptsFav" name="selGrupoPromptsFav" class="infraSelect"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" required>
                <?= $strItensSelGrupoFavoritos ?>
            </select>
            <?= $strImgNovoGrupoPromptsFav ?>
        </div>
        <div class="form-group">
            <label id="lblDescricaoPrompt" for="txaDescricaoPrompt" accesskey="O" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">D</span>escrição Prompt:</label>
            <textarea rows="4" required name="txaDescricaoPrompt" id="txaDescricaoPrompt" class="infraTextarea" maxlength="250" onpaste="return infraLimitarTexto(this,event,250);" onkeypress="return infraLimitarTexto(this,event,250);" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($descricaoPrompt);?></textarea>
        </div>
        <div class="form-group">
            <label id="lblPrompt" for="txaPrompt" accesskey="P" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">P</span>rompt:</label>
            <textarea rows="17" required name="txaPrompt" id="txaPrompt" class="infraTextarea" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= $prompt ?></textarea>
        </div>
        <input type="hidden" id="hdnIdMdIaPromptsFavoritos" name="hdnIdMdIaPromptsFavoritos"  value="<?= $numIdMdIaPromptsFavoritos ?>"/>

        <?

        PaginaSEI::getInstance()->fecharAreaDados();
        //PaginaSEI::getInstance()->montarAreaDebug();
        //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?php
require_once "md_ia_prompts_favoritos_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>