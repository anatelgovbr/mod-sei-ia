<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmUrlIntegracaoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdAdmIaAdmIntegracao(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUrlIntegracaoDTO->getNumIdAdmIaAdmIntegracao())) {
            $objInfraException->adicionarValidacao('Id da Integração não informado.');
        }
    }

    private function validarStrReferencia(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUrlIntegracaoDTO->getStrReferencia())) {
            $objInfraException->adicionarValidacao('Referência não informado.');
        }
    }

    private function validarStrLabel(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUrlIntegracaoDTO->getStrLabel())) {
            $objInfraException->adicionarValidacao('Label não informado.');
        }
    }

    private function validarStrUrl(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmUrlIntegracaoDTO->getStrUrl())) {
            $objInfraException->adicionarValidacao('Url não informada.');
        }
    }

    protected function alterarControlado(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_alterar', __METHOD__, $objMdIaAdmUrlIntegracaoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdAdmIaAdmIntegracao($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrReferencia($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrLabel($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrUrl($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $objInfraException->lancarValidacoes();

            $objMdIaAdmUrlIntegracaoBD = new MdIaAdmUrlIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmUrlIntegracaoBD->alterar($objMdIaAdmUrlIntegracaoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Url da Integração.', $e);
        }
    }

    protected function cadastrarControlado(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_cadastrar', __METHOD__, $objMdIaAdmUrlIntegracaoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdAdmIaAdmIntegracao($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrReferencia($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrLabel($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $this->validarStrUrl($objMdIaAdmUrlIntegracaoDTO, $objInfraException);
            $objInfraException->lancarValidacoes();

            $objMdIaAdmUrlIntegracaoBD = new MdIaAdmUrlIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmUrlIntegracaoBD->cadastrar($objMdIaAdmUrlIntegracaoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Url da Integração.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO)
    {
        try {
            $objMdIaAdmUrlIntegracaoBD = new MdIaAdmUrlIntegracaoBD($this->getObjInfraIBanco());

            return $objMdIaAdmUrlIntegracaoBD->consultar($objMdIaAdmUrlIntegracaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Url da integração.', $e);
        }
    }

    protected function listarConectado(MdIaAdmUrlIntegracaoDTO $objMdIaAdmUrlIntegracaoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_integracao_listar', __METHOD__, $objMdIaAdmUrlIntegracaoDTO);
            $objMdIaAdmUrlIntegracaoBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
            return $objMdIaAdmUrlIntegracaoBD->listar($objMdIaAdmUrlIntegracaoDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro listando Urls da integração.', $e);
        }
    }

}
