<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 29/12/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaClassificacaoOdsRN extends InfraRN
{
    public static $MODULO_IA_ID_USUARIO_SISTEMA = 'MODULO_IA_ID_USUARIO_SISTEMA';
    public static $MSG_SUCESSO_RETORNO_WS       = 'Classifica��o realizada com sucesso';
    public static $MSG_ERROR_JA_CADASTRADA      = 'Meta j� cadastrada';
    public static $MSG_ERROR_JA_SUGERIDA_IA     = 'Meta j� sugerida pela IA';
	public static $MSG_ERROR_JA_SUGERIDA_UE     = 'Meta j� sugerida por Usu�rio Externo';
    public static $MSG_SUCESSO_RETORNO          = 'SUCCESS';
    public static $MSG_ERROR_RETORNO            = 'ERROR';

    public static $USUARIO_PADRAO         = "U";
    public static $USUARIO_EXTERNO        = "E";
    public static $USUARIO_IA             = "I";

    public static $SIM = 'S';
    public static $STR_SIM = 'Sim';

    public static $NAO = 'N';
    public static $STR_NAO = 'N�o';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdProcedimento(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassificacaoOdsDTO->getNumIdProcedimento())) {
            $objInfraException->adicionarValidacao('Id Procedimento n�o informado.');
        }
    }

    private function validarStrStaTipoUltimoUsuario(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario())) {
            $objInfraException->adicionarValidacao(' tipo usu�rio n�o informado.');
        } else {
            $objMdIaClassificacaoOdsDTO->setStrStaTipoUltimoUsuario(trim($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario()));

            if (strlen($objMdIaClassificacaoOdsDTO->getStrStaTipoUltimoUsuario()) > 1) {
                $objInfraException->adicionarValidacao('  tipo usu�rio possui tamanho superior a 1 caractere.');
            }
        }
    }

    private function validarDthAlteracao(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaClassificacaoOdsDTO->getDthAlteracao())) {
            $objMdIaClassificacaoOdsDTO->setDthAlteracao(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaClassificacaoOdsDTO->getDthAlteracao())) {
                $objInfraException->adicionarValidacao('Data Altera��o inv�lida.');
            }
        }
    }

    protected function cadastrarControlado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_cadastrar', __METHOD__, $objMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdProcedimento($objMdIaClassificacaoOdsDTO, $objInfraException);
            $this->validarStrStaTipoUltimoUsuario($objMdIaClassificacaoOdsDTO, $objInfraException);
            $this->validarDthAlteracao($objMdIaClassificacaoOdsDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaClassificacaoOdsBD->cadastrar($objMdIaClassificacaoOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Avalia��o.', $e);
        }
    }

    protected function alterarControlado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_alterar', __METHOD__, $objMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaClassificacaoOdsDTO->isSetNumIdProcedimento()) {
                $this->validarNumIdProcedimento($objMdIaClassificacaoOdsDTO, $objInfraException);
            }
            if ($objMdIaClassificacaoOdsDTO->isSetStrStaTipoUltimoUsuario()) {
                $this->validarStrStaTipoUltimoUsuario($objMdIaClassificacaoOdsDTO, $objInfraException);
            }
            if ($objMdIaClassificacaoOdsDTO->isSetDthAlteracao()) {
                $this->validarDthAlteracao($objMdIaClassificacaoOdsDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
            $ret =  $objMdIaClassificacaoOdsBD->alterar($objMdIaClassificacaoOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Avalia��o.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_excluir', __METHOD__, $arrObjMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaClassificacaoOdsDTO); $i++) {
                $objMdIaClassificacaoOdsBD->excluir($arrObjMdIaClassificacaoOdsDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Avalia��o.', $e);
        }
    }

    protected function consultarConectado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_consultar', __METHOD__, $objMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());

            /** @var MdIaClassificacaoOdsDTO $ret */
            $ret = $objMdIaClassificacaoOdsBD->consultar($objMdIaClassificacaoOdsDTO);

            return $ret;
        } catch (Exception $e) {

            throw new InfraException('Erro consultando Avalia��o.', $e);
        }
    }

    protected function listarConectado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_listar', __METHOD__, $objMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());

            /** @var MdIaClassificacaoOdsDTO[] $ret */
            $ret = $objMdIaClassificacaoOdsBD->listar($objMdIaClassificacaoOdsDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Avalia��es.', $e);
        }
    }

    protected function contarConectado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_listar', __METHOD__, $objMdIaClassificacaoOdsDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
            $ret = $objMdIaClassificacaoOdsBD->contar($objMdIaClassificacaoOdsDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Avalia��es.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaClassificacaoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_desativar', __METHOD__, $arrObjMdIaClassificacaoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaClassificacaoOdsDTO);$i++){
            $objMdIaClassificacaoOdsBD->desativar($arrObjMdIaClassificacaoOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Avalia��o.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaClassificacaoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_reativar', __METHOD__, $arrObjMdIaClassificacaoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaClassificacaoOdsDTO);$i++){
            $objMdIaClassificacaoOdsBD->reativar($arrObjMdIaClassificacaoOdsDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Avalia��o.',$e);
        }
      }

      protected function bloquearControlado(MdIaClassificacaoOdsDTO $objMdIaClassificacaoOdsDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_classificacao_ods_consultar', __METHOD__, $objMdIaClassificacaoOdsDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaClassificacaoOdsBD = new MdIaClassificacaoOdsBD($this->getObjInfraIBanco());
          $ret = $objMdIaClassificacaoOdsBD->bloquear($objMdIaClassificacaoOdsDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Avalia��o.',$e);
        }
      }

     */
}
