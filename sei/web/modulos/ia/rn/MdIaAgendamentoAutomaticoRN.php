<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4 REGIO
 *
 * 05/01/2018 - criado por ellyson.cast
 *
 * Verso do Gerador de Cdigo: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAgendamentoAutomaticoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function atualizarListaProcedimentosRelevantesControlado()
    {

        try {
            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Processos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            //BUSCAR OS DADOS PARA COMPARACAO E ATUALIZACAO
            $listaAtual = $this->buscarHashProcessosRelevantes();
            $listaSalva = $this->listaProcessosIndexacao();
            if (!is_null($listaAtual)) {
                InfraDebug::getInstance()->gravar('Quantidade de processos : ' . count($listaAtual));

                // ATUALIZAR REGISTROS EXISTENTES
                foreach ($listaAtual as $idProcedimento => $hash) {
                    if ($listaSalva[$idProcedimento]) {
                        if ($listaSalva[$idProcedimento]['hash'] != $hash) {
                            $this->atualizarRegistroProcessoIndexado($idProcedimento, $hash);
                        }
                        unset($listaSalva[$idProcedimento]);
                        unset($listaAtual[$idProcedimento]);
                    }
                }

                // CADASTRAR REGISTROS NOVOS
                foreach ($listaAtual as $idProcedimento => $hash) {
                    $this->cadastrarRegistroProcessoIndexado($idProcedimento, $hash);
                }
            }

            // CANCELAR REGISTROS REMOVIDOS
            foreach ($listaSalva as $idProcedimento => $processoIndexado) {
                $this->removerRegistroProcessoIndexado($idProcedimento);
                $this->cadastrarRegistroProcessoIndexadoCancelados($idProcedimento);
            }


            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

            throw new InfraException('Erro removendo dados temporários de auditoria.', $e);
        }
    }

    private function buscarHashProcessosRelevantes()
    {
        $arrProcessosDocumentosRelevantes = $this->buscarProcessosDocumentosRelevantes();
        $this->atualizarArrComInformacoesNecessarias($arrProcessosDocumentosRelevantes);

        return $this->montarRetornoProcessoIndexados($arrProcessosDocumentosRelevantes);
    }

    private function montarRetornoProcessoIndexados($arrProcessosDocumentosRelevantes)
    {
        foreach ($arrProcessosDocumentosRelevantes as $idProcedimento => $processoDocumentosRelevantes) {
            $retorno[$idProcedimento] = md5(
                $arrProcessosDocumentosRelevantes[$idProcedimento]['idTipoProcesso'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['listaInteressados'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['idProcessosAnexados'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['listaDocumentos'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoProcesso'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoDocumentos']
            );
        }

        return $retorno;
    }

    private function atualizarArrComInformacoesNecessarias(&$arrProcessosDocumentosRelevantes)
    {
        foreach ($arrProcessosDocumentosRelevantes as $idProcedimento => $processoDocumentosRelevantes) {
            $objProcedimentoDTO = $this->buscarProcedimento($idProcedimento);

            //ID_TIPO_PROCESSO
            $arrProcessosDocumentosRelevantes[$idProcedimento]['idTipoProcesso'] = $objProcedimentoDTO->getNumIdTipoProcedimento();

            //ID_INTERESSADOS
            $arrProcessosDocumentosRelevantes[$idProcedimento]['listaInteressados'] = '';
            $arrObjParticipanteDTO = $this->listarInteressadosPorProcedimento($idProcedimento);
            foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
                $arrProcessosDocumentosRelevantes[$idProcedimento]['listaInteressados'] .= $objParticipanteDTO->getNumIdContato();
            }

            //ESPECIFICAÇÃO DO PROCESSO
            $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoProcesso'] = $objProcedimentoDTO->getStrDescricaoProtocolo();

            //PROCESSOS_ANEXADOS
            $arrProcessosDocumentosRelevantes[$idProcedimento]['idProcessosAnexados'] = '';
            $arrObjProcedimentoAnexadosDTO = (new ProcedimentoRN())->listarProcessosAnexados($objProcedimentoDTO);
            foreach ($arrObjProcedimentoAnexadosDTO as $objProcedimentoAnexadosDTO) {
                if (isset($arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()])) {
                    $objProcedimentoAnexadoDTO = $this->buscarProcedimento($objProcedimentoAnexadosDTO->getDblIdProcedimento());

                    $arrProcessosDocumentosRelevantes[$idProcedimento]['idTipoProcesso'] .= $objProcedimentoAnexadoDTO->getNumIdTipoProcedimento();

                    $arrObjParticipanteAnexoDTO = $this->listarInteressadosPorProcedimento($objProcedimentoAnexadosDTO->getDblIdProcedimento());
                    foreach ($arrObjParticipanteAnexoDTO as $objParticipanteDTO) {
                        $arrProcessosDocumentosRelevantes[$idProcedimento]['listaInteressados'] .= $objParticipanteDTO->getNumIdContato();
                    }

                    $arrProcessosDocumentosRelevantes[$idProcedimento]['idProcessosAnexados'] .= $objProcedimentoAnexadosDTO->getDblIdProcedimento();
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['listaDocumentos'] .= $arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()]['listaDocumentos'];
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoDocumentos'] .= $arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()]['especificacaoDocumentos'];
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoProcesso'] = $objProcedimentoAnexadoDTO->getStrDescricaoProtocolo();

                    unset($arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()]);
                }
            }
        }
    }

    private function buscarProcedimento($idProcedimento)
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retNumIdTipoProcedimento();
        $objProcedimentoDTO->retStrDescricaoProtocolo();
        return (new ProcedimentoRN())->consultarRN0201($objProcedimentoDTO);
    }

    private function buscarProcessosDocumentosRelevantes()
    {
        $arrExtensoesPermitidas = ["pdf", "html", "htm", "txt", "ods", "xlsx", "csv", "xml", "odt", "odp", "doc", "docx", "json", "ppt", "pptx", "rtf", "xls", "xlsm"];

        $arrObjMdIaAdmDocRelevDTO = $this->listaTiposDocumentosRelevantes();
        $retorno = array();

        $ext = 0;
        $int = 0;

        foreach ($arrObjMdIaAdmDocRelevDTO as $objMdIaAdmDocRelevDTO) {

            // ATRIBUTOS QUE TORNA O DOCUMENTO PASSILVEL DE SER RELEVANTES
            $objMdIaDocumentoDTO = new MdIaDocumentoDTO();
            $objMdIaDocumentoDTO->retDblIdDocumento();
            $objMdIaDocumentoDTO->retDblIdProcedimento();
            $objMdIaDocumentoDTO->retStrStaDocumento();
            $objMdIaDocumentoDTO->retStrNomeAnexo();
            $objMdIaDocumentoDTO->retStrStaEstadoProcedimento();
            $objMdIaDocumentoDTO->retStrEspecificacaoDocumento();
            $objMdIaDocumentoDTO->setStrStaEstadoProcedimento(array(ProtocoloRN::$TE_NORMAL, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO, ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO), InfraDTO::$OPER_IN);
            $objMdIaDocumentoDTO->setStrSinBloqueado('S');
            $objMdIaDocumentoDTO->setStrStaEstadoProtocolo(ProtocoloRN::$TE_NORMAL);
            $objMdIaDocumentoDTO->setOrdDblIdDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);

            // ATRIBUTOS DA TABELA DE DOCUMENTOS RELEVANTES
            $objMdIaDocumentoDTO->setNumIdSerie($objMdIaAdmDocRelevDTO->getNumIdSerie());
            $objMdIaDocumentoDTO->setStrStaDocumento($objMdIaAdmDocRelevDTO->getStrAplicabilidade() == 'E' ? DocumentoRN::$TD_EXTERNO : $objMdIaAdmDocRelevDTO->getStrAplicabilidade());
            if ($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento() != null) {
                $objMdIaDocumentoDTO->setNumIdTipoProcedimentoProcedimento($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento());
            }

            $arrObjDocumentoDTO = (new MdIaDocumentoRN())->listar($objMdIaDocumentoDTO);

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
                    $extensaoAnexo = end(explode('.', $objDocumentoDTO->getStrNomeAnexo()));
                    if (in_array($extensaoAnexo, $arrExtensoesPermitidas)) {
                        $this->adicionarAoRetorno($objDocumentoDTO, $retorno);
                        $ext++;
                    }
                } else {
                    $this->adicionarAoRetorno($objDocumentoDTO, $retorno);
                    $int++;
                }

                if ($objDocumentoDTO->getStrStaEstadoProcedimento() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
                    $this->criarIndiceProcessoPai($objDocumentoDTO, $retorno);
                }
            }
        }

        InfraDebug::getInstance()->gravar('Quantidade de documentos externos: ' . $ext);
        InfraDebug::getInstance()->gravar('Quantidade de documentos internos: ' . $int);

        return $retorno;
    }

    private function listaTiposDocumentosRelevantes()
    {
        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
        $objMdIaAdmDocRelevDTO->retNumIdSerie();
        $objMdIaAdmDocRelevDTO->retStrAplicabilidade();
        $objMdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
        $objMdIaAdmDocRelevDTO->setStrSinAtivo('S');
        return (new MdIaAdmDocRelevRN())->listar($objMdIaAdmDocRelevDTO);
    }

    private function listarInteressadosPorProcedimento($idProtocolo)
    {
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteDTO->setDblIdProtocolo($idProtocolo);
        $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipanteDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);

        return (new ParticipanteRN())->listarRN0189($objParticipanteDTO);
    }

    private function adicionarAoRetorno(MdIaDocumentoDTO $documento, array &$retorno)
    {
        $indice = $documento->getDblIdProcedimento();
        if (!isset($retorno[$indice])) {
            $retorno[$indice]['listaDocumentos'] = '';
            $retorno[$indice]['especificacaoProcesso'] = '';
        }
        $retorno[$indice]['listaDocumentos'] .= $documento->getDblIdDocumento();
        $retorno[$indice]['especificacaoDocumentos'] .= str_replace(' ', '', $documento->getStrEspecificacaoDocumento());
    }

    private function criarIndiceProcessoPai(MdIaDocumentoDTO $documento, array &$retorno)
    {

        $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
        $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
        $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($documento->getDblIdProcedimento());
        $objRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->consultarRN0841($objRelProtocoloProtocoloDTO);

        if ($objRelProtocoloProtocoloDTO && !isset($retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()])) {
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['listaDocumentos'] = '';
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['especificacaoDocumentos'] = '';
        }
    }

    private function listaProcessosIndexacao()
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->retDblIdProcedimento();
        $objMdIaProcIndexaveisDTO->retStrHash();
        $objMdIaProcIndexaveisDTO->retStrSinIndexado();

        $arrObjMdIaProcIndexaveisDTO = (new MdIaProcIndexaveisRN())->listar($objMdIaProcIndexaveisDTO);
        $retorno = [];

        foreach ($arrObjMdIaProcIndexaveisDTO as $objMdIaProcIndexaveisDTO) {
            $retorno[$objMdIaProcIndexaveisDTO->getDblIdProcedimento()]['hash'] = $objMdIaProcIndexaveisDTO->getStrHash();
            $retorno[$objMdIaProcIndexaveisDTO->getDblIdProcedimento()]['sinIndexado'] = $objMdIaProcIndexaveisDTO->getStrSinIndexado();
        }

        unset($arrObjMdIaProcIndexaveisDTO);

        return $retorno;
    }

    private function atualizarRegistroProcessoIndexado($idProcedimento, $hash)
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaProcIndexaveisDTO->setStrHash($hash);
        $objMdIaProcIndexaveisDTO->setStrSinIndexado('N');
        $objMdIaProcIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        (new MdIaProcIndexaveisRN())->alterar($objMdIaProcIndexaveisDTO);
    }

    private function cadastrarRegistroProcessoIndexado($idProcedimento, $hash)
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaProcIndexaveisDTO->setStrHash($hash);
        $objMdIaProcIndexaveisDTO->setStrSinIndexado('N');
        $objMdIaProcIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        (new MdIaProcIndexaveisRN())->cadastrar($objMdIaProcIndexaveisDTO);
    }

    private function removerRegistroProcessoIndexado($idProcedimento)
    {
        $objMdIaProcIndexaveisRN = new MdIaProcIndexaveisRN();
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaProcIndexaveisDTO->retDblIdProcedimento();
        $arrObjMdIaProcIndexaveisDTO = $objMdIaProcIndexaveisRN->listar($objMdIaProcIndexaveisDTO);
        $objMdIaProcIndexaveisRN->excluir($arrObjMdIaProcIndexaveisDTO);
    }

    private function cadastrarRegistroProcessoIndexadoCancelados($idProcedimento)
    {
        $objMdIaProcIndexCancDTO = new MdIaProcIndexCancDTO;
        $objMdIaProcIndexCancDTO->retDblIdProcedimento();
        $objMdIaProcIndexCancDTO->setDblIdProcedimento($idProcedimento);
        $idCadastrado = (new MdIaProcIndexCancRN())->consultar($objMdIaProcIndexCancDTO);
        if (!$idCadastrado) {
            (new MdIaProcIndexCancRN())->cadastrar($objMdIaProcIndexCancDTO);
        }
    }

    protected function atualizarListaDocsElegiveisPesquisaDocumentosControlado()
    {

        try {
            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Documentos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            //BUSCAR OS DADOS PARA COMPARACAO E ATUALIZACAO
            $listaAtual = $this->buscarDocumentosRelevantes();
            $listaSalva = $this->listaDocumentosIndexacao();

            InfraDebug::getInstance()->gravar('Quantidade de documentos : ' . count($listaAtual));

            // ATUALIZAR REGISTROS EXISTENTES
            foreach ($listaAtual as $idDocumento) {
                if ($listaSalva[$idDocumento]) {
                    unset($listaSalva[$idDocumento]);
                    unset($listaAtual[$idDocumento]);
                }
            }

            // CADASTRAR REGISTROS NOVOS
            foreach ($listaAtual as $idDocumento) {
                $this->cadastrarRegistroDocumentoIndexado($idDocumento);
            }

            // CANCELAR REGISTROS REMOVIDOS
            foreach ($listaSalva as $idDocumento) {
                $this->removerRegistroDocumentoIndexado($idDocumento);
                $this->cadastrarRegistroDocumentoIndexadoCancelados($idDocumento);
            }


            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

            throw new InfraException('Erro agendamento atualiza lista de documentos relevantes.', $e);
        }
    }

    private function listaDocumentosIndexacao()
    {
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
        $objMdIaDocIndexaveisDTO->retDblIdDocumento();
        $objMdIaDocIndexaveisDTO->retStrSinIndexado();

        $arrObjMdIaDocIndexaveisDTO = (new MdIaDocIndexaveisRN())->listar($objMdIaDocIndexaveisDTO);

        $arrIdsDocumentos = InfraArray::converterArrInfraDTO($arrObjMdIaDocIndexaveisDTO, 'IdDocumento', 'IdDocumento');
        return $arrIdsDocumentos;
    }

    private function cadastrarRegistroDocumentoIndexado($idDocumento)
    {
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
        $objMdIaDocIndexaveisDTO->setDblIdDocumento($idDocumento);
        $objMdIaDocIndexaveisDTO->setStrSinIndexado('N');
        $objMdIaDocIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        (new MdIaDocIndexaveisRN())->cadastrar($objMdIaDocIndexaveisDTO);
    }

    private function removerRegistroDocumentoIndexado($idDocumento)
    {
        $objMdIaDocIndexaveisRN = new MdIaDocIndexaveisRN();
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
        $objMdIaDocIndexaveisDTO->setDblIdDocumento($idDocumento);
        $objMdIaDocIndexaveisDTO->retDblIdDocumento();
        $arrObjMdIaDocIndexaveisDTO = $objMdIaDocIndexaveisRN->listar($objMdIaDocIndexaveisDTO);
        $objMdIaDocIndexaveisRN->excluir($arrObjMdIaDocIndexaveisDTO);
    }

    private function cadastrarRegistroDocumentoIndexadoCancelados($idDocumento)
    {
        $objMdIaDocIndexCancDTO = new MdIaDocIndexCancDTO;
        $objMdIaDocIndexCancDTO->retDblIdDocumento();
        $objMdIaDocIndexCancDTO->setDblIdDocumento($idDocumento);
        $idCadastrado = (new MdIaDocIndexCancRN())->consultar($objMdIaDocIndexCancDTO);
        if (!$idCadastrado) {
            (new MdIaDocIndexCancRN())->cadastrar($objMdIaDocIndexCancDTO);
        }
    }

    private function buscarDocumentosRelevantes()
    {
        //buscar id_serie cujo documento precisa pertencer
        $MdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();
        $MdIaAdmTpDocPesqDTO->setStrSinAtivo('S');
        $MdIaAdmTpDocPesqDTO->retNumIdSerie();
        $arrMdIaAdmTpDocPesqDTO = (new MdIaAdmTpDocPesqRN)->listar($MdIaAdmTpDocPesqDTO);
        $arrIdsSerie = InfraArray::converterArrInfraDTO($arrMdIaAdmTpDocPesqDTO, 'IdSerie');
        unset($arrMdIaAdmTpDocPesqDTO);

        $documentoDTO = new DocumentoDTO();
        $documentoDTO->setNumIdSerie($arrIdsSerie, InfraDTO::$OPER_IN);
        $documentoDTO->setStrStaEstadoProtocolo(array(ProtocoloRN::$TE_NORMAL, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO), InfraDTO::$OPER_IN);
        $documentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO, InfraDTO::$OPER_DIFERENTE);
        $documentoDTO->setStrStaEstadoProcedimento(ProtocoloRN::$TE_NORMAL);
        $documentoDTO->setStrSinBloqueado('S');
        $documentoDTO->retDblIdDocumento();

        $arrDocumentoDTO = (new DocumentoRN())->listarRN0008($documentoDTO);
        $arrIdsDocumentos = InfraArray::converterArrInfraDTO($arrDocumentoDTO, 'IdDocumento', 'IdDocumento');

        if (empty($arrDocumentoDTO)) {
            throw new Exception('Nenhum registro encontrado.', 404);
        }

        return $arrIdsDocumentos;
    }

    protected function classificarMetasOdsTiposProcessosControlado()
    {
        try {
            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Classificação das Metas ODS');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            (new MdIaClassMetaOdsINT())->classificarAuto();

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

            throw new InfraException('Erro classificar metas ODS.', $e);
        }
    }
}
