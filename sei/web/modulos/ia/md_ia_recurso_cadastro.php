<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
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

    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    PaginaSEI::getInstance()->verificarSelecao('md_ia_similaridade_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();

    $objProcedimentoRN = new ProcedimentoRN();

    $objMdIaAdmConfigSimilarDTO = new MdIaAdmConfigSimilarDTO();
    $objMdIaAdmConfigSimilarDTO->retStrSinExibirFuncionalidade();
    $objMdIaAdmConfigSimilarDTO->retStrOrientacoesGerais();
    $objMdIaAdmConfigSimilarDTO->retNumQtdProcessListagem();
    $objMdIaAdmConfigSimilarRN = new MdIaAdmConfigSimilarRN();
    $objMdIaAdmConfigSimilarDTO = $objMdIaAdmConfigSimilarRN->consultar($objMdIaAdmConfigSimilarDTO);

    $objMdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
    $objMdIaAdmPesqDocDTO->retNumIdMdIaAdmPesqDoc();
    $objMdIaAdmPesqDocDTO->retStrSinExibirFuncionalidade();
    $objMdIaAdmPesqDocDTO->retStrNomeSecao();
    $objMdIaAdmPesqDocDTO->retStrOrientacoesGerais();
    $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
    $objMdIaAdmPesqDocDTO = $objMdIaAdmPesqDocRN->consultar($objMdIaAdmPesqDocDTO);

    if ($objMdIaAdmPesqDocDTO) {
        $objMdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();
        $objMdIaAdmTpDocPesqDTO->retNumIdSerie();
        $objMdIaAdmTpDocPesqDTO->retStrNomeSerie();
        $objMdIaAdmTpDocPesqDTO->setNumIdMdIaAdmPesqDoc($objMdIaAdmPesqDocDTO->getNumIdMdIaAdmPesqDoc());
        $objMdIaAdmTpDocPesqRN = new MdIaAdmTpDocPesqRN();

        $arrObjMdIaAdmTpDocPesqDTO = $objMdIaAdmTpDocPesqRN->listar($objMdIaAdmTpDocPesqDTO);
    }

    $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
    $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
    $objMdIaAdmOdsOnuDTO->retNumIdMdIaAdmOdsOnu();
    $objMdIaAdmOdsOnuDTO->retStrOrientacoesGerais();
    $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
    $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

    if ($objMdIaAdmOdsOnuDTO) {
        $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
        $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
        $objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
        $objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
        $objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmOdsOnu($objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu());
        $objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();

        $arrObjMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->listar($objMdIaAdmObjetivoOdsDTO);
    }

    if ($_POST['hdnIdProcedimento'] != "") {
        $idProcedimento = $_POST['hdnIdProcedimento'];
    } else {
        $idProcedimento = $_GET['id_procedimento'];
    }

    $objSeiRN = new SeiRN();
    $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
    $objEntradaConsultarProcedimentoAPI->setIdProcedimento($idProcedimento);

    $objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

    $objMdIaRecursoRN = new MdIaRecursoRN();
    $itensSimilares = $objMdIaRecursoRN->conexaoApiRecomendacaoSimilaridade(array($objMdIaAdmConfigSimilarDTO, $objSaidaConsultarProcedimentoAPI));

    if ($objMdIaAdmConfigSimilarDTO == null) {
        throw new InfraException("Parâmetros das configurações similares não cadastrados.");
    }

    $strLinkDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_protocolos_selecionar&tipo_selecao=2&id_procedimento=' . $_GET["id_procedimento"] . '&id_object=objLupaDocumento');

    $strLinkPesquisaDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_resultado_pesquisa_documento');

    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $exibirRacional = $objInfraParametro->getValor('MODULO_IA_EXIBIR_RACIONAL_PROCESSOS_SIMILARES', false);

    switch ($_GET['acao']) {
        case 'md_ia_recurso':
            $strTitulo = 'SEI IA';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arvore_visualizar&acao_origem=' . $_GET['acao'] . '&arvore=1&id_protocolo=' . $_GET['id_procedimento']) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
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
include_once('md_ia_recurso_cadastro_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
//PaginaSEI::getInstance()->montarAreaValidacao();
?>
    <br>

<?php
if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
    ?>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-justify">
            <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                <legend class="infraLegend">Objetivos de Desenvolvimento Sustentável da ONU</legend>
                <?= $objMdIaAdmOdsOnuDTO->getStrOrientacoesGerais() ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" id="objetivosOds">
                        <div class="container">
                            <div class="row">
                                <?php
                                foreach($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {
                                    $classe = "desfoque";

                                    $objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
                                    $objMdIaClassificacaoOdsDTO->setNumIdProcedimento($idProcedimento);
                                    $objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds());
                                    $objMdIaClassificacaoOdsDTO->retStrStaTipoUltimoUsuario();
                                    $objMdIaClassificacaoOdsDTO->retNumIdMdIaClassificacaoOds();
                                    $objMdIaClassificacaoOdsRN = new MdIaClassificacaoOdsRN();
                                    $objMdIaClassificacaoOdsDTO = $objMdIaClassificacaoOdsRN->consultar($objMdIaClassificacaoOdsDTO);

                                    if(!is_null($objMdIaClassificacaoOdsDTO)) {

                                        // CASO JA TENHA SIDO CLASSIFICADO O CARD DA ODS APARECE COLORIDO
                                        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                                        $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
                                        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(array("1", "3"), InfraDTO::$OPER_NOT_IN);
                                        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
                                        $objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
                                        $lista = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);
                                        $objMdIaClassMetaOdsDTO = (new MdIaClassMetaOdsRN())->contar($objMdIaClassMetaOdsDTO);
                                        if($objMdIaClassMetaOdsDTO > 0) {
                                            $classe = "colorido";
                                        }

                                        //ADICIONA ICONE DA SUGESTAO
                                        switch ($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario()){
                                            case MdIaClassificacaoOdsRN::$USUARIO_EXTERNO:
                                                $classe .= " sugestaoUsuExt";
                                                break;

                                            case MdIaClassificacaoOdsRN::$USUARIO_IA:
                                                $classe .= " sugestaoIa";
                                                break;

                                            case MdIaClassificacaoOdsRN::$USUARIO_PADRAO:
                                                // CASO O ULTIMO USUARIO TENHA SIDO UM USUARIO PADRAO E NÃO TENHA META CLASSIFICADA EXIBE O ICONE DO HISTORICO
                                                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                                                $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
                                                $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
                                                $objMdIaClassMetaOdsRN = new MdIaClassMetaOdsRN();
                                                $objMdIaClassMetaOdsDTO = $objMdIaClassMetaOdsRN->contar($objMdIaClassMetaOdsDTO);

                                                if($objMdIaClassMetaOdsDTO == 0) {
                                                    $classe = "historico";
                                                }
                                                break;
                                        }
                                    }
                                ?>
                                    <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2 text-center" style="margin-bottom: 15px">
                                        <a onclick='consultarObjetivoOds("<?= $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() ?>")'>
                                            <div class="imagem text-center">
                                                <img src="modulos/ia/imagens/Icones_Oficiais_ONU/<?= $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() ?>" class="<?= $classe ?>" alt="Objetivo ODS: <?= $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() ?>"/>
                                                <div id="sobreposicao_imagem" style="display: none" class="<?= $classe ?>"></div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="hdnIdSelecaoObjetivo" name="hdnIdSelecaoObjetivo"/>
                <input type="hidden" id="hdntelaConsulta" name="hdntelaConsulta" value="false"/>
                <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento"
                       value="<?= $_GET['id_procedimento'] ?> "/>
            </fieldset>
            <br/>
        </div>
    </div>
    <?php
}
if ($objMdIaAdmConfigSimilarDTO->getStrSinExibirFuncionalidade() == "S") {
    ?>
    <form id="frmMdIaSimilaridadeCadastro" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-justify">
                <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                    <?php
                        if($exibirRacional != "1") {
                    ?>
                        <legend class="infraLegend">Processos Similares</legend>
                    <?php
                        } else {
                    ?>
                        <legend class="infraLegend">Avaliação de Similaridade entre Processos</legend>
                        <?php
                            if ($itensSimilares->recommendation != "" && $itensSimilares != "404") {
                                ?>
                                <div style="text-align: right; margin-top: -10px;">
                                    <button accesskey="S" name="sbmCadastrarMdIaAvaliacao" value="Salvar" class="infraButton"
                                            onclick="submeterDadosViaAjax()"><span class="infraTeclaAtalho">S</span>ubmeter
                                        Avaliação
                                    </button>
                                </div>
                    <?php
                            }
                        }
                    ?>

                    <?= $objMdIaAdmConfigSimilarDTO->getStrOrientacoesGerais() ?>
                    <div id="divMsgProcessosSimilares">
                        <div class="alert" role="alert">
                            <label></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <?php
                            if ($itensSimilares->recommendation != "" && $itensSimilares != "404") {
                                ?>
                                <table class="infraTable " id="tabela_ordenada" aria-describedby="Tabela de Processos Similares">
                                    <thead>
                                    <tr>
                                        <th class="infraTh" width="5%">Ranking</th>
                                        <th class="infraTh" width="14%">Processo</th>
                                        <?php
                                            if($exibirRacional != "1") {
                                        ?>
                                            <th class="infraTh" width="72%"> Tipo de Processo</th>
                                            <th class="infraTh" width="9%" >Avaliação</th>
                                        <?php
                                            } else {
                                        ?>
                                            <th class="infraTh" width="35%"> Tipo de Processo</th>
                                            <th class="infraTh" width="9%">Avaliação</th>
                                            <th class="infraTh">Racional</th>
                                        <?php
                                            }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contador = 0;
                                    $idRecommendation = $itensSimilares->id;
                                    foreach ($itensSimilares->recommendation as $arrayItemSimilar) {

                                        $objProcedimentoDTO = new ProcedimentoDTO();
                                        $objProcedimentoDTO->retDblIdProcedimento();
                                        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
                                        $objProcedimentoDTO->retStrNomeTipoProcedimento();
                                        $objProcedimentoDTO->setDblIdProcedimento($arrayItemSimilar->id_protocolo);

                                        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

                                        $contador++;
                                        ?>
                                        <tr data-index="<?= $contador ?>" data-position="<?= $contador ?>">
                                            <td class="idRanking">
                                                <?= $contador ?><em class="gg-arrows-v mr-2"></em>
                                                <input type="hidden" id="hdnRanking<?= $contador ?>"
                                                       name="hdnRanking<?= $contador ?>" value="<?= $contador ?>"/>
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                   href="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&id_procedimento=' . $arrayItemSimilar->id_protocolo) ?> "><?= $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() ?></a>
                                                <input type="hidden" id="hdnIdProtRecomend<?= $contador ?>"
                                                       name="hdnIdProtRecomend<?= $contador ?>"
                                                       value="<?= $arrayItemSimilar->id_protocolo ?>"/>
                                            </td>
                                            <td>
                                                <?= $objProcedimentoDTO->getStrNomeTipoProcedimento() ?>
                                                <input type="hidden" id="hdnDescProtRecomend<?= $contador ?>"
                                                       name="hdnDescProtRecomend<?= $contador ?>"
                                                       value="<?= $objProcedimentoDTO->getStrNomeTipoProcedimento() ?>"/>
                                            </td>
                                            <td class="text-center" style="padding-left: 20px; padding-right: 20px">
                                                <div class="rounded-pill p-2 d-flex justify-content-around align-items-center"
                                                     style="background: #EEE;">
                                                    <span class="btn_thumbs up bubbly-button"></span><span
                                                            style="color:#BBB">|</span>
                                                    <span class="btn_thumbs down bubbly-button"></span>
                                                    <input type="hidden" class="hdnAproved" id="hdnLike<?= $contador ?>"
                                                           name="hdnLike<?= $contador ?>" value=""/>
                                                </div>
                                            </td>
                                            <?php
                                                if($exibirRacional == "1") {
                                            ?>
                                                <td>
                                                    <input type="text" class="form-control" id="txtRacional<?= $contador ?>"
                                                           name="txtRacional<?= $contador ?>" maxlength="250"/>
                                                </td>
                                            <?php
                                                }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <input type="hidden" id="hdnNumeroElementos" name="hdnNumeroElementos"
                                           value="<?= $contador ?>"/>
                                    <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento"
                                           value="<?= $idProcedimento ?>"/>
                                    <input type="hidden" id="hdnIdRecommendation" name="hdnIdRecommendation"
                                           value="<?= $idRecommendation ?>"/>

                                    </tbody>
                                </table>

                                <?php
                            } elseif ($itensSimilares != "404") {
                                ?>
                                <div class="alert alert-danger">
                                    <label class="infraLabelOpcional">
                                        O recurso especfico do SEI IA está indisponível no momento.
                                    </label>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-warning">
                                    <label class="infraLabelOpcional">
                                        Este Processo ainda está pendente de processamento pelo SEI IA.
                                    </label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                        if($exibirRacional == "1") {
                            if ($itensSimilares->recommendation != "") {
                                ?>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <label for="txaSugestoes" class="infraLabelOpcional">
                                            Sugestões
                                        </label>
                                        <label for="txaSugestoes" class="infraLabelOpcional">
                                        </label>
                                        <textarea class="infraTextArea form-control" name="txaSugestoes" id="txaSugestoes"
                                                  rows="3"
                                                  cols="150"
                                                  onkeypress="return infraMascaraTexto(this, event, 500);"
                                                  maxlength="500"
                                                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"></textarea>
                                    </div>
                                </div>
                                <?
                            }
                        }
                    ?>
                </fieldset>
                <br/>
            </div>
        </div>
    </form>
    <?php
}

if ($objMdIaAdmPesqDocDTO->getStrSinExibirFuncionalidade() == "S") {
    ?>
    <form id="frmMdIaPesquisaDocumentos" method="post"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-justify">
            <fieldset class="infraFieldset form-control mb-3 py-3" style="height: auto">
                <legend class="infraLegend"><?= $objMdIaAdmPesqDocDTO->getStrNomeSecao() ?></legend>
                <?= $objMdIaAdmPesqDocDTO->getStrOrientacoesGerais() ?>
                <div id="divMsgPesquisaDocumento">
                    <div class="alert" role="alert">
                        <label></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="form-group">
                            <label id="lblQtdProcessListagem" for="txtTextoPesquisa" accesskey=""
                                   class="infraLabelOpcional">Texto para Pesquisa:</label>
                            <img align="top" alt="Ícone de Ajuda"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escrever aqui texto de pesquisa caso queira combinar ou não com a pesquisa confrontando o conteúdo dos documentos do processo que forem selecionados para montar a pesquisa.

Atenção que esta funcionalidade NÃO tem por objetivo substituir a pesquisa tradicional do SEI. Esta pesquisa utiliza técnicas de Inteligência Artificial úteis  para pesquisar texto com recursos de similaridade e semântica, inclusive selecionando documentos para confrontar com documentos, sem escrever nenhum texto complementar para a pesquisa.', 'Ajuda') ?>
                                 class="infraImg"/>
                            <input type="text" id="txtTextoPesquisa" name="txtTextoPesquisa"
                                   class="infraText form-control"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <label id="lblTpProcesso" for="selTpProcesso" accesskey=""
                                       class="infraLabelOpcional">Documentos para montar a pesquisa:
                                </label>
                                <img align="top"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escolha os documentos para montar a pesquisa. Caso selecione protocolo de Processo Anexados, todos os documentos dentro dele serão utilizados na montagem da pesquisa.

Atenção que esta funcionalidade NÃO tem por objetivo substituir a pesquisa tradicional do SEI. Esta pesquisa utiliza técnicas de Inteligência Artificial úteis para pesquisar texto com recursos de similaridade e semântica, inclusive selecionando documentos para confrontar com documentos, sem escrever nenhum texto complementar para a pesquisa.', 'Ajuda') ?>
                                     class="infraImg" alt="Ícone de Ajuda"/>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group">
                                    <select id="selDocumento" name="selDocumento" size="8" multiple="multiple"
                                            class="infraSelect form-control"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    </select>

                                    <div id="_divOpcoesDocumentos" class="ml-1">
                                        <img id="imgLupaDocumento"
                                             onclick="objLupaDocumento.selecionar(700,500);"
                                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/pesquisar.svg' ?>"
                                             alt="Selecionar Documento" title="Selecionar Documento"
                                             class="infraImg"/>
                                        <br>
                                        <img id="imgExcluirTpProcesso" onclick="objLupaDocumento.remover();"
                                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/remover.svg' ?>"
                                             alt="Remover Documento" title="Remover Documento"
                                             class="infraImg"/>
                                    </div>
                                    <input type="hidden" id="hdnIdDocumento" name="hdnIdDocumento">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="form-group">
                            <label id="lblTiposDocumentos" for="txtTiposDocumentos" accesskey=""
                                   class="infraLabelObrigatorio">Tipos de Documentos Alvo a serem
                                pesquisados:</label>
                            <img align="top"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('É obrigatório escolher pelo menos um tipo de documento alvo para a pesquisa.

Aqui são listados apenas tipos de documentos definidos pela Administração do SEI, pois envolve pré processamento pela IA do conteúdo de todos os documentos desses tipos. Ainda, são indicados apenas tipos de documentos que decidem de forma relevante o resultado do mérito dos processos.', 'Ajuda') ?>
                                 class="infraImg" alt="Ícone de Ajuda"/>
                            <br>
                            <?php
                            foreach ($arrObjMdIaAdmTpDocPesqDTO as $objMdIaAdmTpDocPesqDTO) { ?>
                                <input type="checkbox"
                                       name="ckbTipoDocumento[]"
                                       id="ckb<?= $objMdIaAdmTpDocPesqDTO->getNumIdSerie() ?>"
                                       value="<?= $objMdIaAdmTpDocPesqDTO->getNumIdSerie() ?>"
                                       class="infraCheckbox">
                                <label class="infraLabelChec infraLabelOpcional mr-3"
                                       for="ckb<?= $objMdIaAdmTpDocPesqDTO->getNumIdSerie() ?>"><?= $objMdIaAdmTpDocPesqDTO->getStrNomeSerie() ?></label>
                                <br>
                                <?php
                            }
                            ?>
                        </div>
                        <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento"
                               value="<?= $idProcedimento ?>"/>
                        <button type="button" accesskey="P" name="btnPesquisar" id="btnPesquisar" value="Pesquisar"
                                onclick="pesquisarDocumentos()" class="infraButton"><span
                                    class="infraTeclaAtalho">P</span>esquisar
                        </button>
                    </div>
                </div>
            </fieldset>
            <br/>
        </div>
    </div>
    <?
}
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
?>
    </form>
<?
require_once "md_ia_recurso_cadastro_js.php";
?>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
