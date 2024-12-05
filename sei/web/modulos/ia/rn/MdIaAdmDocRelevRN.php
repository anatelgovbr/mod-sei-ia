<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmDocRelevRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdSerie(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmDocRelevDTO->getNumIdSerie())) {
            $objInfraException->adicionarValidacao('Id Série não informado.');
        }
    }

    protected function cadastrarControlado(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_cadastrar', __METHOD__, $objMdIaAdmDocRelevDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdSerie($objMdIaAdmDocRelevDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmDocRelevBD->cadastrar($objMdIaAdmDocRelevDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Documento Relevante.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_alterar', __METHOD__, $objMdIaAdmDocRelevDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmDocRelevDTO->isSetNumIdSerie()) {
                $this->validarNumIdSerie($objMdIaAdmDocRelevDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            $objMdIaAdmDocRelevBD->alterar($objMdIaAdmDocRelevDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Documento Relevante.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_excluir', __METHOD__, $arrObjMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmDocRelevDTO); $i++) {
                $objMdIaAdmDocRelevBD->excluir($arrObjMdIaAdmDocRelevDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Documento Relevante.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_consultar', __METHOD__, $objMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());

            /** @var MdIaAdmDocRelevDTO $ret */
            $ret = $objMdIaAdmDocRelevBD->consultar($objMdIaAdmDocRelevDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Documento Relevante.', $e);
        }
    }

    protected function listarConectado(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_listar', __METHOD__, $objMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());

            /** @var MdIaAdmDocRelevDTO[] $ret */
            $ret = $objMdIaAdmDocRelevBD->listar($objMdIaAdmDocRelevDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Documentos Relevantes.', $e);
        }
    }

    protected function contarConectado(MdIaAdmDocRelevDTO $objMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_listar', __METHOD__, $objMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmDocRelevBD->contar($objMdIaAdmDocRelevDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Documentos Relevantes.', $e);
        }
    }

    protected function montarArrTpProcessoControlado($idMdIaAdmDocRelev)
    {

        $mdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
        $mdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($idMdIaAdmDocRelev);
        $mdIaAdmDocRelevDTO->retNumIdTipoProcedimento();
        $mdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();

        $tipoProcedimento = $this->listar($mdIaAdmDocRelevDTO);
        $arrTpProcedimento = array();
        for ($i = 0; $i < count($tipoProcedimento); $i++) {

            $tpProc = array();
            $tpProc[] = $tipoProcedimento[$i]->getNumIdTipoProcedimento();
            $tpProc[] = $tipoProcedimento[$i]->getStrNomeTipoProcedimento();

            $arrTpProcedimento[] = $tpProc;
        }

        $arrItenLupaTpProcesso = PaginaSEI::getInstance()->gerarItensLupa($arrTpProcedimento);
        return $arrItenLupaTpProcesso;
    }

    protected function desativarControlado($arrObjMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_desativar', __METHOD__, $arrObjMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmDocRelevDTO); $i++) {
                $objMdIaAdmDocRelevBD->desativar($arrObjMdIaAdmDocRelevDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro desativando Documento Relevante.', $e);
        }
    }

    protected function reativarControlado($arrObjMdIaAdmDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_doc_relev_reativar', __METHOD__, $arrObjMdIaAdmDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmDocRelevBD = new MdIaAdmDocRelevBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmDocRelevDTO); $i++) {
                $objMdIaAdmDocRelevBD->reativar($arrObjMdIaAdmDocRelevDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro reativando Documento Relevante.', $e);
        }
    }
}
