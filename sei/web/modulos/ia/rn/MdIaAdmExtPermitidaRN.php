<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 22/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

/**
 * @method MdIaAdmExtPermitidaDTO cadastrar(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO)
 * @method MdIaAdmExtPermitidaDTO[] listar(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO)
 * @method MdIaAdmExtPermitidaDTO|null consultar(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO)
 * @method MdIaAdmExtPermitidaDTO|null bloquear(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO)
 * @method void alterar(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO)
 * @method void excluir(MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO)
 * @method void desativar(MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO)
 * @method void reativar(MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO)
 */
class MdIaAdmExtPermitidaRN extends InfraRN
{

    const LIMITE_NGINX = 512;
    const LIMITE_WHISPER_COMPRIMIDO = 120;
    const LIMITE_WHISPER_SEM_COMPRESSAO = 350;
    const LIMITE_WHISPER_VIDEO = 500;
    const LIMITE_LLM_IMAGENS = 512;

    public static function calcularLimiteTamanhoMaximoPermitido($numTamMbDocExterno, $strLimiteConsiderar): array
    {
        if (!is_numeric($numTamMbDocExterno) || $numTamMbDocExterno < 1) {
            return array(
                'parametro_valido' => false,
                'limite_maximo' => null,
                'motivo' => ''
            );
        }

        $numLimiteEfetivo = (int)$numTamMbDocExterno;
        $strMotivoLimite = 'limite geral configurado no parâmetro SEI_TAM_MB_DOC_EXTERNO.';

        $arrLimitesWhisper = [
            'whisper_comprimido' => [
                'limite' => self::LIMITE_WHISPER_COMPRIMIDO,
                'motivo' => 'limite do Whisper para transcrição de áudio comprimido.'
            ],
            'whisper_sem_compressao' => [
                'limite' => self::LIMITE_WHISPER_SEM_COMPRESSAO,
                'motivo' => 'limite do Whisper para transcrição de áudio sem compressão.'
            ],
            'whisper_video' => [
                'limite' => self::LIMITE_WHISPER_VIDEO,
                'motivo' => 'limite do Whisper para transcrição de vídeo.'
            ]
        ];

        if (isset($arrLimitesWhisper[$strLimiteConsiderar]) && $arrLimitesWhisper[$strLimiteConsiderar]['limite'] < $numLimiteEfetivo) {
            $numLimiteEfetivo = $arrLimitesWhisper[$strLimiteConsiderar]['limite'];
            $strMotivoLimite = $arrLimitesWhisper[$strLimiteConsiderar]['motivo'];
        }

        if ($strLimiteConsiderar == 'input_llm' && self::LIMITE_LLM_IMAGENS < $numLimiteEfetivo) {
            $numLimiteEfetivo = self::LIMITE_LLM_IMAGENS;
            $strMotivoLimite = 'limite de input do multimodal da API.';
        }

        if (self::LIMITE_NGINX < $numLimiteEfetivo) {
            $numLimiteEfetivo = self::LIMITE_NGINX;
            $strMotivoLimite = 'limite do NGINX no Servidor IA.';
        }

        return array(
            'parametro_valido' => true,
            'limite_maximo' => $numLimiteEfetivo,
            'motivo' => $strMotivoLimite
        );
    }

    protected function inicializarObjInfraIBanco(): InfraIBanco
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmExtPermitida(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida())) {
            $objInfraException->adicionarValidacao('ID não informad.');
        }
    }

    private function validarStrExtensao(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getStrExtensao())) {
            $objInfraException->adicionarValidacao('Extensão não informad.');
        } else {
            $objMdIaAdmExtPermitidaDTO->setStrExtensao(trim($objMdIaAdmExtPermitidaDTO->getStrExtensao()));
            if (strlen($objMdIaAdmExtPermitidaDTO->getStrExtensao()) > 20) {
                $objInfraException->adicionarValidacao('Extensão possui tamanho superior a 20 caracteres.');
            }
        }
    }

    private function validarNumFormaEntregaArquivo(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        $arrValidos = ['0', '1', '2', '3'];
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getNumFormaEntregaArquivo()) || !in_array((string)$objMdIaAdmExtPermitidaDTO->getNumFormaEntregaArquivo(), $arrValidos, true)) {
            $objInfraException->adicionarValidacao('Forma de entrega do arquivo não informada ou inválida.');
        }
    }

    private function validarStrDescTratamentoProtocolo(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (!InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoProtocolo())) {
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoProtocolo(trim($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoProtocolo()));
            if (strlen($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoProtocolo()) > 500) {
                $objInfraException->adicionarValidacao('Descrição de tratamento (protocolo) possui tamanho superior a 500 caracteres.');
            }
        } else {
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoProtocolo(null);
        }
    }

    private function validarStrDescTratamentoAvulso(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (!InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoAvulso())) {
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoAvulso(trim($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoAvulso()));
            if (strlen($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoAvulso()) > 500) {
                $objInfraException->adicionarValidacao('Descrição de tratamento (avulso) possui tamanho superior a 500 caracteres.');
            }
        } else {
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoAvulso(null);
        }
    }

    private function validarNumTamanhoMaximoPermitido(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido())) {
            $objMdIaAdmExtPermitidaDTO->setNumTamanhoMaximoPermitido(null);
        } else {

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $numTamMbDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');

            $arrInfoLimite = self::calcularLimiteTamanhoMaximoPermitido($numTamMbDocExterno, $objMdIaAdmExtPermitidaDTO->getStrLimiteConsiderar());

            if (!$arrInfoLimite['parametro_valido']) {
                $objInfraException->adicionarValidacao('Valor do parâmetro SEI_TAM_MB_DOC_EXTERNO inválido.');
                return;
            }

            $tamanhoMaximoPermitido = $arrInfoLimite['limite_maximo'];

            if (!is_numeric($objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido()) || ($objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido() < 1 || $objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido() > $tamanhoMaximoPermitido)) {
                $objInfraException->adicionarValidacao('Tamanho máximo deve ser um valor entre 1 e ' . $tamanhoMaximoPermitido . '. Motivo do limite máximo: ' . $arrInfoLimite['motivo']);
            }
        }
    }

    private function validarStrSinAtivo(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getStrSinAtivo())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        } elseif (!InfraUtil::isBolSinalizadorValido($objMdIaAdmExtPermitidaDTO->getStrSinAtivo())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
        }
    }


    private function validarStrSinConteudoVetorizavel(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaAdmExtPermitidaDTO->getStrSinConteudoVetorizavel())) {
            $objInfraException->adicionarValidacao('Sinalizador de Conteúdo Vetorizável não informado.');
        } elseif (!InfraUtil::isBolSinalizadorValido($objMdIaAdmExtPermitidaDTO->getStrSinConteudoVetorizavel())) {
            $objInfraException->adicionarValidacao('Sinalizador de Conteúdo Vetorizável inválido.');
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return MdIaAdmExtPermitidaDTO
     * @throws InfraException
     */
    protected function cadastrarControlado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): MdIaAdmExtPermitidaDTO
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_cadastrar', __METHOD__, $objMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrExtensao($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarNumFormaEntregaArquivo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarStrDescTratamentoProtocolo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarStrDescTratamentoAvulso($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarNumTamanhoMaximoPermitido($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarStrSinAtivo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            $this->validarStrSinConteudoVetorizavel($objMdIaAdmExtPermitidaDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            return $objMdIaAdmExtPermitidaBD->cadastrar($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Extensão Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return void
     * @throws InfraException
     */
    protected function alterarControlado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_alterar', __METHOD__, $objMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmExtPermitidaDTO->isSetNumIdMdIaAdmExtPermitida()) {
                $this->validarNumIdMdIaAdmExtPermitida($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrExtensao()) {
                $this->validarStrExtensao($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            /*if ($objMdIaAdmExtPermitidaDTO->isSetStrSinPermitidaProtocolo()) {
                $this->validarStrSinPermitidaProtocolo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrSinPermitidaAvulso()) {
                $this->validarStrSinPermitidaAvulso($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }
*/
            if ($objMdIaAdmExtPermitidaDTO->isSetNumFormaEntregaArquivo()) {
                $this->validarNumFormaEntregaArquivo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrDescTratamentoProtocolo()) {
                $this->validarStrDescTratamentoProtocolo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrDescTratamentoAvulso()) {
                $this->validarStrDescTratamentoAvulso($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetNumTamanhoMaximoPermitido()) {
                $this->validarNumTamanhoMaximoPermitido($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrSinConteudoVetorizavel()) {
                $this->validarStrSinConteudoVetorizavel($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }

            if ($objMdIaAdmExtPermitidaDTO->isSetStrSinAtivo()) {
                $this->validarStrSinAtivo($objMdIaAdmExtPermitidaDTO, $objInfraException);
            }


            $objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            $objMdIaAdmExtPermitidaBD->alterar($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro alterando Extensão Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado(array $arrObjMdIaAdmExtPermitidaDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_excluir', __METHOD__, $arrObjMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaAdmExtPermitidaDTO as $objMdIaAdmExtPermitidaDTO) {
                $objMdIaAdmExtPermitidaBD->excluir($objMdIaAdmExtPermitidaDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Extensão Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return MdIaAdmExtPermitidaDTO|null
     * @throws InfraException
     */
    protected function consultarConectado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): ?MdIaAdmExtPermitidaDTO
    {
        try {
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_ext_permitida_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            return $objMdIaAdmExtPermitidaBD->consultar($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Extensão Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return MdIaAdmExtPermitidaDTO[]
     * @throws InfraException
     */
    protected function listarConectado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): array
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_listar', __METHOD__, $objMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            return $objMdIaAdmExtPermitidaBD->listar($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Extensões Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return int
     * @throws InfraException
     */
    protected function contarConectado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): int
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_listar', __METHOD__, $objMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            return $objMdIaAdmExtPermitidaBD->contar($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Extensão Permitidas.', $e);
        }
    }
    /**
     * @param MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO
     * @return void
     * @throws InfraException
     */
    protected function desativarControlado(array $arrObjMdIaAdmExtPermitidaDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_desativar', __METHOD__, $arrObjMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaAdmExtPermitidaDTO as $objMdIaAdmExtPermitidaDTO) {
                $objMdIaAdmExtPermitidaBD->desativar($objMdIaAdmExtPermitidaDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro desativando Extensão Permitidas.', $e);
        }
    }

    /**
     * @param MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO
     * @return void
     * @throws InfraException
     */
    protected function reativarControlado(array $arrObjMdIaAdmExtPermitidaDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ext_permitida_reativar', __METHOD__, $arrObjMdIaAdmExtPermitidaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaAdmExtPermitidaDTO as $objMdIaAdmExtPermitidaDTO) {
                $objMdIaAdmExtPermitidaBD->reativar($objMdIaAdmExtPermitidaDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro reativando Extensão Permitidas.', $e);
        }
    }

    /**
     * @param  MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO
     * @return MdIaAdmExtPermitidaDTO|null
     * @throws InfraException
     */
    protected function bloquearConectado(MdIaAdmExtPermitidaDTO $objMdIaAdmExtPermitidaDTO): ?MdIaAdmExtPermitidaDTO
    {
        try {
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_ext_permitida_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmExtPermitidaBD = new MdIaAdmExtPermitidaBD($this->getObjInfraIBanco());

            return $objMdIaAdmExtPermitidaBD->bloquear($objMdIaAdmExtPermitidaDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro bloqueando Extensão Permitidas.', $e);
        }
    }
}
