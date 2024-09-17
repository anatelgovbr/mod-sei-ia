<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/01/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaHistClassRN extends InfraRN
{
    public static $OPERACAO_INSERT                 = "I";
    public static $OPERACAO_INSERT_DESC            = "Adicionado";

    public static $OPERACAO_DELETE                 = "D";
    public static $OPERACAO_DELETE_DESC            = "Excluído";

    public static $OPERACAO_CONFIRMACAO            = "C";
    public static $OPERACAO_CONFIRMACAO_DESC       = "Sugestão Confirmada";

    public static $OPERACAO_NÃO_CONFIRMACAO        = "N";
    public static $OPERACAO_NÃO_CONFIRMACAO_DESC   = "Sugestão Não Confirmada";
	
	public static $OPERACAO_SOBRESCRITA            = "S";
	public static $OPERACAO_SOBRESCRITA_DESC       = "Sobrescreveu sugestão dada por IA";

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaClassificacaoOds(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getNumIdMdIaClassificacaoOds())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumIdMdIaAdmMetaOds(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getNumIdMdIaAdmMetaOds())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarStrOperacao(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getStrOperacao())) {
            $objInfraException->adicionarValidacao(' não informad.');
        } else {
            $objMdIaHistClassDTO->setStrOperacao(trim($objMdIaHistClassDTO->getStrOperacao()));

            if (strlen($objMdIaHistClassDTO->getStrOperacao()) > 1) {
                $objInfraException->adicionarValidacao(' possui tamanho superior a 1 caracteres.');
            }
        }
    }

    private function validarNumIdUsuario(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getNumIdUsuario())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumIdUnidade(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getNumIdUnidade())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarDthCadastro(MdIaHistClassDTO $objMdIaHistClassDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaHistClassDTO->getDthCadastro())) {
            $objMdIaHistClassDTO->setDthCadastro(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaHistClassDTO->getDthCadastro())) {
                $objInfraException->adicionarValidacao(' inválid.');
            }
        }
    }

    protected function cadastrarControlado(MdIaHistClassDTO $objMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_cadastrar', __METHOD__, $objMdIaHistClassDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaClassificacaoOds($objMdIaHistClassDTO, $objInfraException);
            $this->validarNumIdMdIaAdmMetaOds($objMdIaHistClassDTO, $objInfraException);
            $this->validarStrOperacao($objMdIaHistClassDTO, $objInfraException);
            $this->validarNumIdUsuario($objMdIaHistClassDTO, $objInfraException);
            $this->validarDthCadastro($objMdIaHistClassDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
            $ret = $objMdIaHistClassBD->cadastrar($objMdIaHistClassDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Histórico de Classificação.', $e);
        }
    }

    protected function alterarControlado(MdIaHistClassDTO $objMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_alterar', __METHOD__, $objMdIaHistClassDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaHistClassDTO->isSetNumIdMdIaClassificacaoOds()) {
                $this->validarNumIdMdIaClassificacaoOds($objMdIaHistClassDTO, $objInfraException);
            }
            if ($objMdIaHistClassDTO->isSetNumIdMdIaAdmMetaOds()) {
                $this->validarNumIdMdIaAdmMetaOds($objMdIaHistClassDTO, $objInfraException);
            }
            if ($objMdIaHistClassDTO->isSetStrOperacao()) {
                $this->validarStrOperacao($objMdIaHistClassDTO, $objInfraException);
            }
            if ($objMdIaHistClassDTO->isSetNumIdUsuario()) {
                $this->validarNumIdUsuario($objMdIaHistClassDTO, $objInfraException);
            }
            if ($objMdIaHistClassDTO->isSetNumIdUnidade()) {
                $this->validarNumIdUnidade($objMdIaHistClassDTO, $objInfraException);
            }
            if ($objMdIaHistClassDTO->isSetDthCadastro()) {
                $this->validarDthCadastro($objMdIaHistClassDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
            $objMdIaHistClassBD->alterar($objMdIaHistClassDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Histórico de Classificação.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_excluir', __METHOD__, $arrObjMdIaHistClassDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaHistClassDTO); $i++) {
                $objMdIaHistClassBD->excluir($arrObjMdIaHistClassDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Histórico de Classificação.', $e);
        }
    }

    protected function consultarConectado(MdIaHistClassDTO $objMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_consultar', __METHOD__, $objMdIaHistClassDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());

            /** @var MdIaHistClassDTO $ret */
            $ret = $objMdIaHistClassBD->consultar($objMdIaHistClassDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Histórico de Classificação.', $e);
        }
    }

    protected function listarConectado(MdIaHistClassDTO $objMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_listar', __METHOD__, $objMdIaHistClassDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());

            /** @var MdIaHistClassDTO[] $ret */
            $ret = $objMdIaHistClassBD->listar($objMdIaHistClassDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Histórico de Classificações.', $e);
        }
    }

    protected function contarConectado(MdIaHistClassDTO $objMdIaHistClassDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_listar', __METHOD__, $objMdIaHistClassDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
            $ret = $objMdIaHistClassBD->contar($objMdIaHistClassDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Histórico de Classificações.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaHistClassDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_desativar', __METHOD__, $arrObjMdIaHistClassDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaHistClassDTO);$i++){
            $objMdIaHistClassBD->desativar($arrObjMdIaHistClassDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Histórico de Classificação.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaHistClassDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_reativar', __METHOD__, $arrObjMdIaHistClassDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaHistClassDTO);$i++){
            $objMdIaHistClassBD->reativar($arrObjMdIaHistClassDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Histórico de Classificação.',$e);
        }
      }

      protected function bloquearControlado(MdIaHistClassDTO $objMdIaHistClassDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_hist_class_consultar', __METHOD__, $objMdIaHistClassDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaHistClassBD = new MdIaHistClassBD($this->getObjInfraIBanco());
          $ret = $objMdIaHistClassBD->bloquear($objMdIaHistClassDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Histórico de Classificação.',$e);
        }
      }

     */
}
