<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmObjetivoOdsRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmObjetivoOds(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds())) {
            $objInfraException->adicionarValidacao('Id  Objetivo da ODS não informado.');
        }
    }
    private function validarNumIdMdIaAdmOdsOnu(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmOdsOnu())) {
            $objInfraException->adicionarValidacao('Id ODS ONU não informado.');
        }
    }

    private function validarStrNomeOds(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmObjetivoOdsDTO->getStrNomeOds())) {
            $objInfraException->adicionarValidacao('Nome da ODS não informado.');
        } else {
            $objMdIaAdmObjetivoOdsDTO->setStrNomeOds(trim($objMdIaAdmObjetivoOdsDTO->getStrNomeOds()));
        }
    }

    private function validarStrDescricaoOds(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds())) {
            $objInfraException->adicionarValidacao('Descrição da ODS não informado.');
        } else {
            $objMdIaAdmObjetivoOdsDTO->setStrDescricaoOds(trim($objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds()));
        }
    }

    private function validarStrIconeOds(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmObjetivoOdsDTO->getStrIconeOds())) {
            $objInfraException->adicionarValidacao('Icone da ODS não informado.');
        } else {
            $objMdIaAdmObjetivoOdsDTO->setStrIconeOds(trim($objMdIaAdmObjetivoOdsDTO->getStrIconeOds()));
        }
    }

    protected function cadastrarControlado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_cadastrar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmOdsOnu($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            $this->validarStrNomeOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            $this->validarStrDescricaoOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            $this->validarStrIconeOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmObjetivoOdsBD->cadastrar($objMdIaAdmObjetivoOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Objetivo da ODS.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_alterar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmObjetivoOdsDTO->isSetNumIdMdIaAdmObjetivoOds()) {
                $this->validarNumIdMdIaAdmObjetivoOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmObjetivoOdsDTO->isSetNumIdMdIaAdmOdsOnu()) {
                $this->validarNumIdMdIaAdmOdsOnu($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmObjetivoOdsDTO->isSetStrNomeOds()) {
                $this->validarStrNomeOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmObjetivoOdsDTO->isSetStrDescricaoOds()) {
                $this->validarStrDescricaoOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmObjetivoOdsDTO->isSetStrIconeOds()) {
                $this->validarStrIconeOds($objMdIaAdmObjetivoOdsDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
            $objMdIaAdmObjetivoOdsBD->alterar($objMdIaAdmObjetivoOdsDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Objetivo da ODS.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_excluir', __METHOD__, $arrObjMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmObjetivoOdsDTO); $i++) {
                $objMdIaAdmObjetivoOdsBD->excluir($arrObjMdIaAdmObjetivoOdsDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Objetivo da ODS.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_consultar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
            /** @var MdIaAdmObjetivoOdsDTO $ret */
            $ret = $objMdIaAdmObjetivoOdsBD->consultar($objMdIaAdmObjetivoOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Objetivo da ODS.', $e);
        }
    }

    protected function listarConectado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_listar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());

            /** @var MdIaAdmObjetivoOdsDTO[] $ret */
            $ret = $objMdIaAdmObjetivoOdsBD->listar($objMdIaAdmObjetivoOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Objetivo da ODS.', $e);
        }
    }

    protected function contarConectado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_listar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmObjetivoOdsBD->contar($objMdIaAdmObjetivoOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Objetivo da ODS.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmObjetivoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_desativar', __METHOD__, $arrObjMdIaAdmObjetivoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmObjetivoOdsDTO);$i++){
            $objMdIaAdmObjetivoOdsBD->desativar($arrObjMdIaAdmObjetivoOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Objetivo da ODS.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmObjetivoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_reativar', __METHOD__, $arrObjMdIaAdmObjetivoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmObjetivoOdsDTO);$i++){
            $objMdIaAdmObjetivoOdsBD->reativar($arrObjMdIaAdmObjetivoOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Objetivo da ODS.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmObjetivoOdsDTO $objMdIaAdmObjetivoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_objetivo_ods_consultar', __METHOD__, $objMdIaAdmObjetivoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmObjetivoOdsBD = new MdIaAdmObjetivoOdsBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmObjetivoOdsBD->bloquear($objMdIaAdmObjetivoOdsDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Objetivo da ODS.',$e);
        }
      }

     */
}
