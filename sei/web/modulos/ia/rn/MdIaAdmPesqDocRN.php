<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmPesqDocRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumQtdProcessListagem(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPesqDocDTO->getNumQtdProcessListagem())) {
            $objInfraException->adicionarValidacao('Resultados a serem apresentados não informada.');
        }
    }

    private function validarStrOrientacoesGerais(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPesqDocDTO->getStrOrientacoesGerais())) {
            $objInfraException->adicionarValidacao('Orientações Gerais não informada.');
        } else {
            $objMdIaAdmPesqDocDTO->setStrOrientacoesGerais(trim($objMdIaAdmPesqDocDTO->getStrOrientacoesGerais()));
        }
    }

    private function validarStrNomeSecao(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmPesqDocDTO->getStrNomeSecao())) {
            $objInfraException->adicionarValidacao('Nome da Seção na Tela do Usuário não informado.');
        } else {
            $objMdIaAdmPesqDocDTO->setStrNomeSecao(trim($objMdIaAdmPesqDocDTO->getStrNomeSecao()));

            if (strlen($objMdIaAdmPesqDocDTO->getStrNomeSecao()) > 100) {
                $objInfraException->adicionarValidacao('Nome da Seção na Tela do Usuário possui tamanho superior a 100 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_cadastrar', __METHOD__, $objMdIaAdmPesqDocDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumQtdProcessListagem($objMdIaAdmPesqDocDTO, $objInfraException);
            $this->validarStrOrientacoesGerais($objMdIaAdmPesqDocDTO, $objInfraException);
            $this->validarStrNomeSecao($objMdIaAdmPesqDocDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmPesqDocBD->cadastrar($objMdIaAdmPesqDocDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Pesquisa de Documentos.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_alterar', __METHOD__, $objMdIaAdmPesqDocDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmPesqDocDTO->isSetNumQtdProcessListagem()) {
                $this->validarNumQtdProcessListagem($objMdIaAdmPesqDocDTO, $objInfraException);
            }
            if ($objMdIaAdmPesqDocDTO->isSetStrOrientacoesGerais()) {
                $this->validarStrOrientacoesGerais($objMdIaAdmPesqDocDTO, $objInfraException);
            }
            if ($objMdIaAdmPesqDocDTO->isSetStrNomeSecao()) {
                $this->validarStrNomeSecao($objMdIaAdmPesqDocDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
            $objMdIaAdmPesqDocBD->alterar($objMdIaAdmPesqDocDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Pesquisa de Documentos.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_excluir', __METHOD__, $arrObjMdIaAdmPesqDocDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmPesqDocDTO); $i++) {
                $objMdIaAdmPesqDocBD->excluir($arrObjMdIaAdmPesqDocDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Pesquisa de Documentos.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_consultar', __METHOD__, $objMdIaAdmPesqDocDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());

            /** @var MdIaAdmPesqDocDTO $ret */
            $ret = $objMdIaAdmPesqDocBD->consultar($objMdIaAdmPesqDocDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Pesquisa de Documentos.', $e);
        }
    }

    protected function listarConectado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_listar', __METHOD__, $objMdIaAdmPesqDocDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());

            /** @var MdIaAdmPesqDocDTO[] $ret */
            $ret = $objMdIaAdmPesqDocBD->listar($objMdIaAdmPesqDocDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Pesquisa de Documentos.', $e);
        }
    }

    protected function contarConectado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_listar', __METHOD__, $objMdIaAdmPesqDocDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmPesqDocBD->contar($objMdIaAdmPesqDocDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Pesquisa de Documentos.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmPesqDocDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_desativar', __METHOD__, $arrObjMdIaAdmPesqDocDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmPesqDocDTO);$i++){
            $objMdIaAdmPesqDocBD->desativar($arrObjMdIaAdmPesqDocDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Pesquisa de Documentos.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmPesqDocDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_reativar', __METHOD__, $arrObjMdIaAdmPesqDocDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmPesqDocDTO);$i++){
            $objMdIaAdmPesqDocBD->reativar($arrObjMdIaAdmPesqDocDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Pesquisa de Documentos.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmPesqDocDTO $objMdIaAdmPesqDocDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_pesq_doc_consultar', __METHOD__, $objMdIaAdmPesqDocDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmPesqDocBD = new MdIaAdmPesqDocBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmPesqDocBD->bloquear($objMdIaAdmPesqDocDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Pesquisa de Documentos.',$e);
        }
      }

     */
}
