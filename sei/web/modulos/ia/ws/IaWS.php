<?

/**
 * ANATEL
 *
 * 28/06/2024 - criado por Willian Christian - sabino.colab@anatel.gov.br
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';


class IaWS extends MdIaUtilWS
{

    public function getObjInfraLog()
    {
        return LogSEI::getInstance();
    }

    public function cadastrarClassificacaoMonitorado($identificacaoServico, $idProcedimento, $meta)
    {
        try {

            $objMdIaAdmObjetivoOdsINT = new MdIaAdmObjetivoOdsINT();
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $idUsuario = $objInfraParametro->getValor(MdIaClassMetaOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA, false);
            $staTipoUsuario = MdIaClassMetaOdsRN::$USUARIO_IA;

            $retorno = $objMdIaAdmObjetivoOdsINT->classificarOdsWS($idProcedimento, $meta, $idUsuario, $staTipoUsuario);

            return $retorno;
        } catch (Exception $e) {
            throw new InfraException('Erro ao Classificar meta.', $e);
        }
    }

    public function consultarOperacaoConsultarDocumento()
    {

        $objServicoRN = new ServicoRN();

        $objServicoDTO = new ServicoDTO();
        $objServicoDTO->retNumIdServico();
        $objServicoDTO->setStrIdentificacao("consultarDocumentoExternoIA");
        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);
        if (!is_null($objServicoDTO)) {
            $operacaoServicoDTO = new OperacaoServicoDTO();
            $operacaoServicoRN = new OperacaoServicoRN();

            $operacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_CONSULTAR_DOCUMENTO);
            $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
            $operacaoServicoDTO->retNumIdServico();
            $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

            // Verifica se a operação 'Consultar Documento' está habilitada para o serviço
            if (empty($objOperacaoServicoDTO)) {
                $IaWs = new IaWS();
                $codigoErro = 401;
                $msg = "Operação não permitida pois não consta para a integração deste Sistema e Serviço ao menos a operação 'Consultar Documento'. Entre em contato com a Administração do SEI.";
                http_response_code($codigoErro);
                echo json_encode($IaWs->retornoErro($msg, $codigoErro), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
                exit;
            }
        } else {
            $IaWs = new IaWS();
            $codigoErro = 404;
            $msg = "Serviço [consultarDocumentoExternoIA] não encontrado.";
            http_response_code($codigoErro);
            echo json_encode($IaWs->retornoErro($msg, $codigoErro), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
            exit;
        }
    }

    private function listarAnexosDocumento($idDocumento)
    {
        $objAnexoDTO = new AnexoDTO();
        $objAnexoDTO->retNumIdAnexo();
        $objAnexoDTO->retStrNome();
        $objAnexoDTO->retNumIdAnexo();
        $objAnexoDTO->retStrHash();
        $objAnexoDTO->setDblIdProtocolo($idDocumento);
        $objAnexoDTO->retDblIdProtocolo();
        $objAnexoDTO->retDthInclusao();
        $objAnexoDTO->retStrProtocoloFormatadoProtocolo();

        $objAnexoRN = new AnexoRN();
        return $objAnexoRN->listarRN0218($objAnexoDTO);
    }

    public function downloadArquivoDocumentoExterno($parametros)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->retStrStaProtocolo();
            $objProtocoloDTO->setDblIdProtocolo($parametros['IdDocumento']);
            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

            if (!is_null($objProtocoloDTO)) {
                if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO || $parametros['IdAnexo']) {
                    $objAnexoDTO = new AnexoDTO();
                    $objAnexoDTO->retNumIdAnexo();
                    $objAnexoDTO->retStrNome();
                    $objAnexoDTO->retNumIdAnexo();
                    $objAnexoDTO->retStrHash();
                    $objAnexoDTO->setDblIdProtocolo($parametros['IdDocumento']);
                    if ($parametros['IdAnexo']) {
                        $objAnexoDTO->setNumIdAnexo($parametros['IdAnexo']);
                    }
                    $objAnexoDTO->retDblIdProtocolo();
                    $objAnexoDTO->retDthInclusao();
                    $objAnexoDTO->retStrProtocoloFormatadoProtocolo();

                    $objAnexoRN = new AnexoRN();
                    $arrObjAnexoDTO = $objAnexoRN->listarRN0218($objAnexoDTO);

                    if (count($arrObjAnexoDTO) == 1) {
                        AuditoriaSEI::getInstance()->auditar('md_ia_consultar_documento_externo_ia', __METHOD__, $arrObjAnexoDTO);
                        $anexo = $arrObjAnexoDTO[0];
                        try {
                            SeiINT::download($anexo);
                        } catch (Exception $e) {
                            return $this->retornoErro($e->getMessage(), 404);
                        }
                    } elseif (count($arrObjAnexoDTO) > 1) {
                        // Lógica para múltiplos anexos
                        throw new Exception('Este documento possui anexos. Você deve preencher o parâmetro "idAnexo".', 400);
                    } else {
                        throw new Exception('Documento não tem anexo.', 404);
                    }
                } else {
                    throw new Exception('Arquivo buscado não é um documento externo.', 404);
                }
            } else {
                throw new Exception('Documento não encontrado.', 404);
            }
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function consultarDocumento($parametros)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $arrIdDocumento = array_map('intval', array_map('trim', explode(',', $parametros['IdDocumentos'])));

            $documentoDTO = new DocumentoDTO();
            $documentoDTO->retDblIdProcedimento();
            $documentoDTO->retStrProtocoloDocumentoFormatado();
            $documentoDTO->retStrDescricaoProtocolo();
            $documentoDTO->retNumIdSerie();
            $documentoDTO->retStrConteudo();
            $documentoDTO->retDtaInclusaoProtocolo();
            $documentoDTO->retStrNomeSerie();
            $documentoDTO->retDblIdDocumento();
            $documentoDTO->retStrStaDocumento();
            $documentoDTO->retStrProtocoloProcedimentoFormatado();
            $documentoDTO->retNumIdTipoProcedimentoProcedimento();
            $documentoDTO->setDblIdDocumento($arrIdDocumento, InfraDTO::$OPER_IN);

            //APLICAR FILTRO DE DOCUMENTOS BLOQUEADOS
            if ($parametros['SinFiltraBloqueados'] === 'S') {
                $documentoDTO->setStrSinBloqueado('S');
            }

            //APLICAR FILTRO DE DOCUMENTOS ATIVOS
            if ($parametros['SinFiltraAtivos'] === 'S') {
                $documentoDTO->setStrStaEstadoProtocolo(ProtocoloRN::$TE_NORMAL);
            }

            $arrDocumentoDTO = (new DocumentoRN())->listarRN0008($documentoDTO);

            if (empty($arrDocumentoDTO)) {
                throw new Exception("Nenhum Documento encontrado.", 404);
            }

            $retorno = [];
            foreach ($arrDocumentoDTO as $documentoDTO) {

                //APLICAR FILTRO DE DOCUMENTOS RELEVANTES
                if ($parametros['SinFiltraDocumentosRelevantes'] === 'S' && $this->consultarDocumentosRelevante($documentoDTO) == false) {
                    continue;
                }

                $nomeArquivoAnexo = '';

                if ($documentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
                    $objAnexoDTO = new AnexoDTO();
                    $objAnexoDTO->retStrNome();
                    $objAnexoDTO->setDblIdProtocolo($documentoDTO->getDblIdDocumento());
                    $objAnexoDTO = (new AnexoRN())->consultarRN0736($objAnexoDTO);
                    $nomeArquivoAnexo = $this->tratarEncodeString($objAnexoDTO->getStrNome());
                }

                $retorno[] = [
                    'IdProcedimento'          => (int) $documentoDTO->getDblIdProcedimento(),
                    'NumeroDocumento'         => $documentoDTO->getStrProtocoloDocumentoFormatado(),
                    'EspecificacaoDocumento'  => $this->tratarEncodeString($documentoDTO->getStrDescricaoProtocolo()),
                    'IdTipoDocumento'         => (int) $documentoDTO->getNumIdSerie(),
                    'DataInclusao'            => $documentoDTO->getDtaInclusaoProtocolo(),
                    'NomeTipoDocumento'       => $this->tratarEncodeString($documentoDTO->getStrNomeSerie()),
                    'StaTipoDocumento'        => $documentoDTO->getStrStaDocumento(),
                    'NomeArquivo'             => $this->tratarEncodeString($nomeArquivoAnexo),
                    'NumeroProcesso'          => $documentoDTO->getStrProtocoloProcedimentoFormatado(),
                    'IdDocumento'             => (int) $documentoDTO->getDblIdDocumento()
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function consultarProcesso($parametros)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $arrIdProcessos = array_map('intval', array_map('trim', explode(',', $parametros['IdProcedimentos'])));

            $protocoloDTO = new ProtocoloDTO();
            $protocoloDTO->setDblIdProtocolo($arrIdProcessos, InfraDTO::$OPER_IN);
            $protocoloDTO->retStrProtocoloFormatado();
            $protocoloDTO->retStrDescricao();
            $protocoloDTO->retStrNomeTipoProcedimentoProcedimento();
            $protocoloDTO->retStrSiglaUnidadeGeradora();
            $protocoloDTO->retStrDescricaoUnidadeGeradora();
            $protocoloDTO->retNumIdTipoProcedimentoProcedimento();
            $protocoloDTO->retNumIdUnidadeGeradora();
            $protocoloDTO->retDblIdProtocolo();

            $arrprotocoloDTO = (new ProtocoloRN())->listarRN0668($protocoloDTO);

            $retorno = [];
            if (empty($arrprotocoloDTO)) {
                throw new Exception("Nenhum Processo encontrado.", 404);
            }
            foreach ($arrprotocoloDTO as $protocoloDTO) {
                $retorno[] = [
                    'NumeroProcesso'                => $protocoloDTO->getStrProtocoloFormatado(),
                    'EspecificacaoProcesso'             => $this->tratarEncodeString($protocoloDTO->getStrDescricao()),
                    'IdTipoProcesso'                    => $protocoloDTO->getNumIdTipoProcedimentoProcedimento(),
                    'TipoProcesso'                      => $this->tratarEncodeString($protocoloDTO->getStrNomeTipoProcedimentoProcedimento()),
                    'IdUnidadeGeradoraProcesso'         => $protocoloDTO->getNumIdUnidadeGeradora(),
                    'SiglaUnidadeGeradoraProcesso'      => $this->tratarEncodeString($protocoloDTO->getStrSiglaUnidadeGeradora()),
                    'DescricaoUnidadeGeradoraProcesso'  => $this->tratarEncodeString($protocoloDTO->getStrDescricaoUnidadeGeradora()),
                    'ProcessosFilhoRelacionado'         => $this->buscarProcessoFilho($protocoloDTO->getDblIdProtocolo()),
                    'ProcessosPaiRelacionado'           => $this->buscarProcessoPai($protocoloDTO->getDblIdProtocolo()),
                    'IdProcessosAnexados'               => $this->buscarProcessoAnexado($protocoloDTO->getDblIdProtocolo()),
                    'Interessados'                      => $this->buscarInteressadosPorProcedimento($protocoloDTO->getDblIdProtocolo()),
                    'IdProcedimento'             => (int) $protocoloDTO->getDblIdProtocolo()
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function gerarHashConteudoDocumento($parametros)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            if (!$parametros['IdDocumento']) {
                throw new Exception('IdDocumento é um parametro obrigatório.');
            }

            $arrIdDocumento = array_map('trim', explode(',', $parametros['IdDocumento']));
            $arrIdDocumento = array_map('intval', $arrIdDocumento);

            $documentoDTO = new DocumentoDTO();
            $documentoDTO->setDblIdDocumento($arrIdDocumento, InfraDTO::$OPER_IN);
            $documentoDTO->setStrStaEstadoProtocolo(0);
            $documentoDTO->retStrStaDocumento();
            $documentoDTO->retDblIdDocumento();
            $documentoDTO->retStrSinBloqueado();

            $arrDocumentoDTO = (new DocumentoRN())->listarRN0008($documentoDTO);

            if (empty($arrDocumentoDTO)) {
                throw new Exception("Nenhum Documento encontrado.", 404);
            }

            $retorno = [];
            foreach ($arrDocumentoDTO as $documentoDTO) {

                $hashContent = $documentoDTO->getDblIdDocumento();

                if ($documentoDTO->getStrSinBloqueado() === 'N' && ($documentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO || $documentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EDITOR_INTERNO)) {

                    $hashDocumentoExterno = '';
                    $dthMaxAtualizacaoDocumento = '';
                    if ($documentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
                        $objAnexoDTO = new AnexoDTO();
                        $objAnexoDTO->retStrHash();
                        $objAnexoDTO->setDblIdProtocolo($documentoDTO->getDblIdDocumento());
                        $objAnexoDTO = (new AnexoRN())->consultarRN0736($objAnexoDTO);
                        $hashDocumentoExterno = $this->tratarEncodeString($objAnexoDTO->getStrHash());
                    } else {
                        $dthMaxAtualizacaoDocumento = $this->buscarDthMaxAtualizacaoDocumento($documentoDTO->getDblIdDocumento());
                    }

                    $hashContent = md5(
                        $documentoDTO->getDblIdDocumento() .
                            $hashDocumentoExterno .
                            $dthMaxAtualizacaoDocumento
                    );
                }

                $retorno[] = [
                    'IdDocumento' => $documentoDTO->getDblIdDocumento(),
                    'HashConteudoDocumento' => $hashContent
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarTipoDocumento()
    {
        try {
            $SerieDTO = new SerieDTO();
            $SerieDTO->retNumIdSerie();
            $SerieDTO->retStrNome();
            $SerieDTO->setOrdNumIdSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
            $SerieDTO->setBolExclusaoLogica(false);

            $arrSerieDTO = (new SerieRN())->listarRN0646($SerieDTO);

            if (empty($arrSerieDTO)) {
                throw new Exception('Nenhum Tipo de Documento encontrado.', 404);
            }

            $retorno = [];
            foreach ($arrSerieDTO as $SerieDTO) {
                $retorno[] = [
                    'IdTipoDocumento' => (int) $SerieDTO->getNumIdSerie(),
                    'TipoDocumento' => $this->tratarEncodeString($SerieDTO->getStrNome())
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarSegmentosDocRelevantes()
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $MdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
            $MdIaAdmSegDocRelevDTO->retNumIdMdIaAdmDocRelev();
            $MdIaAdmSegDocRelevDTO->retStrSegmentoDocumento();
            $MdIaAdmSegDocRelevDTO->retNumPercentualRelevancia();
            $MdIaAdmSegDocRelevDTO->retNumIdSerie();

            $arrMdIaAdmSegDocRelevDTO = $this->listaSegDocumentosRelevantes();

            if (empty($arrMdIaAdmSegDocRelevDTO)) {
                return $this->retornoErro('Nenhum segmento de documento relevante encontrado.', 404, false);
            }

            $retorno = [];
            foreach ($arrMdIaAdmSegDocRelevDTO as $MdIaAdmSegDocRelevDTO) {
                $retorno[] = [
                    'IdDocumentoRelevante'      => (int) $MdIaAdmSegDocRelevDTO->getNumIdMdIaAdmDocRelev(),
                    'SegmentoDocumento'         => $MdIaAdmSegDocRelevDTO->getStrSegmentoDocumento(),
                    'IdTipoDocumentoRelevante'  => (int) $MdIaAdmSegDocRelevDTO->getNumIdSerie(),
                    'PercentualRelevancia'      => (int) $MdIaAdmSegDocRelevDTO->getNumPercentualRelevancia()
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarPercentualRelevanciaMetadados()
    {
        try {

            $MdIaAdmSegDocRelevDTO = new MdIaAdmPercRelevMetDTO();
            $MdIaAdmSegDocRelevDTO->retStrMetadado();
            $MdIaAdmSegDocRelevDTO->retNumPercentualRelevancia();

            $arrMdIaAdmSegDocRelevDTO = (new MdIaAdmPercRelevMetRN())->listar($MdIaAdmSegDocRelevDTO);

            if (empty($arrMdIaAdmSegDocRelevDTO)) {
                throw new Exception('Nenhum registro encontrado.', 404);
            }

            $retorno = [];
            foreach ($arrMdIaAdmSegDocRelevDTO as $MdIaAdmSegDocRelevDTO) {
                $retorno[] = [
                    'Metadado'   => $this->tratarEncodeString($MdIaAdmSegDocRelevDTO->getStrMetadado()),
                    'Relevancia' => (int) $MdIaAdmSegDocRelevDTO->getNumPercentualRelevancia()
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function consultarConteudoDocumento($parametros)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $retorno = $this->retornarConteudoDocumentoInterno($parametros['IdDocumento']);

            if (!$retorno) {
                $retorno = $this->montarRetornoConteudoDocumentoExterno($parametros['IdDocumento']);
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarProcessosPendenteIndexacao($parametros)
    {
        try {
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '-1');

            $arrObjMdIaProcIndexaveisDTO = $this->listaProcessosIndexaveis($parametros);

            if ($arrObjMdIaProcIndexaveisDTO["quantidadeRegistrosTotal"] == 0) {
                return $this->retornoErro('Nenhum Processo pendente de indexação.', 404, false);
            }

            $arrIdProcIndexaveis = InfraArray::converterArrInfraDTO($arrObjMdIaProcIndexaveisDTO["registros"], 'IdProcedimento');

            return [
                'status' => 'success',
                'data' => [
                    'IdProcedimentos'                => $arrIdProcIndexaveis,
                    'QuantidadeRegistrosEntregue'                => count($arrObjMdIaProcIndexaveisDTO["registros"]),
                    'QuantidadeRegistrosTotal'                => $arrObjMdIaProcIndexaveisDTO["quantidadeRegistrosTotal"],
                    'IdUltimoRegistroEntregue'                => (int) end($arrObjMdIaProcIndexaveisDTO["registros"])->getDblIdProcedimento()
                ]
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarDocumentosPendenteIndexacao($parametros)
    {
        try {
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '-1');

            $arrObjMdIaDocIndexaveisDTO = $this->listaDocumentosIndexaveis($parametros);

            if ($arrObjMdIaDocIndexaveisDTO["quantidadeRegistrosTotal"] == 0) {
                return $this->retornoErro('Nenhum documento pendente de indexação.', 404, false);
            }

            $arrIdDocIndexaveis = InfraArray::converterArrInfraDTO($arrObjMdIaDocIndexaveisDTO["registros"], 'IdDocumento');
            return [
                'status' => 'success',
                'data' => [
                    'IdDocumentos'                => $arrIdDocIndexaveis,
                    'QuantidadeRegistrosEntregue'                => count($arrObjMdIaDocIndexaveisDTO["registros"]),
                    'QuantidadeRegistrosTotal'                => $arrObjMdIaDocIndexaveisDTO["quantidadeRegistrosTotal"],
                    'IdUltimoRegistroEntregue'                => (int) end($arrObjMdIaDocIndexaveisDTO["registros"])->getDblIdDocumento()
                ]
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarDocumentosRelevantesProcesso($params)
    {

        try {

            $arrIdDocumento = [];
            $arrExtensoesPermitidas = ["pdf", "html", "htm", "txt", "ods", "xlsx", "csv", "xml", "odt", "odp", "doc", "docx", "json", "ppt", "pptx", "rtf", "xls", "xlsm"];

            // ATRIBUTOS QUE TORNA O DOCUMENTO PASSILVEL DE SER RELEVANTES
            $objMdIaDocumentoDTO = new MdIaDocumentoDTO();
            $objMdIaDocumentoDTO->retDblIdDocumento();
            $objMdIaDocumentoDTO->retNumIdSerie();
            $objMdIaDocumentoDTO->retStrStaDocumento();
            $objMdIaDocumentoDTO->retStrNomeAnexo();
            $objMdIaDocumentoDTO->retNumIdTipoProcedimentoProcedimento();
            $objMdIaDocumentoDTO->setStrStaEstadoProcedimento(array(ProtocoloRN::$TE_NORMAL, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO, ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO), InfraDTO::$OPER_IN);
            $objMdIaDocumentoDTO->setStrSinBloqueado('S');
            $objMdIaDocumentoDTO->setStrStaEstadoProtocolo(ProtocoloRN::$TE_NORMAL);
            $objMdIaDocumentoDTO->setOrdDblIdDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdIaDocumentoDTO->setDblIdProcedimento($params['IdProcedimento']);
            $arrObjDocumentoDTO = (new MdIaDocumentoRN())->listar($objMdIaDocumentoDTO);

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                if ($this->consultarDocumentosRelevante($objDocumentoDTO)) {
                    if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
                        $extensaoAnexo = end(explode('.', $objDocumentoDTO->getStrNomeAnexo()));
                        if (in_array($extensaoAnexo, $arrExtensoesPermitidas)) {
                            $arrIdDocumento[] = (int) $objDocumentoDTO->getDblIdDocumento();
                        }
                    } else {
                        $arrIdDocumento[] = (int) $objDocumentoDTO->getDblIdDocumento();
                    }
                }
            }

            if (empty($arrIdDocumento)) {
                throw new Exception('Nenhum Documento encontrado.', 404);
            }

            return [
                'status' => 'success',
                'data' => $arrIdDocumento
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function atualizarStatusProcessoIndexado($idProcedimento)
    {
        try {
            if (!$idProcedimento) {
                throw new Exception('Id do protocolo é um parametro obrigatório.', 400);
            }

            $objMdIaProcIndexaveisRN = new MdIaProcIndexaveisRN();
            $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
            $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
            $objMdIaProcIndexaveisDTO->retDblIdProcedimento();

            $objMdIaProcIndexaveisDTO = $objMdIaProcIndexaveisRN->consultar($objMdIaProcIndexaveisDTO);

            if (!$objMdIaProcIndexaveisDTO) {
                throw new Exception('Processo não encontrado.', 404);
            }

            $objMdIaProcIndexaveisDTO->setStrSinIndexado('S');
            $objMdIaProcIndexaveisDTO->setDthIndexacao(InfraData::getStrDataHoraAtual());
            $objMdIaProcIndexaveisRN->alterar($objMdIaProcIndexaveisDTO);

            return [
                'status' => 'success',
                'message' => 'Status atualizado com sucesso.'
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function atualizarStatusDocumentoIndexado($idDocumento)
    {
        try {
            if (!$idDocumento) {
                throw new Exception('Id do documento é um parametro obrigatório.', 400);
            }

            $objMdIaDocIndexaveisRN = new MdIaDocIndexaveisRN();
            $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
            $objMdIaDocIndexaveisDTO->setDblIdDocumento($idDocumento);
            $objMdIaDocIndexaveisDTO->retDblIdDocumento();

            $objMdIaDocIndexaveisDTO = $objMdIaDocIndexaveisRN->consultar($objMdIaDocIndexaveisDTO);

            if (!$objMdIaDocIndexaveisDTO) {
                throw new Exception('Documento não encontrado.', 404);
            }

            $objMdIaDocIndexaveisDTO->setStrSinIndexado('S');
            $objMdIaDocIndexaveisDTO->setDthIndexacao(InfraData::getStrDataHoraAtual());
            $objMdIaDocIndexaveisRN->alterar($objMdIaDocIndexaveisDTO);

            return [
                'status' => 'success',
                'message' => 'Status atualizado com sucesso.'
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarProcessosIndexadosCancelados($parametros)
    {
        try {
            $arrObjMdIaProcIndexCanc = $this->listarProcessosIndexaveisCancelados($parametros);

            if ($arrObjMdIaProcIndexCanc["quantidadeRegistrosTotal"] == 0) {
                return $this->retornoErro('Nenhum Processo pendente de cancelamento.', 404, false);
            }

            $arrIdProcedimentos = InfraArray::converterArrInfraDTO($arrObjMdIaProcIndexCanc["registros"], 'IdProcedimento');

            return [
                'status' => 'success',
                'data' => [
                    'IdProcedimentos'                => $arrIdProcedimentos,
                    'QuantidadeRegistrosEntregue'                => count($arrObjMdIaProcIndexCanc["registros"]),
                    'QuantidadeRegistrosTotal'                => $arrObjMdIaProcIndexCanc["quantidadeRegistrosTotal"],
                    'IdUltimoRegistroEntregue'                => (int) end($arrObjMdIaProcIndexCanc["registros"])->getDblIdProcedimento()
                ]
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function removerProcessoIndexadoListaCancelados($idProcedimento)
    {
        try {
            $objMdIaProcIndexCancRN = new MdIaProcIndexCancRN();
            $objMdIaProcIndexCancDTO = new MdIaProcIndexCancDTO();
            $objMdIaProcIndexCancDTO->setDblIdProcedimento($idProcedimento);
            $objMdIaProcIndexCancDTO->retDblIdProcedimento();
            $objMdIaProcIndexCancDTO->setNumMaxRegistrosRetorno(1);
            $objMdIaProcIndexCancDTO = $objMdIaProcIndexCancRN->consultar($objMdIaProcIndexCancDTO);

            if (empty($objMdIaProcIndexCancDTO)) {
                throw new Exception('Processo não encontrado.', 404);
            }

            $objMdIaProcIndexCancRN->excluir(array($objMdIaProcIndexCancDTO));

            return [
                'status' => 'success',
                'message' => 'Processo removido da lista de cancelados com sucesso.'
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }


    public function removerDocumentoIndexadoListaCancelados($idDocumento)
    {
        try {
            $objMdIaDocIndexCancRN = new MdIaDocIndexCancRN();
            $objMdIaDocIndexCancDTO = new MdIaDocIndexCancDTO();
            $objMdIaDocIndexCancDTO->setDblIdDocumento($idDocumento);
            $objMdIaDocIndexCancDTO->retDblIdDocumento();
            $objMdIaDocIndexCancDTO->setNumMaxRegistrosRetorno(1);
            $objMdIaDocIndexCancDTO = $objMdIaDocIndexCancRN->consultar($objMdIaDocIndexCancDTO);

            if (empty($objMdIaDocIndexCancDTO)) {
                throw new Exception('Documento não encontrado.', 404);
            }

            $objMdIaDocIndexCancRN->excluir(array($objMdIaDocIndexCancDTO));

            return [
                'status' => 'success',
                'message' => 'Documento removido da lista de cancelados com sucesso.'
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function listarDocumentosIndexadosCancelados($parametros)
    {
        try {
            $arrObjMdIaDocIndexCanc = $this->listarDocumentosIndexaveisCancelados($parametros);

            if ($arrObjMdIaDocIndexCanc["quantidadeRegistrosTotal"] == 0) {
                return $this->retornoErro('Nenhum Documento pendente de cancelamento.', 404, false);
            }

            $arrIdDocumentos = InfraArray::converterArrInfraDTO($arrObjMdIaDocIndexCanc["registros"], 'IdDocumento');

            return [
                'status' => 'success',
                'data' => [
                    'IdDocumentos'                => $arrIdDocumentos,
                    'QuantidadeRegistrosEntregue'                => count($arrObjMdIaDocIndexCanc["registros"]),
                    'QuantidadeRegistrosTotal'                => $arrObjMdIaDocIndexCanc["quantidadeRegistrosTotal"],
                    'IdUltimoRegistroEntregue'                => (int) end($arrObjMdIaDocIndexCanc["registros"])->getDblIdDocumento()
                ]
            ];
            return [
                'status' => 'success',
                'IdDocumentos' => $arrIdDocumentos
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    private function listarDocumentosIndexaveisCancelados($parametros)
    {
        $objMdIaDocIndexCancDTO = new MdIaDocIndexCancDTO();
        $objMdIaDocIndexCancDTO->retDblIdDocumento();

        $quantidadeRegistrosTotal =  (new MdIaDocIndexCancRN())->contar($objMdIaDocIndexCancDTO);

        if ($parametros["IdUltimoRegistro"] > 0) {
            $objMdIaDocIndexCancDTO->adicionarCriterio(
                array('IdDocumento'),
                array(InfraDTO::$OPER_MAIOR),
                array($parametros["IdUltimoRegistro"])
            );
        }
        $objMdIaDocIndexCancDTO->setNumMaxRegistrosRetorno($parametros["QuantidadeRegistros"]);
        $registros =  (new MdIaDocIndexCancRN())->listar($objMdIaDocIndexCancDTO);

        return array("registros" => $registros, "quantidadeRegistrosTotal" => $quantidadeRegistrosTotal);
    }

    public function consultarHistoricoTopico($idTopico)
    {
        try {

            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($idTopico);
            $objMdIaInteracaoChatDTO->setOrdNumIdMdIaTopicoChat(InfraDTO::$TIPO_ORDENACAO_DESC);
            $objMdIaInteracaoChatDTO->setNumStatusRequisicao(200);
            $objMdIaInteracaoChatDTO->retStrPergunta();
            $objMdIaInteracaoChatDTO->retStrResposta();
            $objMdIaInteracaoChatDTO->retDthCadastro();
            $objMdIaInteracaoChatDTO->retNumTotalTokens();

            $arrObjMdIaInteracaoChatDTO = (new MdIaInteracaoChatRN())->listar($objMdIaInteracaoChatDTO);

            if (empty($arrObjMdIaInteracaoChatDTO)) {
                return $this->retornoErro('O Tópico do Id informado não possui histórico.', 404, false);
            }

            $retorno = [];
            foreach ($arrObjMdIaInteracaoChatDTO as $objMdIaInteracaoChatDTO) {
                $retorno[] = [
                    'Pergunta'    => mb_convert_encoding($objMdIaInteracaoChatDTO->getStrPergunta(), 'UTF-8', 'HTML-ENTITIES'),
                    'Resposta'    => mb_convert_encoding($objMdIaInteracaoChatDTO->getStrResposta(), 'UTF-8', 'HTML-ENTITIES'),
                    'DthCadastro' => $objMdIaInteracaoChatDTO->getDthCadastro(),
                    'TotalTokens' => $objMdIaInteracaoChatDTO->getNumTotalTokens()
                ];
            }

            return [
                'status' => 'success',
                'data' => $retorno
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    public function consultarUltimoIdMessage()
    {
        try {

            $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
            $objMdIaInteracaoChatDTO->retNumIdMessage();
            $objMdIaInteracaoChatDTO->setOrdNumIdMessage(InfraDTO::$TIPO_ORDENACAO_DESC);
            $objMdIaInteracaoChatDTO->setNumMaxRegistrosRetorno(1);

            $objMdIaInteracaoChatDTO = (new MdIaInteracaoChatRN())->consultar($objMdIaInteracaoChatDTO);

            if (!$objMdIaInteracaoChatDTO) {
                throw new Exception('Não existe registros.', 404);
            }

            return [
                'status' => 'success',
                'data' => (int) $objMdIaInteracaoChatDTO->getNumIdMessage()
            ];
        } catch (Exception $e) {
            return $this->retornoErro($e->getMessage(), $e->getCode());
        }
    }

    private function retornarConteudoDocumentoInterno($idDocumento)
    {
        $retorno = null;

        $documento = new DocumentoDTO();
        $documento->setDblIdDocumento($idDocumento);
        $documento->setStrStaDocumento(DocumentoRN::$TD_EXTERNO, InfraDTO::$OPER_DIFERENTE);
        $documento->retStrConteudo();

        $documento->retNumIdSerie();
        $documento->retStrStaDocumento();
        $documento->retDblIdProtocoloProtocolo();
        $documento = (new DocumentoRN())->consultarRN0005($documento);

        if ($documento) {
            $conteudoDocumento = $documento->getStrConteudo();
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            if ($documento->getNumIdSerie() == $objInfraParametro->getValor('ID_SERIE_EMAIL')) {
                $anexos = self::listarAnexosDocumento($idDocumento);
                if (count($anexos) > 0) {
                    foreach ($anexos as $anexo) {
                        $IdAnexos[] = $anexo->getNumIdAnexo();
                    }
                }
            }
            $retorno = [
                'TipoConteudo'  => "text/html",
                'ConteudoDocumento'     => $this->tratarEncodeString($conteudoDocumento),
                'IdAnexos' => $IdAnexos
            ];
        }

        return $retorno;
    }

    private function montarRetornoConteudoDocumentoExterno($idDocumento)
    {
        $documento = $this->consultarConteudoDocumentoSolr($idDocumento);

        return [
            'TipoConteudo'  => $this->tratarEncodeString($documento->arr[0]->str[0]),
            'ConteudoDocumento'     => $this->tratarEncodeString(mb_convert_encoding($documento->arr[1]->str[0], 'ISO-8859-1', 'UTF-8')),
        ];
    }

    private function listaSegDocumentosRelevantes()
    {
        $MdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
        $MdIaAdmSegDocRelevDTO->retNumIdMdIaAdmDocRelev();
        $MdIaAdmSegDocRelevDTO->retStrSegmentoDocumento();
        $MdIaAdmSegDocRelevDTO->retNumPercentualRelevancia();
        $MdIaAdmSegDocRelevDTO->retNumIdSerie();

        return (new MdIaAdmSegDocRelevRN())->listar($MdIaAdmSegDocRelevDTO);
    }

    private function consultarDocumentosRelevante($documentoDTO)
    {
        $retorno = false;

        $MdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $MdIaAdmDocRelevDTO->setStrAplicabilidade($documentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO ? 'E' : 'I');
        $MdIaAdmDocRelevDTO->setNumIdSerie($documentoDTO->getNumIdSerie());
        $MdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
        $MdIaAdmDocRelevDTO->retNumIdTipoProcedimento();

        $arrMdIaAdmDocRelevDTO = (new MdIaAdmDocRelevRN())->listar($MdIaAdmDocRelevDTO);

        foreach ($arrMdIaAdmDocRelevDTO as $MdIaAdmDocRelevDTO) {
            if (
                ($MdIaAdmDocRelevDTO && $MdIaAdmDocRelevDTO->getNumIdTipoProcedimento() == null) ||
                ($MdIaAdmDocRelevDTO && $MdIaAdmDocRelevDTO->getNumIdTipoProcedimento() == $documentoDTO->getNumIdTipoProcedimentoProcedimento())
            ) {
                $retorno = true;
            }
        }

        return $retorno;
    }

    private function buscarProcessoFilho($idProtocolo)
    {
        $protocoloProtocoloRN = new RelProtocoloProtocoloRN();
        $relProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $relProtocoloProtocoloDTO->setDblIdProtocolo2($idProtocolo);
        $relProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_RELACIONADO);
        $relProtocoloProtocoloDTO->retDblIdProtocolo1();
        $arrRelProtocoloProtocoloDTO = $protocoloProtocoloRN->listarRN0187($relProtocoloProtocoloDTO);

        return $this->buscarDadosProcessoRelacionado(InfraArray::converterArrInfraDTO($arrRelProtocoloProtocoloDTO, 'IdProtocolo1'));
    }

    private function buscarProcessoPai($idProtocolo)
    {
        $protocoloProtocoloRN = new RelProtocoloProtocoloRN();
        $relProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $relProtocoloProtocoloDTO->setDblIdProtocolo1($idProtocolo);
        $relProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_RELACIONADO);
        $relProtocoloProtocoloDTO->retDblIdProtocolo2();
        $arrRelProtocoloProtocoloDTO = $protocoloProtocoloRN->listarRN0187($relProtocoloProtocoloDTO);

        return $this->buscarDadosProcessoRelacionado(InfraArray::converterArrInfraDTO($arrRelProtocoloProtocoloDTO, 'IdProtocolo2'));
    }

    private function buscarProcessoAnexado($idProtocolo)
    {
        $protocoloProtocoloRN = new RelProtocoloProtocoloRN();
        $relProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $relProtocoloProtocoloDTO->setDblIdProtocolo1($idProtocolo);
        $relProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
        $relProtocoloProtocoloDTO->retDblIdProtocolo2();
        $arrRelProtocoloProtocoloDTO = $protocoloProtocoloRN->listarRN0187($relProtocoloProtocoloDTO);

        return InfraArray::converterArrInfraDTO($arrRelProtocoloProtocoloDTO, 'IdProtocolo2');
    }

    private function buscarInteressadosPorProcedimento($idProtocolo)
    {
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteDTO->retStrNomeContato();
        $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipanteDTO->setDblIdProtocolo($idProtocolo);
        $objParticipanteDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrObjParticipanteDTO = (new ParticipanteRN())->listarRN0189($objParticipanteDTO);

        $retorno = [];
        foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
            $retorno[] = [
                'IdInteressado' => (int) $this->tratarEncodeString($objParticipanteDTO->getNumIdContato()),
                'NomeInteressado'      => $this->tratarEncodeString($objParticipanteDTO->getStrNomeContato())
            ];
        }

        return $retorno;
    }

    private function buscarDadosProcessoRelacionado($arrIdProtocolo)
    {
        if (empty($arrIdProtocolo)) {
            return null;
        }
        $protocoloDTO = new ProtocoloDTO();
        $protocoloDTO->setDblIdProtocolo($arrIdProtocolo, InfraDTO::$OPER_IN);
        $protocoloDTO->retStrProtocoloFormatado();
        $protocoloDTO->retStrDescricao();
        $protocoloDTO->retStrNomeTipoProcedimentoProcedimento();
        $protocoloDTO->retStrSiglaUnidadeGeradora();
        $protocoloDTO->retStrDescricaoUnidadeGeradora();

        $arrProtocoloDTO = (new ProtocoloRN())->listarRN0668($protocoloDTO);

        $retorno = [];

        foreach ($arrProtocoloDTO as $protocoloDTO) {
            $retorno[] = [
                'SiglaUnidadeGeradoraProcesso' => $this->tratarEncodeString($protocoloDTO->getStrSiglaUnidadeGeradora()),
                'Especificacao'                => $this->tratarEncodeString($protocoloDTO->getStrDescricao())
            ];
        }

        return $retorno;
    }

    private function buscarDthMaxAtualizacaoDocumento($idDocumento)
    {
        $SecaoDocumentoDTO = new SecaoDocumentoDTO();
        $SecaoDocumentoDTO->setDblIdDocumento($idDocumento);
        $SecaoDocumentoDTO->retNumIdSecaoDocumento();
        $arrSecaoDocumentoDTO = (new SecaoDocumentoRN())->listar($SecaoDocumentoDTO);

        $arrIdSecaoDocumento = InfraArray::converterArrInfraDTO($arrSecaoDocumentoDTO, 'IdSecaoDocumento');

        $VersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
        $VersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($arrIdSecaoDocumento, InfraDTO::$OPER_IN);
        $VersaoSecaoDocumentoDTO->setOrdDthAtualizacao(InfraDTO::$TIPO_ORDENACAO_DESC);
        $VersaoSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);
        $VersaoSecaoDocumentoDTO->retDthAtualizacao();
        $VersaoSecaoDocumentoDTO = (new VersaoSecaoDocumentoRN())->consultar($VersaoSecaoDocumentoDTO);


        //formatação da data para o mesmo formato gravado em banco pois o formato é alterado ao recuperar do banco de dados
        $dataOriginal = $VersaoSecaoDocumentoDTO->getDthAtualizacao();
        $data = DateTime::createFromFormat('d/m/Y H:i:s', $dataOriginal);
        $data = $data->format('Y-m-d H:i:s');

        return $data;
    }

    private function listaProcessosIndexaveis($parametros)
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->retDblIdProcedimento();
        $objMdIaProcIndexaveisDTO->setStrSinIndexado('N');

        $quantidadeRegistrosTotal =  (new MdIaProcIndexaveisRN())->contar($objMdIaProcIndexaveisDTO);
        if ($parametros["IdUltimoRegistro"] > 0) {
            $objMdIaProcIndexaveisDTO->adicionarCriterio(
                array('IdProcedimento'),
                array(InfraDTO::$OPER_MAIOR),
                array($parametros["IdUltimoRegistro"])
            );
        }
        $objMdIaProcIndexaveisDTO->setNumMaxRegistrosRetorno($parametros["QuantidadeRegistros"]);
        $registros =  (new MdIaProcIndexaveisRN())->listar($objMdIaProcIndexaveisDTO);

        return array("registros" => $registros, "quantidadeRegistrosTotal" => $quantidadeRegistrosTotal);
    }

    private function listaDocumentosIndexaveis($parametros)
    {
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
        $objMdIaDocIndexaveisDTO->retDblIdDocumento();
        $objMdIaDocIndexaveisDTO->setStrSinIndexado('N');

        $quantidadeRegistrosTotal =  (new MdIaDocIndexaveisRN())->contar($objMdIaDocIndexaveisDTO);
        if ($parametros["IdUltimoRegistro"] > 0) {
            $objMdIaDocIndexaveisDTO->adicionarCriterio(
                array('IdDocumento'),
                array(InfraDTO::$OPER_MAIOR),
                array($parametros["IdUltimoRegistro"])
            );
        }
        $objMdIaDocIndexaveisDTO->setNumMaxRegistrosRetorno($parametros["QuantidadeRegistros"]);
        $registros =  (new MdIaDocIndexaveisRN())->listar($objMdIaDocIndexaveisDTO);

        return array("registros" => $registros, "quantidadeRegistrosTotal" => $quantidadeRegistrosTotal);
    }

    private function listarProcessosIndexaveisCancelados($parametros)
    {
        $objMdIaProcIndexCancDTO = new MdIaProcIndexCancDTO();
        $objMdIaProcIndexCancDTO->retDblIdProcedimento();

        $quantidadeRegistrosTotal =  (new MdIaProcIndexCancRN())->contar($objMdIaProcIndexCancDTO);

        if ($parametros["IdUltimoRegistro"] > 0) {
            $objMdIaProcIndexCancDTO->adicionarCriterio(
                array('IdProcedimento'),
                array(InfraDTO::$OPER_MAIOR),
                array($parametros["IdUltimoRegistro"])
            );
        }

        $objMdIaProcIndexCancDTO->setNumMaxRegistrosRetorno($parametros["QuantidadeRegistros"]);

        $registros =  (new MdIaProcIndexCancRN())->listar($objMdIaProcIndexCancDTO);

        return array("registros" => $registros, "quantidadeRegistrosTotal" => $quantidadeRegistrosTotal);
    }

    public function retornoErro($msg, $codigoErro, $log = true)
    {
        if ($log) {
            $this->gravarLogSei($msg, $codigoErro);
        }
        $erroMensagem = mb_convert_encoding($msg, 'UTF-8', 'ISO-8859-1');
        header('Content-Type: text/html; charset=utf-8');
        return [
            'status' => 'error',
            'code' => $codigoErro,
            'message' => $erroMensagem
        ];
    }

    private function tratarEncodeString($string)
    {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }

    private function consultarConteudoDocumentoSolr($idDocumento)
    {

        $queryParams = [];

        if (!empty($idDocumento)) {
            $queryParams[] = "id_doc:" . intval($idDocumento);
        }

        // Monta a query final (caso tenha múltiplos filtros, usa "AND" para combiná-los)
        $queryString = count($queryParams) > 0 ? implode(" AND ", $queryParams) : "*:*";

        $parametros = new stdClass();
        $parametros->q = $queryString;
        $parametros->start = 0;
        $parametros->rows = 1;

        $urlBusca = ConfiguracaoSEI::getInstance()->getValor('Solr', 'Servidor') . '/' . ConfiguracaoSEI::getInstance()->getValor('Solr', 'CoreProtocolos') . '/select?' . http_build_query($parametros) . '&hl=true&hl.snippets=2&hl.fl=content&hl.fragsize=100&hl.maxAnalyzedChars=1048576&hl.alternateField=content&hl.maxAlternateFieldLength=100&fl=id_proc,content,content_type';

        // Faz a requisição HTTP ao Solr
        $resultados = file_get_contents($urlBusca);

        $xml = simplexml_load_string($resultados);

        if (!$xml->xpath('/response/result/doc')) {
            throw new Exception('Nenhum documento encontrado para o id_documento: ' . $idDocumento, 404);
        }

        $registros = $xml->xpath('/response/result/doc');

        return current($registros);
    }


    private function gravarLogSei($mensagem, $codigoErro)
    {
        $log = "00001 - ERRO DE RECURSO NA API DO SEI IA \n";
        $log .= "00002 - PARAMETROS DA OPERAÇÃO: \n";
        unset($_REQUEST['IdentificacaoServico']);
        $log .= print_r($_REQUEST, true);
        $log .= "00003 - Tipo de Indisponibilidade: " . $codigoErro . " \n";
        $log .= "00004 - Mensagem retornada pelo Servidor: " . $mensagem . " \n";
        $log .= "00005 - Data e hora: " . InfraData::getStrDataHoraAtual() . " \n";
        $log .= "00006 - FIM \n";
        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
    }
}

//TODO para funcionar SOAP e REST deve ter essa validação. E quando deixar de existir serviço do tipo SOAP no modulo esse trecho deve ser removido
if (empty($_GET) && empty($_GET)) {
    $servidorSoap = new BeSimple\SoapServer\SoapServer("wsia.wsdl", array(
        'encoding' => 'ISO-8859-1',
        'soap_version' => SOAP_1_1,
        'attachment_type' => BeSimple\SoapCommon\Helper::ATTACHMENTS_TYPE_MTOM
    ));
    $servidorSoap->setClass("IaWS");

    //Só processa se acessado via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $servidorSoap->handle();
    }
}
