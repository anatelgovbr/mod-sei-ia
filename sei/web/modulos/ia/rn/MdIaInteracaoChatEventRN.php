<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 07/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

/**
 * @method MdIaInteracaoChatEventDTO cadastrar(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO)
 * @method MdIaInteracaoChatEventDTO[] listar(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO)
 * @method MdIaInteracaoChatEventDTO|null consultar(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO)
 * @method MdIaInteracaoChatEventDTO|null bloquear(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO)
 * @method void alterar(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO)
 * @method void excluir(MdIaInteracaoChatEventDTO[] $arrObjMdIaInteracaoChatEventDTO)
 * @method void desativar(MdIaInteracaoChatEventDTO[] $arrObjMdIaInteracaoChatEventDTO)
 * @method void reativar(MdIaInteracaoChatEventDTO[] $arrObjMdIaInteracaoChatEventDTO)
 */
class MdIaInteracaoChatEventRN extends InfraRN
{
    private const PERMISSAO_ASSISTENTE = 'md_ia_adm_config_assist_ia_consultar';

    protected function inicializarObjInfraIBanco(): InfraIBanco
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaInteracaoChat(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatEventDTO->getNumIdMdIaInteracaoChat())) {
            $objInfraException->adicionarValidacao('Id da Interação não informada.');
        }
    }

    private function validarStrTipoEvento(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatEventDTO->getStrTipoEvento())) {
            $objInfraException->adicionarValidacao('Tipo do Evento não informado.');
        } else {
            $objMdIaInteracaoChatEventDTO->setStrTipoEvento(trim($objMdIaInteracaoChatEventDTO->getStrTipoEvento()));
            if (strlen($objMdIaInteracaoChatEventDTO->getStrTipoEvento()) > 20) {
                $objInfraException->adicionarValidacao('Tipo do Evento possui tamanho superior a 20 caracteres.');
            }
        }
    }

    private function validarStrConteudo(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatEventDTO->getStrConteudo())) {
            $objMdIaInteracaoChatEventDTO->setStrConteudo(null);
        } else {
            $objMdIaInteracaoChatEventDTO->setStrConteudo(trim($objMdIaInteracaoChatEventDTO->getStrConteudo()));
        }
    }

    private function validarDthEvento(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO, InfraException $objInfraException): void
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatEventDTO->getDthEvento())) {
            $objInfraException->adicionarValidacao('Data do Evento não informada.');
        } elseif (!InfraData::validarDataHora($objMdIaInteracaoChatEventDTO->getDthEvento())) {
            $objInfraException->adicionarValidacao('Data do Evento inválida.');
        }
    }

    protected function cadastrarControlado(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO): MdIaInteracaoChatEventDTO
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

            $objInfraException = new InfraException();
            $this->validarNumIdMdIaInteracaoChat($objMdIaInteracaoChatEventDTO, $objInfraException);
            $this->validarStrTipoEvento($objMdIaInteracaoChatEventDTO, $objInfraException);
            $this->validarStrConteudo($objMdIaInteracaoChatEventDTO, $objInfraException);
            $this->validarDthEvento($objMdIaInteracaoChatEventDTO, $objInfraException);
            $objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            return $objMdIaInteracaoChatEventBD->cadastrar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Evento.', $e);
        }
    }

    protected function alterarControlado(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

            $objInfraException = new InfraException();
            if ($objMdIaInteracaoChatEventDTO->isSetNumIdMdIaInteracaoChat()) {
                $this->validarNumIdMdIaInteracaoChat($objMdIaInteracaoChatEventDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatEventDTO->isSetStrTipoEvento()) {
                $this->validarStrTipoEvento($objMdIaInteracaoChatEventDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatEventDTO->isSetStrConteudo()) {
                $this->validarStrConteudo($objMdIaInteracaoChatEventDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatEventDTO->isSetDthEvento()) {
                $this->validarDthEvento($objMdIaInteracaoChatEventDTO, $objInfraException);
            }
            $objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            $objMdIaInteracaoChatEventBD->alterar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro alterando Evento.', $e);
        }
    }

    protected function excluirControlado(array $arrObjMdIaInteracaoChatEventDTO): void
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            foreach ($arrObjMdIaInteracaoChatEventDTO as $objMdIaInteracaoChatEventDTO) {
                $objMdIaInteracaoChatEventBD->excluir($objMdIaInteracaoChatEventDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Evento.', $e);
        }
    }

    protected function consultarConectado(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO): ?MdIaInteracaoChatEventDTO
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);
            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            return $objMdIaInteracaoChatEventBD->consultar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Evento.', $e);
        }
    }

    protected function listarConectado(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO): array
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);
            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            return $objMdIaInteracaoChatEventBD->listar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Eventos.', $e);
        }
    }

    protected function contarConectado(MdIaInteracaoChatEventDTO $objMdIaInteracaoChatEventDTO): int
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);
            $objMdIaInteracaoChatEventBD = new MdIaInteracaoChatEventBD($this->getObjInfraIBanco());
            return $objMdIaInteracaoChatEventBD->contar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Evento.', $e);
        }
    }

    public function registrarStatusInteracao(int $idInteracao, ?string $conteudo): ?MdIaInteracaoChatEventDTO
    {
        return $this->registrarEventoInteracao($idInteracao, 'status', $conteudo, true);
    }

    public function registrarReasoningInteracao(int $idInteracao, ?string $conteudo): ?MdIaInteracaoChatEventDTO
    {
        return $this->registrarEventoInteracao($idInteracao, 'reasoning', $conteudo, false);
    }

    private function registrarEventoInteracao(int $idInteracao, string $tipoEvento, ?string $conteudo, bool $deduplicar): ?MdIaInteracaoChatEventDTO
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

            $conteudo = trim((string)$conteudo);
            if ($conteudo === '') {
                return null;
            }

            if ($deduplicar) {
                $ultimoEvento = $this->consultarUltimoEventoPorTipo($idInteracao, $tipoEvento);
                if ($ultimoEvento !== null && $ultimoEvento->getStrConteudo() === $conteudo) {
                    return $ultimoEvento;
                }
            }

            $objMdIaInteracaoChatEventDTO = new MdIaInteracaoChatEventDTO();
            $objMdIaInteracaoChatEventDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatEventDTO->setStrTipoEvento($tipoEvento);
            $objMdIaInteracaoChatEventDTO->setStrConteudo($conteudo);
            $objMdIaInteracaoChatEventDTO->setDthEvento(InfraData::getStrDataHoraAtual());

            return $this->cadastrar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro registrando evento da interação.', $e);
        }
    }

    public function consultarUltimoEventoStatus(int $idInteracao): ?MdIaInteracaoChatEventDTO
    {
        try {
            return $this->consultarUltimoEventoPorTipo($idInteracao, 'status');
        } catch (Exception $e) {
            throw new InfraException('Erro consultando ultimo status da interação.', $e);
        }
    }

    public function consultarUltimoEventoReasoning(int $idInteracao): ?MdIaInteracaoChatEventDTO
    {
        try {
            return $this->consultarUltimoEventoPorTipo($idInteracao, 'reasoning');
        } catch (Exception $e) {
            throw new InfraException('Erro consultando ultimo reasoning da interação.', $e);
        }
    }

    private function consultarUltimoEventoPorTipo(int $idInteracao, string $tipoEvento): ?MdIaInteracaoChatEventDTO
    {
        SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

        $objMdIaInteracaoChatEventDTO = new MdIaInteracaoChatEventDTO();
        $objMdIaInteracaoChatEventDTO->setNumIdMdIaInteracaoChat($idInteracao);
        $objMdIaInteracaoChatEventDTO->setStrTipoEvento($tipoEvento);
        $objMdIaInteracaoChatEventDTO->retNumIdMdIaInteracaoChatEvent();
        $objMdIaInteracaoChatEventDTO->retNumIdMdIaInteracaoChat();
        $objMdIaInteracaoChatEventDTO->retStrTipoEvento();
        $objMdIaInteracaoChatEventDTO->retStrConteudo();
        $objMdIaInteracaoChatEventDTO->retDthEvento();
        $objMdIaInteracaoChatEventDTO->setOrdNumIdMdIaInteracaoChatEvent(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdIaInteracaoChatEventDTO->setNumMaxRegistrosRetorno(1);

        $arrObjMdIaInteracaoChatEventDTO = $this->listar($objMdIaInteracaoChatEventDTO);
        if (count($arrObjMdIaInteracaoChatEventDTO) === 0) {
            return null;
        }

        return $arrObjMdIaInteracaoChatEventDTO[0];
    }

    public function listarEventosInteracao(int $idInteracao): array
    {
        try {
            SessaoSEI::getInstance()->validarPermissao(self::PERMISSAO_ASSISTENTE);

            $objMdIaInteracaoChatEventDTO = new MdIaInteracaoChatEventDTO();
            $objMdIaInteracaoChatEventDTO->setNumIdMdIaInteracaoChat($idInteracao);
            $objMdIaInteracaoChatEventDTO->retNumIdMdIaInteracaoChatEvent();
            $objMdIaInteracaoChatEventDTO->retNumIdMdIaInteracaoChat();
            $objMdIaInteracaoChatEventDTO->retStrTipoEvento();
            $objMdIaInteracaoChatEventDTO->retStrConteudo();
            $objMdIaInteracaoChatEventDTO->retDthEvento();
            $objMdIaInteracaoChatEventDTO->setOrdNumIdMdIaInteracaoChatEvent(InfraDTO::$TIPO_ORDENACAO_ASC);

            return $this->listar($objMdIaInteracaoChatEventDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando histórico de eventos da interação.', $e);
        }
    }
}
