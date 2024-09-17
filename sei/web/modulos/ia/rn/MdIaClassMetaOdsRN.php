<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/12/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaClassMetaOdsRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaClassificacaoOds(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassMetaOdsDTO->getNumIdMdIaClassificacaoOds())) {
            $objInfraException->adicionarValidacao('Avaliação ODS não informada.');
        }
    }

    private function validarNumIdMdIaAdmMetaOds(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassMetaOdsDTO->getNumIdMdIaAdmMetaOds())) {
            $objInfraException->adicionarValidacao('Meta ODS não informada.');
        }
    }

    private function validarNumIdUsuario(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassMetaOdsDTO->getNumIdUsuario())) {
            $objInfraException->adicionarValidacao('Id Usuário não informado.');
        }
    }


    private function validarNumIdUnidade(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassMetaOdsDTO->getNumIdUnidade())) {
            $objInfraException->adicionarValidacao('Id Unidade não informado.');
        }
    }

    private function validarDthCadastro(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassMetaOdsDTO->getDthCadastro())) {
            $objMdIaClassMetaOdsDTO->setDthCadastro(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaClassMetaOdsDTO->getDthCadastro())) {
                $objInfraException->adicionarValidacao('Data de Cadastro inválida.');
            }
        }
    }

    protected function cadastrarControlado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_cadastrar', __METHOD__, $objMdIaClassMetaOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaClassificacaoOds($objMdIaClassMetaOdsDTO, $objInfraException);
            $this->validarNumIdMdIaAdmMetaOds($objMdIaClassMetaOdsDTO, $objInfraException);
            $this->validarNumIdUsuario($objMdIaClassMetaOdsDTO, $objInfraException);
            $this->validarDthCadastro($objMdIaClassMetaOdsDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());

            $ret = $objMdIaClassMetaOdsBD->cadastrar($objMdIaClassMetaOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Meta Avaliação.', $e);
        }
    }

    protected function alterarControlado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_alterar', __METHOD__, $objMdIaClassMetaOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaClassMetaOdsDTO->isSetNumIdMdIaClassificacaoOds()) {
                $this->validarNumIdMdIaClassificacaoOds($objMdIaClassMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaClassMetaOdsDTO->isSetNumIdMdIaAdmMetaOds()) {
                $this->validarNumIdMdIaAdmMetaOds($objMdIaClassMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaClassMetaOdsDTO->isSetNumIdUsuario()) {
                $this->validarNumIdUsuario($objMdIaClassMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaClassMetaOdsDTO->isSetNumIdUnidade()) {
                $this->validarNumIdUnidade($objMdIaClassMetaOdsDTO, $objInfraException);
            }
            if ($objMdIaClassMetaOdsDTO->isSetDthCadastro()) {
                $this->validarDthCadastro($objMdIaClassMetaOdsDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());
            $objMdIaClassMetaOdsBD->alterar($objMdIaClassMetaOdsDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Meta Avaliação.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_excluir', __METHOD__, $arrObjMdIaClassMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaClassMetaOdsDTO); $i++) {
                $objMdIaClassMetaOdsBD->excluir($arrObjMdIaClassMetaOdsDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Meta Avaliação.', $e);
        }
    }

    protected function consultarConectado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_consultar', __METHOD__, $objMdIaClassMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());

            /** @var MdIaClassMetaOdsDTO $ret */
            $ret = $objMdIaClassMetaOdsBD->consultar($objMdIaClassMetaOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Meta Avaliação.', $e);
        }
    }

    protected function listarConectado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_listar', __METHOD__, $objMdIaClassMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());

            /** @var MdIaClassMetaOdsDTO[] $ret */
            $ret = $objMdIaClassMetaOdsBD->listar($objMdIaClassMetaOdsDTO);


            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Metas Avaliação.', $e);
        }
    }

    protected function contarConectado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_listar', __METHOD__, $objMdIaClassMetaOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());

            $ret = $objMdIaClassMetaOdsBD->contar($objMdIaClassMetaOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Metas Avaliação.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaClassMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_desativar', __METHOD__, $arrObjMdIaClassMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaClassMetaOdsDTO);$i++){
            $objMdIaClassMetaOdsBD->desativar($arrObjMdIaClassMetaOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Meta Avaliação.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaClassMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_reativar', __METHOD__, $arrObjMdIaClassMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaClassMetaOdsDTO);$i++){
            $objMdIaClassMetaOdsBD->reativar($arrObjMdIaClassMetaOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Meta Avaliação.',$e);
        }
      }

      protected function bloquearControlado(MdIaClassMetaOdsDTO $objMdIaClassMetaOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_class_meta_ods_consultar', __METHOD__, $objMdIaClassMetaOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassMetaOdsBD = new MdIaClassMetaOdsBD($this->getObjInfraIBanco());
          $ret = $objMdIaClassMetaOdsBD->bloquear($objMdIaClassMetaOdsDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Meta Avaliação.',$e);
        }
      }

     */
}
