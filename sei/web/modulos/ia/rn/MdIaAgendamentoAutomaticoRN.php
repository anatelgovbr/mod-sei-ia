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
    const URL_ANATEL = 'https://sei.anatel.gov.br/sei/controlador_ws.php';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function atualizarListaProcedimentosRelevantesProcessosAbertosControlado()
    {
        try {
            $sinProcessoAberto = 'S';
            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Processos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            $this->atualizarListaProcedimentosRelevantes($sinProcessoAberto);

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

    protected function atualizarListaProcedimentosRelevantesProcessosConcluidosControlado()
    {
        try {
            $sinProcessoAberto = 'N';
            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Processos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            $this->atualizarListaProcedimentosRelevantes($sinProcessoAberto);

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

    protected function atualizarListaProcedimentosRelevantes($sinProcessoAberto)
    {
        //BUSCAR OS DADOS PARA COMPARACAO E ATUALIZACAO
        $listaAtual = $this->buscarHashProcessosRelevantes($sinProcessoAberto);
        $listaSalva = $this->listaProcessosIndexacao($sinProcessoAberto);
        if (!is_null($listaAtual)) {
            InfraDebug::getInstance()->gravar('Quantidade de processos : ' . count($listaAtual));

            // ATUALIZAR REGISTROS EXISTENTES
            foreach ($listaAtual as $idProcedimento => $hash) {
                if ($listaSalva[$idProcedimento]) {
                    if ($listaSalva[$idProcedimento]['hash'] != $hash || is_null($listaSalva[$idProcedimento]['sinProcessoAberto'])) {
                        $this->atualizarRegistroProcessoIndexado($idProcedimento, $hash, $sinProcessoAberto);
                    }
                    unset($listaSalva[$idProcedimento]);
                    unset($listaAtual[$idProcedimento]);
                }
            }

            // CADASTRAR REGISTROS NOVOS
            foreach ($listaAtual as $idProcedimento => $hash) {
                $this->cadastrarRegistroProcessoIndexado($idProcedimento, $hash, $sinProcessoAberto);
            }
        }

        // CANCELAR REGISTROS REMOVIDOS
        foreach ($listaSalva as $idProcedimento => $processoIndexado) {
            $this->removerRegistroProcessoIndexado($idProcedimento);
            $this->cadastrarRegistroProcessoIndexadoCancelados($idProcedimento);
        }
    }

    private function buscarHashProcessosRelevantes($sinProcessoAberto)
    {
        $arrProcessosDocumentosRelevantes = $this->buscarProcessosDocumentosRelevantes($sinProcessoAberto);
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
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['especificacaoDocumentos'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['idAssinaturas'] .
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['dthInclusaoAnexos']
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
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['idAssinaturas'] .= $arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()]['idAssinaturas'];
                    $arrProcessosDocumentosRelevantes[$idProcedimento]['dthInclusaoAnexos'] .= $arrProcessosDocumentosRelevantes[$objProcedimentoAnexadosDTO->getDblIdProcedimento()]['dthInclusaoAnexos'];
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
        $objProcedimentoDTO->retDtaConclusao();
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retNumIdTipoProcedimento();
        $objProcedimentoDTO->retStrDescricaoProtocolo();
        return (new ProcedimentoRN())->consultarRN0201($objProcedimentoDTO);
    }

    private function buscarProcessosDocumentosRelevantes($sinProcessoAberto)
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
            $objMdIaDocumentoDTO->retDthInclusaoAnexo();
            $objMdIaDocumentoDTO->retStrNomeAnexo();
            $objMdIaDocumentoDTO->retStrStaEstadoProcedimento();
            $objMdIaDocumentoDTO->retStrEspecificacaoDocumento();
            $objMdIaDocumentoDTO->retStrSinAtivoAssinatura();
            $objMdIaDocumentoDTO->retNumIdAtividade();
            $objMdIaDocumentoDTO->setStrStaEstadoProcedimento(array(ProtocoloRN::$TE_NORMAL, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO, ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO), InfraDTO::$OPER_IN);
            $objMdIaDocumentoDTO->setStrStaEstadoProtocolo(ProtocoloRN::$TE_NORMAL);
            if ($sinProcessoAberto == 'S') {
                $objMdIaDocumentoDTO->setDthConclusaoProcedimento(null, InfraDTO::$OPER_IGUAL);
            } else {
                $objMdIaDocumentoDTO->setDthConclusaoProcedimento(null, InfraDTO::$OPER_DIFERENTE);
            }
            $objMdIaDocumentoDTO->setOrdDblIdDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);

            // ATRIBUTOS DA TABELA DE DOCUMENTOS RELEVANTES
            $objMdIaDocumentoDTO->setNumIdSerie($objMdIaAdmDocRelevDTO->getNumIdSerie());
            if ($objMdIaAdmDocRelevDTO->getStrAplicabilidade() == "I") {
                $aplicabilidade = array(DocumentoRN::$TD_EDITOR_INTERNO, DocumentoRN::$TD_FORMULARIO_AUTOMATICO, DocumentoRN::$TD_FORMULARIO_GERADO);
            } else {
                $aplicabilidade = array(DocumentoRN::$TD_EXTERNO);
            }
            $objMdIaDocumentoDTO->setStrStaDocumento($aplicabilidade, InfraDTO::$OPER_IN);
            if ($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento() != null) {
                $objMdIaDocumentoDTO->setNumIdTipoProcedimentoProcedimento($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento());
            }
            $arrObjDocumentosDTO = (new MdIaDocumentoRN())->listar($objMdIaDocumentoDTO);

            foreach ($arrObjDocumentosDTO as $objDocumentoDTO) {
                if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
                    $extensaoAnexo = end(explode('.', $objDocumentoDTO->getStrNomeAnexo()));
                    if (in_array($extensaoAnexo, $arrExtensoesPermitidas)) {
                        $this->adicionarAoRetorno($objDocumentoDTO, $retorno, $sinProcessoAberto);
                        $ext++;
                    }
                } else {
                    if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EDITOR_INTERNO || $objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_FORMULARIO_GERADO) {
                        if ($objDocumentoDTO->getStrSinAtivoAssinatura() == 'S') {
                            $this->adicionarAoRetorno($objDocumentoDTO, $retorno, $sinProcessoAberto);
                        }
                    } else {
                        $this->adicionarAoRetorno($objDocumentoDTO, $retorno, $sinProcessoAberto);
                    }
                    $int++;
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

    private function adicionarAoRetorno(MdIaDocumentoDTO $documento, array &$retorno, $sinProcessoAberto)
    {
        if ($documento->getStrStaEstadoProcedimento() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
            $this->criarIndiceProcessoPai($documento, $retorno, $sinProcessoAberto);
        } else if ($documento->getStrStaEstadoProcedimento() != ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
            $indice = $documento->getDblIdProcedimento();
            if (!isset($retorno[$indice])) {
                $retorno[$indice]['listaDocumentos'] = '';
                $retorno[$indice]['especificacaoProcesso'] = '';
                $retorno[$indice]['idAssinaturas'] = '';
                $retorno[$indice]['dthInclusaoAnexos'] = '';
            }
            $retorno[$indice]['listaDocumentos'] .= $documento->getDblIdDocumento();
            $retorno[$indice]['especificacaoDocumentos'] .= str_replace(' ', '', $documento->getStrEspecificacaoDocumento());
            $retorno[$indice]['idAssinaturas'] .= $documento->getNumIdAtividade();
            $retorno[$indice]['dthInclusaoAnexos'] .= $documento->getDthInclusaoAnexo();
        }
    }

    private function criarIndiceProcessoPai(MdIaDocumentoDTO $documento, array &$retorno, $sinProcessoAberto)
    {

        $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
        $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
        $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($documento->getDblIdProcedimento());
        $objRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->consultarRN0841($objRelProtocoloProtocoloDTO);

        $processoPai = $this->buscarProcedimento($objRelProtocoloProtocoloDTO->getDblIdProtocolo1());
        if (($processoPai->getDtaConclusao() == null && $sinProcessoAberto == 'S') || ($processoPai->getDtaConclusao() != null && $sinProcessoAberto == 'N')) {
            if ($objRelProtocoloProtocoloDTO && !isset($retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()])) {
                $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['listaDocumentos'] = '';
                $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['especificacaoDocumentos'] = '';
                $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['idAssinaturas'] = '';
                $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['dthInclusaoAnexos'] = '';
            }
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['listaDocumentos'] .= $documento->getDblIdDocumento();
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['especificacaoDocumentos'] .= str_replace(' ', '', $documento->getStrEspecificacaoDocumento());
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['idAssinaturas'] .= $documento->getNumIdAtividade();
            $retorno[$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]['dthInclusaoAnexos'] .= $documento->getDthInclusaoAnexo();
        }
    }

    private function listaProcessosIndexacao($sinProcessoAberto)
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->retDblIdProcedimento();
        $objMdIaProcIndexaveisDTO->retStrHash();
        $objMdIaProcIndexaveisDTO->retStrSinIndexado();
        $objMdIaProcIndexaveisDTO->retStrSinProcessoAberto();
        $objMdIaProcIndexaveisDTO->adicionarCriterio(['SinProcessoAberto', 'SinProcessoAberto'], [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL], [$sinProcessoAberto, null], InfraDTO::$OPER_LOGICO_OR);

        $arrObjMdIaProcIndexaveisDTO = (new MdIaProcIndexaveisRN())->listar($objMdIaProcIndexaveisDTO);
        $retorno = [];

        foreach ($arrObjMdIaProcIndexaveisDTO as $objMdIaProcIndexaveisDTO) {
            $retorno[$objMdIaProcIndexaveisDTO->getDblIdProcedimento()]['hash'] = $objMdIaProcIndexaveisDTO->getStrHash();
            $retorno[$objMdIaProcIndexaveisDTO->getDblIdProcedimento()]['sinIndexado'] = $objMdIaProcIndexaveisDTO->getStrSinIndexado();
            $retorno[$objMdIaProcIndexaveisDTO->getDblIdProcedimento()]['sinProcessoAberto'] = $objMdIaProcIndexaveisDTO->getStrSinProcessoAberto();
        }

        unset($arrObjMdIaProcIndexaveisDTO);

        return $retorno;
    }

    private function atualizarRegistroProcessoIndexado($idProcedimento, $hash, $sinProcessoAberto)
    {
        $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
        $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
        $objMdIaProcIndexaveisDTO->setStrHash($hash);
        $objMdIaProcIndexaveisDTO->setStrSinIndexado('N');
        $objMdIaProcIndexaveisDTO->setStrSinVetorizado('N');
        $objMdIaProcIndexaveisDTO->setStrSinProcessoAberto($sinProcessoAberto);
        $objMdIaProcIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        (new MdIaProcIndexaveisRN())->alterar($objMdIaProcIndexaveisDTO);
    }

    private function cadastrarRegistroProcessoIndexado($idProcedimento, $hash, $sinProcessoAberto)
    {
        try {
            $objMdIaProcIndexaveisExistenteDTO = new MdIaProcIndexaveisDTO;
            $objMdIaProcIndexaveisExistenteDTO->retDblIdProcedimento();
            $objMdIaProcIndexaveisExistenteDTO->setDblIdProcedimento($idProcedimento);
            if ((new MdIaProcIndexaveisRN())->consultar($objMdIaProcIndexaveisExistenteDTO)) {
                $this->atualizarRegistroProcessoIndexado($idProcedimento, $hash, $sinProcessoAberto);
                return;
            }

            $objMdIaProcIndexaveisDTO = new MdIaProcIndexaveisDTO;
            $objMdIaProcIndexaveisDTO->setDblIdProcedimento($idProcedimento);
            $objMdIaProcIndexaveisDTO->setStrHash($hash);
            $objMdIaProcIndexaveisDTO->setStrSinIndexado('N');
            $objMdIaProcIndexaveisDTO->setStrSinVetorizado('N');
            $objMdIaProcIndexaveisDTO->setStrSinProcessoAberto($sinProcessoAberto);
            $objMdIaProcIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
            (new MdIaProcIndexaveisRN())->cadastrar($objMdIaProcIndexaveisDTO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->gravar('Erro ao cadastrar processo indexável. IdProcedimento: ' . $idProcedimento . ' - ' . $e->getMessage());
        }
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

    protected function atualizarListaDocsElegiveisPesquisaDocumentosProcessosAbertosControlado()
    {
        try {

            $sinProcessoAberto = 'S';

            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Documentos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            $this->atualizarListaDocsElegiveisPesquisaDocumentos($sinProcessoAberto);

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

    protected function atualizarListaDocsElegiveisPesquisaDocumentosProcessosConcluidosControlado()
    {
        try {

            $sinProcessoAberto = 'N';

            ini_set('max_execution_time', '18000');
            ini_set('memory_limit', '-1');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            InfraDebug::getInstance()->gravar('Atualizar Lista de Documentos Indexáveis');

            $numSeg = InfraUtil::verificarTempoProcessamento();

            $this->atualizarListaDocsElegiveisPesquisaDocumentos($sinProcessoAberto);

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

    private function buscarHashDocumentosRelevantes($sinProcessoAberto)
    {
        $arrDocumentosRelevantes = $this->buscarDocumentosRelevantes($sinProcessoAberto);

        return $this->montarRetornoDocumentosPesquisaDocumentos($arrDocumentosRelevantes);
    }


    protected function atualizarListaDocsElegiveisPesquisaDocumentos($sinProcessoAberto)
    {

        //BUSCAR OS DADOS PARA COMPARACAO E ATUALIZACAO
        $listaAtual = $this->buscarHashDocumentosRelevantes($sinProcessoAberto);
        $listaSalva = $this->listaDocumentosIndexacao($sinProcessoAberto);

        if (!is_null($listaAtual)) {
            InfraDebug::getInstance()->gravar('Quantidade de documentos : ' . count($listaAtual));

            // ATUALIZAR REGISTROS EXISTENTES
            foreach ($listaAtual as $idDocumento => $hash) {
                if ($listaSalva[$idDocumento]) {
                    if ($listaSalva[$idDocumento]['hash'] != $hash) {
                        $this->atualizarRegistroDocumentoIndexado($idDocumento, $hash, $sinProcessoAberto);
                    }
                    unset($listaSalva[$idDocumento]);
                    unset($listaAtual[$idDocumento]);
                }
            }

            // CADASTRAR REGISTROS NOVOS
            foreach ($listaAtual as $idDocumento => $hash) {
                $this->cadastrarRegistroDocumentoIndexado($idDocumento, $hash, $sinProcessoAberto);
            }
        }

        // CANCELAR REGISTROS REMOVIDOS
        foreach ($listaSalva as $idDocumento => $documentoIndexado) {
            $this->removerRegistroDocumentoIndexado($idDocumento);
            $this->cadastrarRegistroDocumentoIndexadoCancelados($idDocumento);
        }
    }

    private function atualizarRegistroDocumentoIndexado($idDocumento, $hash, $sinProcessoAberto)
    {
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO();
        $objMdIaDocIndexaveisDTO->setDblIdDocumento($idDocumento);
        $objMdIaDocIndexaveisDTO->setStrHash($hash);
        $objMdIaDocIndexaveisDTO->setStrSinIndexado('N');
        $objMdIaDocIndexaveisDTO->setStrSinVetorizado('N');
        $objMdIaDocIndexaveisDTO->setStrSinProcessoAberto($sinProcessoAberto);
        $objMdIaDocIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
        (new MdIaDocIndexaveisRN())->alterar($objMdIaDocIndexaveisDTO);
    }


    private function listaDocumentosIndexacao($sinProcessoAberto)
    {
        $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
        $objMdIaDocIndexaveisDTO->retDblIdDocumento();
        $objMdIaDocIndexaveisDTO->retStrSinIndexado();
        $objMdIaDocIndexaveisDTO->retStrHash();
        $objMdIaDocIndexaveisDTO->retStrSinProcessoAberto();
        $objMdIaDocIndexaveisDTO->adicionarCriterio(['SinProcessoAberto', 'SinProcessoAberto'], [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL], [$sinProcessoAberto, null], InfraDTO::$OPER_LOGICO_OR);

        $arrObjMdIaDocIndexaveisDTO = (new MdIaDocIndexaveisRN())->listar($objMdIaDocIndexaveisDTO);

        $retorno = [];

        foreach ($arrObjMdIaDocIndexaveisDTO as $objMdIaDocIndexaveisDTO) {
            $retorno[$objMdIaDocIndexaveisDTO->getDblIdDocumento()]['hash'] = $objMdIaDocIndexaveisDTO->getStrHash();
            $retorno[$objMdIaDocIndexaveisDTO->getDblIdDocumento()]['sinIndexado'] = $objMdIaDocIndexaveisDTO->getStrSinIndexado();
        }

        unset($arrObjMdIaDocIndexaveisDTO);

        return $retorno;
    }

    private function cadastrarRegistroDocumentoIndexado($idDocumento, $hash, $sinProcessoAberto)
    {
        try {
            $objMdIaDocIndexaveisExistenteDTO = new MdIaDocIndexaveisDTO;
            $objMdIaDocIndexaveisExistenteDTO->retDblIdDocumento();
            $objMdIaDocIndexaveisExistenteDTO->setDblIdDocumento($idDocumento);
            if ((new MdIaDocIndexaveisRN())->consultar($objMdIaDocIndexaveisExistenteDTO)) {
                $this->atualizarRegistroDocumentoIndexado($idDocumento, $hash, $sinProcessoAberto);
                return;
            }

            $objMdIaDocIndexaveisDTO = new MdIaDocIndexaveisDTO;
            $objMdIaDocIndexaveisDTO->setDblIdDocumento($idDocumento);
            $objMdIaDocIndexaveisDTO->setStrSinIndexado('N');
            $objMdIaDocIndexaveisDTO->setStrHash($hash);
            $objMdIaDocIndexaveisDTO->setStrSinVetorizado('N');
            $objMdIaDocIndexaveisDTO->setStrSinProcessoAberto($sinProcessoAberto);
            $objMdIaDocIndexaveisDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
            (new MdIaDocIndexaveisRN())->cadastrar($objMdIaDocIndexaveisDTO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->gravar('Erro ao cadastrar documento indexável. IdDocumento       : ' . $idDocumento . ' - ' . $e->getMessage());
        }
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

    private function montarRetornoDocumentosPesquisaDocumentos($arrDocumentosRelevantes)
    {
        foreach ($arrDocumentosRelevantes as $idDocumento => $documentoRelevante) {
            $retorno[$idDocumento] = md5(
                $arrDocumentosRelevantes['idTipoProcesso'] .
                    $arrDocumentosRelevantes[$idDocumento]['especificacaoDocumentos'] .
                    $arrDocumentosRelevantes[$idDocumento]['idAssinaturas'] .
                    $arrDocumentosRelevantes[$idDocumento]['dthInclusaoAnexos']
            );
        }

        return $retorno;
    }


    private function buscarDocumentosRelevantes($sinProcessoAberto)
    {
        $arrExtensoesPermitidas = ["pdf", "html", "htm", "txt", "ods", "xlsx", "csv", "xml", "odt", "odp", "doc", "docx", "json", "ppt", "pptx", "rtf", "xls", "xlsm"];

        $retorno = array();

        //buscar id_serie cujo documento precisa pertencer
        $MdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();
        $MdIaAdmTpDocPesqDTO->setStrSinAtivo('S');
        $MdIaAdmTpDocPesqDTO->retNumIdSerie();
        $arrMdIaAdmTpDocPesqDTO = (new MdIaAdmTpDocPesqRN)->listar($MdIaAdmTpDocPesqDTO);
        $arrIdsSerie = InfraArray::converterArrInfraDTO($arrMdIaAdmTpDocPesqDTO, 'IdSerie');
        unset($arrMdIaAdmTpDocPesqDTO);

        $documentoDTO = new MdIaDocumentoDTO();
        $documentoDTO->setNumIdSerie($arrIdsSerie, InfraDTO::$OPER_IN);
        $documentoDTO->setStrStaEstadoProtocolo(array(ProtocoloRN::$TE_NORMAL, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO), InfraDTO::$OPER_IN);
        $documentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO, InfraDTO::$OPER_DIFERENTE);
        $documentoDTO->setStrStaEstadoProcedimento(ProtocoloRN::$TE_NORMAL);
        if ($sinProcessoAberto == 'S') {
            $documentoDTO->setDthConclusaoProcedimento(null, InfraDTO::$OPER_IGUAL);
        } else {
            $documentoDTO->setDthConclusaoProcedimento(null, InfraDTO::$OPER_DIFERENTE);
        }
        $documentoDTO->retDblIdDocumento();
        $documentoDTO->retStrEspecificacaoDocumento();
        $documentoDTO->retNumIdAtividade();
        $documentoDTO->retStrSinAtivoAssinatura();
        $documentoDTO->retStrStaDocumento();
        $documentoDTO->retDthInclusaoAnexo();

        $arrDocumentoDTO = (new MdIaDocumentoRN())->listar($documentoDTO);

        foreach ($arrDocumentoDTO as $objDocumentoDTO) {
            if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EDITOR_INTERNO || $objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_FORMULARIO_GERADO) {
                if ($objDocumentoDTO->getStrSinAtivoAssinatura() == "S") {
                    $this->adicionarAoRetornoDocumentos($objDocumentoDTO, $retorno);
                }
            } else {
                $this->adicionarAoRetornoDocumentos($objDocumentoDTO, $retorno);
            }
        }

        return $retorno;
    }


    private function adicionarAoRetornoDocumentos(MdIaDocumentoDTO $documento, array &$retorno)
    {
        $indice = $documento->getDblIdDocumento();
        if (!isset($retorno[$indice])) {
            $retorno[$indice]['especificacaoProcesso'] = '';
            $retorno[$indice]['idAssinaturas'] = '';
            $retorno[$indice]['dthInclusaoAnexos'] = '';
        }
        $retorno[$indice]['especificacaoDocumentos'] .= str_replace(' ', '', $documento->getStrEspecificacaoDocumento());
        $retorno[$indice]['idAssinaturas'] .= $documento->getNumIdAtividade();
        $retorno[$indice]['dthInclusaoAnexos'] .= $documento->getDthInclusaoAnexo();
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

    protected function EnviarDadosSistemaModuloConectado(): void
    {
        try {
            $config = ConfiguracaoSEI::getInstance()->getValor('SEI');

            if ($config['producao'] === false) {
                return;
            }

            ini_set('max_execution_time', '0');
            $debug = InfraDebug::getInstance();
            $debug->setBolLigado(true);
            $debug->setBolDebugInfra(false);
            $debug->setBolEcho(false);
            $debug->limpar();

            $inicioProcessamento = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('INICIANDO ENVIO DE DADOS PARA CENTRALIZA MODULOS');

            // 1) Tentativa de obter/gerar chave
            $payloadChave = self::montarPayloadGerarChave();
            $palavraChave = $this->obterChaveAcessoValida($payloadChave);

            if (empty($palavraChave)) {
                InfraDebug::getInstance()->gravar('ERRO: CHAVE DE ACESSO NÃO ENCONTRADA OU NÃO GERADA.');
                $this->finalizarProcesso($inicioProcessamento);
                return;
            }

            // 2) Obter lista de módulos
            $modulosMantidos = self::listarModulosMantidosAnatel($palavraChave, $payloadChave['SiglaOrgao']);

            // 3) Se a lista falhar (chave expirada), tenta renovar UMA vez
            if (empty($modulosMantidos->data)) {
                InfraDebug::getInstance()->gravar('CHAVE POSSIVELMENTE EXPIRADA. TENTANDO RENOVAÇÃO ÚNICA...');
                $palavraChave = $this->renovarChaveAcesso($payloadChave);

                if (!empty($palavraChave)) {
                    $modulosMantidos = self::listarModulosMantidosAnatel($palavraChave, $payloadChave['SiglaOrgao']);
                }
            }

            // 4) Processa os módulos se houver dados
            if (!empty($modulosMantidos->data)) {
                $configuracoes = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
                $modulosInstalados = $configuracoes['SEI']['Modulos'] ?? [];

                foreach ($modulosMantidos->data as $moduloAnatel) {
                    $nomeClasse = $moduloAnatel->Modulo;

                    if (!isset($modulosInstalados[$nomeClasse]) || !class_exists($nomeClasse)) {
                        continue;
                    }

                    $objModulo = new $nomeClasse();
                    InfraDebug::getInstance()->gravar("ENVIANDO: " . $objModulo->getNome());

                    $payload = $this->montarPayloadEnviarDados($objModulo, $palavraChave, $nomeClasse);
                    $resposta = $this->enviarDados($payload, 30);

                    InfraDebug::getInstance()->gravar($this->validarRespostaEnviarDados($resposta));
                    $this->verificarNotificarUltimaVersaoModuloDisponivel($objModulo, $moduloAnatel->Versao, $moduloAnatel->URLRepositorio);
                }
            } else {
                InfraDebug::getInstance()->gravar('AVISO: NENHUM MÓDULO PENDENTE DE ENVIO OU RETORNO VAZIO DA API.');
            }

            $this->finalizarProcesso($inicioProcessamento);
        } catch (Exception $e) {
            // Log de erro crítico sem dar throw para não quebrar o agendador
            InfraDebug::getInstance()->gravar('ERRO CRÍTICO NA FUNÇÃO: ' . $e->getMessage());
            $debug->setBolLigado(false);
            $debug->setBolDebugInfra(false);
            $debug->setBolEcho(false);
        }
    }

    /**
     * Encapsula o fechamento do log e tempo de execução
     */
    private function finalizarProcesso($tempoInicio): void
    {
        $tempoTotal = InfraUtil::verificarTempoProcessamento($tempoInicio);
        InfraDebug::getInstance()->gravar("TEMPO TOTAL: {$tempoTotal}s - FIM.");
        // Desliga o debug para evitar logs desnecessários
        //LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
    }

    private function obterChaveAcessoValida(array $payload): string
    {
        $chave = self::recuperaChaveAcessoParametro();
        return !empty($chave) ? $chave : $this->renovarChaveAcesso($payload);
    }

    protected function renovarChaveAcesso(array $payload): string
    {
        $arrChave = self::gerarChaveAcesso($payload, 30);
        if ($arrChave['http_code'] == 200 && isset($arrChave['json']['message'])) {
            self::salvarChaveAcessoAgendamento($arrChave['json']['message']);
            return $arrChave['json']['message'];
        }

        InfraDebug::getInstance()->gravar('FALHA AO RENOVAR CHAVE DE ACESSO. HTTP CODE: ' . $arrChave['http_code'] . ' - RESPOSTA: ' . $arrChave['json']['message']);
        return '';
    }

    protected function recuperaChaveAcessoParametro()
    {
        $retorno = '';
        $infraAgendamentoDTO    = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->setStrComando('MdIaAgendamentoAutomaticoRN::EnviarDadosSistemaModulo');
        $infraAgendamentoDTO->retStrParametro();
        $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
        $agendamento = (new InfraAgendamentoTarefaRN())->consultar($infraAgendamentoDTO);

        if (!empty($agendamento)) {
            $arrParametros = explode(',', $agendamento->getStrParametro());

            foreach ($arrParametros as $parametro) {
                $obj = explode('=', $parametro);
                if ($obj[0] == 'palavraChave') {
                    $retorno = $obj[1];
                }
            }
        }

        return $retorno;
    }

    private function salvarChaveAcessoAgendamento($palavraChave)
    {

        $infraAgendamentoTarefaRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->setStrComando('MdIaAgendamentoAutomaticoRN::EnviarDadosSistemaModulo');
        $infraAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
        $infraAgendamentoDTO->retStrParametro();
        $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
        $objInfraAgendamentoDTO = $infraAgendamentoTarefaRN->consultar($infraAgendamentoDTO);
        $objInfraAgendamentoDTO->setStrParametro('palavraChave=' . $palavraChave);
        $infraAgendamentoTarefaRN->alterar($objInfraAgendamentoDTO);
    }

    private static function gerarChaveAcesso(array $body, int $timeout = 30)
    {
        // ----------------------------------------------------------------------------
        // 1) Monta URL do serviço
        // ----------------------------------------------------------------------------

        $base = self::URL_ANATEL;

        $path = '/md_central_mds_gerar_chave_acesso';
        $servico = 'md_central_mds_gerar_chave_acesso';

        $url = $base . $path . '?servico=' . urlencode($servico);

        $ch = curl_init($url);

        $json = json_encode($body, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new InfraException('Falha ao gerar JSON do payload: ' . json_last_error_msg());
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $err = curl_error($ch);
            $no  = curl_errno($ch);
            throw new InfraException("Erro ao chamar serviço REST (cURL #$no): $err");
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // nem sempre a API devolve JSON em erro; então retornamos raw junto
            return [
                'http_code' => $httpCode,
                'raw' => $raw,
                'json' => null
            ];
        }

        return [
            'http_code' => $httpCode,
            'raw' => $raw,
            'json' => $decoded
        ];
    }

    private static function listarModulosMantidosAnatel($palavraChave, $siglaOrgao)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => self::URL_ANATEL . '/md_central_mds_lista_modulos_distribuidos'
                . '?servico=md_central_mds_lista_modulos_distribuidos'
                . '&SiglaOrgao=' . $siglaOrgao
                . '&IdentificacaoServico=' . $palavraChave,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT_MS => 15000
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode === 200 && $response !== false) {
            return json_decode($response);
        }

        self::logErroConsultaModulosAnatel();
        return null;
    }

    private static function logErroConsultaModulosAnatel()
    {
        $log  = "00001 - ERRO DE RECURSO NO SEI IA\n";
        $log .= "00002 - NÃO FOI POSSIVEL CONSULTAR OS MÓDULOS MANTIDOS PELA ANATEL\n";
        $log .= "00003 - DATA E HORA: " . InfraData::getStrDataHoraAtual() . "\n";
        $log .= "00004 - FIM\n";

        //LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
    }

    private function montarPayloadEnviarDados($moduloIntegracao, $palavraChave, $nomeClasse)
    {
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

        return [
            'SiglaOrgao'           => mb_convert_encoding($SessaoSei['SiglaOrgaoSistema'], 'UTF-8', 'ISO-8859-1'),
            'IdentificacaoServico' => $palavraChave,
            'Modulo'               => mb_convert_encoding($nomeClasse, 'UTF-8', 'ISO-8859-1'),
            'VersaoModulo'         => mb_convert_encoding($moduloIntegracao->getVersao(), 'UTF-8', 'ISO-8859-1')
        ];
    }

    private function montarPayloadGerarChave()
    {
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');
        $BancoSEI = $objInfraConfiguracao->getValor('BancoSEI');
        $SEI = $objInfraConfiguracao->getValor('SEI');
        $emailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

        return [
            'SiglaOrgao'            => mb_convert_encoding($SessaoSei['SiglaOrgaoSistema'], 'UTF-8', 'ISO-8859-1'),
            'VersaoSei'             => SEI_VERSAO,
            'Url'                   => $SEI['URL'],
            'EmailAdministrador'    => $emailAdministrador,
            'TipoBancoDados'        => $BancoSEI['Tipo']
        ];
    }

    private function validarRespostaEnviarDados($resp)
    {
        // ----------------------------------------------------------------------------
        // 4) Valida retorno
        // ----------------------------------------------------------------------------
        $msgRetotorno = 'DADOS ENVIADOS COM SUCESSO.';
        if ($resp['http_code'] < 200 || $resp['http_code'] >= 300) {
            $msg = 'Falha ao enviar dados. HTTP ' . $resp['http_code'];
            if (is_array($resp['json']) && isset($resp['json']['message'])) {
                $msg .= ' - ' . $resp['json']['message'];
            }
            $msgRetotorno = $msg;
        }

        if (is_array($resp['json']) && ($resp['json']['status'] ?? '') !== 'success') {
            $msgRetotorno = 'SERVIÇO RETORNOU ERRO: ' . mb_convert_encoding($resp['json']['message'] ?? $resp['raw'], 'ISO-8859-1', 'UTF-8');
        }

        return $msgRetotorno;
    }

    private function enviarDados(array $body, int $timeout = 30): array
    {
        // ----------------------------------------------------------------------------
        // 1) Monta URL do serviço
        // ----------------------------------------------------------------------------

        $base = self::URL_ANATEL;

        $path = '/md_central_mds_recebe_dados_orgaos_externos';
        $servico = 'md_central_mds_recebe_dados_orgaos_externos';

        $url = $base . $path . '?servico=' . urlencode($servico);

        $ch = curl_init($url);

        $json = json_encode($body, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new InfraException('Falha ao gerar JSON do payload: ' . json_last_error_msg());
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $err = curl_error($ch);
            $no  = curl_errno($ch);
            throw new InfraException("Erro ao chamar serviço REST (cURL #$no): $err");
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // nem sempre a API devolve JSON em erro; então retornamos raw junto
            return [
                'http_code' => $httpCode,
                'raw' => $raw,
                'json' => null
            ];
        }

        return [
            'http_code' => $httpCode,
            'raw' => $raw,
            'json' => $decoded
        ];
    }

    private function verificarNotificarUltimaVersaoModuloDisponivel($moduloIntegracao, $versaoModuloDisponivel, $urlRepositorio)
    {

        $versaoGitHub = ltrim($versaoModuloDisponivel, 'vV');
        if (version_compare($moduloIntegracao->getVersao(), $versaoGitHub) < 0) {
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
            $strEmailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

            $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
            $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

            if (!InfraString::isBolVazia($strEmailSistema) && !InfraString::isBolVazia($strEmailAdministrador)) {

                MailSEI::getInstance()->limpar();

                $objEmailDTO = new EmailDTO();
                $objEmailDTO->setStrDe($strEmailSistema);
                $objEmailDTO->setStrPara($strEmailAdministrador);
                $objEmailDTO->setStrAssunto('SEI - Nova versão do módulo ' . $moduloIntegracao->getNome() . ' disponível.');

                $strConteudo = 'Prezado(a),' . "\n\n\n";
                $strConteudo .= 'Informamos que foi disponibilizada uma nova versão do módulo ' . $moduloIntegracao->getNome() . ', mantido pela Anatel.' . "\n\n";
                $strConteudo .= 'Versão ' . $versaoModuloDisponivel . ' disponível.' . "\n\n";
                $strConteudo .= 'Atualmente, o órgão ' . $SessaoSei['SiglaOrgaoSistema'] . ' utiliza a versão ' . $moduloIntegracao->getVersao() . ' deste módulo.' . "\n\n";
                $strConteudo .= 'Recomendamos que seja verificada a compatibilidade da nova versão com a versão do SEI instalada no ambiente, a fim de possibilitar a atualização e o aproveitamento das melhorias, correções e eventuais ajustes de segurança disponibilizados.' . "\n\n";
                $strConteudo .= 'Para esclarecimento de dúvidas ou solicitação de suporte, utilize as issues do repositório oficial no GitHub ' . $urlRepositorio . ', onde a equipe responsável realiza o acompanhamento.' . "\n\n\n";
                $strConteudo .= 'Atenciosamente,' . "\n";
                $strConteudo .= 'SEI - Sistema Eletrônico de Informações' . "\n";

                $objEmailDTO->setStrMensagem($strConteudo);

                MailSEI::getInstance()->adicionar($objEmailDTO);
                MailSEI::getInstance()->enviar();
            }
        }
    }
}
