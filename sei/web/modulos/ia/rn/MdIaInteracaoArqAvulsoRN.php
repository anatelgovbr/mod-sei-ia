<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 30/12/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaInteracaoArqAvulsoRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }


    protected function cadastrarControlado(MdIaInteracaoArqAvulsoDTO $objMdIaInteracaoArqAvulsoDTO)
    {
        try {

            // Regras de Negócio
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacoes();

            $objMdIaInteracaoArqAvulsoBD = new MdIaInteracaoArqAvulsoBD($this->getObjInfraIBanco());
            $ret = $objMdIaInteracaoArqAvulsoBD->cadastrar($objMdIaInteracaoArqAvulsoDTO);

            // Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Documento.', $e);
        }
    }

    protected function consultarConectado(MdIaInteracaoArqAvulsoDTO $objMdIaInteracaoArqAvulsoDTO)
    {
        try {

            $objMdIaInteracaoArqAvulsoBD = new MdIaInteracaoArqAvulsoBD($this->getObjInfraIBanco());
            $ret = $objMdIaInteracaoArqAvulsoBD->consultar($objMdIaInteracaoArqAvulsoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Arquivo.', $e);
        }
    }


    protected function listarConectado(MdIaInteracaoArqAvulsoDTO $objMdIaInteracaoArqAvulsoDTO)
    {
        try {

            $objMdIaInteracaoArqAvulsoBD = new MdIaInteracaoArqAvulsoBD($this->getObjInfraIBanco());
            $ret = $objMdIaInteracaoArqAvulsoBD->listar($objMdIaInteracaoArqAvulsoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro listando Documentos.', $e);
        }
    }
}
