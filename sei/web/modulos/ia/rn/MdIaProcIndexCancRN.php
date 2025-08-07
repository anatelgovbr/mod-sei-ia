<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/03/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaProcIndexCancRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdIaProcIndexCancDTO $objMdIaProcIndexCancDTO)
    {
        try {
            $objMdIaProcIndexCancBD = new MdIaProcIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexCancBD->cadastrar($objMdIaProcIndexCancDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro no cadastro.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaProcIndexCancDTO)
    {
        try {
            $objMdIaProcIndexCancBD = new MdIaProcIndexCancBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaProcIndexCancDTO); $i++) {
                $objMdIaProcIndexCancBD->excluir($arrObjMdIaProcIndexCancDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro de exclusão de registro.', $e);
        }
    }

    protected function listarConectado(MdIaProcIndexCancDTO $objMdIaProcIndexCancDTO)
    {
        try {
            $objMdIaProcIndexCancBD = new MdIaProcIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexCancBD->listar($objMdIaProcIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro na listagem.', $e);
        }
    }

    protected function contarConectado(MdIaProcIndexCancDTO $objMdIaProcIndexCancDTO)
    {
        try {

            $objMdIaProcIndexCancBD = new MdIaProcIndexCancBD($this->getObjInfraIBanco());
            $ret = $objMdIaProcIndexCancBD->contar($objMdIaProcIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando.', $e);
        }
    }

    protected function consultarConectado(MdIaProcIndexCancDTO $objMdIaProcIndexCancDTO)
    {
        try {
            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaProcIndexCancBD($this->getObjInfraIBanco());

            /** @var MdIaProcIndexCancDTO $ret */
            $ret = $objMdIaAdmCfgAssiIaUsuBD->consultar($objMdIaProcIndexCancDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Processos Indexados', $e);
        }
    }
}
