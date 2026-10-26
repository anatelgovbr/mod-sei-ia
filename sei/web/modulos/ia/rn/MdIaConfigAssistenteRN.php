<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';
require_once dirname(__FILE__) . '/MdIaAutenticacaoSSLRN.php';

class MdIaConfigAssistenteRN extends InfraRN
{
    const TIME_OUT = '600000';
    const NUM_TENTATIVAS_CONEXAO = 2;
    const CONTEXTO_PROTOCOLO = 'protocolo';
    const CONTEXTO_AVULSO = 'avulso';

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function listarExtensoesPermitidasProtocolo()
    {
        return array_keys($this->listarRegrasExtensoesPermitidas(self::CONTEXTO_PROTOCOLO));
    }

    public function listarExtensoesPermitidasAvulso()
    {
        return array_keys($this->listarRegrasExtensoesPermitidas(self::CONTEXTO_AVULSO));
    }

    public function listarMapaExtensoesPermitidasUpload()
    {
        $arrRegras = $this->listarRegrasExtensoesPermitidas(self::CONTEXTO_AVULSO);
        $arrRetorno = array();

        foreach ($arrRegras as $strExtensao => $arrRegra) {
            $arrRetorno[$strExtensao] = $arrRegra['tamanhoMaximoMb'];
        }

        return $arrRetorno;
    }

    public function verificarExtensaoPermitida($strExtensao, $strContexto)
    {
        $strExtensao = $this->normalizarExtensao($strExtensao);

        if ($strExtensao === null) {
            return false;
        }

        $arrRegras = $this->listarRegrasExtensoesPermitidas($strContexto);

        return isset($arrRegras[$strExtensao]);
    }

    public function consultarRegraExtensaoPermitida($strExtensao, $strContexto)
    {
        $strExtensao = $this->normalizarExtensao($strExtensao);

        if ($strExtensao === null) {
            return null;
        }

        $arrRegras = $this->listarRegrasExtensoesPermitidas($strContexto);

        if (!isset($arrRegras[$strExtensao])) {
            return null;
        }

        return $arrRegras[$strExtensao];
    }

    public function listarRegrasExtensoesPermitidas($strContexto)
    {
        $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
        $objMdIaAdmExtPermitidaDTO->retStrExtensao();
        $objMdIaAdmExtPermitidaDTO->retNumTamanhoMaximoPermitido();
        $objMdIaAdmExtPermitidaDTO->retNumFormaEntregaArquivo();
        $objMdIaAdmExtPermitidaDTO->setStrSinAtivo('S');

        if ($strContexto == self::CONTEXTO_PROTOCOLO) {
            $objMdIaAdmExtPermitidaDTO->setStrSinPermitidaProtocolo('S');
        } elseif ($strContexto == self::CONTEXTO_AVULSO) {
            $objMdIaAdmExtPermitidaDTO->setStrSinPermitidaAvulso('S');
        } else {
            throw new InfraException('Contexto de extensao permitida invalido.');
        }

        $arrObjMdIaAdmExtPermitidaDTO = (new MdIaAdmExtPermitidaRN())->listar($objMdIaAdmExtPermitidaDTO);
        $numLimitePadraoMb = $this->consultarTamanhoMaximoPadraoUploadMb();
        $arrRetorno = array();

        foreach ($arrObjMdIaAdmExtPermitidaDTO as $objMdIaAdmExtPermitidaDTO) {
            $strExtensao = $this->normalizarExtensao($objMdIaAdmExtPermitidaDTO->getStrExtensao());

            if ($strExtensao === null) {
                continue;
            }

            $numTamanhoMaximoMb = $objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido();
            if ($strContexto == self::CONTEXTO_AVULSO && InfraString::isBolVazia($numTamanhoMaximoMb)) {
                $numTamanhoMaximoMb = $numLimitePadraoMb;
            }

            $arrRetorno[$strExtensao] = array(
                'extensao' => $strExtensao,
                'forma_entrega_arquivo' => $objMdIaAdmExtPermitidaDTO->getNumFormaEntregaArquivo(),
                'tamanhoMaximoMb' => InfraString::isBolVazia($numTamanhoMaximoMb) ? null : (int)$numTamanhoMaximoMb,
            );
        }

        return $arrRetorno;
    }

    private function consultarTamanhoMaximoPadraoUploadMb()
    {
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $numLimitePadraoMb = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');

        if (!is_numeric($numLimitePadraoMb) || $numLimitePadraoMb < 1) {
            throw new InfraException('Valor do parametro SEI_TAM_MB_DOC_EXTERNO invalido.');
        }

        return (int)$numLimitePadraoMb;
    }

    private function normalizarExtensao($strExtensao)
    {
        if ($strExtensao === null) {
            return null;
        }

        $strExtensao = trim($strExtensao);
        $strExtensao = ltrim($strExtensao, '.');
        $strExtensao = InfraString::transformarCaixaBaixa($strExtensao);
        $strExtensao = str_replace(' ', '', $strExtensao);

        if ($strExtensao === '') {
            return null;
        }

        return $strExtensao;
    }

    public function consultarDisponibilidadeApi($url)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT_MS => '5000',
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            MdIaAutenticacaoSSLRN::configurarAutenticacaoSSL($curl);

            $response = false;
            $httpcode = 0;
            for ($numTentativa = 1; $numTentativa <= self::NUM_TENTATIVAS_CONEXAO; $numTentativa++) {
                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if ($response !== false) {
                    break;
                }
            }

            // Fecha e limpa recursos
            curl_close($curl);
            if ($httpcode == "200") {
                return json_decode($response);
            } else {
                $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $response);
                $log = "00001 - ERRO DE RECURSO NO ASSISTENTE DO SEI IA \n";
                $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00003 - Endpoint do Recurso: " . $url . " \n";
                $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00005 - Mensagem retornada pelo Servidor: " . $resposta . " \n";
                $log .= "00006 - Mensagem apresentada ao usuário:  Assistente de IA está indisponível no momento. Tente novamente mais tarde. \n";
                $log .= "00007 - Data e hora: " . InfraData::getStrDataHoraAtual() . " \n";
                $log .= "00008 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "ERRO DE RECURSO NO ASSISTENTE DO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, '', '', $strAssunto, $strConteudo);

                return false;
            }
        } catch (Exception $e) {
            throw new InfraException('Erro ao buscar recomendações de processos..', $e);
        }
    }

    public function retornarUrlApi()
    {
        $idMdIaAdmIntegFuncion = 2;
        $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion($idMdIaAdmIntegFuncion);
        $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
        $objMdIaAdmIntegracaoDTO = (new MdIaAdmIntegracaoRN())->consultar($objMdIaAdmIntegracaoDTO);

        $urlsIntegracao = array();
        if ($objMdIaAdmIntegracaoDTO) {
            $urlsIntegracao['urlBase'] = $objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl();
            $urlsIntegracao['linkEndpoint'] = $urlsIntegracao['urlBase'] . $this->consultarUrlApi($idMdIaAdmIntegFuncion, 'linkEndpoint');
            $urlsIntegracao['janelaContexto'] = MdIaAdmConfigAssistIARN::$LLM_GPT_4_128K_CONTEXTO;
        }
        $urlsIntegracao['linkSwagger'] = $this->consultarUrlApi($idMdIaAdmIntegFuncion, 'linkSwagger');
        $urlsIntegracao['linkConsultaDisponibilidade'] = $this->consultarUrlApi($idMdIaAdmIntegFuncion, 'linkConsultaDisponibilidade');
        $urlsIntegracao['linkFeedback'] = $this->consultarUrlApi($idMdIaAdmIntegFuncion, 'linkFeedback');

        return $urlsIntegracao;
    }

    private function consultarUrlApi($IdMdIaAdmIntegFuncion, $referencia)
    {
        $objMdIaAdmUrlIntegracaoDTO = new MdIaAdmUrlIntegracaoDTO();
        $objMdIaAdmUrlIntegracaoDTO->setStrReferencia($referencia);
        $objMdIaAdmUrlIntegracaoDTO->setNumIdAdmIaAdmIntegracao($IdMdIaAdmIntegFuncion);
        $objMdIaAdmUrlIntegracaoDTO->retStrUrl();
        $objMdIaAdmUrlIntegracaoDTO->setNumMaxRegistrosRetorno(1);
        $objMdIaAdmUrlIntegracaoDTO = (new MdIaAdmUrlIntegracaoRN)->consultar($objMdIaAdmUrlIntegracaoDTO);

        return $objMdIaAdmUrlIntegracaoDTO->getStrUrl();
    }

    public function enviarFeedbackResposta($dadosEnviados)
    {

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $dadosEnviados[1],
                CURLOPT_POSTFIELDS => $dadosEnviados[0],
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_TIMEOUT_MS => self::TIME_OUT
            ]);
            MdIaAutenticacaoSSLRN::configurarAutenticacaoSSL($curl);
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode == "200") {
                return array($httpcode, json_decode($response));
            } else {
                $resposta = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $response);
                $log = "00001 - ERRO DE RECURSO NO FEEDBACK  DO ASSISTENTE DO SEI IA \n";
                $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00003 - Endpoint do Recurso: " . $dadosEnviados[1] . " \n";
                $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00005 - Mensagem retornada pelo Servidor: " . $resposta . " \n";
                $log .= "00006 - Mensagem apresentada ao usuário:  Não é apresentada mensagem ao usuário. \n";
                $log .= "00007 - Data e hora: " . InfraData::getStrDataHoraAtual() . " \n";
                $log .= "00008 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "ERRO DE RECURSO NO FEEDBACK  DO ASSISTENTE DO SEI IA ";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, '', '', $strAssunto, $strConteudo);
                return false;
            }
        } catch (Exception $e) {
            throw new InfraException('Erro ao cadastrar Similaridade', $e);
        }
    }

    protected function consultarAnexoControlado($dblIdDocumento)
    {
        if (!isset($dblIdDocumento)) {
            throw new InfraException('Parâmetro $dblIdDocumento não informado.');
        }
        $objAnexoRN = new AnexoRN();

        $objAnexoDTO = new AnexoDTO();
        $objAnexoDTO->retNumIdAnexo();
        $objAnexoDTO->retStrNome();
        $objAnexoDTO->retDblIdProtocolo();
        $objAnexoDTO->retDthInclusao();
        $objAnexoDTO->retNumTamanho();
        $objAnexoDTO->retStrProtocoloFormatadoProtocolo();
        $objAnexoDTO->setDblIdProtocolo($dblIdDocumento);

        return $objAnexoRN->consultarRN0736($objAnexoDTO);
    }
}
