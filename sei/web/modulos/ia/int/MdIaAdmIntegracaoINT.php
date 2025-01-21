<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmIntegracaoINT extends InfraINT
{

    public static function montarOperacaoSOAP($data)
    {
        $enderecoWSDL = $data['urlServico'];
        $xml = "<operacoes>\n";
        try {
            if (!filter_var($enderecoWSDL, FILTER_VALIDATE_URL) || !InfraUtil::isBolUrlValida($enderecoWSDL, FILTER_VALIDATE_URL))
                throw new InfraException("Endereço do WebService inválido.");

            if ($data['tipoWs'] != 'SOAP')
                throw new InfraException('O tipo de integração informado deve ser do tipo SOAP.');

            $client = new MdIaSoapClienteRN($enderecoWSDL, 'wsdl');
            $client->setSoapVersion($data['versaoSoap']);
            $operacaoArr = $client->getFunctions();

            if (empty($operacaoArr)) {
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>Não existe operação.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $xml .= "<success>true</success>\n";
            asort($operacaoArr);
            foreach ($operacaoArr as $key => $operacao) {
                $xml .= "<operacao key='{$key}'>{$operacao}</operacao>\n";
            }
            $xml .= '</operacoes>';
            return $xml;

        } catch (Exception $e) {
            throw new InfraException("Erro Operação SOAP: {$e->getMessage()}", $e);
        }
    }

    public static function getDadosServicoREST($post)
    {

        if ($post["funcionalidade"] == MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL) {
            $objMdIaRecursoRN = new MdIaRecursoRN();
            $urlApi = $objMdIaRecursoRN->retornarUrlApi();
        } elseif ($post["funcionalidade"] == MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTERFACE_LLM) {
            $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
            $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();
        }
        $urlServico = trim($post['urlServico'] . $urlApi['linkSwagger']);

        if (!filter_var($urlServico, FILTER_VALIDATE_URL) /*|| !InfraUtil::isBolUrlValida( $urlServico , FILTER_VALIDATE_URL )*/)
            throw new InfraException("Endereço do WebService inválido.");

        if ($post['tipoWs'] != 'REST')
            throw new InfraException('O Tipo de Integração informado deve ser do tipo REST.');

        $curl = curl_init($urlServico);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT_MS => '5000',
            CURLOPT_CUSTOMREQUEST => $post['tipoRequisicao'],
        ]);

        // monta dados de parametros necessarios
        if (array_key_exists('parametros', $post)) {
            $payload = json_encode($post['parametros']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        // monta dados de cabecalho cadastrados
        if (array_key_exists('headers', $post)) {
            $header = ['Content-Type: application/json'];
            foreach ($post['headers'] as $head) {
                $vlrConteudo = $head->getStrSinDadoConfidencial() == 'S'
                    ? MdIaAdmIntegracaoINT::gerenciaDadosRestritos($head->getStrConteudo(), 'D')
                    : $head->getStrConteudo();

                $header[] = $head->getStrAtributo() . ': ' . $vlrConteudo;
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        $ret = self::trataRetornoCurl(curl_exec($curl));

        if ($ret === false) {
            $strError = curl_error($curl);
            throw new InfraException($strError);
        } else {
            curl_close($curl);
            return $ret;
        }
    }

    public static function montarOperacaoREST($post)
    {
        $xml = '';

        try {
            $rs = self::getDadosServicoREST($post);

            if (empty($rs)) throw new InfraException("Não retornou dados para filtro via Json.");

            if (!$rs->paths) throw new InfraException("Não existe operação.");

            $xml .= "<operacoes>\n";
            $xml .= "<success>true</success>\n";
            $xml .= "<json>" . self::filtrarJSON($rs) . "</json>\n";
            $cont = 1;

            foreach ($rs->paths as $key => $value) {
                $xml .= "<operacao key='{$cont}'>" . $key . "</operacao>\n";
                $cont++;
            }
            $xml .= '</operacoes>';
            return $xml;

        } catch (Exception $e) {
            throw new InfraException("Erro Operação REST: {$e->getMessage()}", $e);
        }
    }

    private static function filtrarJSON($arrJson)
    {
        $arrDados = [];

        foreach ($arrJson as $k => $v) {
            if ($k == 'paths') {
                foreach ($v as $k1 => $v1) {
                    //monta dados de entrada
                    $arrItem = (array)$v1->post->parameters[0]->schema;
                    $arrItem = explode('/', $arrItem['$ref']);
                    $strAcao = end($arrItem);
                    $arrDados['operacoes'][$k1]['parametros']['entrada']['nome'] = $strAcao;

                    $arrDD = (array)$arrJson->definitions;
                    foreach ($arrDD as $k2 => $v2) {
                        if ($k2 == $strAcao) {
                            $arrParam = (array)$v2->properties;
                            foreach ($arrParam as $k3 => $v3) {
                                $arrDados['operacoes'][$k1]['parametros']['entrada']['valores'][] = $k3;
                            }
                        }
                    }

                    //monta dados de saida
                    $arrItem = (array)$v1->post->responses->{200}->schema;
                    if (empty($arrItem['$ref'])) $arrItem = (array)$v1->post->responses->{200}->schema->items;
                    $arrItem = explode('/', $arrItem['$ref']);
                    $strAcao = end($arrItem);
                    $arrDados['operacoes'][$k1]['parametros']['saida']['nome'] = $strAcao;

                    $arrDD = (array)$arrJson->definitions;
                    foreach ($arrDD as $k2 => $v2) {
                        if ($k2 == $strAcao) {
                            $arrParam = (array)$v2->properties;
                            foreach ($arrParam as $k3 => $v3) {
                                $arrDados['operacoes'][$k1]['parametros']['saida']['valores'][] = $k3;
                            }
                        }
                    }
                }
            }
        }
        return json_encode($arrDados);
    }

    private static function trataRetornoCurl($ret)
    {
        $type = gettype($ret);

        switch ($type) {
            case 'string':
                $rs = json_decode($ret);
                if (is_object($rs)) {
                    if (!is_null($rs->error))
                        throw new InfraException($rs->message);
                    else
                        return $rs;
                } else if (is_array($rs)) {
                    return $rs;
                }
                break;

            case 'boolean':
                return $ret;
                break;

            default:
                break;
        }
    }

    /* *********************************************************************************
        Executa consulta no Webservice e retorna os dados no formato json
    ********************************************************************************** */

    public static function executarConsultaREST($arrObjIntegracao, $parametros = [])
    {

        if ($arrObjIntegracao['integracao']->getStrTipoIntegracao() != 'RE') throw new InfraException('Execução somente via REST.');

        $strEnderecoServico = $arrObjIntegracao['integracao']->getStrOperacaoWsdl();

        $params = [
            'urlServico' => $strEnderecoServico,
            'tipoWs' => $arrObjIntegracao['integracao']->getStrTipoIntegracao() == 'RE' ? 'REST' : 'SOAP',
            'tipoRequisicao' => MdIaAdmIntegracaoINT::montarSelectMetodoRequisicao(null, $arrObjIntegracao['integracao']->getNumMetodoRequisicao()),
        ];

        //$parametros => array de parametros para consulta no ws
        if (!empty($parametros)) $params['parametros'] = $parametros;
        if (!empty($arrObjIntegracao['headers-integracao'])) $params['headers'] = $arrObjIntegracao['headers-integracao'];

        $dados = self::getDadosServicoREST($params);

        return empty($dados) ? [] : $dados;
    }

    public static function montarSelectFuncionalidade($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $NumIdMdIaAdmIntegracao = null)
    {

        $objMdIaAdmIntegFuncionRN = new MdIaAdmIntegFuncionRN();
        $arrIdMdIaAdmIntegFuncionUtilizado = $objMdIaAdmIntegFuncionRN->verificarMdIaIntegFuncionalidUtilizado($NumIdMdIaAdmIntegracao);

        $objMdIaAdmIntegFuncionDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaAdmIntegFuncionDTO->retNumIdMdIaAdmIntegFuncion();
        $objMdIaAdmIntegFuncionDTO->retStrNome();
        if ($arrIdMdIaAdmIntegFuncionUtilizado) {
            $objMdIaAdmIntegFuncionDTO->setNumIdMdIaAdmIntegFuncion($arrIdMdIaAdmIntegFuncionUtilizado, InfraDTO::$OPER_NOT_IN);
        }

        $objMdIaAdmIntegFuncionDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjMdIaAdmIntegFuncionDTO = $objMdIaAdmIntegFuncionRN->listar($objMdIaAdmIntegFuncionDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdIaAdmIntegFuncionDTO, 'IdMdIaAdmIntegFuncion', 'Nome');
    }


    public static function montarSelectNome($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdIaAdmIntegFuncionDTO = new MdIaAdmIntegFuncionDTO();
        $objMdIaAdmIntegFuncionDTO->retNumIdMdIaAdmIntegFuncion();
        $objMdIaAdmIntegFuncionDTO->retStrNome();

        $objMdIaAdmIntegFuncionDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdIaAdmIntegFuncionRN = new MdIaAdmIntegFuncionRN();
        $arrObjMdIaAdmIntegFuncionDTO = $objMdIaAdmIntegFuncionRN->listar($objMdIaAdmIntegFuncionDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdIaAdmIntegFuncionDTO, 'IdMdIaAdmIntegFuncion', 'Nome');
    }

    public static function montarSelectMetodoAutenticacao($itemSelecionado = null, $retornaItem = false)
    {
        $arrMetAut = [
            MdIaAdmIntegracaoRN::$AUT_VAZIA => MdIaAdmIntegracaoRN::$STR_AUT_VAZIA,
            MdIaAdmIntegracaoRN::$AUT_HEADER_TOKEN => MdIaAdmIntegracaoRN::$STR_AUT_HEADER_TOKEN,
            MdIaAdmIntegracaoRN::$AUT_BODY_TOKEN => MdIaAdmIntegracaoRN::$STR_AUT_BODY_TOKEN,
        ];

        if ($retornaItem) return $arrMetAut[$retornaItem];

        $strOptions = '<option value="">Selecione</option>';

        foreach ($arrMetAut as $k => $v) {
            $selected = '';
            if ($itemSelecionado && $itemSelecionado == $k) $selected = 'selected';
            $strOptions .= "<option value='$k' $selected>$v</option>";
        }
        return $strOptions;
    }

    public static function montarSelectMetodoRequisicao($itemSelecionado = null, $retornaItem = false)
    {
        $arrMetReq = [
            MdIaAdmIntegracaoRN::$REQUISICAO_POST => MdIaAdmIntegracaoRN::$STR_REQUISICAO_POST,
            MdIaAdmIntegracaoRN::$REQUISICAO_GET => MdIaAdmIntegracaoRN::$STR_REQUISICAO_GET,
        ];

        if ($retornaItem) return $arrMetReq[$retornaItem];

        $strOptions = '<option value="">Selecione</option>';

        foreach ($arrMetReq as $k => $v) {
            $selected = '';
            if ($itemSelecionado && $itemSelecionado == $k) $selected = 'selected';
            $strOptions .= "<option value='$k' $selected>$v</option>";
        }
        return $strOptions;
    }

    public static function montarSelectFormato($itemSelecionado = null, $retornaItem = false)
    {
        $arrFormato = [
            MdIaAdmIntegracaoRN::$FORMATO_JSON => MdIaAdmIntegracaoRN::$STR_FORMATO_JSON,
            MdIaAdmIntegracaoRN::$FORMATO_XML => MdIaAdmIntegracaoRN::$STR_FORMATO_XML,
        ];

        if ($retornaItem) return $arrFormato[$retornaItem];

        $strOptions = '<option value="">Selecione</option>';

        foreach ($arrFormato as $k => $v) {
            $selected = '';
            if ($itemSelecionado && $itemSelecionado == $k) $selected = 'selected';
            $strOptions .= "<option value='$k' $selected>$v</option>";
        }
        return $strOptions;
    }

    public static function geraOperacao($operacao = null)
    {
        if (!empty($operacao)) {
            $arrOperacao = explode('/', $operacao);
            return end($arrOperacao);
        }
        return "";
    }

    public static function gerenciaDadosRestritos($valor, $acao = 'C')
    {
        switch ($acao) {
            case 'C':
                return base64_encode(strrev(base64_encode(strrev($valor))));
                break;

            case 'D':
                return strrev(base64_decode(strrev(base64_decode($valor))));
                break;

            default:
                throw new InfraException('Tipo de Ação não declarado na função.');
        }
    }
    public function buscaDadosIntegracao($dados) {
        $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion($dados["idIntegracao"]);
        $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
        $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);
        $dadosIntegracao = [];
        $dadosIntegracao["operacaoWsdl"] = $objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl();
        return $dadosIntegracao;
    }
}