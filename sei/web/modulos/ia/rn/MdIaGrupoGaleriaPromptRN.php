<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.45
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

/**
 * @method MdIaGrupoGaleriaPromptDTO cadastrar(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO)
 * @method MdIaGrupoGaleriaPromptDTO[] listar(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO)
 * @method MdIaGrupoGaleriaPromptDTO|null consultar(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO)
 * @method MdIaGrupoGaleriaPromptDTO|null bloquear(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO)
 * @method void alterar(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO)
 * @method void excluir(MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO)
 * @method void desativar(MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO)
 * @method void reativar(MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO)
 */
class MdIaGrupoGaleriaPromptRN extends InfraRN
{
    protected function inicializarObjInfraIBanco(): InfraIBanco
    {
        return BancoSEI::getInstance();
    }

    private function validarStrNomeGrupo(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo())) {
            $objInfraException->adicionarValidacao('Nome não informado.');
        } else {

            $objMdIaGrupoGaleriaPromptDTO->setStrNomeGrupo(trim($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo()));

            $objMdIaGrupoGaleriaPromptDTO->setStrNomeGrupo(InfraUtil::filtrarISO88591($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo()));

            if (strlen($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo()) > $this->getNumMaxTamanhoNome()) {
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a ' . $this->getNumMaxTamanhoNome() . ' caracteres.');
            }

            $dto = new MdIaGrupoGaleriaPromptDTO();
            $dto->retNumIdMdIaGrupoGaleriaPrompt();
            $dto->setNumIdMdIaGrupoGaleriaPrompt($objMdIaGrupoGaleriaPromptDTO->getNumIdMdIaGrupoGaleriaPrompt(), InfraDTO::$OPER_DIFERENTE);
            $dto->setStrNomeGrupo($objMdIaGrupoGaleriaPromptDTO->getStrNomeGrupo());
            $dto = $this->consultar($dto);
            if ($dto != null) {
                $objInfraException->adicionarValidacao('Existe outro Grupo de Galeria de Prompts com mesmo nome nesta unidade.');
            }
        }
    }

    public function getNumMaxTamanhoNome()
    {
        return 100;
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return MdIaGrupoGaleriaPromptDTO
     * @throws InfraException
     */
    protected function cadastrarControlado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): MdIaGrupoGaleriaPromptDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_cadastrar', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrNomeGrupo($objMdIaGrupoGaleriaPromptDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            return $objMdIaGrupoGaleriaPromptBD->cadastrar($objMdIaGrupoGaleriaPromptDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Grupo de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return void
     * @throws InfraException
     */
    protected function alterarControlado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_alterar', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaGrupoGaleriaPromptDTO->isSetStrNomeGrupo()) {
                $this->validarStrNomeGrupo($objMdIaGrupoGaleriaPromptDTO, $objInfraException);
            }


            $objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            $objMdIaGrupoGaleriaPromptBD->alterar($objMdIaGrupoGaleriaPromptDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro alterando Grupo de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado(array $arrObjMdIaGrupoGaleriaPromptDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_excluir', __METHOD__, $arrObjMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaGrupoGaleriaPromptDTO as $objMdIaGrupoGaleriaPromptDTO) {
                $objMdIaGrupoGaleriaPromptBD->excluir($objMdIaGrupoGaleriaPromptDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Grupo de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return MdIaGrupoGaleriaPromptDTO|null
     * @throws InfraException
     */
    protected function consultarConectado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): ?MdIaGrupoGaleriaPromptDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_grupos_galeria_prompts', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            return $objMdIaGrupoGaleriaPromptBD->consultar($objMdIaGrupoGaleriaPromptDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Grupo de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return MdIaGrupoGaleriaPromptDTO[]
     * @throws InfraException
     */
    protected function listarConectado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): array
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_grupos_galeria_prompts', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            return $objMdIaGrupoGaleriaPromptBD->listar($objMdIaGrupoGaleriaPromptDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Grupos de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return int
     * @throws InfraException
     */
    protected function contarConectado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): int
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_listar', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
            return $objMdIaGrupoGaleriaPromptBD->contar($objMdIaGrupoGaleriaPromptDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Grupo de Galeria de Prompts.', $e);
        }
    }

    /**
     * @param MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO
     * @return void
     * @throws InfraException
     */
    /*   protected function desativarControlado(array $arrObjMdIaGrupoGaleriaPromptDTO): void
  {
    try {
      SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_desativar', __METHOD__, $arrObjMdIaGrupoGaleriaPromptDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
      foreach ($arrObjMdIaGrupoGaleriaPromptDTO as $objMdIaGrupoGaleriaPromptDTO) {
        $objMdIaGrupoGaleriaPromptBD->desativar($objMdIaGrupoGaleriaPromptDTO);
      }

    } catch (Exception $e) {
      throw new InfraException('Erro desativando Grupo de Galeria de Prompts.', $e);
    }
  }
 */
    /**
     * @param MdIaGrupoGaleriaPromptDTO[] $arrObjMdIaGrupoGaleriaPromptDTO
     * @return void
     * @throws InfraException
     */
    /*   protected function reativarControlado(array $arrObjMdIaGrupoGaleriaPromptDTO): void
  {
    try {
      SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_grupo_galeria_prompt_reativar', __METHOD__, $arrObjMdIaGrupoGaleriaPromptDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());
      foreach ($arrObjMdIaGrupoGaleriaPromptDTO as $objMdIaGrupoGaleriaPromptDTO) {
        $objMdIaGrupoGaleriaPromptBD->reativar($objMdIaGrupoGaleriaPromptDTO);
      }

    } catch (Exception $e) {
      throw new InfraException('Erro reativando Grupo de Galeria de Prompts.', $e);
    }
  }
 */
    /**
     * @param  MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO
     * @return MdIaGrupoGaleriaPromptDTO|null
     * @throws InfraException
     */
    /*   protected function bloquearConectado(MdIaGrupoGaleriaPromptDTO $objMdIaGrupoGaleriaPromptDTO): ?MdIaGrupoGaleriaPromptDTO
  {
    try {
      SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_grupos_galeria_prompts', __METHOD__, $objMdIaGrupoGaleriaPromptDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaGrupoGaleriaPromptBD = new MdIaGrupoGaleriaPromptBD($this->getObjInfraIBanco());

      return $objMdIaGrupoGaleriaPromptBD->bloquear($objMdIaGrupoGaleriaPromptDTO);

    } catch (Exception $e) {
      throw new InfraException('Erro bloqueando Grupo de Galeria de Prompts.', $e);
    }
  } */
}
