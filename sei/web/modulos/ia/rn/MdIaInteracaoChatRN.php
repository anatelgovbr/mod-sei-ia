<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaInteracaoChatRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaTopicoChat(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getNumIdMdIaTopicoChat())) {
            $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat(null);
        }
    }

    private function validarNumIdMessage(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getNumIdMessage())) {
            $objMdIaInteracaoChatDTO->setNumIdMessage(null);
        }
    }

    private function validarNumTotalTokens(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getNumTotalTokens())) {
            $objMdIaInteracaoChatDTO->setNumTotalTokens(null);
        }
    }

    private function validarStrPergunta(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getStrPergunta())) {
            $objMdIaInteracaoChatDTO->setStrPergunta(null);
        } else {
            $objMdIaInteracaoChatDTO->setStrPergunta(trim($objMdIaInteracaoChatDTO->getStrPergunta()));
        }
    }

    private function validarStrResposta(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getStrResposta())) {
            $objMdIaInteracaoChatDTO->setStrResposta(null);
        } else {
            $objMdIaInteracaoChatDTO->setStrResposta(trim($objMdIaInteracaoChatDTO->getStrResposta()));
        }
    }

    private function validarNumFeedback(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getNumFeedback())) {
            $objMdIaInteracaoChatDTO->setNumFeedback(null);
        }
    }

    private function validarDthCadastro(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaInteracaoChatDTO->getDthCadastro())) {
            $objMdIaInteracaoChatDTO->setDthCadastro(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaInteracaoChatDTO->getDthCadastro())) {
                $objInfraException->adicionarValidacao(' inválid.');
            }
        }
    }

    protected function cadastrarControlado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaTopicoChat($objMdIaInteracaoChatDTO, $objInfraException);
            $this->validarStrPergunta($objMdIaInteracaoChatDTO, $objInfraException);
            $this->validarDthCadastro($objMdIaInteracaoChatDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
            $ret = $objMdIaInteracaoChatBD->cadastrar($objMdIaInteracaoChatDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Interação.', $e);
        }
    }

    protected function alterarControlado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaInteracaoChatDTO->isSetNumIdMdIaTopicoChat()) {
                $this->validarNumIdMdIaTopicoChat($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetNumIdMessage()) {
                $this->validarNumIdMessage($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetNumTotalTokens()) {
                $this->validarNumTotalTokens($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetStrPergunta()) {
                $this->validarStrPergunta($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetStrResposta()) {
                $this->validarStrResposta($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetNumFeedback()) {
                $this->validarNumFeedback($objMdIaInteracaoChatDTO, $objInfraException);
            }
            if ($objMdIaInteracaoChatDTO->isSetDthCadastro()) {
                $this->validarDthCadastro($objMdIaInteracaoChatDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
            $objMdIaInteracaoChatBD->alterar($objMdIaInteracaoChatDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Interação.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaInteracaoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaInteracaoChatDTO); $i++) {
                $objMdIaInteracaoChatBD->excluir($arrObjMdIaInteracaoChatDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Interação.', $e);
        }
    }

    protected function consultarConectado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());

            /** @var MdIaInteracaoChatDTO $ret */
            $ret = $objMdIaInteracaoChatBD->consultar($objMdIaInteracaoChatDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Interação.', $e);
        }
    }

    protected function listarConectado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());

            /** @var MdIaInteracaoChatDTO[] $ret */
            $ret = $objMdIaInteracaoChatBD->listar($objMdIaInteracaoChatDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Interações.', $e);
        }
    }

    protected function contarConectado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
            $ret = $objMdIaInteracaoChatBD->contar($objMdIaInteracaoChatDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Interações.', $e);
        }
    }

    /*
      protected function desativarControlado($arrObjMdIaInteracaoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaInteracaoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaInteracaoChatDTO);$i++){
            $objMdIaInteracaoChatBD->desativar($arrObjMdIaInteracaoChatDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Interação.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaInteracaoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaInteracaoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaInteracaoChatDTO);$i++){
            $objMdIaInteracaoChatBD->reativar($arrObjMdIaInteracaoChatDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Interação.',$e);
        }
      }

      protected function bloquearControlado(MdIaInteracaoChatDTO $objMdIaInteracaoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaInteracaoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD($this->getObjInfraIBanco());
          $ret = $objMdIaInteracaoChatBD->bloquear($objMdIaInteracaoChatDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Interação.',$e);
        }
      }

     */
}
