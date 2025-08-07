<?

/**
 * ANATEL
 *
 * 28/06/2024 - criado por Willian Christian - sabino.colab@anatel.gov.br
 *
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

abstract class MdIaUtilWS extends InfraWS
{

    public function validarPermissao()
    {
        $parametros = $_POST ?: $_GET;
        SessaoSEI::getInstance(false);

        BancoSEI::getInstance()->abrirConexao();

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->setStrSigla($parametros['SiglaSistema']);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if ($objUsuarioDTO == null) {
            $msg = 'Sistema [' . $parametros['SiglaSistema'] . '] não encontrado.';
            $IaWs = new IaWS();
            $codigoErro = 401;
            http_response_code($codigoErro);
            echo json_encode($IaWs->retornoErro($msg, $codigoErro), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
            exit;
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
        $objServicoDTO->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());


        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

        BancoSEI::getInstance()->fecharConexao();

        $objInfraBcrypt = new InfraBcrypt();
        if (!$objInfraBcrypt->verificar(md5(substr($parametros['IdentificacaoServico'], 8)), $objServicoDTO->getStrChaveAcesso())) {
            $msg = 'Chave de Acesso ou SiglaSistema inválida para o serviço [' . $objServicoDTO->getStrIdentificacao() . '] do sistema [' . $objServicoDTO->getStrSiglaUsuario() . '].';
            $IaWs = new IaWS();
            $codigoErro = 401;
            http_response_code($codigoErro);
            echo json_encode($IaWs->retornoErro($msg, $codigoErro), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
            exit;
        }
    }
}
