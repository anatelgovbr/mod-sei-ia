<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';


class MdIaRecursoRN extends InfraRN
{
    const TIME_OUT = '10000';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function conexaoApiRecomendacaoSimilaridadeConectado($dadosProcesso)
    {

        try {

            $urlApi = $this->retornarUrlApi();

            $urlConsulta = $urlApi["linkRecomendacaoProcesso"] . $dadosProcesso[1]->getIdProcedimento() . '?rows=' . $dadosProcesso[0]->getNumQtdProcessListagem();

            $curl = curl_init();

            // Configura
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $urlConsulta,
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);

            // Envio e armazenamento da resposta
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos
            curl_close($curl);
            if ($httpcode == "200") {
                return json_decode($response);
            } elseif($httpcode == "404") {
                return $httpcode;
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO SEI IA \n";
                $log .= "00002 - Processo: " . $dadosProcesso[1]->getProcedimentoFormatado() . " \n";
                $log .= "00003 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00004 - Endpoint do Recurso: " . $urlConsulta . " \n";
                $log .= "00005 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00006 - Mensagem retornada pelo Servidor: " . utf8_decode($response) . " \n";
                $log .= "00007 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                return null;
            }

            return json_decode($response);
        } catch (Exception $e) {
            throw new InfraException('Erro ao buscar recomenda��es de processos..', $e);
        }
    }

    protected function submeteSimilaridadeConectado($dadosEnviados)
    {
        try {
            $urlApi = $this->retornarUrlApi();
            $urlOperacao = $urlApi['linkFeedbackRecomendacaoProcesso'];
            // Inicia
            $curl = curl_init();
            // Configura
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $urlOperacao,
                CURLOPT_POSTFIELDS => $dadosEnviados[0],
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);
            // Envio e armazenamento da resposta
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos
            if ($httpcode == "200") {
                return array($httpcode, json_decode($response));
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO DE FEEDBACK DE PROCESSOS SIMILARES NO SEI IA \n";
                $log .= "00002 - Processo: " . $dadosEnviados[1]->getProcedimentoFormatado() . " \n";
                $log .= "00003 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00004 - Endpoint do Recurso: " . $urlOperacao . " \n";
                $log .= "00005 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00006 - Mensagem retornada pelo Servidor: " . utf8_decode($response) . " \n";
                $log .= "00007 - JSON enviado ao Servidor: " . $dadosEnviados[0] . " \n";
                $log .= "00008 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO DE FEEDBACK DE PROCESSOS SIMILARES NO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                throw new InfraException('Erro ao submeter Feedback de Processos Similares');
            }

            return json_decode($response);

        } catch (Exception $e) {
            throw new InfraException('Erro ao submeter Feedback de Processos Similares', $e);
        }
    }

    protected function submetePesquisaDocumentoConectado($dadosEnviados)
    {
        try {

            $urlApi = $this->retornarUrlApi();
            $urlOperacao = $urlApi['linkFeedbackRecomendacaoDocumento'];
            // Inicia
            $curl = curl_init();
            // Configura
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $urlOperacao,
                CURLOPT_POSTFIELDS => $dadosEnviados[0],
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);
            // Envio e armazenamento da resposta
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos
            if ($httpcode == "200") {
                return array($httpcode, json_decode($response));
            } else {
                $log = "00001 - INDISPONIBILIDADE DO RECURSO DE FEEDBACK DE PESQUISA DE DOCUMENTO NO SEI IA \n";
                $log .= "00002 - Processo: " . $dadosEnviados[1]->getProcedimentoFormatado() . " \n";
                $log .= "00003 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00004 - Endpoint do Recurso: " . $urlOperacao . " \n";
                $log .= "00005 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00006 - Mensagem retornada pelo Servidor: " . utf8_decode($response) . " \n";
                $log .= "00007 - JSON enviado ao Servidor: " . $dadosEnviados[0] . " \n";
                $log .= "00008 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DO RECURSO DE FEEDBACK DE PESQUISA DE DOCUMENTO NO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                throw new InfraException('Erro ao submeter Feedback de Pesquisa de Documento');
            }

            return json_decode($response);

        } catch (Exception $e) {
            throw new InfraException('Erro ao submeter Feedback de Pesquisa de Documento', $e);
        }
    }
    public function retornarUrlApi()
    {
        $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion("1");
        $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
        $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);

        $urlsIntegracao = array();
        if ($objMdIaAdmIntegracaoDTO) {
            $urlsIntegracao['urlBase'] = $objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl();
            $urlsIntegracao['linkRecomendacaoProcesso'] = $urlsIntegracao['urlBase'] . ":8082/process-recommenders/weighted-mlt-recommender/recommendations/";
            $urlsIntegracao['linkFeedbackRecomendacaoProcesso'] = $urlsIntegracao['urlBase'] . ":8086/process-recommenders/feedbacks";
            $urlsIntegracao['linkIndexacaoProcesso'] = $urlsIntegracao['urlBase'] . ":8082/process-recommenders/weighted-mlt-recommender/indexed-ids/";
            $urlsIntegracao['linkRecomendacaoDocumentos'] = $urlsIntegracao['urlBase'] . ":8082/document-recommenders/mlt-recommender/recommendations?";
            $urlsIntegracao['linkFeedbackRecomendacaoDocumento'] = $urlsIntegracao['urlBase'] . ":8086/document-recommenders/feedbacks";
        }
        $urlsIntegracao['linkSwagger'] = ":8082/openapi.json";
        $urlsIntegracao['linkConsultaDisponibilidade'] = ":8082/health";

        return $urlsIntegracao;
    }

    public function enviaPostPesquisaDocumento($dadosConsulta)
    {

        try {
            $urlOperacao = $dadosConsulta[0];
            $curl = curl_init();

            // Configura
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $urlOperacao,
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);

            // Envio e armazenamento da resposta
            $response = curl_exec($curl);

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos
            curl_close($curl);
            if ($httpcode == "200") {
                return $response;
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO SEI IA \n";
                $log .= "00002 - Processo: " . $dadosConsulta[1]->getProcedimentoFormatado() . " \n";
                $log .= "00003 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00004 - Endpoint do Recurso: " . $urlOperacao . " \n";
                $log .= "00005 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00006 - Mensagem retornada pelo Servidor: " . utf8_decode($response) . " \n";
                $log .= "00007 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                throw new InfraException('Erro retornando Pesquisa Documentos!');
            }

            return json_decode($response);
        } catch (Exception $e) {
            throw new InfraException('Erro retornando Pesquisa Documentos.', $e);
        }
    }

    public function validaConexaoApi()
    {
        try {

            $urlApi = $this->retornarUrlApi();

            $urlConsulta = $urlApi['urlBase'] . $urlApi["linkConsultaDisponibilidade"];

            $curl = curl_init();

            // Configura
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $urlConsulta,
                CURLOPT_TIMEOUT_MS => '2000'
            ]);

            // Envio e armazenamento da resposta
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos
            curl_close($curl);
            if ($httpcode == "200") {
                return true;
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO SEI IA \n";
                $log .= "00003 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00004 - Endpoint do Recurso: " . $urlConsulta . " \n";
                $log .= "00005 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00006 - Mensagem retornada pelo Servidor: " . utf8_decode($response) . " \n";
                $log .= "00007 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                return false;
            }

            return json_decode($response);
        } catch (Exception $e) {
            throw new InfraException('Erro validando conex�o com API.', $e);
        }
    }

    public function exibeFuncionalidade()
    {

        $bolExibirFuncionalidade = false;

        $objMdIaAdmConfigSimilarDTO = new MdIaAdmConfigSimilarDTO();
        $objMdIaAdmConfigSimilarDTO->retStrSinExibirFuncionalidade();
        $objMdIaAdmConfigSimilarRN = new MdIaAdmConfigSimilarRN();
        $objMdIaAdmConfigSimilarDTO = $objMdIaAdmConfigSimilarRN->consultar($objMdIaAdmConfigSimilarDTO);

        if ($objMdIaAdmConfigSimilarDTO->getStrSinExibirFuncionalidade() == "S") {
            $bolExibirFuncionalidade = true;
            return $bolExibirFuncionalidade;
        }

        $objMdIaAdmPesqDocDTO = new MdIaAdmPesqDocDTO();
        $objMdIaAdmPesqDocDTO->setNumIdMdIaAdmPesqDoc(1);
        $objMdIaAdmPesqDocDTO->retStrSinExibirFuncionalidade();
        $objMdIaAdmPesqDocRN = new MdIaAdmPesqDocRN();
        $objMdIaAdmPesqDocDTO = $objMdIaAdmPesqDocRN->consultar($objMdIaAdmPesqDocDTO);

        if ($objMdIaAdmPesqDocDTO) {
            if ($objMdIaAdmPesqDocDTO->getStrSinExibirFuncionalidade() == "S") {
                $bolExibirFuncionalidade = true;
                return $bolExibirFuncionalidade;
            }
        }

        $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
        $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
        $objMdIaAdmOdsOnuRN = new MdIaAdmOdsOnuRN();
        $objMdIaAdmOdsOnuDTO = $objMdIaAdmOdsOnuRN->consultar($objMdIaAdmOdsOnuDTO);

        if ($objMdIaAdmOdsOnuDTO) {
            if ($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == "S") {
                $bolExibirFuncionalidade = true;
                return $bolExibirFuncionalidade;
            }
        }

        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSinExibirFuncionalidade();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        if ($objMdIaAdmConfigAssistIADTO) {
            if ($objMdIaAdmConfigAssistIADTO->getStrSinExibirFuncionalidade() == "S") {
                $bolExibirFuncionalidade = true;
                return $bolExibirFuncionalidade;
            }
        }
    }

    public function verificarSelecaoDocumentoAlvo(DocumentoDTO $objDocumentoDTO)
    {
        try {

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->retStrStaProtocolo();
            $objProtocoloDTO->retDblIdProtocolo();
            $objProtocoloDTO->retDblIdProtocolo();
            $objProtocoloDTO->retStrStaNivelAcessoGlobal();
            $objProtocoloDTO->retStrStaNivelAcessoLocal();
            $objProtocoloDTO->retDblIdProcedimentoDocumento();
            $objProtocoloDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());
            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

            if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_SIGILOSO) {
                $objEntradaConsultarDocumentoAPI = new EntradaConsultarDocumentoAPI();
                $objEntradaConsultarDocumentoAPI->setIdDocumento($objDocumentoDTO->getDblIdDocumento());

                $objSeiRN = new SeiRN();
                $objSaidaConsultarDocumentoAPI = new SaidaConsultarDocumentoAPI();
                $objSaidaConsultarDocumentoAPI = $objSeiRN->consultarDocumento($objEntradaConsultarDocumentoAPI);
            } else {

                $objProtocoloRN = new ProtocoloRN();
                $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();

                $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS);
                $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
                $objPesquisaProtocoloDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());

                $objProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);
                return ($objDocumentoDTO->getStrStaEstadoProtocolo() != ProtocoloRN::$TE_DOCUMENTO_CANCELADO
                    &&
                    ($objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo() == SessaoSEI::getInstance()->getNumIdUnidadeAtual() || $objDocumentoDTO->getStrSinPublicado() == 'S' || $objDocumentoDTO->getStrSinAssinado()=='S' || $objProtocoloDTO[0]->getStrSinAcessoAssinaturaBloco()=='S' || $objProtocoloDTO[0]->getStrSinAcessoRascunhoBloco()=='S')
                );
            }
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    protected function verificarExistenciaTipoDocumentoConectado($params)
    {
        $arrObjSerieAPI = $params;

        $arrIds = array();
        foreach ($arrObjSerieAPI as $objSerie) {
            $arrIds[] = $objSerie->getIdSerie();
        }

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();

        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->setNumIdSerie($arrIds, InfraDTO::$OPER_IN);
        $objMdIaAdmDocRelevDTO->retStrNomeSerie();
        $objMdIaAdmDocRelevDTO->setNumMaxRegistrosRetorno(1);
        $tipoDocumento = $objMdIaAdmDocRelevRN->consultar($objMdIaAdmDocRelevDTO);
        $msg = "";
        if($tipoDocumento) {
            $msg .= "N�o � permitido excluir o Tipo de Documento ".$tipoDocumento->getStrNomeSerie().", pois ele � utilizado pelo M�dulo de Intelig�ncia Artificial. \n";
            $msg .= "Verifique as parametriza��es no menu Administra��o > Intelig�ncia Artificial > Documentos Relevantes.";
        }
        return $msg;
    }

    protected function verificarExistenciaTipoProcessoConectado($params)
    {
        $arrObjTpProcAPI = $params;

        $arrIds = array();
        foreach ($arrObjTpProcAPI as $objSerie) {
            $arrIds[] = $objSerie->getIdTipoProcedimento();
        }

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();

        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
        $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento($arrIds, InfraDTO::$OPER_IN);
        $objMdIaAdmDocRelevDTO->setNumMaxRegistrosRetorno(1);
        $tipoProcesso = $objMdIaAdmDocRelevRN->consultar($objMdIaAdmDocRelevDTO);

        $msg = "";
        if($tipoProcesso) {
            $msg .= "N�o � permitido excluir o Tipo de Processo " . $tipoProcesso->getStrNomeTipoProcedimento() . ", pois ele � utilizado  pelo M�dulo de Intelig�ncia Artificial. \n";
            $msg .= " Verifique as parametriza��es no menu Administra��o > Intelig�ncia Artificial > Documentos Relevantes.";
        }
        return $msg;
    }
}
