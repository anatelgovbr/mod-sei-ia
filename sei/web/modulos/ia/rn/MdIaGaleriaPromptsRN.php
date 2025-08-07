<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.45
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

/**
 * @method MdIaGaleriaPromptsDTO cadastrar(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO)
 * @method MdIaGaleriaPromptsDTO[] listar(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO)
 * @method MdIaGaleriaPromptsDTO|null consultar(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO)
 * @method MdIaGaleriaPromptsDTO|null bloquear(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO)
 * @method void alterar(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO)
 * @method void excluir(MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO)
 * @method void desativar(MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO)
 * @method void reativar(MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO)
 */
class MdIaGaleriaPromptsRN extends InfraRN
{
    protected function inicializarObjInfraIBanco(): InfraIBanco
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaGaleriaPrompts(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getNumIdMdIaGaleriaPrompts())) {
            $objInfraException->adicionarValidacao('Id Prompt não informadO');
        }
    }

    private function validarNumIdMdIaGrupoGaleriaPrompt(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getNumIdMdIaGrupoGaleriaPrompt())) {
            $objInfraException->adicionarValidacao('Id Grupo não informadO');
        }
    }

    private function validarNumIdUsuario(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getNumIdUsuario())) {
            $objInfraException->adicionarValidacao('Id Usuário não informadO');
        }
    }

    private function validarNumIdUnidade(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getNumIdUnidade())) {
            $objInfraException->adicionarValidacao('Id Unidade não informadA');
        }
    }

    private function validarStrDescricao(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getStrDescricao())) {
            $objInfraException->adicionarValidacao('Descrição não informadA');
        } else {
            $objMdIaGaleriaPromptsDTO->setStrDescricao(trim($objMdIaGaleriaPromptsDTO->getStrDescricao()));
            if (strlen($objMdIaGaleriaPromptsDTO->getStrDescricao()) > 500) {
                $objInfraException->adicionarValidacao('Descrição possui tamanho superior a 500 caracteres.');
            }
        }
    }

    private function validarStrPrompt(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getStrPrompt())) {
            $objMdIaGaleriaPromptsDTO->setStrPrompt(null);
        } else {
            $objMdIaGaleriaPromptsDTO->setStrPrompt(trim($objMdIaGaleriaPromptsDTO->getStrPrompt()));
        }
    }

    private function validarDthAlteracao(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaGaleriaPromptsDTO->getDthAlteracao())) {
            $objMdIaGaleriaPromptsDTO->setDthAlteracao(null);
        } elseif (!InfraData::validarDataHora($objMdIaGaleriaPromptsDTO->getDthAlteracao())) {
            $objInfraException->adicionarValidacao('Data de Alteração inválidA.');
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return MdIaGaleriaPromptsDTO
     * @throws InfraException
     */
    protected function cadastrarControlado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): MdIaGaleriaPromptsDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_galeria_prompt_cadastrar', __METHOD__, $objMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaGrupoGaleriaPrompt($objMdIaGaleriaPromptsDTO, $objInfraException);
            $this->validarNumIdUsuario($objMdIaGaleriaPromptsDTO, $objInfraException);
            $this->validarNumIdUnidade($objMdIaGaleriaPromptsDTO, $objInfraException);
            $this->validarStrDescricao($objMdIaGaleriaPromptsDTO, $objInfraException);
            $this->validarStrPrompt($objMdIaGaleriaPromptsDTO, $objInfraException);
            $this->validarDthAlteracao($objMdIaGaleriaPromptsDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            return $objMdIaGaleriaPromptsBD->cadastrar($objMdIaGaleriaPromptsDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Prompt Publicado.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return void
     * @throws InfraException
     */
    protected function alterarControlado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_galeria_prompt_alterar', __METHOD__, $objMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaGaleriaPromptsDTO->isSetNumIdMdIaGaleriaPrompts()) {
                $this->validarNumIdMdIaGaleriaPrompts($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetNumIdMdIaGrupoGaleriaPrompt()) {
                $this->validarNumIdMdIaGrupoGaleriaPrompt($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetNumIdUsuario()) {
                $this->validarNumIdUsuario($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetNumIdUnidade()) {
                $this->validarNumIdUnidade($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetStrDescricao()) {
                $this->validarStrDescricao($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetStrPrompt()) {
                $this->validarStrPrompt($objMdIaGaleriaPromptsDTO, $objInfraException);
            }

            if ($objMdIaGaleriaPromptsDTO->isSetDthAlteracao()) {
                $this->validarDthAlteracao($objMdIaGaleriaPromptsDTO, $objInfraException);
            }


            $objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            $objMdIaGaleriaPromptsBD->alterar($objMdIaGaleriaPromptsDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro alterando Prompt Publicado.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado(array $arrObjMdIaGaleriaPromptsDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_galeria_prompt_excluir', __METHOD__, $arrObjMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaGaleriaPromptsDTO as $objMdIaGaleriaPromptsDTO) {
                $objMdIaGaleriaPromptsBD->excluir($objMdIaGaleriaPromptsDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Prompt Publicado.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return MdIaGaleriaPromptsDTO|null
     * @throws InfraException
     */
    protected function consultarConectado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): ?MdIaGaleriaPromptsDTO
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            return $objMdIaGaleriaPromptsBD->consultar($objMdIaGaleriaPromptsDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Prompt Publicado.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return MdIaGaleriaPromptsDTO[]
     * @throws InfraException
     */
    protected function listarConectado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): array
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            return $objMdIaGaleriaPromptsBD->listar($objMdIaGaleriaPromptsDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Prompts Publicados.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return int
     * @throws InfraException
     */
    protected function contarConectado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): int
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            return $objMdIaGaleriaPromptsBD->contar($objMdIaGaleriaPromptsDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Prompt Publicado.', $e);
        }
    }

    public static function consultarPromptGaleriaPrompts($dadosEnviados)
    {
        $objMdIaGaleriaPromptsDTO = new MdIaGaleriaPromptsDTO();
        $objMdIaGaleriaPromptsDTO->setNumIdMdIaGaleriaPrompts($dadosEnviados["IdMdIaGaleriaPrompts"]);
        $objMdIaGaleriaPromptsDTO->retStrPrompt();
        $objMdIaGaleriaPromptsDTO->retStrDescricao();
        $objMdIaGaleriaPromptsDTO->retNumIdMdIaGrupoGaleriaPrompt();
        $objMdIaGaleriaPromptsRN = new MdIaGaleriaPromptsRN();
        $prompt = $objMdIaGaleriaPromptsRN->consultar($objMdIaGaleriaPromptsDTO);

        if (!is_null($prompt)) {
            return array(
                "result" => "true",
                "prompt" => mb_convert_encoding($prompt->getStrPrompt(), 'UTF-8', 'ISO-8859-1'),
                "descricao" => mb_convert_encoding($prompt->retStrDescricao(), 'UTF-8', 'ISO-8859-1'),
                "grupo" => $prompt->getNumIdMdIaGrupoGaleriaPrompt()
            );
        } else {
            return array("result" => "false");
        }
    }
    /**
     * @param MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO
     * @return void
     * @throws InfraException
     */
    protected function desativarControlado(array $arrObjMdIaGaleriaPromptsDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_galeria_prompt_desativar', __METHOD__, $arrObjMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaGaleriaPromptsDTO as $objMdIaGaleriaPromptsDTO) {
                $objMdIaGaleriaPromptsBD->desativar($objMdIaGaleriaPromptsDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro desativando Prompt Publicado.', $e);
        }
    }

    /**
     * @param MdIaGaleriaPromptsDTO[] $arrObjMdIaGaleriaPromptsDTO
     * @return void
     * @throws InfraException
     */
    protected function reativarControlado(array $arrObjMdIaGaleriaPromptsDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_galeria_prompt_reativar', __METHOD__, $arrObjMdIaGaleriaPromptsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaGaleriaPromptsDTO as $objMdIaGaleriaPromptsDTO) {
                $objMdIaGaleriaPromptsBD->reativar($objMdIaGaleriaPromptsDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro reativando Prompt Publicado.', $e);
        }
    }

    /**
     * @param  MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO
     * @return MdIaGaleriaPromptsDTO|null
     * @throws InfraException
     */
    /*   protected function bloquearConectado(MdIaGaleriaPromptsDTO $objMdIaGaleriaPromptsDTO): ?MdIaGaleriaPromptsDTO
  {
    try {
      SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaGaleriaPromptsDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaGaleriaPromptsBD = new MdIaGaleriaPromptsBD($this->getObjInfraIBanco());

      return $objMdIaGaleriaPromptsBD->bloquear($objMdIaGaleriaPromptsDTO);

    } catch (Exception $e) {
      throw new InfraException('Erro bloqueando Prompt Publicado.', $e);
    }
  } */
}
