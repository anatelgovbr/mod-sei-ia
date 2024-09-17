<?
/**
 * ANATEL
 *
 * 28/06/2024 - criado por Willian Christian - sabino.colab@anatel.gov.br
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

abstract class MdIaUtilWS extends InfraWS
{

    public function __call($func, $params)
    {

        try {

            SessaoSEI::getInstance(false);
            BancoSEI::getInstance()->abrirConexao();

            $ret = call_user_func_array(array($this, $func . 'Monitorado'), $params);

            if (!$ret) {
                throw new InfraException('Serviço [' . get_class($this) . '.' . $func . '] não encontrado.');
            }

            $objServicoRN = new ServicoRN();

            $objServicoDTO = new ServicoDTO();
            $objServicoDTO->retNumIdServico();
            $objServicoDTO->retStrIdentificacao();
            $objServicoDTO->retStrSiglaUsuario();
            $objServicoDTO->retNumIdUsuario();
            $objServicoDTO->retStrServidor();
            $objServicoDTO->retStrSinLinkExterno();
            $objServicoDTO->retNumIdContatoUsuario();
            $objServicoDTO->retStrChaveAcesso();
            $objServicoDTO->retStrSinServidor();
            $objServicoDTO->retStrSinChaveAcesso();
            $objServicoDTO->setStrIdentificacao($func);
            $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

            if ($objServicoDTO) {
                $objInfraBcrypt = new InfraBcrypt();
                if (!$objInfraBcrypt->verificar(md5(substr($params[0], 8)), $objServicoDTO->getStrChaveAcesso())) {
                    throw new InfraException('Chave de Acesso inválida para o serviço [' . $objServicoDTO->getStrIdentificacao() . '] do sistema [' . $objServicoDTO->getStrSiglaUsuario() . '].');
                }
            }

            BancoSEI::getInstance()->fecharConexao();

            return $ret;

        } catch (Throwable $e) {

            try {
                BancoSEI::getInstance()->fecharConexao();
            } catch (Throwable $e2) {
            }

            $this->processarExcecao($e);
        }
    }

}
