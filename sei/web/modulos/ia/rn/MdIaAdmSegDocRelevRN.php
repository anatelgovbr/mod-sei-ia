<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmSegDocRelevRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmDocRelev(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmDocRelev())) {
            $objInfraException->adicionarValidacao('Id Documento Relevante não informado.');
        }
    }

    private function validarStrSegmentoDocumento(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento())) {
            $objInfraException->adicionarValidacao('Segmento do Documento não informado.');
        } else {
            $objMdIaAdmSegDocRelevDTO->setStrSegmentoDocumento(trim($objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento()));

            if (strlen($objMdIaAdmSegDocRelevDTO->getStrSegmentoDocumento()) > 100) {
                $objInfraException->adicionarValidacao('Segmento do Documento possui tamanho superior a 100 caracteres.');
            }
        }
    }

    private function validarNumPercentualRelevancia(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmSegDocRelevDTO->getNumPercentualRelevancia())) {
            $objInfraException->adicionarValidacao('Percentual de Relevância não informado.');
        }
    }

    protected function cadastrarControlado(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_cadastrar', __METHOD__, $objMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmDocRelev($objMdIaAdmSegDocRelevDTO, $objInfraException);
            $this->validarStrSegmentoDocumento($objMdIaAdmSegDocRelevDTO, $objInfraException);
            $this->validarNumPercentualRelevancia($objMdIaAdmSegDocRelevDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmSegDocRelevBD->cadastrar($objMdIaAdmSegDocRelevDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Relevância do Segmento do Documento.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_alterar', __METHOD__, $objMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmSegDocRelevDTO->isSetNumIdMdIaAdmDocRelev()) {
                $this->validarNumIdMdIaAdmDocRelev($objMdIaAdmSegDocRelevDTO, $objInfraException);
            }
            if ($objMdIaAdmSegDocRelevDTO->isSetStrSegmentoDocumento()) {
                $this->validarStrSegmentoDocumento($objMdIaAdmSegDocRelevDTO, $objInfraException);
            }
            if ($objMdIaAdmSegDocRelevDTO->isSetNumPercentualRelevancia()) {
                $this->validarNumPercentualRelevancia($objMdIaAdmSegDocRelevDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());
            $objMdIaAdmSegDocRelevBD->alterar($objMdIaAdmSegDocRelevDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Relevância do Segmento do Documento.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_excluir', __METHOD__, $arrObjMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmSegDocRelevDTO); $i++) {
                $objMdIaAdmSegDocRelevBD->excluir($arrObjMdIaAdmSegDocRelevDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Relevância do Segmento do Documento.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_consultar', __METHOD__, $objMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());

            /** @var MdIaAdmSegDocRelevDTO $ret */
            $ret = $objMdIaAdmSegDocRelevBD->consultar($objMdIaAdmSegDocRelevDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Relevância do Segmento do Documento.', $e);
        }
    }

    protected function listarConectado(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_listar', __METHOD__, $objMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());

            /** @var MdIaAdmSegDocRelevDTO[] $ret */
            $ret = $objMdIaAdmSegDocRelevBD->listar($objMdIaAdmSegDocRelevDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Relevância do Segmentos dos Documentos.', $e);
        }
    }

    protected function contarConectado(MdIaAdmSegDocRelevDTO $objMdIaAdmSegDocRelevDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_seg_doc_relev_listar', __METHOD__, $objMdIaAdmSegDocRelevDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmSegDocRelevBD = new MdIaAdmSegDocRelevBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmSegDocRelevBD->contar($objMdIaAdmSegDocRelevDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Relevância do Segmentos dos Documentos.', $e);
        }
    }

    protected function cadastrarRelacionamentoConectado($arrSegmentoDocumento)
    {
        $idMdIaAdmDocRelev = $arrSegmentoDocumento[1];
        foreach ($arrSegmentoDocumento[0] as $segDocRelev) {
            if ($segDocRelev[0] != "") {
                $objMdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
                $objMdIaAdmSegDocRelevDTO->setNumIdMdIaAdmDocRelev($idMdIaAdmDocRelev);
                $objMdIaAdmSegDocRelevDTO->setStrSegmentoDocumento($segDocRelev[1]);
                $objMdIaAdmSegDocRelevDTO->setNumPercentualRelevancia($segDocRelev[2]);
                $this->cadastrar($objMdIaAdmSegDocRelevDTO);
            }
        }
    }
    protected function excluirRelacionamentoConectado($idMdIaAdmDocRelev)
    {
        $objMdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
        $objMdIaAdmSegDocRelevDTO->retNumIdMdIaAdmSegDocRelev();
        $objMdIaAdmSegDocRelevDTO->setNumIdMdIaAdmDocRelev($idMdIaAdmDocRelev);
        $this->listar($objMdIaAdmSegDocRelevDTO);

        $this->excluir($this->listar($objMdIaAdmSegDocRelevDTO));
    }
}
