<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/03/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaProcIndexaveisRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdIaProcIndexaveisDTO $objMdIaProcIndexaveisDTO)
    {
        try {
            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexaveisBD->cadastrar($objMdIaProcIndexaveisDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro no cadastro.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaProcIndexaveisDTO)
    {
        try {
            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaProcIndexaveisDTO); $i++) {
                $objMdIaProcIndexaveisBD->excluir($arrObjMdIaProcIndexaveisDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro de exclusão de registro.', $e);
        }
    }

    protected function listarConectado(MdIaProcIndexaveisDTO $objMdIaProcIndexaveisDTO)
    {
        try {

            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexaveisBD->listar($objMdIaProcIndexaveisDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro na listagem.', $e);
        }
    }

    protected function alterarControlado(MdIaProcIndexaveisDTO $objMdIaProcIndexaveisDTO)
    {
        try {
            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            $objMdIaProcIndexaveisBD->alterar($objMdIaProcIndexaveisDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro na alteração.', $e);
        }
    }

    protected function consultarControlado(MdIaProcIndexaveisDTO $objMdIaProcIndexaveisDTO)
    {
        try {
            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            return $objMdIaProcIndexaveisBD->consultar($objMdIaProcIndexaveisDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro ao consultar.', $e);
        }
    }

    protected function contarConectado(MdIaProcIndexaveisDTO $objMdIaProcIndexaveisDTO)
    {
        try {

            $objMdIaProcIndexaveisBD = new MdIaProcIndexaveisBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexaveisBD->contar($objMdIaProcIndexaveisDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando.', $e);
        }
    }
}
