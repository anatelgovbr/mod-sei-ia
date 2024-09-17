<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaTopicoChatRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdUsuario(MdIaTopicoChatDTO $objMdIaTopicoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaTopicoChatDTO->getNumIdUsuario())) {
            $objInfraException->adicionarValidacao(' n�o informad.');
        }
    }

    private function validarNumIdUnidade(MdIaTopicoChatDTO $objMdIaTopicoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaTopicoChatDTO->getNumIdUnidade())) {
            $objMdIaTopicoChatDTO->setNumIdUnidade(null);
        }
    }

    private function validarStrNome(MdIaTopicoChatDTO $objMdIaTopicoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaTopicoChatDTO->getStrNome())) {
            $objMdIaTopicoChatDTO->setStrNome(null);
        } else {
            $objMdIaTopicoChatDTO->setStrNome(trim($objMdIaTopicoChatDTO->getStrNome()));

            if (strlen($objMdIaTopicoChatDTO->getStrNome()) > 80) {
                $objInfraException->adicionarValidacao(' possui tamanho superior a 80 caracteres.');
            }
        }
    }

    private function validarDthCadastro(MdIaTopicoChatDTO $objMdIaTopicoChatDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaTopicoChatDTO->getDthCadastro())) {
            $objMdIaTopicoChatDTO->setDthCadastro(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaTopicoChatDTO->getDthCadastro())) {
                $objInfraException->adicionarValidacao(' inv�lid.');
            }
        }
    }

    protected function cadastrarControlado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaTopicoChatDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdUsuario($objMdIaTopicoChatDTO, $objInfraException);
            $this->validarNumIdUnidade($objMdIaTopicoChatDTO, $objInfraException);
            $this->validarStrNome($objMdIaTopicoChatDTO, $objInfraException);
            $this->validarDthCadastro($objMdIaTopicoChatDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
            $ret = $objMdIaTopicoChatBD->cadastrar($objMdIaTopicoChatDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando T�pico.', $e);
        }
    }

    protected function alterarControlado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaTopicoChatDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaTopicoChatDTO->isSetNumIdUsuario()) {
                $this->validarNumIdUsuario($objMdIaTopicoChatDTO, $objInfraException);
            }
            if ($objMdIaTopicoChatDTO->isSetNumIdUnidade()) {
                $this->validarNumIdUnidade($objMdIaTopicoChatDTO, $objInfraException);
            }
            if ($objMdIaTopicoChatDTO->isSetStrNome()) {
                $this->validarStrNome($objMdIaTopicoChatDTO, $objInfraException);
            }
            if ($objMdIaTopicoChatDTO->isSetDthCadastro()) {
                $this->validarDthCadastro($objMdIaTopicoChatDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
            $objMdIaTopicoChatBD->alterar($objMdIaTopicoChatDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando T�pico.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaTopicoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaTopicoChatDTO); $i++) {
                $objMdIaTopicoChatBD->excluir($arrObjMdIaTopicoChatDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo T�pico.', $e);
        }
    }

    protected function consultarConectado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaTopicoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());

            /** @var MdIaTopicoChatDTO $ret */
            $ret = $objMdIaTopicoChatBD->consultar($objMdIaTopicoChatDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando T�pico.', $e);
        }
    }

    protected function listarConectado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaTopicoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());

            /** @var MdIaTopicoChatDTO[] $ret */
            $ret = $objMdIaTopicoChatBD->listar($objMdIaTopicoChatDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando T�picos.', $e);
        }
    }

    protected function contarConectado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaTopicoChatDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
            $ret = $objMdIaTopicoChatBD->contar($objMdIaTopicoChatDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando T�picos.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaTopicoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaTopicoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaTopicoChatDTO);$i++){
            $objMdIaTopicoChatBD->desativar($arrObjMdIaTopicoChatDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando T�pico.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaTopicoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $arrObjMdIaTopicoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaTopicoChatDTO);$i++){
            $objMdIaTopicoChatBD->reativar($arrObjMdIaTopicoChatDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando T�pico.',$e);
        }
      }

      protected function bloquearControlado(MdIaTopicoChatDTO $objMdIaTopicoChatDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_topico_chat_consultar', __METHOD__, $objMdIaTopicoChatDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaTopicoChatBD = new MdIaTopicoChatBD($this->getObjInfraIBanco());
          $ret = $objMdIaTopicoChatBD->bloquear($objMdIaTopicoChatDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando T�pico.',$e);
        }
      }

     */
}
