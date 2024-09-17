<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 19/05/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';


class MdIaConfigAssistenteRN extends InfraRN
{
    const TIME_OUT = '600000';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
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

            // Envio e armazenamento da resposta
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Fecha e limpa recursos

            curl_close($curl);
            if ($httpcode == "200") {
                return json_decode($response);
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO ASSISTENTE DO SEI IA \n";
                $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00003 - Endpoint do Recurso: " . $url . " \n";
                $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00005 - Mensagem retornada pelo Servidor: " . utf8_decode($resposta) . " \n";
                $log .= "00006 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO ASSISTENTE DO SEI IA";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);

                return false;
            }
            return json_decode($response);
        } catch (Exception $e) {
            throw new InfraException('Erro ao buscar recomendações de processos..', $e);
        }
    }

    public function retornarUrlApi()
    {
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $objMdIaAdmConfigAssistIADTO->retNumLlmAtivo();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion("2");
        $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
        $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);

        $urlsIntegracao = array();
        if ($objMdIaAdmConfigAssistIADTO && $objMdIaAdmIntegracaoDTO) {
            $urlsIntegracao['urlBase'] = $objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl();
            switch ($objMdIaAdmConfigAssistIADTO->getNumLlmAtivo()) {
                case MdIaAdmConfigAssistIARN::$LLM_GPT_4_128K_ID:
                    $urlsIntegracao['linkEndpoint'] = $urlsIntegracao['urlBase'] . ":8088/llm_lang/chat_gpt_4_128k";
                    $urlsIntegracao['janelaContexto'] = MdIaAdmConfigAssistIARN::$LLM_GPT_4_128K_CONTEXTO;
                    break;
            }
        }

        $urlsIntegracao['linkSwagger'] = ":8088/openapi.json";
        $urlsIntegracao['linkConsultaDisponibilidade'] = ":8088/health";
        $urlsIntegracao['linkFeedback'] = ":8088/feedback/feedback";

        return $urlsIntegracao;
    }

    public function enviarFeedbackProcessos($dadosEnviados)
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
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode == "200") {
                return array($httpcode, json_decode($response));
            } else {
                $log = "00001 - INDISPONIBILIDADE DE RECURSO NO FEEDBACK  DO ASSISTENTE DO SEI IA \n";
                $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
                $log .= "00003 - Endpoint do Recurso: " . $dadosEnviados[1] . " \n";
                $log .= "00004 - Tipo de Indisponibilidade: " . $httpcode . " \n";
                $log .= "00005 - Mensagem retornada pelo Servidor: " . utf8_decode($resposta) . " \n";
                $log .= "00006 - JSON enviado ao Servidor: " . $dadosEnviados[0] . " \n";
                $log .= "00007 - FIM \n";
                LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);

                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

                $strDe = SessaoSEIExterna::getInstance()->getStrSiglaSistema() . "<" . $objInfraParametro->getValor('SEI_EMAIL_SISTEMA') . ">";
                $strPara = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');
                $strAssunto = "INDISPONIBILIDADE DE RECURSO NO FEEDBACK  DO ASSISTENTE DO SEI IA ";
                $strConteudo = $log;
                InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strConteudo);
                return false;
            }

            return json_decode($response);

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
