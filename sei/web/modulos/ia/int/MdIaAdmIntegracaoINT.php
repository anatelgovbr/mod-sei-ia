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
            CURLOPT_SSL_VERIFYHOST => 0,       // ignora verificação do nome do host (0 desabilita, 2 habilita)
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

    private static function getRefName($ref)
    {
        if (!is_string($ref)) return null;
        $parts = explode('/', $ref);
        return end($parts);
    }

    private static function filtrarJSON($arrJson)
    {
        $arrDados = [];

        // pega as definições/schemas tanto para swagger v2 (definitions) quanto openapi v3 (components.schemas)
        $arrSchemas = [];
        if (isset($arrJson->components) && isset($arrJson->components->schemas)) {
            $arrSchemas = (array) $arrJson->components->schemas;
        } elseif (isset($arrJson->definitions)) {
            $arrSchemas = (array) $arrJson->definitions;
        }

        if (empty($arrJson->paths) || !is_object($arrJson->paths)) {
            return json_encode($arrDados);
        }

        foreach ($arrJson->paths as $path => $pathObj) {
            // cada $pathObj pode ter vários métodos (get, post, put, ...)
            foreach ($pathObj as $method => $operation) {
                $method = strtolower($method);

                // --- ENTRADA ---
                $entradaNome = null;
                $entradaValores = [];

                // 1) parâmetros (query/path) -> OpenAPI2/3: array de parameters
                if (!empty($operation->parameters) && is_array($operation->parameters)) {
                    foreach ($operation->parameters as $param) {
                        // se o parâmetro referencia um schema por $ref
                        if (isset($param->schema) && isset($param->schema->{'$ref'})) {
                            $ref = self::getRefName($param->schema->{'$ref'});
                            if ($ref) {
                                $entradaNome = $ref;
                                if (isset($arrSchemas[$ref]) && isset($arrSchemas[$ref]->properties)) {
                                    foreach ((array) $arrSchemas[$ref]->properties as $prop => $_) {
                                        $entradaValores[] = $prop;
                                    }
                                }
                            }
                        } else {
                            // parâmetro simples (query/path) -> usa o nome do parâmetro
                            if (isset($param->name)) {
                                $entradaValores[] = $param->name;
                            }
                        }
                    }
                }

                // 2) requestBody (OpenAPI3) -> content -> application/json -> schema
                if (empty($entradaNome) && isset($operation->requestBody)) {
                    $schema = null;
                    if (isset($operation->requestBody->content)) {
                        $contentArr = (array) $operation->requestBody->content;
                        // tenta application/json primeiro, senão pega o primeiro content disponível
                        if (isset($operation->requestBody->content->{'application/json'}->schema)) {
                            $schema = $operation->requestBody->content->{'application/json'}->schema;
                        } else {
                            $first = reset($contentArr);
                            if ($first && isset($first->schema)) {
                                $schema = $first->schema;
                            }
                        }
                    }
                    if ($schema) {
                        if (isset($schema->{'$ref'})) {
                            $ref = self::getRefName($schema->{'$ref'});
                            $entradaNome = $ref;
                            if (isset($arrSchemas[$ref]) && isset($arrSchemas[$ref]->properties)) {
                                foreach ((array) $arrSchemas[$ref]->properties as $prop => $_) {
                                    $entradaValores[] = $prop;
                                }
                            }
                        } elseif (isset($schema->properties)) {
                            foreach ((array) $schema->properties as $prop => $_) {
                                $entradaValores[] = $prop;
                            }
                        }
                    }
                }

                // --- SAÍDA ---
                $saidaNome = null;
                $saidaValores = [];

                if (isset($operation->responses)) {
                    // preferimos 200, senão pega primeiro response disponível
                    $resp = null;
                    if (isset($operation->responses->{200})) {
                        $resp = $operation->responses->{200};
                    } else {
                        $resArr = (array) $operation->responses;
                        $firstResp = reset($resArr);
                        if ($firstResp) $resp = $firstResp;
                    }

                    if ($resp) {
                        // OpenAPI3: responses->{200}->content->{application/json}->schema
                        if (isset($resp->content)) {
                            $contentArr = (array) $resp->content;
                            if (isset($resp->content->{'application/json'}->schema)) {
                                $schema = $resp->content->{'application/json'}->schema;
                            } else {
                                $first = reset($contentArr);
                                $schema = ($first && isset($first->schema)) ? $first->schema : null;
                            }
                        } elseif (isset($resp->schema)) { // swagger v2 style
                            $schema = $resp->schema;
                        } else {
                            $schema = null;
                        }

                        if (isset($schema)) {
                            // caso $ref direto
                            if (isset($schema->{'$ref'})) {
                                $ref = self::getRefName($schema->{'$ref'});
                                $saidaNome = $ref;
                                if ($ref && isset($arrSchemas[$ref]) && isset($arrSchemas[$ref]->properties)) {
                                    foreach ((array) $arrSchemas[$ref]->properties as $prop => $_) {
                                        $saidaValores[] = $prop;
                                    }
                                }
                            } elseif (isset($schema->items) && isset($schema->items->{'$ref'})) {
                                // array de itens -> items.$ref
                                $ref = self::getRefName($schema->items->{'$ref'});
                                $saidaNome = $ref;
                                if ($ref && isset($arrSchemas[$ref]) && isset($arrSchemas[$ref]->properties)) {
                                    foreach ((array) $arrSchemas[$ref]->properties as $prop => $_) {
                                        $saidaValores[] = $prop;
                                    }
                                }
                            } elseif (isset($schema->properties)) {
                                // schema inline
                                foreach ((array) $schema->properties as $prop => $_) {
                                    $saidaValores[] = $prop;
                                }
                            }
                        }
                    }
                }

                // normaliza arrays (remove nulos e duplicates)
                $entradaValores = array_values(array_unique(array_filter($entradaValores)));
                $saidaValores = array_values(array_unique(array_filter($saidaValores)));

                $arrDados['operacoes'][$path]['parametros']['entrada']['nome'] = $entradaNome;
                $arrDados['operacoes'][$path]['parametros']['entrada']['valores'] = $entradaValores;
                $arrDados['operacoes'][$path]['parametros']['saida']['nome'] = $saidaNome;
                $arrDados['operacoes'][$path]['parametros']['saida']['valores'] = $saidaValores;
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
    public static function buscaDadosIntegracao($dados)
    {
        $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
        $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion($dados["idIntegracao"]);
        $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
        $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);
        $dadosIntegracao = [];
        $dadosIntegracao["operacaoWsdl"] = $objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl();
        return $dadosIntegracao;
    }

    public static function recuperarGridUrls($IdMdIaAdmUrlIntegracao)
    {
        $objMdIaAdmUrlIntegracaoDTO = new MdIaAdmUrlIntegracaoDTO();
        $objMdIaAdmUrlIntegracaoDTO->setNumIdAdmIaAdmIntegracao($IdMdIaAdmUrlIntegracao);
        $objMdIaAdmUrlIntegracaoDTO->retNumIdMdIaAdmUrlIntegracao();
        $objMdIaAdmUrlIntegracaoDTO->retNumIdAdmIaAdmIntegracao();
        $objMdIaAdmUrlIntegracaoDTO->retStrReferencia();
        $objMdIaAdmUrlIntegracaoDTO->retStrLabel();
        $objMdIaAdmUrlIntegracaoDTO->retStrUrl();
        $arrObjMdIaAdmUrlIntegracaoDTO = (new MdIaAdmUrlIntegracaoRN)->listar($objMdIaAdmUrlIntegracaoDTO);

        $strCadastroUrls = '';
        if (!empty($arrObjMdIaAdmUrlIntegracaoDTO)) {
            $strCadastroUrls  = '<table width="100%" id="tableParametroEntrada" class="infraTable" summary="Cadastro de URLs">' . "\n";
            $strCadastroUrls .= '<tr>';
            $strCadastroUrls .= '<th class="infraTh" width="30%">&nbsp;Nome&nbsp;</th>' . "\n";
            $strCadastroUrls .= '<th class="infraTh" width="80%">&nbsp;URL&nbsp;</th>' . "\n";
            $strCadastroUrls .= '</tr>' . "\n";

            foreach ($arrObjMdIaAdmUrlIntegracaoDTO as $objMdIaAdmUrlIntegracaoDTO) {
                $strCssTr = '<tr id="cadastroUrls" class="infraTrClara">';
                $strCadastroUrls .= $strCssTr;
                $referencia = $objMdIaAdmUrlIntegracaoDTO->getStrReferencia();
                $strCadastroUrls .= "<td align='left'  style='padding: 8px;' >";
                $strCadastroUrls .= '    <label>' . $objMdIaAdmUrlIntegracaoDTO->getStrLabel() . '</label>';
                $strCadastroUrls .= '</td>';
                $strCadastroUrls .= "<td id='{$objMdIaAdmUrlIntegracaoDTO->getStrReferencia()}'  style='padding: 8px;' >";
                $strCadastroUrls .= "   <div class='form-group'>";
                $strCadastroUrls .= "       <input class='infraText form-control' type='text' name='{$referencia}' onClick='emitirAlerta()' value='" . $objMdIaAdmUrlIntegracaoDTO->getStrUrl() . "'/>";
                $strCadastroUrls .= "   </div>";
                $strCadastroUrls .= "</td>";
                $strCadastroUrls .= '</tr>' . "\n";
            }
            $strCadastroUrls .= '</table>';
        }

        return $strCadastroUrls;
    }
}
