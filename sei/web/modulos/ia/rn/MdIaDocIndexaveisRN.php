<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaDocIndexaveisRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdIaDocIndexaveisDTO $objMdIaDocIndexaveisDTO)
    {
        try {
            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexaveisBD->cadastrar($objMdIaDocIndexaveisDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro no cadastro.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaDocIndexaveisDTO)
    {
        try {
            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaDocIndexaveisDTO); $i++) {
                $objMdIaDocIndexaveisBD->excluir($arrObjMdIaDocIndexaveisDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro de exclusão de registro.', $e);
        }
    }

    protected function listarConectado(MdIaDocIndexaveisDTO $objMdIaDocIndexaveisDTO)
    {
        try {

            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexaveisBD->listar($objMdIaDocIndexaveisDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro na listagem.', $e);
        }
    }

    protected function alterarControlado(MdIaDocIndexaveisDTO $objMdIaDocIndexaveisDTO)
    {
        try {
            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            $objMdIaDocIndexaveisBD->alterar($objMdIaDocIndexaveisDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro na alteração.', $e);
        }
    }

    protected function consultarControlado(MdIaDocIndexaveisDTO $objMdIaDocIndexaveisDTO)
    {
        try {
            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            return $objMdIaDocIndexaveisBD->consultar($objMdIaDocIndexaveisDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro ao consultar.', $e);
        }
    }

    protected function contarConectado(MdIaDocIndexaveisDTO $objMdIaDocIndexaveisDTO)
    {
        try {

            $objMdIaDocIndexaveisBD = new MdIaDocIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaDocIndexaveisBD->contar($objMdIaDocIndexaveisDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando.', $e);
        }
    }
}
