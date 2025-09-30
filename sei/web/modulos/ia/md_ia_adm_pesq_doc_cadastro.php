<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
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

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();

    $strDesabilitar = '';

    $arrComandos = array();


    $objEditorRN = new EditorRN();
    $objEditorDTO = new EditorDTO();

    $objEditorDTO->setStrNomeCampo('txtOrientacoesGerais');
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO->setStrSinEstilos('N');
    $objEditorDTO->setNumTamanhoEditor(220);
    $retEditor = $objEditorRN->montarSimples($objEditorDTO);

    switch ($_GET['acao']) {
        case 'md_ia_adm_pesq_doc_cadastro':
            $strTitulo = 'Pesquisa de Documentos';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmPesqDoc" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $objMdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc(1);
            $objMdIaAdmPesqDocDTO->retStrNomeSecao();
            $objMdIaAdmPesqDocDTO->retNumIdMdIaAdmPesqDoc();
            $objMdIaAdmPesqDocDTO->retStrOrientacoesGerais();
            $objMdIaAdmPesqDocDTO->retNumQtdProcessListagem();
            $objMdIaAdmPesqDocDTO->retStrSinExibirFuncionalidade();
            $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
            $objMdIaAdmPesqDocDTO = $objMdIaAdmPesqDocRN->consultar($objMdIaAdmPesqDocDTO);

            $selectTipoDocumento = MdIaAdmTpDocPesqINT::montarSelectTipoDocumento(0, "Selecione uma opção:", 0);

            if ($objMdIaAdmPesqDocDTO == null) {
                throw new InfraException("Registro não encontrado.");
            }
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();

            $objMdIaAdmTpDocPesqDTO->retNumIdSerie();
            $objMdIaAdmTpDocPesqDTO->retStrSinAtivo();
            $objMdIaAdmTpDocPesqDTO->retStrSinAtivo();
            $objMdIaAdmTpDocPesqDTO->retNumIdSerie();
            $objMdIaAdmTpDocPesqDTO->retStrNomeSerie();
            $objMdIaAdmTpDocPesqDTO->retDthAlteracao();
            $objMdIaAdmTpDocPesqDTO->retStrSinAtivo();
            $objMdIaAdmTpDocPesqDTO->retNumIdSerie();
            $objMdIaAdmTpDocPesqDTO->retNumIdSerie();

            $objMdIaAdmTpDocPesqDTO->retStrNomeSerie();
            $objMdIaAdmTpDocPesqDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdIaAdmTpDocPesqRN = new MdIaAdmTpDocPesqRN();
            $numRegistros = $objMdIaAdmTpDocPesqRN->contar($objMdIaAdmTpDocPesqDTO);
            $arrObjMdIaAdmTpDocPesqDTO = $objMdIaAdmTpDocPesqRN->listar($objMdIaAdmTpDocPesqDTO);
            $tabelaTipoDocumento = "";
            $strSumarioTabela = 'Tipos de Documentos alvo da Pesquisa';
            $strCaptionTabela = 'Tipos de Documentos alvo da Pesquisa';
            foreach ($arrObjMdIaAdmTpDocPesqDTO as $objMdIaAdmTpDocPesqDTO) {
                $arrGrid[] = array($objMdIaAdmTpDocPesqDTO->getNumIdSerie(), $objMdIaAdmTpDocPesqDTO->getStrSinAtivo());
                if ($objMdIaAdmTpDocPesqDTO->getStrSinAtivo() == 'S') {
                    $strCssTr = '<tr id="' . $objMdIaAdmTpDocPesqDTO->getNumIdSerie() . '">';
                } else {
                    $strCssTr = '<tr class="trVermelha"  id="' . $objMdIaAdmTpDocPesqDTO->getNumIdSerie() . '">';
                }
                $tabelaTipoDocumento .= $strCssTr;
                $tabelaTipoDocumento .= "<td>" . $objMdIaAdmTpDocPesqDTO->getStrNomeSerie() . "</td>";
                $tabelaTipoDocumento .= "<td>" . $objMdIaAdmTpDocPesqDTO->getDthAlteracao() . "</td>";
                $tabelaTipoDocumento .= "<td>";
                if ($objMdIaAdmTpDocPesqDTO->getStrSinAtivo() == "S") {
                    $tabelaTipoDocumento .= "<a onclick='desativarTipoDocumento(" . $objMdIaAdmTpDocPesqDTO->getNumIdSerie() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/desativar.svg' title='Desativar Tipo Documento' alt='Desativar Tipo Documento' class='infraImg' /></a>";
                } else {
                    $tabelaTipoDocumento .= "<a onclick='ativarTipoDocumento(" . $objMdIaAdmTpDocPesqDTO->getNumIdSerie() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/reativar.svg' title='Reativar Tipo Documento' alt='Reativar Tipo Documento' class='infraImg' /></a>";
                }
                $tabelaTipoDocumento .= "</td>";
                $tabelaTipoDocumento .= "</tr>";
            }
            $strGridTipoDocumento = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrGrid);

            if ($objMdIaAdmPesqDocDTO->getStrSinExibirFuncionalidade() == "S") {
                $exibirFuncionalidade = "checked='checked'";
                $naoExibirFuncionalidade = "";
            } else {
                $exibirFuncionalidade = "";
                $naoExibirFuncionalidade = "checked='checked'";
            }


            if (isset($_POST['sbmAlterarMdIaAdmPesqDoc'])) {
                try {
                    $objMdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc($_POST['hdnIdMdIaAdmPesqDoc']);
                    $objMdIaAdmPesqDocDTO->setNumQtdProcessListagem($_POST['txtQtdProcessListagem']);
                    $objMdIaAdmPesqDocDTO->setStrOrientacoesGerais($_POST['txtOrientacoesGerais']);
                    $objMdIaAdmPesqDocDTO->setStrNomeSecao($_POST['txtNomeSecao']);
                    $objMdIaAdmPesqDocDTO->setStrSinExibirFuncionalidade($_POST["rdnExibirFuncionalidade"]);
                    $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
                    $objMdIaAdmPesqDocRN->alterar($objMdIaAdmPesqDocDTO);

                    $arrtbTipoDocumento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbTipoDocumento']);

                    if ($strGridTipoDocumento != $_POST['hdnTbTipoDocumento']) {
                        $objMdIaAdmTpDocPesqRN = new MdIaAdmTpDocPesqRN();
                        $objMdIaAdmTpDocPesqRN->excluirRelacionamento($objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc());
                        $objMdIaAdmTpDocPesqRN->cadastrarRelacionamento(array($arrtbTipoDocumento, $objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc()));
                    }

                    PaginaSEI::getInstance()->adicionarMensagem('Pesquisa de Documentos "' . $objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc() . '" alterada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao']));
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
?>
<link rel="stylesheet" type="text/css" href="modulos/ia/css/md_ia_comum.css" />
<?php
PaginaSEI::getInstance()->abrirStyle();
include_once('md_ia_adm_pesq_doc_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdIaAdmPesqDocCadastro" method="post" onsubmit="return OnSubmitForm();"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('auto');
    ?>
    <div id="divMsg">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row">
        <div class="col-10">
            <label id="lblExibirFuncionalidade" for="txtExibirFuncionalidade" accesskey=""
                class="infraLabelObrigatorio">Exibir Funcionalidade:</label>
            <img align="top" alt="Ícone de Ajuda"
                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                name="ajuda" <?= PaginaSEI::montarTitleTooltip('A funcionalidade de "Processos Similares" somente será exibida para os usuários se selecionada a opção "Exibir".', 'Ajuda') ?>
                class="infraImg" />
            <div class="infraDivRadio">
                <input type="radio" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade" id="exibirFuncionalidade"
                    value="S" class="infraRadio" <?= $exibirFuncionalidade ?>>
                <label id="lblTodosProcessos" name="lblTodosProcessos" for="exibirFuncionalidade"
                    class="infraLabelOpcional infraLabelRadio">Exibir</label>
            </div>

            <div class="infraDivRadio">
                <input type="radio" utlCampoObrigatorio="o" name="rdnExibirFuncionalidade" value="N" id="naoExibirFuncionalidade"
                    class="infraRadio" <?= $naoExibirFuncionalidade ?>>
                <label id="lblProcessosEspecificos" name="lblProcessosEspecificos"
                    for="naoExibirFuncionalidade"
                    class="infraLabelOpcional infraLabelRadio">Não Exibir</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-5 col-lg-6 col-md-7 col-sm-12 col-xs-12">
            <div class="form-group">
                <label id="lblNomeSecao" for="txtNomeSecao" accesskey="" class="infraLabelObrigatorio">Nome da Seção
                    na Tela do
                    Usuário:</label>
                <img align="top"
                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Configura o nome da seção da funcionalidade "Pesquisa de Documentos" na tela do usuário no "SEI IA" sobre os processos.
- Em alguns órgãos pode haver preferência para outro nome para a funcionalidade, como "Pesquisa de Jurisprudência".

Atenção que esta funcionalidade NÃO tem por objetivo substituir a pesquisa tradicional do SEI e muito menos NÃO deve indicar muitos Tipos de Documentos Alvo da Pesquisa.', 'Ajuda') ?>
                    class="infraImg" />
                <input type="text" id="txtNomeSecao" name="txtNomeSecao" class="infraText form-control"
                    value="<?= PaginaSEI::tratarHTML($objMdIaAdmPesqDocDTO->getStrNomeSecao()); ?>"
                    onkeypress="return infraMascaraTexto(this,event,100);" maxlength="100"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-5 col-lg-6 col-md-7 col-sm-12 col-xs-12">
            <div class="form-group">
                <label id="lblQtdProcessListagem" for="txtQtdProcessListagem" accesskey=""
                    class="infraLabelObrigatorio">Resultados a serem listados:</label>
                <img align="top"
                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Configura a quantidade de documentos a serem listados para o usuário na modal de resultado da pesquisa, sendo o mínimo 1 e o máximo 15. O valor padrão é 5.
Deve se evitar utilizar o retorno acima de 10 resultados, pois comprometerá o desempenho do processamento e retorno resultado da pesquisa.', 'Ajuda') ?>
                    class="infraImg" />
                <input type="number" id="txtQtdProcessListagem" name="txtQtdProcessListagem"
                    onkeypress="return infraMascaraNumero(this, event)" class="infraText form-control" min="1" max="15"
                    value="<?= PaginaSEI::tratarHTML($objMdIaAdmPesqDocDTO->getNumQtdProcessListagem()); ?>"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
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
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orientações descritas abaixo serão exibidas na tela do SEI IA na seção da funcionalidade "Pesquisa de Documentos".', 'Ajuda') ?>
                    class="infraImg" />
                <div id="divEditores" style="overflow: auto;">
                    <textarea id="txtOrientacoesGerais" name="txtOrientacoesGerais"
                        rows="<?= PaginaSEI::getInstance()->isBolNavegadorFirefox() ? '3' : '4' ?>"
                        class="infraTextarea"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmPesqDocDTO->getStrOrientacoesGerais()); ?></textarea>
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
                <legend class="infraLegend">Tipos de Documentos Alvo da Pesquisa</legend>
                <div class="row">
                    <div class="col-12 col-sm-9 col-md-9 col-lg-7 col-xl-6">
                        <div class="form-group">
                            <label id="lblMetadado" for="txtMetadado" accesskey="o" class="infraLabelObrigatorio">Tipo
                                de Documento:</label>
                            <img align="top" alt="Ícone de Ajuda"
                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('Apresenta Tipos de Documentos apenas Gerados, que possuem aplicabilidade na Administração de Tipos de Documentos como "Interno" e "Interno e Externo". Apenas documentos gerados no SEI para esta funcionalidade pelo SEI IA.

Atenção que esta funcionalidade NÃO tem por objetivo substituir a pesquisa tradicional do SEI e muito menos NÃO deve indicar muitos Tipos de Documentos Alvo da Pesquisa.
- Deve indicar Tipos de Documentos que decidem de forma relevante o resultado do mérito dos processos.', 'Ajuda') ?>
                                class="infraImg" />
                            <select class="infraSelect form-control" name="selTipoDocumento" id="selTipoDocumento"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" <?= $alterar ?>>
                                <?= $selectTipoDocumento ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 col-md-3 col-lg-2 col-xl-4">
                        <button type="button" name="sbmadicionarTipoDocumento"
                            onclick="adicionarTipoDocumento()"
                            id="sbmadicionarTipoDocumento"
                            value="Adicionar"
                            class="infraButton"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                            style="margin-top:30px">
                            Adicionar
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <table class="infraTable " id="tbTipoDocumento" width="100%">
                            <caption class='infraCaption'> <?= PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) ?> </caption>
                            <thead>
                                <tr>
                                    <th class="infraTh" width="65%">Tipo de Documento</th>
                                    <th class="infraTh" width="15%">Data/Hora da Última Alteração</th>
                                    <th class="infraTh" width="5%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $tabelaTipoDocumento ?>
                            </tbody>
                        </table>
                        <input type="hidden" id="hdnTbTipoDocumento" name="hdnTbTipoDocumento"
                            value="<?= $strGridTipoDocumento ?>" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <input type="hidden" id="hdnIdMdIaAdmPesqDoc" name="hdnIdMdIaAdmPesqDoc"
        value="<?= $objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc(); ?>" />
    <?
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?
require_once "md_ia_adm_pesq_doc_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
