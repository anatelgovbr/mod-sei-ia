<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaRecursoINT extends InfraINT
{
    function enviarFeedbackProcessos($dados)
    {
        $objMdIaRecursoRN = new MdIaRecursoRN();

        $arrayObjetosSimilaridade = [];

        $objSeiRN = new SeiRN();
        $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultarProcedimentoAPI->setIdProcedimento($dados['hdnIdProcedimento']);

        $objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

        for ($i = 1; $i <= $dados["hdnNumeroElementos"]; $i++) {
            if ($dados['hdnLike' . $i] != "") {
                $arrayMdIaSimilaridadeDTO["id_recommended"] = $dados['hdnIdProtRecomend' . $i];
                $arrayMdIaSimilaridadeDTO["like_flag"] = $dados['hdnLike' . $i];
                $arrayMdIaSimilaridadeDTO['ranking_user'] = $dados['hdnRanking' . $i];
                $arrayMdIaSimilaridadeDTO["racional"] = mb_convert_encoding($dados['txtRacional' . $i], 'UTF-8', 'ISO-8859-1');
                $arrayMdIaSimilaridadeDTO['sugesty'] = mb_convert_encoding($dados['txaSugestoes'], 'UTF-8', 'ISO-8859-1');
                $arrayObjetosSimilaridade[] = $arrayMdIaSimilaridadeDTO;
            }
        }
        $arrayAvaliacaoRecomendacao["id_recommendation"] = $dados['hdnIdRecommendation'];
        $arrayAvaliacaoRecomendacao["result"] = $arrayObjetosSimilaridade;
        $arrayAvaliacao[] = $arrayAvaliacaoRecomendacao;
        $objMdIaSimilaridadeDTO = $objMdIaRecursoRN->submeteSimilaridade(array(json_encode($arrayAvaliacao), $objSaidaConsultarProcedimentoAPI));
        return $objMdIaSimilaridadeDTO;
    }
    function enviarFeedbackDocumentos($dados)
    {
        $objMdIaRecursoRN = new MdIaRecursoRN();

        $objSeiRN = new SeiRN();
        $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultarProcedimentoAPI->setIdProcedimento($dados['hdnIdProcedimento']);

        $objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

        for ($i = 1; $i <= $dados["hdnNumeroElementos"]; $i++) {
            if ($dados['hdnLike' . $i] != "") {
                $arrayMdIaPesquisaDocumentoDTO["id_recommended"] = $dados['hdnIdProtRecomend' . $i];
                $arrayMdIaPesquisaDocumentoDTO["like_flag"] = $dados['hdnLike' . $i];
                $arrayMdIaPesquisaDocumentoDTO['ranking_user'] = $dados['hdnRanking' . $i];
                $arrayMdIaPesquisaDocumentoDTO['sugesty'] = "";
                $arrayMdIaPesquisaDocumentoDTO['racional'] = "";
                $arrayObjetosPesquisaDocumento[] = $arrayMdIaPesquisaDocumentoDTO;
            }
        }
        $arrayAvaliacaoRecomendacao["id_recommendation"] = $dados['hdnIdRecomendacao'];
        $arrayAvaliacaoRecomendacao["result"] = $arrayObjetosPesquisaDocumento;
        $arrayAvaliacao = array($arrayAvaliacaoRecomendacao);
        $objMdIaPesquisaDocumentoDTO = $objMdIaRecursoRN->submetePesquisaDocumento(array(json_encode($arrayAvaliacao), $objSaidaConsultarProcedimentoAPI));
        return $objMdIaPesquisaDocumentoDTO;
    }

    public function consultaPesquisaDocumento($dados)
    {
        $objMdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
        $objMdIaAdmPesqDocDTO->retNumQtdProcessListagem();
        $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
        $objMdIaAdmPesqDocDTO = $objMdIaAdmPesqDocRN->consultar($objMdIaAdmPesqDocDTO);

        $parametrosUrl = "";
        if ($dados["txtTextoPesquisa"] != "") {
            $parametrosUrl .= "&text=" . urlencode(mb_convert_encoding($dados["txtTextoPesquisa"], 'UTF-8', 'ISO-8859-1'));
        }
        $objSeiRN = new SeiRN();
        $objProtocoloRN = new ProtocoloRN();

        if ($dados["hdnIdDocumento"] != "") {
            $protocolos = explode("¥", $dados["hdnIdDocumento"]);
            foreach ($protocolos as $protocolo) {
                $idProtocolo = explode("±", $protocolo);

                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->retStrStaProtocolo();
                $objProtocoloDTO->setDblIdProtocolo($idProtocolo[0]);

                $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

                if ($objProtocoloDTO->getStrStaProtocolo() == "P") {
                    //Carregar dados do cabeçalho
                    $objProcedimentoDTO = new ProcedimentoDTO();
                    $objProcedimentoDTO->retDblIdProcedimento();
                    $objProcedimentoDTO->setDblIdProcedimento($idProtocolo[0]);
                    $objProcedimentoDTO->setStrSinDocTodos('S');
                    //$objProcedimentoDTO->setStrSinProcAnexados('S');

                    $objProcedimentoRN = new ProcedimentoRN();
                    $documentosProcessoAnexado = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);
                    $documentosProcessoAnexado = $documentosProcessoAnexado[0]->getArrObjRelProtocoloProtocoloDTO();
                    foreach ($documentosProcessoAnexado as $documentoProcessoAnexado) {
                        if ($documentoProcessoAnexado->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO) {
                            $documento = $documentoProcessoAnexado->getObjProtocoloDTO2();
                            $parametrosUrl .= "&list_id_doc=" . $documento->getDblIdDocumento();
                        }
                    }
                } else {
                    $parametrosUrl .= "&list_id_doc=" . $idProtocolo[0];
                }
            }
        }
        if ($dados["ckbTipoDocumentoChecked"] != "") {
            foreach ($dados["ckbTipoDocumentoChecked"] as $tipoDocumento) {
                $parametrosUrl .= "&list_type_id_doc=" . $tipoDocumento;
            }
        }
        $parametrosUrl .= "&rows=" . $objMdIaAdmPesqDocDTO->getNumQtdProcessListagem();
        $parametrosUrl .= "&id_user=" . SessaoSEI::getInstance()->getNumIdUsuario();

        $objMdIaRecursoRN = new MdIaRecursoRN();

        $urlApi = $objMdIaRecursoRN->retornarUrlApi();

        $urlConsulta = $urlApi["linkRecomendacaoDocumentos"] . substr($parametrosUrl, 1);

        $objSeiRN = new SeiRN();
        $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultarProcedimentoAPI->setIdProcedimento($dados['hdnIdProcedimento']);

        $objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

        $pesquisaDocumentos = json_decode($objMdIaRecursoRN->enviaPostPesquisaDocumento(array($urlConsulta, $objSaidaConsultarProcedimentoAPI)));

        if (!empty($pesquisaDocumentos->recommendation)) {
            $contador = 0;

            $topoTabela = '<table class="infraTable " id="tabela_ordenada">
                <thead>
                <tr>
                    <th class="infraTh" width="5%">Ranking</th>
                    <th class="infraTh" width="38%">Documento Resultado da Pesquisa</th>
                    <th class="infraTh" width="15%">Processo</th>
                    <th class="infraTh" width="30%">Tipo de Processo</th>
                    <th class="infraTh" width="12%">Avaliação</th>
                </tr>
                </thead>
                <tbody>';

            $registrosPesquisa = "";

            $objDocumentoRN = new DocumentoRN();

            foreach ($pesquisaDocumentos->recommendation as $arrayItemSimilar) {
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->retDblIdProcedimento();
                $objDocumentoDTO->retDblIdDocumento();
                $objDocumentoDTO->retStrNomeSerie();
                $objDocumentoDTO->retStrNumero();
                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                $objDocumentoDTO->retStrNomeArvore();
                $objDocumentoDTO->setDblIdDocumento($arrayItemSimilar->id_document);
                $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
                if ($objDocumentoDTO) {
                    $objProcedimentoDTO = new ProcedimentoDTO();
                    $objProcedimentoDTO->retDblIdProcedimento();
                    $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
                    $objProcedimentoDTO->retStrNomeTipoProcedimento();
                    $objProcedimentoDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());

                    $objProcedimentoRN = new ProcedimentoRN();
                    $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

                    $contador++;

                    $registrosPesquisa .= '
                        <tr data-index="' . $contador . '" data-position="' . $contador . '">
                            <td class="idRanking">
                                ' . $contador . '<i class="gg-arrows-v mr-2"></i>
                                <input type="hidden" id="hdnRanking' . $contador . '"
                                       name="hdnRanking' . $contador . '" value="' . $contador . '"/>
                            </td>
                            <td>
                                <a target="_blank"
                                   href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&id_procedimento=' . $objDocumentoDTO->getDblIdProcedimento() . '&id_documento=' . $objDocumentoDTO->getDblIdDocumento()) . '">' . $objDocumentoDTO->getStrNomeSerie() . ' ' . $objDocumentoDTO->getStrNumero() . ' ' . $objDocumentoDTO->getStrNomeArvore() . ' (' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . ')</a>
                                <input type="hidden" id="hdnIdProtRecomend' . $contador . '"
                                       name="hdnIdProtRecomend' . $contador . '"
                                       value="' . $arrayItemSimilar->id_document . '"/>
                            </td>
                            <td>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ' </td>
                            <td>' . $objProcedimentoDTO->getStrNomeTipoProcedimento() . '</td>
                            <td class="text-center" style="padding-left: 20px; padding-right: 20px">
                                <div class="rounded-pill p-2 d-flex justify-content-around align-items-center"
                                     style="background: #EEE;">
                                    <span class="btn_thumbs up bubbly-button"></span><span
                                        style="color:#BBB">|</span>
                                    <span class="btn_thumbs down bubbly-button"></span>
                                    <input type="hidden" class="hdnAproved" id="hdnLike' . $contador . '"
                                           name="hdnLike' . $contador . '" value=""/>
                                </div>
                            </td>
                        </tr>';
                }
            }
            $rodapeTabela = '
                <input type="hidden" id="hdnNumeroElementos" name="hdnNumeroElementos"
                       value="' . $contador . '"/>
                <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento"
                       value="' . $dados['hdnIdProcedimento'] . '"/>
                <input type="hidden" id="hdnIdRecomendacao" name="hdnIdRecomendacao"
                       value="' . $pesquisaDocumentos->id_recommendation . '"/>

                </tbody>
            </table>';
        } elseif (is_null($pesquisaDocumentos)) {
            $topoTabela = '
            <div class="alert alert-danger">
                <label class="infraLabelOpcional">
                   Ocorreu um erro ao consultar a API de recomendação de documentos.
                </label>
            </div>';
            $registrosPesquisa = '';
            $rodapeTabela = '';
        } else {
            $topoTabela = '
            <div class="alert alert-warning">
                <label class="infraLabelOpcional">
                    Esta pesquisa não retornou registros.
                </label>
            </div>';
        }
        return json_encode(mb_convert_encoding($topoTabela . $registrosPesquisa . $rodapeTabela, 'UTF-8', 'ISO-8859-1'));
    }

    public function listarDocumentosProcesso($idProcedimento)
    {
        //Carregar dados do cabeçalho
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->retStrNomeTipoProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retDtaGeracaoProtocolo();
        $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();

        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->setStrSinDocTodos('S');
        $objProcedimentoDTO->setStrSinProcAnexados('S');

        $objProcedimentoRN = new ProcedimentoRN();
        $arr = $objProcedimentoRN->listarCompleto($objProcedimentoDTO);

        return $arr;
    }

    public static function verificarStatusIndexacao($idProcedimento)
    {
        $objMdIaProcIndexaveisRN = new MdIaProcIndexaveisRN();
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO();
        $objMdIaProcIndexaveisDTO->retStrSinIndexado();
        $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaProcIndexaveisDTO = $objMdIaProcIndexaveisRN->consultar($objMdIaProcIndexaveisDTO);


        if (is_null($objMdIaProcIndexaveisDTO)) {
            return "Este processo não atende aos requisitos de documentos relevantes para similaridade e não vai ser processado pelo SEI IA, ressalvado se mais documentos forem gerados e passar a atender aos requisitos.";
        } elseif ($objMdIaProcIndexaveisDTO->getStrSinIndexado() == 'N') {
            return "Este processo atende aos requisitos de documentos relevantes para similaridade, mas ainda está pendente de processamento pelo SEI IA. Espere alguns minutos.";
        }
    }
}
