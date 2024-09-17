<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 03/09/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmIntegFuncionRN extends InfraRN
{

    public static $ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL = '1';
    public static $ID_FUNCIONALIDADE_INTERFACE_LLM = '2';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarStrNome(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmIntegFuncionDTO->getStrNome())) {
            $objInfraException->adicionarValidacao('Funcionalidade não informad.');
        } else {
            $objMdIaAdmIntegFuncionDTO->setStrNome(trim($objMdIaAdmIntegFuncionDTO->getStrNome()));

            if (strlen($objMdIaAdmIntegFuncionDTO->getStrNome()) > 100) {
                $objInfraException->adicionarValidacao('Funcionalidade possui tamanho superior a 100 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrNome($objMdIaAdmIntegFuncionDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegFuncionBD->cadastrar($objMdIaAdmIntegFuncionDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Singular.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmIntegFuncionDTO->isSetStrNome()) {
                $this->validarStrNome($objMdIaAdmIntegFuncionDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            $objMdIaAdmIntegFuncionBD->alterar($objMdIaAdmIntegFuncionDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Singular.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmIntegFuncionDTO); $i++) {
                $objMdIaAdmIntegFuncionBD->excluir($arrObjMdIaAdmIntegFuncionDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Singular.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegFuncionBD->consultar($objMdIaAdmIntegFuncionDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Singular.', $e);
        }
    }

    protected function listarConectado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegFuncionBD->listar($objMdIaAdmIntegFuncionDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Plural.', $e);
        }
    }

    protected function contarConectado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegFuncionBD->contar($objMdIaAdmIntegFuncionDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Plural.', $e);
        }
    }

    public static function verificarMdIaIntegFuncionalidUtilizado($NumIdMdIaAdmIntegracao = null, $NumIdMdIaAdmIntegFuncion = null, $strNomeMdIaIntegFuncionalid = null)
    {
        $objMdIaIntegracaoDTO = new MdIaAdmIntegracaoDTO();
        $objMdIaIntegracaoDTO->retNumIdMdIaAdmIntegFuncion();
        $objMdIaIntegracaoDTO->setBolExclusaoLogica(true);
        $objMdIaIntegracaoDTO->setDistinct(true);
        if ($NumIdMdIaAdmIntegracao != null) {
            $objMdIaIntegracaoDTO->setNumIdMdIaAdmIntegracao($NumIdMdIaAdmIntegracao, InfraDTO::$OPER_DIFERENTE);
        }
        if ($NumIdMdIaAdmIntegFuncion != null) {
            $objMdIaIntegracaoDTO->setNumIdMdIaAdmIntegFuncion($NumIdMdIaAdmIntegFuncion);
        }
        if ($strNomeMdIaIntegFuncionalid != null) {
            $objMdIaIntegracaoDTO->setStrNomeMdIaIntegFuncionalid($strNomeMdIaIntegFuncionalid);
        }

        $objMdIaIntegracaoRN = new MdIaAdmIntegracaoRN();
        $arrObjMdIaIntegracaoDTO = $objMdIaIntegracaoRN->listar($objMdIaIntegracaoDTO);

        if (count($arrObjMdIaIntegracaoDTO) > 0) {
            $arrIdMdIaAdmIntegFuncionUtilizado = InfraArray::converterArrInfraDTO($arrObjMdIaIntegracaoDTO, 'IdMdIaAdmIntegFuncion');
        }

        return $arrIdMdIaAdmIntegFuncionUtilizado;
    }

    /*
      protected function desativarControlado($arrObjMdIaAdmIntegFuncionDTO){
        try {

          //Valida Permissao
          SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_desativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmIntegFuncionDTO);$i++){
            $objMdIaAdmIntegFuncionBD->desativar($arrObjMdIaAdmIntegFuncionDTO[$i]);
          }

          //Auditoria

        }catch(Exception $e){
          throw new InfraException('Erro desativando Singular.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmIntegFuncionDTO){
        try {

          //Valida Permissao
          SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_reativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmIntegFuncionDTO);$i++){
            $objMdIaAdmIntegFuncionBD->reativar($arrObjMdIaAdmIntegFuncionDTO[$i]);
          }

          //Auditoria

        }catch(Exception $e){
          throw new InfraException('Erro reativando Singular.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmIntegFuncionDTO $objMdIaAdmIntegFuncionDTO){
        try {

          //Valida Permissao
          SessaoSEI::getInstance()->validarPermissao('md_ia_adm_integ_funcion_consultar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmIntegFuncionBD = new MdIaAdmIntegFuncionBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmIntegFuncionBD->bloquear($objMdIaAdmIntegFuncionDTO);

          //Auditoria

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Singular.',$e);
        }
      }

     */
}
