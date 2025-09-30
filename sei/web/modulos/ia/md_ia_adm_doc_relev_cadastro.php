<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
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

    PaginaSEI::getInstance()->verificarSelecao('md_ia_adm_doc_relev_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();

    $strDesabilitar = '';

    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_ia_adm_doc_relev_cadastrar':
            $strTitulo = 'Documentos Relevantes';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdIaAdmDocRelev" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($_POST['txtIdMdIaAdmDocRelev']);
            $objMdIaAdmDocRelevDTO->setNumIdSerie($_POST['txtIdSerie']);


            if (isset($_POST['sbmCadastrarMdIaAdmDocRelev'])) {
                try {
                    $arrTipoProcessoEspecifico = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnIdTpProcesso']);

                    $arrSegmentoDocumento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbPercRelevSegmento']);
                    if ($_POST['hdnIdTpProcesso'] != "") {
                        foreach ($arrTipoProcessoEspecifico as $tipoProcessoEspecifico) {
                            $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                            $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                            $objMdIaAdmDocRelevDTO->setNumIdSerie($_POST["selTipoDocumento"]);
                            $objMdIaAdmDocRelevDTO->setStrAplicabilidade($_POST["selAplicabilidade"]);
                            $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento($tipoProcessoEspecifico[0]);
                            $objMdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                            $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                            $objMdIaAdmDocRelevRN->cadastrar($objMdIaAdmDocRelevDTO);

                            $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
                            $objMdIaAdmSegDocRelevRN->cadastrarRelacionamento(array($arrSegmentoDocumento, $objMdIaAdmDocRelevDTO->getNumIdMdIaAdmDocRelev()));

                        }
                    } else {
                        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                        $objMdIaAdmDocRelevDTO->setNumIdSerie($_POST["selTipoDocumento"]);
                        $objMdIaAdmDocRelevDTO->setStrAplicabilidade($_POST["selAplicabilidade"]);
                        $objMdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                        $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                        $objMdIaAdmDocRelevRN->cadastrar($objMdIaAdmDocRelevDTO);

                        $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
                        $objMdIaAdmSegDocRelevRN->cadastrarRelacionamento(array($arrSegmentoDocumento, $objMdIaAdmDocRelevDTO->getNumIdMdIaAdmDocRelev()));
                    }

                    PaginaSEI::getInstance()->adicionarMensagem('Documento Relevante "' . $objMdIaAdmDocRelevDTO->getNumIdMdIaAdmDocRelev() . '" cadastrado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_adm_doc_relev_alterar':
            $strTitulo = 'Alterar Documento Relevante';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmDocRelev" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $alterar = 'disabled';

            if (isset($_POST['sbmAlterarMdIaAdmDocRelev'])) {
                try {
                    $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                    $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                    $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($_POST["hdnIdMdIaAdmDocRelev"]);
                    $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();

                    $objMdIaAdmDocRelevRN->consultar($objMdIaAdmDocRelevDTO);
                    $objMdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                    $objMdIaAdmDocRelevRN->alterar($objMdIaAdmDocRelevDTO);

                    $arrSegmentoDocumento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbPercRelevSegmento']);
                    $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
                    $objMdIaAdmSegDocRelevRN->excluirRelacionamento($_POST["hdnIdMdIaAdmDocRelev"]);
                    $objMdIaAdmSegDocRelevRN->cadastrarRelacionamento(array($arrSegmentoDocumento, $_POST["hdnIdMdIaAdmDocRelev"]));

                    PaginaSEI::getInstance()->adicionarMensagem('Documento Relevante "' . $_POST["hdnIdMdIaAdmDocRelev"] . '" alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_adm_doc_relev_consultar':
            $strTitulo = 'Consultar Documento Relevante';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

            if ($objMdIaAdmDocRelevDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }
    if ($_GET['documento_relevante'] > 0) {
        $objMdIaAdmDocRelevDTO->setBolExclusaoLogica(false);
        $objMdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
        $objMdIaAdmDocRelevDTO->retStrAplicabilidade();
        $objMdIaAdmDocRelevDTO->retNumIdSerie();
        $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($_GET['documento_relevante']);
        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
        $objMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->consultar($objMdIaAdmDocRelevDTO);

        if ($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento() > 0) {
            $todosTiposProcessos = "";
            $processosEspecificos = "checked='checked'";
        } else {
            $todosTiposProcessos = "checked='checked'";
            $processosEspecificos = "";
        }

        $objMdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();

        $objMdIaAdmSegDocRelevDTO->retNumIdMdIaAdmSegDocRelev();
        $objMdIaAdmSegDocRelevDTO->retStrSegmentoDocumento();
        $objMdIaAdmSegDocRelevDTO->retNumPercentualRelevancia();
        $objMdIaAdmSegDocRelevDTO->retNumIdMdIaAdmSegDocRelev();
        $objMdIaAdmSegDocRelevDTO->retNumIdMdIaAdmDocRelev();

        $objMdIaAdmSegDocRelevDTO->setNumIdMdIaAdmDocRelev($_GET['documento_relevante']);
        $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
        $numRegistros = $objMdIaAdmSegDocRelevRN->contar($objMdIaAdmSegDocRelevDTO);
        $arrObjMdIaAdmSegDocRelevDTO = $objMdIaAdmSegDocRelevRN->listar($objMdIaAdmSegDocRelevDTO);
        $tabelaPercRelevMet = "";
        $somaPesosAdicionados = 0;
        $strSumarioTabela = 'Segmentos do Documento';
        $strCaptionTabela = 'Segmentos do Documento';
        foreach ($arrObjMdIaAdmSegDocRelevDTO as $objMdIaAdmSegDocRelevDTO) {
            $arrGrid[] = array($objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmSegDocRelev(), $objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento(), $objMdIaAdmSegDocRelevDTO->getNumPercentualRelevancia());
            $tabelaPercRelevMet .= "<tr id='" . $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmSegDocRelev() . "' peso_segmento='" . $objMdIaAdmSegDocRelevDTO->getNumPercentualRelevancia() . "' segmento='" . $objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento() . "'>";
            $tabelaPercRelevMet .= "<td>" . $objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento() . "</td>";
            $tabelaPercRelevMet .= "<td>" . $objMdIaAdmSegDocRelevDTO->getNumPercentualRelevancia() . "%</td>";
            $tabelaPercRelevMet .= "<td>";
            $tabelaPercRelevMet .= "<a onclick='editarPercRelevanciaSegmento(" . $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmSegDocRelev() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/alterar.svg' title='Alterar Percentual de Relevância do Segmento' alt='Alterar Percentual de Relevância do Segmento' class='infraImg' /></a>";
            $tabelaPercRelevMet .= "<a onclick='removerPercRelevanciaSegmento(" . $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmSegDocRelev() . ")'><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/excluir.svg' title='Excluir Percentual de Relevância do Segmento' alt='Excluir Percentual de Relevância do Segmento' class='infraImg' /></a>";
            $tabelaPercRelevMet .= "</td>";
            $tabelaPercRelevMet .= "</tr>";
            $somaPesosAdicionados += $objMdIaAdmSegDocRelevDTO->getNumPercentualRelevancia();
            $contadorSegmento = $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmSegDocRelev();
        }
        $strGridSegmentos = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrGrid);

        $strLupaTpProcesso = $objMdIaAdmDocRelevRN->montarArrTpProcesso($_GET['documento_relevante']);
        $arrLupaTpProcessoOrigin = PaginaSEI::getInstance()->getArrItensTabelaDinamica($strLupaTpProcesso);
        $aplicabilidade = $objMdIaAdmDocRelevDTO->getStrAplicabilidade();
        $idProcedimento = $objMdIaAdmDocRelevDTO->getNumIdSerie();
        $idMdIaAdmDocRelev = $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmDocRelev();

    } else {
        $contadorSegmento = 0;
        $strSumarioTabela = 'Segmentos do Documento';
        $strCaptionTabela = 'Segmentos do Documento';
    }
    //$strItensSelTipoProcedimento = InfraINT::montarSelectArrInfraDTO(null, null, null, $objBaseConhecimentoDTO->getArrObjRelBaseConhecTipoProcedDTO(), 'IdTipoProcedimento', 'NomeTipoProcedimento');

    $strLinkAjaxTipoProcedimento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_procedimento_auto_completar');
    $strLinkTipoProcedimentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=2&id_object=objLupaTipoProcedimento');


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
include_once('md_ia_adm_doc_relev_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdIaAdmDocRelevCadastro" method="post" onsubmit="return OnSubmitForm(event);"
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
            <div class="col-5">
                <div class="form-group">
                    <label id="lblAplicabilidade" for="selAplicabilidade" accesskey="o" class="infraLabelObrigatorio">Aplicabilidade:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Selecione "Interno" para abranger tipos de documentos Gerados no Editor do SEI, documentos Automáticos como de Correspondência Eletrônica e documentos Formulário.

Selecione "Externo" para abranger tipos de documentos Externos, ou seja, que teve o upload de arquivo.', 'Ajuda') ?>
                         class="infraImg" alt="Ícone de Ajuda"/>
                    <select class="infraSelect form-control" name="selAplicabilidade" id="selAplicabilidade"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"
                            onchange="retornaTiposDocumentos()" <?= $alterar ?>>
                        <option></option>
                        <option value="I" <?php if ($aplicabilidade == "I") {
                            echo "selected";
                        } ?>>Interno
                        </option>
                        <option value="E" <?php if ($aplicabilidade == "E") {
                            echo "selected";
                        } ?>>Externo
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label id="lblTipoDocumento" for="selTipoDocumento" accesskey="o" class="infraLabelObrigatorio">Tipo
                        de Documento:</label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escolha o Tipo de Documento de fato que será considerado Relevante para a funcionalidade de Processos Similares e outras funcionalidades do SEI IA.', 'Ajuda') ?>
                         class="infraImg" alt="Ícone de Ajuda"/>
                    <select class="infraSelect form-control" name="selTipoDocumento" id="selTipoDocumento"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" <?= $alterar ?>>
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
        <div id="segmentoDocumento" style="display: none; margin-bottom: 25px">
            <div class="row">
                <div class="col-5">
                    <div class="form-group">
                        <label id="lblSegmentoDocumento" for="selSegmentoDocumento" accesskey="o"
                               class="infraLabelOpcional">Segmento do
                            Documento:</label>
                        <img align="top"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda" <?= PaginaSEI::montarTitleTooltip('Apenas excepcionalmente para documentos MUITO relevantes que tenham segmentos bem definidos no modelo (por exemplo: "ASSUNTO", "RELATÓRIO" e "CONCLUSÃO"), é possível cadastrar o nome de cada segmento para indicar o seu percentual de relevância dentro do teor de tais documentos.

- Caso não seja encontrado o Segmento do Documento indicado ele será ignorado.
- Caso um segmento possa ser apresentado de várias formas, utilizar o caractere | ("PIPE") para representar o operador "OU" (por exemplo: conselheiro|relatorio).', 'Ajuda') ?>
                             class="infraImg" alt="Ícone de Ajuda"/>
                        <input type="text" id="txtSegmentoDocumento" name="txtSegmentoDocumento"
                               class="form-control infraText"
                               value=""
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                        />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label id="lblPercRelevSegmentoAdicionar" for="lblPercRelevSegmentoAdicionar" accesskey=""
                               class="infraLabelOpcional">Percentual
                            de Relevância:</label>
                        <img align="top"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda" <?= PaginaSEI::montarTitleTooltip('O total de segmentos adicionados não pode ultrapassar os 100%.

Caso não atinja os 100% os demais segmentos do documento serão considerados como um todo no percentual faltante. ', 'Ajuda') ?>
                             class="infraImg" alt="Ícone de Ajuda"/>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" id="txtPercRelevSegmentoAdicionar" name="txtPercRelevSegmentoAdicionar"
                                   onkeypress="return infraMascaraNumero(this, event)" class="form-control"
                                   value=""
                                   maxlength="3" min="1" max="100"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                   aria-describedby="basic-addon2"/>
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <button type="button" name="sbmAdicionarPercentualRelevanciaSegmento"
                            onclick="adicionarPercentualRelevanciaSegmento();"
                            id="sbmAdicionarPercentualRelevanciaSegmento"
                            value="Adicionar"
                            class="infraButton"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" style="margin-top:29px">
                        Adicionar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-10">
                    <table class="infraTable " id="tbPercRelevSegmento">
                        <caption class='infraCaption'> <?= PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) ?> </caption>
                        <thead>
                        <tr>
                            <th class="infraTh" width="80%">Segmento do Documento</th>
                            <th class="infraTh" width="15%">Percentual de Relevância</th>
                            <th class="infraTh" width="5%">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?= $tabelaPercRelevMet ?>
                        </tbody>
                    </table>
                    <input type="hidden" id="hdnTbPercRelevSegmento" name="hdnTbPercRelevSegmento"
                           value="<?= $strGridSegmentos ?>"/>
                    <input type="hidden" id="hdnPesoAdicionadoTabela" name="hdnPesoAdicionadoTabela"
                           value="<?= $somaPesosAdicionados ?>"/>
                    <input type="hidden" id="hdnCampoEdicao" name="hdnCampoEdicao"
                           value=""/>
                    <input type="hidden" id="hdnIdMdIaAdmDocRelev" name="hdnIdMdIaAdmDocRelev"
                           value="<?= $idMdIaAdmDocRelev ?>"/>
                    <input type="hidden" id="hdnContadorSegmento" name="hdnContadorSegmento"
                           value="<?= $contadorSegmento ?>"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10">
                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnRelevante" id="rdnRelevanteTodosProcessos"
                           value="0" class="infraRadio" <?= $todosTiposProcessos ?>
                           onchange="trocarTipoProcesso(this)" <?= $alterar ?>>
                    <label id="lblTodosProcessos" name="lblTodosProcessos" for="rdnRelevanteTodosProcessos"
                           class="infraLabelOpcional infraLabelRadio">Relevante para todos Tipos de Processos</label>
                </div>

                <div class="infraDivRadio">
                    <input type="radio" utlCampoObrigatorio="o" name="rdnRelevante"
                           id="rdnRelevanteProcessosEspecificos" value="1"
                           class="infraRadio" <?= $processosEspecificos ?>
                           onchange="trocarTipoProcesso(this)" <?= $alterar ?>>
                    <label id="lblProcessosEspecificos" name="lblProcessosEspecificos"
                           for="rdnRelevanteProcessosEspecificos"
                           class="infraLabelOpcional infraLabelRadio">Relevante apenas para Tipos de Processos
                        Específicos</label>
                </div>
            </div>
        </div>
        <div id="tipoProcessoEspecifico" style="display: none; margin-left: 29px;">
            <div class="row">
                <div class="col-xs-2 col-sm-10 col-md-6 col-lg-6">
                    <label id="lblTpProcesso" for="selTpProcesso" accesskey="" class="infraLabelObrigatorio"> Tipo de
                        Processo Específico:
                    </label>
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Este campo está habilitado apenas para cadastro novo e serve para facilitar o cadastro em lote do mesmo Tipo de Documento para diversos Tipos de Processos.', 'Ajuda') ?>
                         class="infraImg" alt="Ícone de Ajuda" />
                    <input type="text" id="txtTpProcesso" name="txtTpProcesso" class="infraText form-control"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $alterar ?> />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-10 col-md-10 col-lg-10">
                    <div class="input-group">
                        <select id="selTpProcesso" name="selTpProcesso" size="6" multiple="multiple"
                                class="infraSelect form-control"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $alterar ?>>
                            <?= $strItensSelTpProcesso ?>
                        </select>

                        <div id="_divOpcoesTpProcesso" class="ml-1">
                            <img id="imgLupaTpProcesso" onclick="objLupaTipoProcedimento.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/pesquisar.svg' ?>"
                                 alt="Selecionar Tipo Processo" title="Selecionar Tipo Processo" class="infraImg"/>
                            <br>
                            <img id="imgExcluirTpProcesso" onclick="objLupaTipoProcedimento.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/remover.svg' ?>"
                                 alt="Remover Tipo Processo Selecionado" title="Remover Tipo Processo Selecionado"
                                 class="infraImg"/>
                        </div>
                        <input type="hidden" id="hdnIdTpProcesso" name="hdnIdTpProcesso"
                               value="<?= $strLupaTpProcesso ?>"/>
                    </div>
                </div>
            </div>
        </div>
        <?
        //PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
require_once "md_ia_adm_doc_relev_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
