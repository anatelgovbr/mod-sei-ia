<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmDocRelevINT extends InfraINT
{

    public static function retornaSelectTipoDocumento($selAplicabilidade)
    {

        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->retStrNome();
        $objSerieDTO->setStrSinAtivo("S");
        if ($selAplicabilidade["aplicabilidade"] == "I") {
            $objSerieDTO->adicionarCriterio(array('StaAplicabilidade', 'StaAplicabilidade', 'StaAplicabilidade'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array('I', 'T', 'F'), array(InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_OR));
        } else {
            $objSerieDTO->adicionarCriterio(array('StaAplicabilidade', 'StaAplicabilidade'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array('E', 'T'), InfraDTO::$OPER_LOGICO_OR);
        }
        $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objSerieRN = new SerieRN();
        $arrObjSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);
        return parent::montarSelectArrInfraDTO(null, null, $selAplicabilidade["valorItemSelecionado"], $arrObjSerieDTO, 'IdSerie', 'Nome');
    }
    public static function verificarItemAdicionado($dados)
    {
        $arrTipoProcessoEspecifico = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dados['hdnIdTpProcesso']);

        $itensAdicionados = array();
        if ($dados['hdnIdTpProcesso'] != "") {
            foreach ($arrTipoProcessoEspecifico as $tpProcesso) {
                $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
                $objMdIaAdmDocRelevDTO->setNumIdSerie($dados['selTipoDocumento']);
                $objMdIaAdmDocRelevDTO->setStrAplicabilidade($dados['selAplicabilidade']);
                $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                $objMdIaAdmDocRelevDTO->adicionarCriterio(array('IdTipoProcedimento', 'IdTipoProcedimento'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array($tpProcesso[0], null), InfraDTO::$OPER_LOGICO_OR);
                $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
                foreach ($arrObjMdIaAdmDocRelevDTO as $documentoRelevanteJaAdicionado) {
                    $itensAdicionados[] = mb_convert_encoding($documentoRelevanteJaAdicionado->getStrNomeTipoProcedimento(), 'UTF-8', 'ISO-8859-1');
                }
            }
            if (!empty($itensAdicionados)) {
                return $itensAdicionados;
            }
        }
        if ($dados['hdnIdTpProcesso'] == "") {
            $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
            $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
            $objMdIaAdmDocRelevDTO->setNumIdSerie($dados['selTipoDocumento']);
            $objMdIaAdmDocRelevDTO->setStrAplicabilidade($dados['selAplicabilidade']);
            $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
            $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
            $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
            foreach ($arrObjMdIaAdmDocRelevDTO as $documentoRelevanteJaAdicionado) {
                $itensAdicionados[] = mb_convert_encoding($documentoRelevanteJaAdicionado->getStrNomeTipoProcedimento(), 'UTF-8', 'ISO-8859-1');
            }
            if (!empty($itensAdicionados)) {
                return $itensAdicionados;
            }
        }

        return array("result" => "false");
    }
    public static function retornaSelectTipoDocumentoCadastrado($selAplicabilidade)
    {

        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retNumIdSerie();
        $objMdIaAdmDocRelevDTO->retStrNomeSerie();
        $objMdIaAdmDocRelevDTO->setStrAplicabilidade($selAplicabilidade["aplicabilidade"]);
        $objMdIaAdmDocRelevDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdIaAdmDocRelevDTO->setDistinct(true);

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
        $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
        return parent::montarSelectArrInfraDTO(0, "Selecione uma opção", $selAplicabilidade["valorItemSelecionado"], $arrObjMdIaAdmDocRelevDTO, 'IdSerie', 'NomeSerie');
    }
    public static function retornaSelectTipoProcessoCadastrado($tipoDocumento)
    {

        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
        $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
        $objMdIaAdmDocRelevDTO->setNumIdSerie($tipoDocumento["tipoDocumento"]);
        $objMdIaAdmDocRelevDTO->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdIaAdmDocRelevDTO->setDistinct(true);

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
        $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
        return parent::montarSelectArrInfraDTO(0, "Todos os Tipos de Processo", $tipoDocumento["valorItemSelecionado"], $arrObjMdIaAdmDocRelevDTO, 'IdTipoProcedimento', 'NomeTipoProcedimento');
    }
    public static function retornaComboboxTipoDocumento($dados)
    {
        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retNumIdSerie();
        $objMdIaAdmDocRelevDTO->retStrNomeSerie();
        $objMdIaAdmDocRelevDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdIaAdmDocRelevDTO->setDistinct(true);

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
        $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
        return parent::montarSelectArrInfraDTO(0, "Selecione uma opção", $dados["selTipoDocumento"], $arrObjMdIaAdmDocRelevDTO, 'IdSerie', 'NomeSerie');
    }
    public static function retornaComboboxTipoProcessos($dados)
    {


        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $objMdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
        $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
        $objMdIaAdmDocRelevDTO->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdIaAdmDocRelevDTO->setDistinct(true);

        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
        $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);

        $arrayTiposProcessoCorrigido = array();
        foreach ($arrObjMdIaAdmDocRelevDTO as $objMdIaAdmDocRelevDTO) {
            if (is_null($objMdIaAdmDocRelevDTO->getNumIdTipoProcedimento())) {
                $objMdIaAdmDocRelevDTO->setStrNomeTipoProcedimento("Todos os Tipos de Processo");
            }
            array_push($arrayTiposProcessoCorrigido, $objMdIaAdmDocRelevDTO);
        }
        return parent::montarSelectArrInfraDTO(0, "Selecione uma opção", $dados["selTipoProcesso"], $arrayTiposProcessoCorrigido, 'IdTipoProcedimento', 'NomeTipoProcedimento');
    }
    public static function validarReativacao($dados)
    {

        $arrayIds = explode(",", $dados["id"]);
        if (!is_array($arrayIds)) {
            $arrayIds = array($dados["id"]);
        }
        foreach ($arrayIds as $idDocumentoRelevante) {
            $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
            $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
            $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($idDocumentoRelevante);
            $objMdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
            $objMdIaAdmDocRelevDTO->retNumIdSerie();
            $objMdIaAdmDocRelevDTO->retStrAplicabilidade();
            $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
            $documentoRelevanteSelecionado = $objMdIaAdmDocRelevRN->consultar($objMdIaAdmDocRelevDTO);
            if ($documentoRelevanteSelecionado->getNumIdTipoProcedimento() > 0) {
                $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
                $objMdIaAdmDocRelevDTO->setNumIdSerie($documentoRelevanteSelecionado->getNumIdSerie());
                $objMdIaAdmDocRelevDTO->setStrAplicabilidade($documentoRelevanteSelecionado->getStrAplicabilidade());
                $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento(null);
                $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->contar($objMdIaAdmDocRelevDTO);

                if ($arrObjMdIaAdmDocRelevDTO > 0) {
                    return array("result" => mb_convert_encoding($documentoRelevanteSelecionado->getStrNomeTipoProcedimento(), 'UTF-8', 'ISO-8859-1'));
                }
            } else {
                $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
                $objMdIaAdmDocRelevDTO->setNumIdSerie($documentoRelevanteSelecionado->getNumIdSerie());
                $objMdIaAdmDocRelevDTO->setStrAplicabilidade($documentoRelevanteSelecionado->getStrAplicabilidade());
                $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->contar($objMdIaAdmDocRelevDTO);

                if ($arrObjMdIaAdmDocRelevDTO > 0) {
                    return array("result" => "todos");
                }
            }
        }

        return array("result" => "false");
    }
    public static function verificarItemAdicionadoDesativado($dados)
    {
        if ($dados['hdnIdTpProcesso'] != "") {
            $arrTipoProcessoEspecifico = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dados['hdnIdTpProcesso']);
            foreach ($arrTipoProcessoEspecifico as $tipoProcessoEspecifico) {
                $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();
                $objMdIaAdmDocRelevDTO->setNumIdSerie($dados['selTipoDocumento']);
                $objMdIaAdmDocRelevDTO->setStrAplicabilidade($dados['selAplicabilidade']);
                $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento($tipoProcessoEspecifico[0]);
                $objMdIaAdmDocRelevDTO->setStrSinAtivo("N");
                $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
                if ($arrObjMdIaAdmDocRelevDTO) {
                    return array("result" => mb_convert_encoding("O Tipo de Processo Específico <strong>" . $arrObjMdIaAdmDocRelevDTO[0]->getStrNomeTipoProcedimento() . "</strong> já existe no cadastro de Documentos Relevantes, porém se encontra com a situação desativado. Para utilizar o mesmo você deve realizar a reativação do Processo Específico informado.", 'UTF-8', 'ISO-8859-1'));
                }
            }
        } else {
            $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
            $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
            $objMdIaAdmDocRelevDTO->setNumIdSerie($dados['selTipoDocumento']);
            $objMdIaAdmDocRelevDTO->setStrAplicabilidade($dados['selAplicabilidade']);
            $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento(null);
            $objMdIaAdmDocRelevDTO->setStrSinAtivo("N");
            $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
            $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);
            if ($arrObjMdIaAdmDocRelevDTO) {
                return array("result" => mb_convert_encoding("A Aplicabilidade combinada com o Tipo de Documento informado já existe no cadastro de Documentos Relevantes sendo Relevante para todos os Tipos de Processos, porém se encontra com a situação desativado. Para utilizar o mesmo você deve realizar a reativação.", 'UTF-8', 'ISO-8859-1'));
            }
        }
        return array("result" => "false");
    }
}
