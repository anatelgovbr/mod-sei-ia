<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmTpDocPesqRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmPesqDoc(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmTpDocPesqDTO->getNumIdMdIaAdmPesqDoc())) {
            $objInfraException->adicionarValidacao('Id Pesquisa Documento não informado.');
        }
    }

    private function validarNumIdSerie(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmTpDocPesqDTO->getNumIdSerie())) {
            $objInfraException->adicionarValidacao('Id Série não informado.');
        }
    }

    private function validarDthAlteracao(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmTpDocPesqDTO->getDthAlteracao())) {
            $objMdIaAdmTpDocPesqDTO->setDthAlteracao(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaAdmTpDocPesqDTO->getDthAlteracao())) {
                $objInfraException->adicionarValidacao('Data de Alteração inválida.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_cadastrar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmPesqDoc($objMdIaAdmTpDocPesqDTO, $objInfraException);
            $this->validarNumIdSerie($objMdIaAdmTpDocPesqDTO, $objInfraException);
            $this->validarDthAlteracao($objMdIaAdmTpDocPesqDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmTpDocPesqBD->cadastrar($objMdIaAdmTpDocPesqDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Tipo de Documento alvo da Pesquisa.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_alterar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmTpDocPesqDTO->isSetNumIdMdIaAdmPesqDoc()) {
                $this->validarNumIdMdIaAdmPesqDoc($objMdIaAdmTpDocPesqDTO, $objInfraException);
            }
            if ($objMdIaAdmTpDocPesqDTO->isSetNumIdSerie()) {
                $this->validarNumIdSerie($objMdIaAdmTpDocPesqDTO, $objInfraException);
            }
            if ($objMdIaAdmTpDocPesqDTO->isSetDthAlteracao()) {
                $this->validarDthAlteracao($objMdIaAdmTpDocPesqDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
            $objMdIaAdmTpDocPesqBD->alterar($objMdIaAdmTpDocPesqDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Tipo de Documento alvo da Pesquisa.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_excluir', __METHOD__, $arrObjMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmTpDocPesqDTO); $i++) {
                $objMdIaAdmTpDocPesqBD->excluir($arrObjMdIaAdmTpDocPesqDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Tipo de Documento alvo da Pesquisa.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_consultar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());

            /** @var MdIaAdmTpDocPesqDTO $ret */
            $ret = $objMdIaAdmTpDocPesqBD->consultar($objMdIaAdmTpDocPesqDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Tipo de Documento alvo da Pesquisa.', $e);
        }
    }

    protected function listarConectado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_listar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());

            /** @var MdIaAdmTpDocPesqDTO[] $ret */
            $ret = $objMdIaAdmTpDocPesqBD->listar($objMdIaAdmTpDocPesqDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Tipos de Documentos alvo da Pesquisa.', $e);
        }
    }

    protected function contarConectado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_listar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmTpDocPesqBD->contar($objMdIaAdmTpDocPesqDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Tipos de Documentos alvo da Pesquisa.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmTpDocPesqDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_desativar', __METHOD__, $arrObjMdIaAdmTpDocPesqDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmTpDocPesqDTO);$i++){
            $objMdIaAdmTpDocPesqBD->desativar($arrObjMdIaAdmTpDocPesqDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Tipo de Documento alvo da Pesquisa.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmTpDocPesqDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_reativar', __METHOD__, $arrObjMdIaAdmTpDocPesqDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmTpDocPesqDTO);$i++){
            $objMdIaAdmTpDocPesqBD->reativar($arrObjMdIaAdmTpDocPesqDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Tipo de Documento alvo da Pesquisa.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmTpDocPesqDTO $objMdIaAdmTpDocPesqDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_tp_doc_pesq_consultar', __METHOD__, $objMdIaAdmTpDocPesqDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmTpDocPesqBD = new MdIaAdmTpDocPesqBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmTpDocPesqBD->bloquear($objMdIaAdmTpDocPesqDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Tipo de Documento alvo da Pesquisa.',$e);
        }
      }

     */


    //todo isso exclui mesmo alguma coisa ??
    protected function excluirRelacionamentoControlado($idMdIaAdmTpDocPesq) {
        $objMdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();
        $objMdIaAdmTpDocPesqDTO->retNumIdMdIaAdmTpDocPesq();
        $arrItensExcluir = $this->listar($objMdIaAdmTpDocPesqDTO);
        $this->excluir($arrItensExcluir);
    }
    protected function cadastrarRelacionamentoConectado($arrtbTipoDocumento)
    {
        $idMdIaAdmTpDocPesq = $arrtbTipoDocumento[1];
        foreach ($arrtbTipoDocumento[0] as $tipoDoc) {
            if($tipoDoc[0] != "") {
                $objMdIaAdmTpDocPesqDTO = new MdIaAdmTpDocPesqDTO();
                $objMdIaAdmTpDocPesqDTO->setNumIdMdIaAdmPesqDoc($idMdIaAdmTpDocPesq);
                $objMdIaAdmTpDocPesqDTO->setNumIdSerie($tipoDoc[0]);
                $objMdIaAdmTpDocPesqDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                $objMdIaAdmTpDocPesqDTO->setStrSinAtivo($tipoDoc[1]);
                $this->cadastrar($objMdIaAdmTpDocPesqDTO);
            }
        }
    }
}
