<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmPercRelevMetRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmConfigSimilar(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmConfigSimilar())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumIdMdIaAdmMetadado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPercRelevMetDTO->getNumIdMdIaAdmMetadado())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumPercentualRelevancia(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia())) {
            $objInfraException->adicionarValidacao('Percentual de Relevância não informado.');
        }
    }

    protected function cadastrarControlado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_cadastrar', __METHOD__, $objMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmConfigSimilar($objMdIaAdmPercRelevMetDTO, $objInfraException);
            $this->validarNumIdMdIaAdmMetadado($objMdIaAdmPercRelevMetDTO, $objInfraException);
            $this->validarNumPercentualRelevancia($objMdIaAdmPercRelevMetDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmPercRelevMetBD->cadastrar($objMdIaAdmPercRelevMetDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_alterar', __METHOD__, $objMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();
            if ($objMdIaAdmPercRelevMetDTO->isSetNumIdMdIaAdmConfigSimilar()) {
                $this->validarNumIdMdIaAdmConfigSimilar($objMdIaAdmPercRelevMetDTO, $objInfraException);
            }
            if ($objMdIaAdmPercRelevMetDTO->isSetNumIdMdIaAdmMetadado()) {
                $this->validarNumIdMdIaAdmMetadado($objMdIaAdmPercRelevMetDTO, $objInfraException);
            }
            if ($objMdIaAdmPercRelevMetDTO->isSetNumPercentualRelevancia()) {
                $this->validarNumPercentualRelevancia($objMdIaAdmPercRelevMetDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());
            $objMdIaAdmPercRelevMetBD->alterar($objMdIaAdmPercRelevMetDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_excluir', __METHOD__, $arrObjMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmPercRelevMetDTO); $i++) {
                $objMdIaAdmPercRelevMetBD->excluir($arrObjMdIaAdmPercRelevMetDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo .', $e);
        }
    }

    protected function consultarConectado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_consultar', __METHOD__, $objMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());

            /** @var MdIaAdmPercRelevMetDTO $ret */
            $ret = $objMdIaAdmPercRelevMetBD->consultar($objMdIaAdmPercRelevMetDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_listar', __METHOD__, $objMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());

            /** @var MdIaAdmPercRelevMetDTO[] $ret */
            $ret = $objMdIaAdmPercRelevMetBD->listar($objMdIaAdmPercRelevMetDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdIaAdmPercRelevMetDTO $objMdIaAdmPercRelevMetDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_perc_relev_met_listar', __METHOD__, $objMdIaAdmPercRelevMetDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPercRelevMetBD = new MdIaAdmPercRelevMetBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmPercRelevMetBD->contar($objMdIaAdmPercRelevMetDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }
    protected function alterarPercentuaisMetadadosControlado($arrTbPercRelevMetadado) {
        $alterado = 0;
        $idMdIaAdmConfigSimilar = $arrTbPercRelevMetadado[1];
        foreach ($arrTbPercRelevMetadado[0] as $percRelevMetadado) {
            $objMdIaAdmPercRelevMetDTO = new MdIaAdmPercRelevMetDTO();
            $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmPercRelevMet();
            $objMdIaAdmPercRelevMetDTO->retNumPercentualRelevancia();
            $objMdIaAdmPercRelevMetDTO->setNumIdMdIaAdmMetadado($percRelevMetadado[0]);
            $objMdIaAdmPercRelevMetDTO->setNumIdMdIaAdmConfigSimilar($idMdIaAdmConfigSimilar);
            $objMdIaAdmPercRelevMetDTO = $this->consultar($objMdIaAdmPercRelevMetDTO);

            if($objMdIaAdmPercRelevMetDTO->getNumPercentualRelevancia() != $percRelevMetadado[2]) {
                $objMdIaAdmPercRelevMetDTO->setNumPercentualRelevancia($percRelevMetadado[2]);
                $this->alterar($objMdIaAdmPercRelevMetDTO);
                $alterado = 1;
            }
            if($alterado == 1) {
                $objMdIaAdmPercRelevMetDTO = new MdIaAdmPercRelevMetDTO();
                $objMdIaAdmPercRelevMetDTO->retNumIdMdIaAdmPercRelevMet();
                $objMdIaAdmPercRelevMetDTO->setNumIdMdIaAdmConfigSimilar($idMdIaAdmConfigSimilar);
                $arrobjMdIaAdmPercRelevMetDTO = $this->listar($objMdIaAdmPercRelevMetDTO);
                foreach($arrobjMdIaAdmPercRelevMetDTO as $objMdIaAdmPercRelevMetDTO) {
                    $objMdIaAdmPercRelevMetDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                    $this->alterar($objMdIaAdmPercRelevMetDTO);
                }
            }
         }
    }
}
