<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 17/09/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaOdsOnuNsaINT extends InfraINT
{
    public static function atualizaNaoSeAplicaOdsOnu($dados)
    {
        $objMdIaOdsOnuNsaDTO = new MdIaOdsOnuNsaDTO();
        $objMdIaOdsOnuNsaRN = new MdIaOdsOnuNsaRN();
        $objMdIaOdsOnuNsaDTO->setDblIdProcedimento($dados["idProcedimento"]);
        $objMdIaOdsOnuNsaDTO->retDblIdProcedimento();
        $registro = $objMdIaOdsOnuNsaRN->consultar($objMdIaOdsOnuNsaDTO);

        if ($dados["naoSeAplica"] == "true") {
            if (!$registro) {
                $objMdIaOdsOnuNsaDTO->setDblIdProcedimento($dados["idProcedimento"]);
                $objMdIaOdsOnuNsaDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
                $objMdIaOdsOnuNsaDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objMdIaOdsOnuNsaDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
                $objMdIaOdsOnuNsaRN->cadastrar($objMdIaOdsOnuNsaDTO);
            }
        } else {
            if ($registro) {
                $objMdIaOdsOnuNsaRN->excluir([$registro]);
            }
        }
        return true;
    }
}
