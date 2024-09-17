<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmConfigSimilarRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmConfigSimilar(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigSimilarDTO->getNumIdMdIaAdmConfigSimilar())) {
            $objInfraException->adicionarValidacao('IdMdIaAdmConfigSimilar não informado.');
        }
    }

    private function validarNumQtdProcessListagem(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigSimilarDTO->getNumQtdProcessListagem())) {
            $objInfraException->adicionarValidacao('Quantidade de Processos para Listagem não informada.');
        }
    }

    private function validarStrOrientacoesGerais(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigSimilarDTO->getStrOrientacoesGerais())) {
            $objInfraException->adicionarValidacao('Orientações Gerais não informada.');
        } else {
            $objMdIaAdmConfigSimilarDTO->setStrOrientacoesGerais(trim($objMdIaAdmConfigSimilarDTO->getStrOrientacoesGerais()));
        }
    }

    private function validarNumPercRelevContDoc(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigSimilarDTO->getNumPercRelevContDoc())) {
            $objInfraException->adicionarValidacao('Percentual de Relevância do Conteúdo dos Documentos não informado.');
        }
    }

    private function validarNumPercRelevMetadados(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigSimilarDTO->getNumPercRelevMetadados())) {
            $objInfraException->adicionarValidacao('Percentual de Relevância dos Metadados não informado.');
        }
    }

    protected function cadastrarControlado(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_cadastrar', __METHOD__, $objMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmConfigSimilar($objMdIaAdmConfigSimilarDTO, $objInfraException);
            $this->validarNumQtdProcessListagem($objMdIaAdmConfigSimilarDTO, $objInfraException);
            $this->validarStrOrientacoesGerais($objMdIaAdmConfigSimilarDTO, $objInfraException);
            $this->validarNumPercRelevContDoc($objMdIaAdmConfigSimilarDTO, $objInfraException);
            $this->validarNumPercRelevMetadados($objMdIaAdmConfigSimilarDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmConfigSimilarBD->cadastrar($objMdIaAdmConfigSimilarDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_alterar', __METHOD__, $objMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmConfigSimilarDTO->isSetNumQtdProcessListagem()) {
                $this->validarNumQtdProcessListagem($objMdIaAdmConfigSimilarDTO, $objInfraException);
            }
            if ($objMdIaAdmConfigSimilarDTO->isSetStrOrientacoesGerais()) {
                $this->validarStrOrientacoesGerais($objMdIaAdmConfigSimilarDTO, $objInfraException);
            }
            if ($objMdIaAdmConfigSimilarDTO->isSetNumPercRelevContDoc()) {
                $this->validarNumPercRelevContDoc($objMdIaAdmConfigSimilarDTO, $objInfraException);
            }
            if ($objMdIaAdmConfigSimilarDTO->isSetNumPercRelevMetadados()) {
                $this->validarNumPercRelevMetadados($objMdIaAdmConfigSimilarDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());
            $objMdIaAdmConfigSimilarBD->alterar($objMdIaAdmConfigSimilarDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_excluir', __METHOD__, $arrObjMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmConfigSimilarDTO); $i++) {
                $objMdIaAdmConfigSimilarBD->excluir($arrObjMdIaAdmConfigSimilarDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo .', $e);
        }
    }

    protected function consultarConectado(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_consultar', __METHOD__, $objMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());

            /** @var MdIaAdmConfigSimilarDTO $ret */
            $ret = $objMdIaAdmConfigSimilarBD->consultar($objMdIaAdmConfigSimilarDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_listar', __METHOD__, $objMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());

            /** @var MdIaAdmConfigSimilarDTO[] $ret */
            $ret = $objMdIaAdmConfigSimilarBD->listar($objMdIaAdmConfigSimilarDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdIaAdmConfigSimilarDTO $objMdIaAdmConfigSimilarDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_similar_listar', __METHOD__, $objMdIaAdmConfigSimilarDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmConfigSimilarBD->contar($objMdIaAdmConfigSimilarDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }
}
