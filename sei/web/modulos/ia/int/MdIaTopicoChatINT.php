<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaTopicoChatINT extends InfraINT
{
    private static function consultarTopicoSessaoAtual($idTopico, $sinAtivo = null)
    {
        if (InfraString::isBolVazia($idTopico) || !is_numeric($idTopico)) {
            return null;
        }

        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();
        $objMdIaTopicoChatDTO->retNumIdMdIaTopicoChat();
        $objMdIaTopicoChatDTO->retNumIdUsuario();
        $objMdIaTopicoChatDTO->retNumIdUnidade();
        $objMdIaTopicoChatDTO->retStrSinAtivo();
        $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat((int)$idTopico);

        $topico = $objMdIaTopicoChatRN->consultar($objMdIaTopicoChatDTO);

        if (is_null($topico)) {
            return null;
        }

        if ($topico->getNumIdUsuario() != SessaoSEI::getInstance()->getNumIdUsuario()) {
            return null;
        }

        if ($topico->getNumIdUnidade() != SessaoSEI::getInstance()->getNumIdUnidadeAtual()) {
            return null;
        }

        if (!InfraString::isBolVazia($sinAtivo) && $topico->getStrSinAtivo() != $sinAtivo) {
            return null;
        }

        return $topico;
    }
    public static function criarTopicoChat()
    {
        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

        $objMdIaTopicoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $objMdIaTopicoChatDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $objMdIaTopicoChatDTO->setStrNome(InfraData::getStrDataAtual() . "-" . InfraData::getStrHoraAtual());
        $objMdIaTopicoChatDTO->setStrSinAtivo("S");
        $objMdIaTopicoChatDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        $topicoCadastrado = $objMdIaTopicoChatRN->cadastrar($objMdIaTopicoChatDTO);
        return $topicoCadastrado->getNumIdMdIaTopicoChat();
    }

    public static function consultarUltimoTopico()
    {
        if (SessaoSEI::getInstance()->isSetAtributo('MD_IA_ID_TOPICO_CHAT_IA')) {
            $topicoCadastrado = self::verificaSessaoTopico();

            if (!empty($topicoCadastrado)) {
                $idTopico = $topicoCadastrado->getNumIdMdIaTopicoChat();
                SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
                return $idTopico;
            }
        } else {
            $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
            $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

            $objMdIaTopicoChatDTO->retNumIdMdIaTopicoChat();
            $objMdIaTopicoChatDTO->retDthCadastro();
            $objMdIaTopicoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
            $objMdIaTopicoChatDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objMdIaTopicoChatDTO->setStrSinAtivo("S");
            $objMdIaTopicoChatDTO->setOrdNumIdMdIaTopicoChat(InfraDTO::$TIPO_ORDENACAO_DESC);
            $objMdIaTopicoChatDTO->setNumMaxRegistrosRetorno(1);

            $topicoCadastrado = $objMdIaTopicoChatRN->consultar($objMdIaTopicoChatDTO);

            if (!empty($topicoCadastrado)) {
                $topicoCadastrado->getNumIdMdIaTopicoChat();
                $dataUltimoTopico = $topicoCadastrado->getDthCadastro();
                if (substr($dataUltimoTopico, 0, 10) == InfraData::getStrDataAtual()) {
                    $idTopico = $topicoCadastrado->getNumIdMdIaTopicoChat();
                    SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
                    return $idTopico;
                }
            }
        }
    }

    public function adicionarTopico()
    {
        $idTopico = self::criarTopicoChat();
        $dadosMensagem["idTopico"] = $idTopico;
        SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
        return $idTopico;
    }

    public static function verificaSessaoTopico()
    {
        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

        $objMdIaTopicoChatDTO->retNumIdMdIaTopicoChat();
        $objMdIaTopicoChatDTO->retNumIdUsuario();
        $objMdIaTopicoChatDTO->retNumIdUnidade();
        $objMdIaTopicoChatDTO->retStrSinAtivo();
        $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat(SessaoSEI::getInstance()->getAtributo('MD_IA_ID_TOPICO_CHAT_IA'));
        $topicoCadastrado = $objMdIaTopicoChatRN->consultar($objMdIaTopicoChatDTO);
        if (!is_null($topicoCadastrado)
            && ($topicoCadastrado->getNumIdUsuario() != SessaoSEI::getInstance()->getNumIdUsuario()
                || $topicoCadastrado->getNumIdUnidade() != SessaoSEI::getInstance()->getNumIdUnidadeAtual()
                || $topicoCadastrado->getStrSinAtivo() != "S")) {
            $topicoCadastrado = null;
        }
        if (empty($topicoCadastrado)) {

            SessaoSEI::getInstance()->removerAtributo('MD_IA_ID_TOPICO_CHAT_IA');
            $idTopico = self::consultarUltimoTopico();

            $dadosMensagem["idTopico"] = $idTopico;

            SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
        }
        return SessaoSEI::getInstance()->getAtributo('MD_IA_ID_TOPICO_CHAT_IA');
    }

    public static function infraDataAtual()
    {
        return date("d/m/Y");
    }

    public static function infraCalcularDataDias($data, $dias)
    {
        $date = DateTime::createFromFormat("d/m/Y", $data);
        $date->modify("$dias days");
        return $date->format("d/m/Y");
    }

    public static function infraCalcularDataMeses($data, $meses)
    {
        $date = DateTime::createFromFormat("d/m/Y", $data);
        $date->modify("$meses months");
        return $date->format("d/m/Y");
    }

    public static function infraCalcularDataFinalMes($data)
    {
        $date = DateTime::createFromFormat("d/m/Y", $data);
        return $date->format("t/m/Y"); // 't' retorna o último dia do mês
    }

    public static function retornaPeriodos()
    {
        $dataAtual = new DateTime();

        $anoAtual = $dataAtual->format('Y'); // Ano atual

        $dataAtual->modify('-1 month');
        $mesAnterior = $dataAtual->format('m'); // Mês anterior
        $anoMesAnterior = $dataAtual->format('Y'); // Ano anterior

        // Formatar como MM/YYYY
        $mesAnoFormatado = $mesAnterior . '/' . $anoMesAnterior;

        $periodosPossiveis = array();
        // Hoje
        $periodosPossiveis["hoje"] = [
            "data_inicial" => self::infraDataAtual() . " 00:00:00",
            "data_final" => self::infraDataAtual() . " 23:59:59"
        ];

        // Ontem
        $periodosPossiveis["ontem"] = [
            "data_inicial" => self::infraCalcularDataDias(self::infraDataAtual(), "-1") . " 00:00:00",
            "data_final" => self::infraCalcularDataDias(self::infraDataAtual(), "-1") . " 23:59:59"
        ];

        // Últimos 7 dias
        $periodosPossiveis["ultimos7dias"] = [
            "data_inicial" => self::infraCalcularDataDias(self::infraDataAtual(), "-7") . " 00:00:00",
            "data_final" => self::infraDataAtual() . " 23:59:59"
        ];

        // Últimos 30 dias
        $periodosPossiveis["ultimos30dias"] = [
            "data_inicial" => self::infraCalcularDataDias(self::infraDataAtual(), "-30") . " 00:00:00",
            "data_final" => self::infraDataAtual() . " 23:59:59"
        ];

        // Último mês
        $periodosPossiveis["ultimoMes"] = [
            "data_inicial" => "01/" . $mesAnoFormatado . " 00:00:00",
            "data_final" => self::infraCalcularDataFinalMes("01/" . $mesAnoFormatado) . " 23:59:59"
        ];

        // Penúltimo mês
        $periodosPossiveis["penultimoMes"] = [
            "data_inicial" => self::infraCalcularDataMeses("01/" . $mesAnoFormatado, -1) . " 00:00:00",
            "data_final" => self::infraCalcularDataFinalMes(self::infraCalcularDataMeses("01/" . $mesAnoFormatado, -1)) . " 23:59:59"
        ];

        // Antepenúltimo mês
        $periodosPossiveis["antepenultimoMes"] = [
            "data_inicial" => self::infraCalcularDataMeses("01/" . $mesAnoFormatado, -2) . " 00:00:00",
            "data_final" => self::infraCalcularDataFinalMes(self::infraCalcularDataMeses("01/" . $mesAnoFormatado, -2)) . " 23:59:59"
        ];

        // Ano atual
        $periodosPossiveis["anoAtual"] = [
            "data_inicial" => "01/01/" . $anoAtual . " 00:00:00",
            "data_final" => "31/12/" . $anoAtual . " 23:59:59"
        ];

        // Último ano
        $periodosPossiveis["ultimoAno"] = [
            "data_inicial" => "01/01/" . ($anoAtual - 1) . " 00:00:00",
            "data_final" => "31/12/" . ($anoAtual - 1) . " 23:59:59"
        ];
        return $periodosPossiveis;
    }

    function converterParaDate($dataString)
    {
        // Converter a string de data para um objeto DateTime
        $dateTime = DateTime::createFromFormat('d/m/Y H:i:s', $dataString);
        return $dateTime;
    }

    public static function definePeriodoTopico($dataBase, $periodos)
    {
        // Converte a data base para um objeto DateTime
        $dataBaseObj = DateTime::createFromFormat('d/m/Y H:i:s', $dataBase);

        // Itera sobre os períodos para verificar se a data base está dentro de algum deles
        foreach ($periodos as $nomePeriodo => $intervalo) {
            $dataInicialObj = DateTime::createFromFormat('d/m/Y H:i:s', $intervalo['data_inicial']);
            $dataFinalObj = DateTime::createFromFormat('d/m/Y H:i:s', $intervalo['data_final']);
            // Verifica se a data base está entre data_inicial e data_final
            if ($dataBaseObj >= $dataInicialObj && $dataBaseObj <= $dataFinalObj) {
                return $nomePeriodo; // Retorna o nome do período correspondente
            }
        }
        // Se a data base não estiver em nenhum período, retorna "Mais Antigos"
        return "maisAntigos";
    }

    public static function listarTopicos()
    {
        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();
        $objMdIaTopicoChatDTO->retNumIdMdIaTopicoChat();
        $objMdIaTopicoChatDTO->retStrNome();
        $objMdIaTopicoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $objMdIaTopicoChatDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $objMdIaTopicoChatDTO->setStrSinAtivo("S");
        $objMdIaTopicoChatDTO->setOrdNumIdMdIaTopicoChat(InfraDTO::$TIPO_ORDENACAO_DESC);

        $listagemTopicos = $objMdIaTopicoChatRN->listar($objMdIaTopicoChatDTO);

        $arrayRetorno["budgetTokens"] = MdIaConfigAssistenteINT::calcularConsumoDiarioToken();

        $periodos = self::retornaPeriodos();
        if (!empty($listagemTopicos)) {
            if (!SessaoSEI::getInstance()->isSetAtributo('MD_IA_ID_TOPICO_CHAT_IA')) {
                $dados["id"] = self::consultarUltimoTopico();
                SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $dados["id"]);
            } else {
                $dados["id"] = self::verificaSessaoTopico();
            }

            $arrayItensTopicos = array();
            $arrayTopicos = array();
            foreach ($listagemTopicos as $itemTopico) {
                $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
                $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
                $objMdIaInteracaoChatDTO->retDthCadastro();
                $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($itemTopico->getNumIdMdIaTopicoChat());
                $objMdIaInteracaoChatDTO->setOrdNumIdMdIaInteracaoChat(InfraDTO::$TIPO_ORDENACAO_DESC);
                $objMdIaInteracaoChatDTO->setNumMaxRegistrosRetorno(1);
                $ultimaInteracao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

                $arrayItensTopicos["idTopico"] = $itemTopico->getNumIdMdIaTopicoChat();

                if ($ultimaInteracao) {
                    $arrayItensTopicos["dataUltimaInteracao"] = $ultimaInteracao->getDthCadastro();
                }

                $periodo = self::definePeriodoTopico($arrayItensTopicos["dataUltimaInteracao"], $periodos);
                $arrayItensTopicos["periodo"] = $periodo;

                $arrayItensTopicos["nome"] = mb_convert_encoding($itemTopico->getStrNome(), 'UTF-8', 'ISO-8859-1');
                if ($arrayItensTopicos["idTopico"] != SessaoSEI::getInstance()->getAtributo('MD_IA_ID_TOPICO_CHAT_IA')) {
                    $arrayItensTopicos["ativo"] = false;
                } else {
                    $arrayItensTopicos["ativo"] = true;
                }
                $arrayTopicos[$periodo][] = $arrayItensTopicos;
            }

            // Opcional: Ordenar os tópicos dentro de cada período pelo campo 'dataUltimaInteracao'
            foreach ($arrayTopicos as $topicos) {
                usort($topicos, function ($a, $b) {
                    $timestampA = strtotime(str_replace("/", "-", $a['dataUltimaInteracao']));
                    $timestampB = strtotime(str_replace("/", "-", $b['dataUltimaInteracao']));
                    return $timestampB - $timestampA; // Ordenação decrescente
                });
            }
            $arrayRetorno["topicos"] = $arrayTopicos;
        } else {
            $arrayRetorno["topicos"] = false;
        }
        return $arrayRetorno;
    }

    public static function listarProcedimentosPrompt($prompt)
    {
        $documentos = [];
        preg_match_all('/#\S*(?=\s|$|\n)/', $prompt, $matches);
        $possiveisCitacoes = array_unique($matches[0]);
        if (!empty($possiveisCitacoes)) {
            foreach ($possiveisCitacoes as $possivelCitacao) {
                if (strpos($possivelCitacao, '[') !== false) {
                    if (preg_match('/#[0-9]+\[[0-9]+(:[0-9]+)?\]/', $possivelCitacao, $match)) {
                        $documentos[] = $match[0];
                    }
                } else {
                    if (preg_match('/#[0-9]+[^[\s]*?(?=\s|$|\n|[.,;:?!)](?![^.,;:?!)]))/', $possivelCitacao, $match)) {
                        $documentos[] = $match[0];
                    }
                }
            }
        }
        $protocolo["documento"] = $documentos;
        $protocolo["consultaTopico"] = true;
        $citacoes = MdIaConfigAssistenteINT::listaProtocolosUtilizados($protocolo);

        return $citacoes;
    }

    public static function selecionarTopico($dados)
    {
        $idTopico = isset($dados["id"]) ? $dados["id"] : null;
        $topico = self::consultarTopicoSessaoAtual($idTopico, "S");

        if (is_null($topico)) {
            return array();
        }

        $idTopico = $topico->getNumIdMdIaTopicoChat();
        SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retStrPergunta();
        $objMdIaInteracaoChatDTO->retStrResposta();
        $objMdIaInteracaoChatDTO->retNumFeedback();
        $objMdIaInteracaoChatDTO->retNumIdMdIaInteracaoChat();
        $objMdIaInteracaoChatDTO->retNumStatusRequisicao();
        $objMdIaInteracaoChatDTO->retNumTempoExecucao();
        $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($idTopico);
        $objMdIaInteracaoChatDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $objMdIaInteracaoChatDTO->setNumIdUnidadeTopico(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $objMdIaInteracaoChatDTO->setOrdNumIdMdIaInteracaoChat(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdIaInteracaoChatDTO->retDthCadastro();

        $listagemInteracoes = $objMdIaInteracaoChatRN->listar($objMdIaInteracaoChatDTO);

        $arrayItensInteracoes = array();
        $arrayInteracoes = array();
        foreach ($listagemInteracoes as $itemInteracao) {
            $arrayItensInteracoes["favorito"] = false;

            $arrayItensInteracoes["id_mensagem"] = $itemInteracao->getNumIdMessage();
            $arrayItensInteracoes["pergunta"] = mb_convert_encoding($itemInteracao->getStrPergunta(), 'UTF-8', 'ISO-8859-1');

            $arrayProcedimentos = self::listarProcedimentosPrompt($itemInteracao->getStrPergunta());

            $resposta = MdIaConfigAssistenteINT::retornaMensagemAmigavelUsuario($itemInteracao->getNumStatusRequisicao(), $itemInteracao->getStrResposta());

            $arrayItensInteracoes["resposta"] = mb_convert_encoding($resposta["resposta"], 'UTF-8', 'ISO-8859-1');
            $arrayItensInteracoes["tipo_critica"] = $resposta["tipoCritica"];

            $arrayItensInteracoes["feedback"] = $itemInteracao->getNumFeedback();
            $arrayItensInteracoes["dadosCitacoes"] = $arrayProcedimentos;
            $arrayItensInteracoes["id_interacao"] = $itemInteracao->getNumIdMdIaInteracaoChat();
            $arrayItensInteracoes["status_requisicao"] = $itemInteracao->getNumStatusRequisicao();
            $arrayItensInteracoes["dth_cadastro"] = substr($itemInteracao->getDthCadastro(), 0, 19);
            $arrayItensInteracoes["tempo_execucao"] = $itemInteracao->getNumTempoExecucao();

            $arrayInteracoes[] = $arrayItensInteracoes;
        }
        return $arrayInteracoes;
    }


    public static function renomearTopico($dados)
    {
        $topico = self::consultarTopicoSessaoAtual($dados["id_topico"]);
        if (is_null($topico)) {
            return false;
        }

        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

        $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat($topico->getNumIdMdIaTopicoChat());
        $objMdIaTopicoChatDTO->setStrNome($dados["nome_topico"]);
        $objMdIaTopicoChatRN->alterar($objMdIaTopicoChatDTO);
        return true;
    }

    public static function arquivarTopico($dados)
    {
        $topico = self::consultarTopicoSessaoAtual($dados["id_topico"], "S");
        if (is_null($topico)) {
            return false;
        }

        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

        $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat($topico->getNumIdMdIaTopicoChat());
        $objMdIaTopicoChatDTO->setStrSinAtivo("N");
        $objMdIaTopicoChatRN->alterar($objMdIaTopicoChatDTO);
        SessaoSEI::getInstance()->removerAtributo('MD_IA_ID_TOPICO_CHAT_IA');
        return true;
    }

    public static function desarquivarTopico($dados)
    {
        $topico = self::consultarTopicoSessaoAtual($dados["id_topico"], "N");
        if (is_null($topico)) {
            return false;
        }

        $objMdIaTopicoChatDTO = new MdIaTopicoChatDTO();
        $objMdIaTopicoChatRN = new MdIaTopicoChatRN();

        $objMdIaTopicoChatDTO->setNumIdMdIaTopicoChat($topico->getNumIdMdIaTopicoChat());
        $objMdIaTopicoChatDTO->setStrSinAtivo("S");
        $objMdIaTopicoChatRN->alterar($objMdIaTopicoChatDTO);
        SessaoSEI::getInstance()->removerAtributo('MD_IA_ID_TOPICO_CHAT_IA');
        return true;
    }
}
