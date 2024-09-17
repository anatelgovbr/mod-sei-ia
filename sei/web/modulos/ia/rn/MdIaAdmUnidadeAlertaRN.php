<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmUnidadeAlertaRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmUnidadeAlerta(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUnidadeAlertaDTO->getNumIdMdIaAdmUnidadeAlerta())) {
            $objInfraException->adicionarValidacao('Id Adm Unidade Alerta não informado.');
        }
    }

    private function validarNumIdMdIaAdmOdsOnu(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUnidadeAlertaDTO->getNumIdMdIaAdmOdsOnu())) {
            $objInfraException->adicionarValidacao('Id ODS ONU não informado.');
        }
    }

    private function validarNumIdUnidade(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUnidadeAlertaDTO->getNumIdUnidade())) {
            $objInfraException->adicionarValidacao('Id Unidade não informado.');
        }
    }

    private function validarDthAlteracao(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUnidadeAlertaDTO->getDthAlteracao())) {
            $objMdIaAdmUnidadeAlertaDTO->setDthAlteracao(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaAdmUnidadeAlertaDTO->getDthAlteracao())) {
                $objInfraException->adicionarValidacao('Data de Alteração inválida.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_cadastrar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmOdsOnu($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            $this->validarNumIdUnidade($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            $this->validarDthAlteracao($objMdIaAdmUnidadeAlertaDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmUnidadeAlertaBD->cadastrar($objMdIaAdmUnidadeAlertaDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Unidade Alerta.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_alterar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmUnidadeAlertaDTO->isSetNumIdMdIaAdmUnidadeAlerta()) {
                $this->validarNumIdMdIaAdmUnidadeAlerta($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            }
            if ($objMdIaAdmUnidadeAlertaDTO->isSetNumIdMdIaAdmOdsOnu()) {
                $this->validarNumIdMdIaAdmOdsOnu($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            }
            if ($objMdIaAdmUnidadeAlertaDTO->isSetNumIdUnidade()) {
                $this->validarNumIdUnidade($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            }
            if ($objMdIaAdmUnidadeAlertaDTO->isSetDthAlteracao()) {
                $this->validarDthAlteracao($objMdIaAdmUnidadeAlertaDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
            $objMdIaAdmUnidadeAlertaBD->alterar($objMdIaAdmUnidadeAlertaDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Unidade Alerta.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_excluir', __METHOD__, $arrObjMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmUnidadeAlertaDTO); $i++) {
                $objMdIaAdmUnidadeAlertaBD->excluir($arrObjMdIaAdmUnidadeAlertaDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Unidade Alerta.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_consultar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());

            /** @var MdIaAdmUnidadeAlertaDTO $ret */
            $ret = $objMdIaAdmUnidadeAlertaBD->consultar($objMdIaAdmUnidadeAlertaDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Unidade Alerta.', $e);
        }
    }

    protected function listarConectado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_listar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());

            /** @var MdIaAdmUnidadeAlertaDTO[] $ret */
            $ret = $objMdIaAdmUnidadeAlertaBD->listar($objMdIaAdmUnidadeAlertaDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Unidade Alerta.', $e);
        }
    }

    protected function contarConectado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_listar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmUnidadeAlertaBD->contar($objMdIaAdmUnidadeAlertaDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Unidade Alerta.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmUnidadeAlertaDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_desativar', __METHOD__, $arrObjMdIaAdmUnidadeAlertaDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmUnidadeAlertaDTO);$i++){
            $objMdIaAdmUnidadeAlertaBD->desativar($arrObjMdIaAdmUnidadeAlertaDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Unidade Alerta.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmUnidadeAlertaDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_reativar', __METHOD__, $arrObjMdIaAdmUnidadeAlertaDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmUnidadeAlertaDTO);$i++){
            $objMdIaAdmUnidadeAlertaBD->reativar($arrObjMdIaAdmUnidadeAlertaDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Unidade Alerta.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmUnidadeAlertaDTO $objMdIaAdmUnidadeAlertaDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_unidade_alerta_consultar', __METHOD__, $objMdIaAdmUnidadeAlertaDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmUnidadeAlertaBD = new MdIaAdmUnidadeAlertaBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmUnidadeAlertaBD->bloquear($objMdIaAdmUnidadeAlertaDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Unidade Alerta.',$e);
        }
      }

     */

    protected function cadastrarRelacionamentoConectado($arrUnidadesAlerta)
    {
        $idMdIaAdmOdsOnu = $arrUnidadesAlerta[1];
        foreach ($arrUnidadesAlerta[0] as $unidadeAlerta) {
            if ($unidadeAlerta[0] != "") {
                $objMdIaAdmUnidadeAlertaDTO = new MdIaAdmUnidadeAlertaDTO();
                $objMdIaAdmUnidadeAlertaDTO->setNumIdMdIaAdmOdsOnu($idMdIaAdmOdsOnu);
                $objMdIaAdmUnidadeAlertaDTO->setNumIdUnidade($unidadeAlerta[0]);
                $objMdIaAdmUnidadeAlertaDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                $this->cadastrar($objMdIaAdmUnidadeAlertaDTO);
            }
        }
    }
    protected function excluirRelacionamentoConectado($idMdIaAdmOdsOnu)
    {
        $objMdIaAdmUnidadeAlertaDTO = new MdIaAdmUnidadeAlertaDTO();
        $objMdIaAdmUnidadeAlertaDTO->retNumIdMdIaAdmUnidadeAlerta();
        $objMdIaAdmUnidadeAlertaDTO->setNumIdMdIaAdmOdsOnu($idMdIaAdmOdsOnu);
        $this->excluir($this->listar($objMdIaAdmUnidadeAlertaDTO));
    }
}
