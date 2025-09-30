<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 17/09/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * @method MdIaOdsOnuNsaDTO cadastrar(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO)
 * @method MdIaOdsOnuNsaDTO[] listar(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO)
 * @method MdIaOdsOnuNsaDTO|null consultar(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO)
 * @method MdIaOdsOnuNsaDTO|null bloquear(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO)
 * @method void alterar(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO)
 * @method void excluir(MdIaOdsOnuNsaDTO[] $arrObjMdIaOdsOnuNsaDTO)
 * @method void desativar(MdIaOdsOnuNsaDTO[] $arrObjMdIaOdsOnuNsaDTO)
 * @method void reativar(MdIaOdsOnuNsaDTO[] $arrObjMdIaOdsOnuNsaDTO)
 */
class MdIaOdsOnuNsaRN extends InfraRN
{
    protected function inicializarObjInfraIBanco(): InfraIBanco
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdUsuario(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaOdsOnuNsaDTO->getNumIdUsuario())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumIdUnidade(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaOdsOnuNsaDTO->getNumIdUnidade())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarDthCadastro(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaOdsOnuNsaDTO->getDthCadastro())) {
            $objMdIaOdsOnuNsaDTO->setDthCadastro(null);
        } elseif (!InfraData::validarDataHora($objMdIaOdsOnuNsaDTO->getDthCadastro())) {
            $objInfraException->adicionarValidacao(' inválid.');
        }
    }

    /**
     * @param MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO
     * @return MdIaOdsOnuNsaDTO
     * @throws InfraException
     */
    protected function cadastrarControlado(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO): MdIaOdsOnuNsaDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_ods_onu_nsa_cadastrar', __METHOD__, $objMdIaOdsOnuNsaDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdUsuario($objMdIaOdsOnuNsaDTO, $objInfraException);
            $this->validarNumIdUnidade($objMdIaOdsOnuNsaDTO, $objInfraException);
            $this->validarDthCadastro($objMdIaOdsOnuNsaDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaOdsOnuNsaBD = new MdIaOdsOnuNsaBD($this->getObjInfraIBanco());
            return $objMdIaOdsOnuNsaBD->cadastrar($objMdIaOdsOnuNsaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando NSA.', $e);
        }
    }

    /**
     * @param MdIaOdsOnuNsaDTO[] $arrObjMdIaOdsOnuNsaDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado(array $arrObjMdIaOdsOnuNsaDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_ods_onu_nsa_excluir', __METHOD__, $arrObjMdIaOdsOnuNsaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaOdsOnuNsaBD = new MdIaOdsOnuNsaBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaOdsOnuNsaDTO as $objMdIaOdsOnuNsaDTO) {
                $objMdIaOdsOnuNsaBD->excluir($objMdIaOdsOnuNsaDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo NSA.', $e);
        }
    }

    /**
     * @param MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO
     * @return MdIaOdsOnuNsaDTO|null
     * @throws InfraException
     */
    protected function consultarConectado(MdIaOdsOnuNsaDTO $objMdIaOdsOnuNsaDTO): ?MdIaOdsOnuNsaDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_ods_onu_nsa_consultar', __METHOD__, $objMdIaOdsOnuNsaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaOdsOnuNsaBD = new MdIaOdsOnuNsaBD($this->getObjInfraIBanco());
            return $objMdIaOdsOnuNsaBD->consultar($objMdIaOdsOnuNsaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando NSA.', $e);
        }
    }
}
