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

    PaginaSEI::getInstance()->verificarSelecao('md_ia_grupo_prompts_fav_selecionar');

    SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

    $objMdIaGrupoPromptsFavDTO = new MdIaGrupoPromptsFavDTO();

    $strDesabilitar = '';

    $arrComandos = array();

    //colocando a pagina sem menu e titulo inicial
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    $bolOk = false;

    switch ($_GET['acao']) {
        case 'md_ia_grupo_prompts_fav_cadastrar':
            $strTitulo = 'Novo Grupo de Prompts Favoritos ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarGrupoPromptsFavoritos" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

            $objMdIaGrupoPromptsFavDTO->setNumIdMdIaGrupoPromptsFav(null);
            $objMdIaGrupoPromptsFavDTO->setStrNomeGrupo($_POST['txtNomeGrupo']);
            $objMdIaGrupoPromptsFavDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objMdIaGrupoPromptsFavDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());

            if (isset($_POST['sbmCadastrarGrupoPromptsFavoritos'])) {
                try {
                    $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
                    $objMdIaGrupoPromptsFavDTO = $objMdIaGrupoPromptsFavRN->cadastrar($objMdIaGrupoPromptsFavDTO);

                    if (PaginaSEI::getInstance()->getAcaoRetorno() != 'md_ia_grupo_prompts_fav_listar') {
                        $bolOk = true;
                    } else {
                        PaginaSEI::getInstance()->setStrMensagem('Grupo de Prompts Favoritos "' . $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() . '" cadastrado com sucesso.');
                        header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_ia_grupo_prompts_fav=' . $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() . PaginaSEI::getInstance()->montarAncora($objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav())));
                        die;
                    }

                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_grupo_prompts_fav_alterar':
            $strTitulo = 'Alterar Grupo de Prompts Favoritos';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarGrupoPromptsFavoritos" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_ia_grupo_prompts_fav'])) {
                $objMdIaGrupoPromptsFavDTO->setNumIdMdIaGrupoPromptsFav($_GET['id_md_ia_grupo_prompts_fav']);
                $objMdIaGrupoPromptsFavDTO->retNumIdMdIaGrupoPromptsFav();
                $objMdIaGrupoPromptsFavDTO->retStrNomeGrupo();
                $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
                $objMdIaGrupoPromptsFavDTO = $objMdIaGrupoPromptsFavRN->consultar($objMdIaGrupoPromptsFavDTO);
                if ($objMdIaGrupoPromptsFavDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            } else {
                $objMdIaGrupoPromptsFavDTO->setNumIdMdIaGrupoPromptsFav($_POST['hdnIdGrupoPromptsFavoritos']);
                $objMdIaGrupoPromptsFavDTO->setStrNomeGrupo($_POST['txtNomeGrupo']);
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarGrupoPromptsFavoritos'])) {
                try {
                    $objMdIaGrupoPromptsFavRN = new MdIaGrupoPromptsFavRN();
                    $objMdIaGrupoPromptsFavDTO->setStrNomeGrupo($_POST['txtNomeGrupo']);
                    $objMdIaGrupoPromptsFavRN->alterar($objMdIaGrupoPromptsFavDTO);
                    PaginaSEI::getInstance()->setStrMensagem('Grupo de Prompts Favoritos "' . $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() . '" alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_ia_grupo_prompts_fav=' . $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() . PaginaSEI::getInstance()->montarAncora($objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav())));
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
?>
    #lblNomeGrupo {position:absolute;left:0%;top:0%;width:75%;}
    #txtNomeGrupo {position:absolute;left:0%;top:40%;width:75%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmGrupoPromptsFavoritosCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        //PaginaSEI::getInstance()->montarAreaValidacao();
        PaginaSEI::getInstance()->abrirAreaDados('5em');
        ?>
        <label id="lblNomeGrupo" for="txtNomeGrupo" accesskey="N" class="infraLabelObrigatorio"><span
                    class="infraTeclaAtalho">N</span>ome:</label>
        <input type="text" id="txtNomeGrupo" name="txtNomeGrupo" class="infraText"
               value="<?= PaginaSEI::tratarHTML($objMdIaGrupoPromptsFavDTO->getStrNomeGrupo()); ?>"
               onkeypress="return infraMascaraTexto(this,event,100);" maxlength="100"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

        <input type="hidden" id="hdnIdGrupoPromptsFavoritos" name="hdnIdGrupoPromptsFavoritos"
               value="<?= $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav(); ?>"/>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        //PaginaSEI::getInstance()->montarAreaDebug();
        //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?php
require_once "md_ia_grupo_prompts_fav_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>