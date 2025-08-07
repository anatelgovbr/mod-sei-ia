<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 03/06/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaDocIndexCancRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdIaDocIndexCancDTO $objMdIaDocIndexCancDTO)
    {
        try {
            $objMdIaDocIndexCancBD = new MdIaDocIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexCancBD->cadastrar($objMdIaDocIndexCancDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro no cadastro.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaDocIndexCancDTO)
    {
        try {
            $objMdIaDocIndexCancBD = new MdIaDocIndexCancBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaDocIndexCancDTO); $i++) {
                $objMdIaDocIndexCancBD->excluir($arrObjMdIaDocIndexCancDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro de exclusão de registro.', $e);
        }
    }

    protected function listarConectado(MdIaDocIndexCancDTO $objMdIaDocIndexCancDTO)
    {
        try {
            $objMdIaDocIndexCancBD = new MdIaDocIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexCancBD->listar($objMdIaDocIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro na listagem.', $e);
        }
    }

    protected function contarConectado(MdIaDocIndexCancDTO $objMdIaDocIndexCancDTO)
    {
        try {

            $objMdIaDocIndexCancBD = new MdIaDocIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexCancBD->contar($objMdIaDocIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando.', $e);
        }
    }

    protected function consultarConectado(MdIaDocIndexCancDTO $objMdIaDocIndexCancDTO)
    {
        try {
            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaDocIndexCancBD = new MdIaDocIndexCancBD($this->getObjInfraIBanco());

            /** @var MdIaDocIndexCancDTO $ret */
            $ret = $objMdIaDocIndexCancBD->consultar($objMdIaDocIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Documentos Indexados', $e);
        }
    }
}
