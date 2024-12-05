<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 06/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmMetadadoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmMetadado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetadadoDTO->getNumIdMdIaAdmMetadado())) {
            $objInfraException->adicionarValidacao('ID Metadado não informado.');
        }
    }

    private function validarStrMetadado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmMetadadoDTO->getStrMetadado())) {
            $objMdIaAdmMetadadoDTO->setStrMetadado(null);
        } else {
            $objMdIaAdmMetadadoDTO->setStrMetadado(trim($objMdIaAdmMetadadoDTO->getStrMetadado()));

            if (strlen($objMdIaAdmMetadadoDTO->getStrMetadado()) > 100) {
                $objInfraException->adicionarValidacao('Metadado possui tamanho superior a 100 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_cadastrar', __METHOD__, $objMdIaAdmMetadadoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmMetadado($objMdIaAdmMetadadoDTO, $objInfraException);
            $this->validarStrMetadado($objMdIaAdmMetadadoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmMetadadoBD->cadastrar($objMdIaAdmMetadadoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Metadado.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_alterar', __METHOD__, $objMdIaAdmMetadadoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmMetadadoDTO->isSetNumIdMdIaAdmMetadado()) {
                $this->validarNumIdMdIaAdmMetadado($objMdIaAdmMetadadoDTO, $objInfraException);
            }
            if ($objMdIaAdmMetadadoDTO->isSetStrMetadado()) {
                $this->validarStrMetadado($objMdIaAdmMetadadoDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());
            $objMdIaAdmMetadadoBD->alterar($objMdIaAdmMetadadoDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Metadado.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_excluir', __METHOD__, $arrObjMdIaAdmMetadadoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmMetadadoDTO); $i++) {
                $objMdIaAdmMetadadoBD->excluir($arrObjMdIaAdmMetadadoDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Metadado.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_consultar', __METHOD__, $objMdIaAdmMetadadoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());

            /** @var MdIaAdmMetadadoDTO $ret */
            $ret = $objMdIaAdmMetadadoBD->consultar($objMdIaAdmMetadadoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Metadado.', $e);
        }
    }

    protected function listarConectado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_listar', __METHOD__, $objMdIaAdmMetadadoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());

            /** @var MdIaAdmMetadadoDTO[] $ret */
            $ret = $objMdIaAdmMetadadoBD->listar($objMdIaAdmMetadadoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Metadados.', $e);
        }
    }

    protected function contarConectado(MdIaAdmMetadadoDTO $objMdIaAdmMetadadoDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_metadado_listar', __METHOD__, $objMdIaAdmMetadadoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmMetadadoBD = new MdIaAdmMetadadoBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmMetadadoBD->contar($objMdIaAdmMetadadoDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Metadados.', $e);
        }
    }
}
