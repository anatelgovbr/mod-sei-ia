<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 14/09/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.43.1
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmIntegracaoRN extends InfraRN
{

    public static $TP_INTEGRACAO_SEM_AUTENTICACAO = 'SI';
    public static $TP_INTEGRACAO_REST = 'RE';
    public static $TP_INTEGRACAO_SOAP = 'SO';

    public static $AUT_VAZIA = '1';
    public static $STR_AUT_VAZIA = 'Sem Autentica��o';
    public static $AUT_HEADER_TOKEN = '2';
    public static $STR_AUT_HEADER_TOKEN = 'Header Authentication by Token';
    public static $AUT_BODY_TOKEN = '3';
    public static $STR_AUT_BODY_TOKEN = 'Body Authentication by Token';

    public static $FORMATO_JSON = '1';
    public static $STR_FORMATO_JSON = 'JSON';
    public static $FORMATO_XML = '2';
    public static $STR_FORMATO_XML = 'XML';

    public static $REQUISICAO_POST = '1';
    public static $STR_REQUISICAO_POST = 'POST';
    public static $REQUISICAO_GET = '2';
    public static $STR_REQUISICAO_GET = 'GET';

    public static $INFO_RESTRITO = '*****';

    public static $SEI_IA_TP_INTEGRACAO = 'RE';
    public static $SEI_IA_AUT = '1';
    public static $SEI_IA_FORMATO = '1';
    public static $SEI_IA_REQUISICAO = '1';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarStrNome(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmIntegracaoDTO->getStrNome())) {
            $objInfraException->adicionarValidacao('Nome n�o informado.');
        } else {
            $objMdIaAdmIntegracaoDTO->setStrNome(trim($objMdIaAdmIntegracaoDTO->getStrNome()));

            if (strlen($objMdIaAdmIntegracaoDTO->getStrNome()) > 100) {
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a 100 caracteres.');
            }
        }
    }

    private function validarNumIdMdIaAdmIntegFuncion(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion())) {
            $objInfraException->adicionarValidacao('IdMdIaAdmIntegFuncion n�o informada.');
        }
    }

    private function validarStrTipoIntegracao(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao())) {
            $objMdIaAdmIntegracaoDTO->setStrTipoIntegracao(null);
        } else {
            $objMdIaAdmIntegracaoDTO->setStrTipoIntegracao(trim($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao()));

            if (strlen($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao()) > 2) {
                $objInfraException->adicionarValidacao('Possui tamanho superior a 2 caracteres.');
            }
        }
    }


    private function validarStrOperacaoWsdl(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl())) {
            $objInfraException->adicionarValidacao('Opera��o n�o informada.');
        } else {
            $objMdIaAdmIntegracaoDTO->setStrOperacaoWsdl(trim($objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl()));

            if (strlen($objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl()) > 250) {
                $objInfraException->adicionarValidacao('Opera��o possui tamanho superior a 250 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_cadastrar', __METHOD__, $objMdIaAdmIntegracaoDTO);
            return $this->processaCadastrarAlterar(array($objMdIaAdmIntegracaoDTO, "cadastrar"));

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Integra��o.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_alterar', __METHOD__, $objMdIaAdmIntegracaoDTO);
            return $this->processaCadastrarAlterar(array($objMdIaAdmIntegracaoDTO, "alterar"));

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Integra��o.', $e);
        }
    }

    private function processaCadastrarAlterar($objMdIaAdmIntegracaoDTO)
    {
        $operacao = $objMdIaAdmIntegracaoDTO[1];
        $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoDTO[0];
        //Regras de Negocio
        $objInfraException = new InfraException();

        $this->validarStrTipoIntegracao($objMdIaAdmIntegracaoDTO, $objInfraException);
        $objInfraException->lancarValidacoes();

        if ($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() != 'SI') {
            $this->validarStrNome($objMdIaAdmIntegracaoDTO, $objInfraException);
            $this->validarNumIdMdIaAdmIntegFuncion($objMdIaAdmIntegracaoDTO, $objInfraException);
            $this->validarStrOperacaoWsdl($objMdIaAdmIntegracaoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();
        }

        $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());

        if ($operacao == "cadastrar") {
            $retIntegracao = $objMdIaAdmIntegracaoBD->cadastrar($objMdIaAdmIntegracaoDTO);
        } else {
            $objMdIaAdmIntegracaoBD->alterar($objMdIaAdmIntegracaoDTO);
            $retIntegracao = $objMdIaAdmIntegracaoDTO;
        }

        return $retIntegracao;
    }

    protected function excluirControlado($arrObjMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_excluir', __METHOD__, $arrObjMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmIntegracaoDTO); $i++) {
                $objMdIaAdmIntegracaoBD->excluir($arrObjMdIaAdmIntegracaoDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Integra��o.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_consultar', __METHOD__, $objMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegracaoBD->consultar($objMdIaAdmIntegracaoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Integra��o.', $e);
        }
    }

    protected function listarConectado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {

            #SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_listar', __METHOD__, $objMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegracaoBD->listar($objMdIaAdmIntegracaoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Integra��es.', $e);
        }
    }

    protected function contarConectado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_listar', __METHOD__, $objMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegracaoBD->contar($objMdIaAdmIntegracaoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Integra��es.', $e);
        }
    }

    protected function desativarControlado($arrObjMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_desativar', __METHOD__, $arrObjMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmIntegracaoDTO); $i++) {
                $objMdIaAdmIntegracaoBD->desativar($arrObjMdIaAdmIntegracaoDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro desativando Integra��o.', $e);
        }
    }

    protected function reativarControlado($arrObjMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_reativar', __METHOD__, $arrObjMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmIntegracaoDTO); $i++) {
                $objMdIaAdmIntegracaoBD->reativar($arrObjMdIaAdmIntegracaoDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro reativando Integra��o.', $e);
        }
    }

    protected function bloquearControlado(MdIaAdmIntegracaoDTO $objMdIaAdmIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_consultar', __METHOD__, $objMdIaAdmIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmIntegracaoBD = new MdIaAdmIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmIntegracaoBD->bloquear($objMdIaAdmIntegracaoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro bloqueando Integra��o.', $e);
        }
    }
}
