<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/12/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaClassMetaOdsINT extends InfraINT
{
    function salvarClassificacaoOds($dados)
    {
        $metasSelecionadasAnteriormente = explode(",", $dados["hdnHistoricoSelecionados"]);
        $metasSelecionadas = explode(",", $dados["hdnInfraItensSelecionados"]);
        $itensSugeridos = explode(",", $dados["hdnItensSugeridos"]);

        // Montando os arrays de trabalho:
        $itensAdicionados    = array_diff($metasSelecionadas, array_merge($metasSelecionadasAnteriormente, $itensSugeridos));
        $itensRemovidos     = array_diff($metasSelecionadasAnteriormente, array_diff($metasSelecionadas, $itensSugeridos));
        $sugestoesAceitas    = array_intersect($metasSelecionadas, $itensSugeridos);
        $sugestoesRecusadas = array_diff($itensSugeridos, array_diff($metasSelecionadas, $metasSelecionadasAnteriormente));
        $itensAtualizacaoRacional = array_intersect($metasSelecionadas, $metasSelecionadasAnteriormente);

        if (!empty(array_merge($itensAdicionados, $itensRemovidos, $sugestoesAceitas, $sugestoesRecusadas, $itensAtualizacaoRacional))) {

            // Adicionando novas classificações que não foram sugeridas
            self::adicionarNovaClassificacao($itensAdicionados, $dados);

            // Aceitando sugestoes de IA ou UE
            self::aceitarsugestoes($sugestoesAceitas, $dados);

            // Removendo classificações anteriores
            self::removerClassificacaoAnterior($itensRemovidos, $dados);

            // Recusando sugestoes
            self::recusarSugestoes($sugestoesRecusadas, $dados);

            // Racionais a serem atualizados
            self::atualizarRacional($itensAtualizacaoRacional, $dados);

            return json_encode(array("result" => "true", "reloadTo" => SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $dados["hdnIdProcedimento"])));
        } else {

            return json_encode(array("result" => "false", "mensagem" => mb_convert_encoding("Nenhum item foi alterado desde a última classificação. Se não desejar realizar alterações clicar no botão Fechar.", 'UTF-8', 'ISO-8859-1')));
        }
    }

    private static function adicionarNovaClassificacao($itensAdicionados, $dados)
    {
        foreach ($itensAdicionados as $itemAdicionado) {

            if (is_numeric($itemAdicionado)) {

                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($itemAdicionado);
                $objMdIaClassMetaOdsDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $objMdIaClassMetaOdsDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                $objMdIaClassMetaOdsDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemAdicionado]));
                $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                (new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);

                $objMdIaHistClassDTO = new MdIaHistClassDTO();
                $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($itemAdicionado);
                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
                $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                $objMdIaHistClassDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemAdicionado]));
                $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
            }
        }
    }

    private static function aceitarsugestoes($sugestoesAceitas, $dados)
    {
        foreach ($sugestoesAceitas as $sugestaoAceita) {

            $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
            $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
            $objMdIaClassMetaOdsDTO->retNumIdUsuario();
            $objMdIaClassMetaOdsDTO->retNumIdUnidade();
            $objMdIaClassMetaOdsDTO->retDthCadastro();
            $objMdIaClassMetaOdsDTO->retStrSinSugestaoAceita();
            $objMdIaClassMetaOdsDTO->retStrRacional();
            $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
            $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
            $objMdIaClassMetaOdsDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
            $objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
            $sugestaoAAceitar = (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);

            if ($sugestaoAAceitar) {

                $sugestaoAAceitar->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $sugestaoAAceitar->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $sugestaoAAceitar->setDthCadastro(InfraData::getStrDataHoraAtual());
                $sugestaoAAceitar->setStrSinSugestaoAceita("S");
                $sugestaoAAceitar->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $sugestaoAceita]));
                $sugestaoAAceitar->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);

                (new MdIaClassMetaOdsRN())->alterar($sugestaoAAceitar);

                $objMdIaHistClassDTO = new MdIaHistClassDTO();
                $objMdIaHistClassDTO->retNumIdMdIaHistClass();
                $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
                $objMdIaHistClassDTO->setStrStaTipoUsuario([MdIaClassMetaOdsRN::$USUARIO_IA, MdIaClassMetaOdsRN::$USUARIO_EXTERNO], InfraDTO::$OPER_IN);
                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
                $objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
                $objMdIaHistClassDTO->setNumMaxRegistrosRetorno(1);
                $itemHistoricoSugerido = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);

                if ($itemHistoricoSugerido) {

                    $objMdIaHistClassDTO = new MdIaHistClassDTO();
                    $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                    $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
                    $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_CONFIRMACAO);
                    $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                    $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                    $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                    $objMdIaHistClassDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $sugestaoAceita]));
                    $objMdIaHistClassDTO->setStrSinSugestaoAceita("S");
                    $objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($itemHistoricoSugerido->getNumIdMdIaHistClass());
                    $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                    (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
                }
            }
        }
    }

    private static function removerClassificacaoAnterior($itensRemovidos, $dados)
    {
        foreach ($itensRemovidos as $itemRemovido) {

            if (is_numeric($itemRemovido)) {

                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($itemRemovido);
                $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
                $itemASerRemovido = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);

                if ($itemASerRemovido) {
                    (new MdIaClassMetaOdsRN())->excluir($itemASerRemovido);

                    $objMdIaHistClassDTO = new MdIaHistClassDTO();
                    $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                    $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($itemRemovido);
                    $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_DELETE);
                    $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                    $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                    $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                    $objMdIaHistClassDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemRemovido]));
                    $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                    (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
                }
            }
        }
    }

    private static function recusarSugestoes($sugestoesRecusadas, $dados)
    {
        foreach ($sugestoesRecusadas as $sugestaoRecusada) {


            $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
            $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
            $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
            $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
            $sugestaoARecusar = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);

            if ($sugestaoARecusar) {

                (new MdIaClassMetaOdsRN())->excluir($sugestaoARecusar);

                $objMdIaHistClassDTO = new MdIaHistClassDTO();
                $objMdIaHistClassDTO->retNumIdMdIaHistClass();
                $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
                $objMdIaHistClassDTO->setStrStaTipoUsuario([MdIaClassMetaOdsRN::$USUARIO_IA, MdIaClassMetaOdsRN::$USUARIO_EXTERNO], InfraDTO::$OPER_IN);
                $objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
                $objMdIaHistClassDTO->setNumMaxRegistrosRetorno(1);
                $itemHistoricoSugerido = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);

                if ($itemHistoricoSugerido) {

                    $objMdIaHistClassDTO = new MdIaHistClassDTO();
                    $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                    $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
                    $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_NAO_CONFIRMACAO);
                    $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                    $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                    $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                    $objMdIaHistClassDTO->setStrSinSugestaoAceita("N");
                    $objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($itemHistoricoSugerido->getNumIdMdIaHistClass());
                    $objMdIaHistClassDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $sugestaoRecusada]));
                    $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                    (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
                }
            }
        }
    }

    private static function atualizarRacional($itensAtualizacaoRacional, $dados)
    {
        foreach ($itensAtualizacaoRacional as $itemAtualizacaoRacional) {
            $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
            $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
            $objMdIaClassMetaOdsDTO->retStrRacional();
            $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
            $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["hdnIdObjetivo"]);
            $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($itemAtualizacaoRacional);
            $objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
            $itemAtualizar = (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);

            if ($itemAtualizar->getStrRacional() != self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemAtualizacaoRacional])) {

                $idMdIaClassMetaOds = $itemAtualizar->getNumIdMdIaClassMetaOds();

                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
                $objMdIaClassMetaOdsDTO->setNumIdMdIaClassMetaOds($idMdIaClassMetaOds);
                $objMdIaClassMetaOdsDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $objMdIaClassMetaOdsDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                $objMdIaClassMetaOdsDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemAtualizacaoRacional]));
                (new MdIaClassMetaOdsRN())->alterar($objMdIaClassMetaOdsDTO);

                $objMdIaHistClassDTO = new MdIaHistClassDTO();
                $objMdIaHistClassDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($itemAtualizacaoRacional);
                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_ATUALIZACAO);
                $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                $objMdIaHistClassDTO->setStrRacional(self::encodeCaracteres($dados["racionais"]["txaRacional_" . $itemAtualizacaoRacional]));
                $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
                (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
            }
        }
    }

    private static function encodeCaracteres($conteudo)
    {
        $conteudo = mb_convert_encoding(urldecode($conteudo), 'HTML-ENTITIES', 'UTF-8');
        $conteudo = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", $conteudo);
        return $conteudo;
    }

    public static function listaOdsOnu($dados)
    {

        $resultado = "";
        $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
        $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
        $objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
        $objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
        $objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmOdsOnu(1);
        $objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();

        $arrObjMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->listar($objMdIaAdmObjetivoOdsDTO);

        foreach ($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {
            $idObjetivo = $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds();
            $classe = "desfoque";
            $classIcone = "";
            $sinExisteSugestaoUsuarioExterno = false;
            $sinExisteSugestaoIA = false;

            $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
            $objMdIaClassMetaOdsDTO->setNumIdProcedimento($dados['id_procedimento']);
            $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($idObjetivo);
            $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
            $objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
            $arrObjMdIaClassMetaOdsDTO = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);

            foreach ($arrObjMdIaClassMetaOdsDTO as $objMdIaClassMetaOdsDTO) {
                if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_PADRAO || $objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO) {
                    $classe = "colorido";
                }

                switch ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario()) {
                    case MdIaClassMetaOdsRN::$USUARIO_EXTERNO:
                        $sinExisteSugestaoUsuarioExterno = true;
                        break;

                    case MdIaClassMetaOdsRN::$USUARIO_IA:
                        $sinExisteSugestaoIA = true;
                        break;
                }
            }

            if (count($arrObjMdIaClassMetaOdsDTO) == 0 && MdIaHistClassINT::existeHistorico($dados['id_procedimento'], $idObjetivo)) {
                $classe = "historico";
            }

            if ($sinExisteSugestaoIA) {
                $classIcone = " sugestaoIa";
            }

            if ($sinExisteSugestaoUsuarioExterno) {
                $classIcone = " sugestaoUsuExt";
            }

            $classe .= $classIcone;

            $arrIdsObjetivosForteRelacao = (new MdIaAdmObjetivoOdsINT())->arrIdsObjetivosForteRelacao();
            $exibirObjetivo = '';
            if ($dados['filtrar_forte_relacao'] == 'true' && !in_array($idObjetivo, $arrIdsObjetivosForteRelacao)) {
                $exibirObjetivo = 'display:none';
            }

            $resultado .=    '<div class="col-sm-12 col-md-3 col-lg-3 col-xl-2 card-objetivo" id="' . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . '" style="' . $exibirObjetivo . '">
                <a onclick=\'consultarObjetivoOds("' . $idObjetivo . '")\'>
                    <div class="imagem text-center">
                        <img src="modulos/ia/imagens/Icones_Oficiais_ONU/' . $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() . '" class="' . $classe . '" alt="Objetivo ODS: ' . $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() . '" />
                        <div id="sobreposicao_imagem" style="display: none" class="' . $classe . '"></div>
                    </div>
                </a>
            </div>';
        }
        return mb_convert_encoding($resultado, 'ISO-8859-1', 'UTF-8');
    }

    public function classificarAuto()
    {
        //BUSCAR NA PARAMETRIZAÇÃO : buscar e organizar em um array sendo o indice o tipo de processo e dentro o array de metas que estão parametrizadas
        $TiposProcessoParametrizadoParaMetas = $this->buscarTiposProcessoParametrizadoParaMetas();
        $arrTipoProcessoMetas = $this->organizarArrayTipoProcessoMetas($TiposProcessoParametrizadoParaMetas);

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $idUsuarioIA = $objInfraParametro->getValor(MdIaClassMetaOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA, false);

        if (!$idUsuarioIA) {
            throw new InfraException('Usuario_IA não encontrado nos parametro MODULO_IA_ID_USUARIO_SISTEMA.', $e);
        }

        $this->classificarMetas($arrTipoProcessoMetas, $idUsuarioIA);
        $this->desclassificarMetas($arrTipoProcessoMetas, $idUsuarioIA);
    }

    private function buscarTiposProcessoParametrizadoParaMetas()
    {
        $objMdIaAdmClassAutTpDTO = new MdIaAdmClassAutTpDTO();
        $objMdIaAdmClassAutTpDTO->retNumIdMdIaAdmMetaOds();
        $objMdIaAdmClassAutTpDTO->retNumIdTipoProcedimento();
        return (new MdIaAdmClassAutTpRN())->listar($objMdIaAdmClassAutTpDTO);
    }

    private function organizarArrayTipoProcessoMetas($arrObjMdIaAdmClassAutTpDTO)
    {
        $arrMetasTipoProcesso = [];
        foreach ($arrObjMdIaAdmClassAutTpDTO as $objMdIaAdmClassAutTpDTO) {
            $indice = $objMdIaAdmClassAutTpDTO->getNumIdTipoProcedimento();
            $arrMetasTipoProcesso[$indice][] = intval($objMdIaAdmClassAutTpDTO->getNumIdMdIaAdmMetaOds());
        }
        return $arrMetasTipoProcesso;
    }

    private function classificarMetas($arrTipoProcessoMetas, $idUsuarioIA)
    {
        foreach ($arrTipoProcessoMetas as $idTipoProcesso => $arrIdMeta) {
            $arrIdProcedimento = $this->buscarArrIdsProcessoPorTipo($idTipoProcesso);
            foreach ($arrIdProcedimento as $idProcedimento) {
                $this->cadastrarOUalterarMetasPorProcedimento($arrIdMeta, $idUsuarioIA, $idProcedimento);
            }
        }
    }

    private function buscarArrIdsProcessoPorTipo($idTipoProcesso)
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setNumIdTipoProcedimento($idTipoProcesso);
        $objProcedimentoDTO->retDblIdProcedimento();
        return InfraArray::converterArrInfraDTO((new ProcedimentoRN())->listarRN0278($objProcedimentoDTO), 'IdProcedimento');
    }

    private function cadastrarOUalterarMetasPorProcedimento($arrIdMeta, $idUsuarioIA, $idProcedimento)
    {
        foreach ($arrIdMeta as $idMeta) {

            //VERIFICA SE JÁ FOI CLASSIFICADO POR UMA PESSOA FÍSICA CASO TENHA SIDO ... NÃO FAZ NADA
            $objMdIaHistClassDTO = $this->verificarSeJaFoiCadastradoPorPessoa($idProcedimento, $idMeta, $idUsuarioIA);

            if (!$objMdIaHistClassDTO) {

                //VERIFICA SE O AGENDAMENTO JÁ CLASSIFICOU E EVITAR CLASSIFICAR NOVAMENTE
                $objMdIaClassMetaOdsDTO = $this->verificarSeJaFoiCadastradoPeloAgendamento($idProcedimento, $idMeta);

                if (!$objMdIaClassMetaOdsDTO) {
                    $params = [
                        'idProcedimento'  => $idProcedimento,
                        'idMeta'          => $idMeta,
                        'idUsuario'       => $idUsuarioIA,
                        'idUnidade'       => null,
                        'racional'        => MdIaClassMetaOdsRN::$RACIONAL_CLASS_AUTOMATICA,
                        'operacao'        => MdIaHistClassRN::$OPERACAO_INSERT
                    ];

                    // CASO TENHA UMA SUGESTÃO DE IA O AGENDAMENTO DEVE DESCARTAR A SUGESTAO E CLASSIFICAR AUTOMATICAMENTE
                    $this->excluirSugestaoIaCasoExista($params);

                    $this->cadastrarClassificacaoMeta($params);
                    $this->cadastrarHistClassificacaoMeta($params);
                }
            }
        }
    }

    private function verificarSeJaFoiCadastradoPorPessoa($idProcedimento, $idMeta)
    {
        $objMdIaHistClassDTO = new MdIaHistClassDTO();
        $objMdIaHistClassDTO->setNumIdProcedimento($idProcedimento);
        $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
        $objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdIaHistClassDTO->setNumMaxRegistrosRetorno(1);
        $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_PADRAO);
        $objMdIaHistClassDTO->retNumIdMdIaHistClass();
        return (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
    }

    private function verificarSeJaFoiCadastradoPeloAgendamento($idProcedimento, $idMeta)
    {
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->setNumIdProcedimento($idProcedimento);
        $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($idMeta);
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO);
        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
        return (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
    }

    private function cadastrarClassificacaoMeta($params)
    {
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->setNumIdProcedimento($params['idProcedimento']);
        $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($params['idMeta']);
        $objMdIaClassMetaOdsDTO->setNumIdUsuario($params['idUsuario']);
        $objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        $objMdIaClassMetaOdsDTO->setNumIdUnidade($params['idUnidade']);
        $objMdIaClassMetaOdsDTO->setStrRacional($params['racional']);
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO);
        (new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);
    }

    private function cadastrarHistClassificacaoMeta($params)
    {
        $objMdIaHistClassDTO = new MdIaHistClassDTO();
        $objMdIaHistClassDTO->setNumIdProcedimento($params['idProcedimento']);
        $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($params['idMeta']);
        $objMdIaHistClassDTO->setStrOperacao($params['operacao']);
        $objMdIaHistClassDTO->setNumIdUsuario($params['idUsuario']);
        $objMdIaHistClassDTO->setNumIdUnidade($params['idUnidade']);
        $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        $objMdIaHistClassDTO->setStrRacional($params['racional']);
        $objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO);
        (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
    }

    private function excluirSugestaoIaCasoExista($params)
    {
        $MdIaClassMetaOdsRN = new MdIaClassMetaOdsRN();
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
        $objMdIaClassMetaOdsDTO->setNumIdProcedimento($params['idProcedimento']);
        $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($params['idMeta']);
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario([MdIaClassMetaOdsRN::$USUARIO_IA, MdIaClassMetaOdsRN::$USUARIO_EXTERNO], InfraDTO::$OPER_IN);
        $objMdIaClassMetaOdsDTO = $MdIaClassMetaOdsRN->consultar($objMdIaClassMetaOdsDTO);

        if ($objMdIaClassMetaOdsDTO) {
            $MdIaClassMetaOdsRN->excluir(array($objMdIaClassMetaOdsDTO));
            $params['operacao'] = MdIaHistClassRN::$OPERACAO_DELETE;
            $params['racional'] = MdIaClassMetaOdsRN::$RACIONAL_DESCLASS_AUTOMATICA;
            $this->cadastrarHistClassificacaoMeta($params);
        }
    }

    private function desclassificarMetas($arrTipoProcessoMetas, $idUsuarioIA)
    {

        $MdIaClassMetaOdsRN = new MdIaClassMetaOdsRN();
        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
        $objMdIaClassMetaOdsDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO);
        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
        $objMdIaClassMetaOdsDTO->retNumIdProcedimento();
        $objMdIaClassMetaOdsDTO->retNumIdMdIaAdmMetaOds();
        $objMdIaClassMetaOdsDTO->retNumIdTipoProcedimento();
        $arrObjMdIaClassMetaOdsDTO = $MdIaClassMetaOdsRN->listar($objMdIaClassMetaOdsDTO);

        foreach ($arrObjMdIaClassMetaOdsDTO as $objMdIaClassMetaOdsDTO) {
            $idTipoProcesso = $objMdIaClassMetaOdsDTO->getNumIdTipoProcedimento();
            $idMeta = $objMdIaClassMetaOdsDTO->getNumIdMdIaAdmMetaOds();

            if (!(isset($arrTipoProcessoMetas[$idTipoProcesso]) && in_array($idMeta, $arrTipoProcessoMetas[$idTipoProcesso]))) {
                $params = [
                    'idProcedimento'  => $objMdIaClassMetaOdsDTO->getNumIdProcedimento(),
                    'idMeta'          => $objMdIaClassMetaOdsDTO->getNumIdMdIaAdmMetaOds(),
                    'idUsuario'       => $idUsuarioIA,
                    'idUnidade'       => null,
                    'racional'        => MdIaClassMetaOdsRN::$RACIONAL_DESCLASS_AUTOMATICA,
                    'operacao'        => MdIaHistClassRN::$OPERACAO_DELETE,
                ];

                $MdIaClassMetaOdsRN->excluir(array($objMdIaClassMetaOdsDTO));
                $this->cadastrarHistClassificacaoMeta($params);
            }
        }
    }
}
