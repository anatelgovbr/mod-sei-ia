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
    private const REGEX_SIGLA_SISTEMA = '/^[A-Za-z0-9_\-]{1,50}$/';
    private const REGEX_IDENTIFICACAO_SERVICO = '/^[0-9a-z]{72}$/';
    private const SERVICO_PADRAO_IA = 'consultarDocumentoExternoIA';
    private const MSG_ERRO_VALIDACAO = 'Erro ao validar as credenciais de acesso.';

    public function validarPermissao()
    {
        $parametros = $_POST ?: $_GET;
        SessaoSEI::getInstance(false);

        $strSiglaSistema = isset($parametros['SiglaSistema']) ? trim($parametros['SiglaSistema']) : null;
        $strIdentificacaoServico = $parametros['IdentificacaoServico'] ?? null;

        if (
            !$this->validarSiglaSistema($strSiglaSistema) ||
            !$this->validarIdentificacaoServico($strIdentificacaoServico)
        ) {
            $this->responderErroPermissao(
                $this->montarMensagemCredenciaisInvalidas(null, $strSiglaSistema),
                401,
                $parametros
            );
        }

        $bolConexaoAberta = false;

        try {
            BancoSEI::getInstance()->abrirConexao();
            $bolConexaoAberta = true;

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdUsuario();
            $objUsuarioDTO->setStrSigla($strSiglaSistema);
            $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

            $objUsuarioRN = new UsuarioRN();
            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            if ($objUsuarioDTO == null) {
                $this->responderErroPermissao('Sistema [' . $strSiglaSistema . '] nao encontrado.', 401, $parametros);
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
            $objServicoDTO->setStrCrc(substr($strIdentificacaoServico, 0, 8));

            $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

            if (
                $objServicoDTO == null ||
                $objServicoDTO->getStrSinChaveAcesso() != 'S' ||
                InfraString::isBolVazia($objServicoDTO->getStrChaveAcesso())
            ) {
                $this->responderErroPermissao(
                    $this->montarMensagemCredenciaisInvalidas(null, $strSiglaSistema),
                    401,
                    $parametros
                );
            }

            $objInfraBcrypt = new InfraBcrypt();
            if (
                !$objInfraBcrypt->verificar(
                    md5(substr($strIdentificacaoServico, 8)),
                    $objServicoDTO->getStrChaveAcesso()
                )
            ) {
                $this->responderErroPermissao(
                    $this->montarMensagemCredenciaisInvalidas($objServicoDTO, $strSiglaSistema),
                    401,
                    $parametros
                );
            }
        } catch (Throwable $e) {
            $this->gravarLogPermissao(self::MSG_ERRO_VALIDACAO, [
                'erro' => $e->getMessage(),
                'codigo' => $e->getCode(),
                'parametros' => $parametros
            ]);
            $this->responderErroPermissao(self::MSG_ERRO_VALIDACAO, 500);
        } finally {
            if ($bolConexaoAberta) {
                try {
                    BancoSEI::getInstance()->fecharConexao();
                } catch (Throwable $e) {
                }
            }
        }
    }

    private function validarSiglaSistema($strSiglaSistema)
    {
        return is_string($strSiglaSistema) && preg_match(self::REGEX_SIGLA_SISTEMA, $strSiglaSistema) === 1;
    }

    private function validarIdentificacaoServico($strIdentificacaoServico)
    {
        return is_string($strIdentificacaoServico) && preg_match(self::REGEX_IDENTIFICACAO_SERVICO, $strIdentificacaoServico) === 1;
    }

    private function responderErroPermissao($msg, $codigoErro, array $parametros = [])
    {
        if (!empty($parametros)) {
            $this->gravarLogPermissao($msg, $parametros);
        }

        http_response_code($codigoErro);
        echo json_encode(
            IaWS::retornoErro($msg, $codigoErro, false),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
        exit;
    }

    private function gravarLogPermissao($mensagem, array $dados = [])
    {
        $dadosRedigidos = $this->redigirCamposSensiveis($dados);
        $log = "WS IA - Falha de validacao de autenticacao\n";
        $log .= "Mensagem: " . $mensagem . "\n";
        $log .= "Metodo: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n";
        $log .= "Endpoint: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
        $log .= "Dados: " . print_r($dadosRedigidos, true);

        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
    }

    private function redigirCamposSensiveis($dados)
    {
        if (!is_array($dados)) {
            return $dados;
        }

        foreach ($dados as $chave => $valor) {
            if (is_array($valor)) {
                $dados[$chave] = $this->redigirCamposSensiveis($valor);
                continue;
            }

            if (strtolower((string)$chave) === 'identificacaoservico') {
                $dados[$chave] = '***REDACTED***';
            }
        }

        return $dados;
    }

    private function montarMensagemCredenciaisInvalidas($objServicoDTO = null, $strSiglaSistema = null)
    {
        if ($objServicoDTO != null) {
            return 'Chave de Acesso ou SiglaSistema inválida para o serviço [' . $objServicoDTO->getStrIdentificacao() . '] do sistema [' . $objServicoDTO->getStrSiglaUsuario() . '].';
        }

        $strSiglaSistema = is_string($strSiglaSistema) && trim($strSiglaSistema) !== ''
            ? trim($strSiglaSistema)
            : 'desconhecido';

        return 'Chave de Acesso ou SiglaSistema inválida para o serviço [' . self::SERVICO_PADRAO_IA . '] do sistema [' . $strSiglaSistema . '].';
    }
}
