<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmOdsOnuRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmOdsOnu(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmOdsOnuDTO->getNumIdMdIaAdmOdsOnu())) {
            $objInfraException->adicionarValidacao('Id ODS ONU não informado.');
        }
    }

    private function validarStrSinExibirFuncionalidade(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exibir Funcionalidade não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade())) {
                $objInfraException->adicionarValidacao('Sinalizador de Exibir Funcionalidade não informado.');
            }
        }
    }

    private function validarStrOrientacoesGerais(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmOdsOnuDTO->getStrOrientacoesGerais())) {
            $objInfraException->adicionarValidacao('Orientações Gerais não informada.');
        } else {
            $objMdIaAdmOdsOnuDTO->setStrOrientacoesGerais(trim($objMdIaAdmOdsOnuDTO->getStrOrientacoesGerais()));
        }
    }


    protected function cadastrarControlado(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_cadastrar', __METHOD__, $objMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrSinExibirFuncionalidade($objMdIaAdmOdsOnuDTO, $objInfraException);
            $this->validarStrOrientacoesGerais($objMdIaAdmOdsOnuDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmOdsOnuBD->cadastrar($objMdIaAdmOdsOnuDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando ODS da ONU.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_alterar', __METHOD__, $objMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmOdsOnuDTO->isSetNumIdMdIaAdmOdsOnu()) {
                $this->validarNumIdMdIaAdmOdsOnu($objMdIaAdmOdsOnuDTO, $objInfraException);
            }
            if ($objMdIaAdmOdsOnuDTO->isSetStrSinExibirFuncionalidade()) {
                $this->validarStrSinExibirFuncionalidade($objMdIaAdmOdsOnuDTO, $objInfraException);
            }
            if ($objMdIaAdmOdsOnuDTO->isSetStrOrientacoesGerais()) {
                $this->validarStrOrientacoesGerais($objMdIaAdmOdsOnuDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
            $objMdIaAdmOdsOnuBD->alterar($objMdIaAdmOdsOnuDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando ODS da ONU.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_excluir', __METHOD__, $arrObjMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmOdsOnuDTO); $i++) {
                $objMdIaAdmOdsOnuBD->excluir($arrObjMdIaAdmOdsOnuDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo ODS da ONU.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_consultar', __METHOD__, $objMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());

            /** @var MdIaAdmOdsOnuDTO $ret */
            $ret = $objMdIaAdmOdsOnuBD->consultar($objMdIaAdmOdsOnuDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando ODS da ONU.', $e);
        }
    }

    protected function listarConectado(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_listar', __METHOD__, $objMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());

            /** @var MdIaAdmOdsOnuDTO[] $ret */
            $ret = $objMdIaAdmOdsOnuBD->listar($objMdIaAdmOdsOnuDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando ODS da ONU.', $e);
        }
    }

    protected function contarConectado(MdIaAdmOdsOnuDTO $objMdIaAdmOdsOnuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_listar', __METHOD__, $objMdIaAdmOdsOnuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmOdsOnuBD->contar($objMdIaAdmOdsOnuDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando ODS da ONU.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmOdsOnuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_desativar', __METHOD__, $arrObjMdIaAdmOdsOnuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmOdsOnuDTO);$i++){
            $objMdIaAdmOdsOnuBD->desativar($arrObjMdIaAdmOdsOnuDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando ODS da ONU.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmOdsOnuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_reativar', __METHOD__, $arrObjMdIaAdmOdsOnuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmOdsOnuDTO);$i++){
            $objMdIaAdmOdsOnuBD->reativar($arrObjMdIaAdmOdsOnuDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando ODS da ONU.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmTpDocPesqDTO $objMdIaAdmOdsOnuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_ods_onu_consultar', __METHOD__, $objMdIaAdmOdsOnuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmOdsOnuBD = new MdIaAdmOdsOnuBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmOdsOnuBD->bloquear($objMdIaAdmOdsOnuDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando ODS da ONU.',$e);
        }
      }

     */
}
