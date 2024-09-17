<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmMetaOdsRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmMetaOds(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds())) {
            $objInfraException->adicionarValidacao('Id Meta ODS não informado.');
        }
    }

    private function validarNumIdAdmObjetivoOds(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds())) {
            $objInfraException->adicionarValidacao('Id Objetivo ODS não informado.');
        }
    }

    private function validarNumOrdem(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetaOdsDTO->getNumOrdem())) {
            $objInfraException->adicionarValidacao('Ordem não informado.');
        }
    }

    private function validarStrIdentificacaoMeta(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta())) {
            $objMdIaAdmMetaOdsDTO->setStrIdentificacaoMeta(null);
        } else {
            $objMdIaAdmMetaOdsDTO->setStrIdentificacaoMeta(trim($objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta()));

            if (strlen($objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta()) > 25) {
                $objInfraException->adicionarValidacao('Identificação da Meta possui tamanho superior a 25 caracteres.');
            }
        }
    }

    private function validarStrDescricaoMeta(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetaOdsDTO->getStrDescricaoMeta())) {
            $objMdIaAdmMetaOdsDTO->setStrDescricaoMeta(null);
        } else {
            $objMdIaAdmMetaOdsDTO->setStrDescricaoMeta(trim($objMdIaAdmMetaOdsDTO->getStrDescricaoMeta()));

            if (strlen($objMdIaAdmMetaOdsDTO->getStrDescricaoMeta()) > 1000) {
                $objInfraException->adicionarValidacao('Descrição da Meta possui tamanho superior a 1000 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_cadastrar', __METHOD__, $objMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdAdmObjetivoOds($objMdIaAdmMetaOdsDTO, $objInfraException);
            $this->validarNumOrdem($objMdIaAdmMetaOdsDTO, $objInfraException);
            $this->validarStrIdentificacaoMeta($objMdIaAdmMetaOdsDTO, $objInfraException);
            $this->validarStrDescricaoMeta($objMdIaAdmMetaOdsDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmMetaOdsBD->cadastrar($objMdIaAdmMetaOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Meta da ODS.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_alterar', __METHOD__, $objMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmMetaOdsDTO->isSetNumIdMdIaAdmMetaOds()) {
                $this->validarNumIdMdIaAdmMetaOds($objMdIaAdmMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmMetaOdsDTO->isSetNumIdMdIaAdmObjetivoOds()) {
                $this->validarNumIdAdmObjetivoOds($objMdIaAdmMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmMetaOdsDTO->isSetNumOrdem()) {
                $this->validarNumOrdem($objMdIaAdmMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmMetaOdsDTO->isSetStrIdentificacaoMeta()) {
                $this->validarStrIdentificacaoMeta($objMdIaAdmMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaAdmMetaOdsDTO->isSetStrDescricaoMeta()) {
                $this->validarStrDescricaoMeta($objMdIaAdmMetaOdsDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
            $objMdIaAdmMetaOdsBD->alterar($objMdIaAdmMetaOdsDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Meta da ODS.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_excluir', __METHOD__, $arrObjMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmMetaOdsDTO); $i++) {
                $objMdIaAdmMetaOdsBD->excluir($arrObjMdIaAdmMetaOdsDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Meta da ODS.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_consultar', __METHOD__, $objMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());

            /** @var MdIaAdmMetaOdsDTO $ret */
            $ret = $objMdIaAdmMetaOdsBD->consultar($objMdIaAdmMetaOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Meta da ODS.', $e);
        }
    }

    protected function listarConectado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_listar', __METHOD__, $objMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());

            /** @var MdIaAdmMetaOdsDTO[] $ret */
            $ret = $objMdIaAdmMetaOdsBD->listar($objMdIaAdmMetaOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Meta da ODS.', $e);
        }
    }

    protected function contarConectado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_listar', __METHOD__, $objMdIaAdmMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmMetaOdsBD->contar($objMdIaAdmMetaOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Meta da ODS.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_desativar', __METHOD__, $arrObjMdIaAdmMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmMetaOdsDTO);$i++){
            $objMdIaAdmMetaOdsBD->desativar($arrObjMdIaAdmMetaOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Meta da ODS.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_reativar', __METHOD__, $arrObjMdIaAdmMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmMetaOdsDTO);$i++){
            $objMdIaAdmMetaOdsBD->reativar($arrObjMdIaAdmMetaOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Meta da ODS.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmMetaOdsDTO $objMdIaAdmMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_meta_ods_consultar', __METHOD__, $objMdIaAdmMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmMetaOdsBD = new MdIaAdmMetaOdsBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmMetaOdsBD->bloquear($objMdIaAdmMetaOdsDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Meta da ODS.',$e);
        }
      }

     */
}
