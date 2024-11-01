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

    public function __call($func, $params)
    {

        try {


            SessaoSEI::getInstance(false);

            if (!method_exists($this, $func . 'Monitorado')) {
                throw new InfraException('ServiÃ§o [' . get_class($this) . '.' . $func . '] nÃ£o encontrado.');
            }

            BancoSEI::getInstance()->abrirConexao();

            $parametros = $params[0];
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdUsuario();
            $objUsuarioDTO->setStrSigla($parametros->SiglaSistema);
            $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

            $objUsuarioRN = new UsuarioRN();
            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            if ($objUsuarioDTO == null) {
                throw new InfraException('Sistema [' . $parametros->SiglaSistema . '] nÃ£o encontrado.');
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

            if (strpos($parametros->IdentificacaoServico, ' ') === false && strlen($parametros->IdentificacaoServico) == 72 && preg_match("/[0-9a-z]/", $parametros->IdentificacaoServico)) {

                $objServicoDTO->setStrCrc(substr($parametros->IdentificacaoServico, 0, 8));
                $objServicoDTO = $objServicoRN->consultar($objServicoDTO);
                if ($objServicoDTO == null) {
                    throw new InfraException('ServiÃ§o do sistema [' . $parametros->SiglaSistema . '] nÃ£o encontrado.');
                }

                if ($objServicoDTO->getStrSinChaveAcesso() == 'N') {
                    throw new InfraException('ServiÃ§o [' . $objServicoDTO->getStrIdentificacao() . '] do sistema [' . $objServicoDTO->getStrSiglaUsuario() . '] nÃ£o possui autenticaÃ§Ã£o por Chave de Acesso.');
                }

                $objInfraBcrypt = new InfraBcrypt();
                if (!$objInfraBcrypt->verificar(md5(substr($parametros->IdentificacaoServico, 8)), $objServicoDTO->getStrChaveAcesso())) {
                    throw new InfraException('Chave de Acesso invÃ¡lida para o serviÃ§o [' . $objServicoDTO->getStrIdentificacao() . '] do sistema [' . $objServicoDTO->getStrSiglaUsuario() . '].');
                }

            } else {

                $objServicoDTO->setStrIdentificacao($parametros->IdentificacaoServico);

                $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

                if ($objServicoDTO == null) {
                    throw new InfraException('ServiÃ§o [' . $parametros->IdentificacaoServico . '] do sistema [' . $parametros->SiglaSistema . '] nÃ£o encontrado.');
                }

                if ($objServicoDTO->getStrSinServidor() == 'N') {
                    throw new InfraException('ServiÃ§o [' . $parametros->IdentificacaoServico . '] do sistema [' . $parametros->SiglaSistema . '] nÃ£o possui autenticaÃ§Ã£o por EndereÃ§o.');
                }

                $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            }

            // Valida se ao menos a operaÃ§Ã£o * Consultar Documentos * estÃ¡ configurada no ServiÃ§o:
            if (!is_null($objServicoDTO)) {
                $operacaoServicoDTO = new OperacaoServicoDTO();
                $operacaoServicoRN = new OperacaoServicoRN();
                $operacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_CONSULTAR_DOCUMENTO);
                $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
                $operacaoServicoDTO->retNumIdServico();
                $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

                if (empty($objOperacaoServicoDTO)) {
                    throw new InfraException('OperaÃ§Ã£o nÃ£o permitida pois nÃ£o consta para a integraÃ§Ã£o deste Sistema e ServiÃ§o ao menos a operaÃ§Ã£o "Consultar Documentos". Entre em contato com a AdministraÃ§Ã£o do SEI.');
                }
            }

            SessaoSEI::getInstance()->setObjServicoDTO($objServicoDTO);

            $numSeg = InfraUtil::verificarTempoProcessamento();

            $debugWebServices = (int)ConfiguracaoSEI::getInstance()->getValor('SEI', 'DebugWebServices', false, 0);

            if ($debugWebServices) {
                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(($debugWebServices == 2));
                InfraDebug::getInstance()->limpar();

                InfraDebug::getInstance()->gravar("ServiÃ§o: " . $func . "\nParÃ¢metros: " . $this->debugParametros($params));

                if ($debugWebServices == 1) {
                    LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$DEBUG);
                }
            }

            $ret = call_user_func_array(array($this, $func . 'Monitorado'), $params);

            if ($debugWebServices == 2) {
                LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$DEBUG);
            }

            try {

                $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);

                $objMonitoramentoServicoDTO = new MonitoramentoServicoDTO();
                $objMonitoramentoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
                $objMonitoramentoServicoDTO->setStrOperacao($func);
                $objMonitoramentoServicoDTO->setDblTempoExecucao($numSeg * 1000);
                $objMonitoramentoServicoDTO->setStrIpAcesso(InfraUtil::getStrIpUsuario());
                $objMonitoramentoServicoDTO->setDthAcesso(InfraData::getStrDataHoraAtual());
                $objMonitoramentoServicoDTO->setStrServidor(substr($_SERVER['SERVER_NAME'] . ' (' . $_SERVER['SERVER_ADDR'] . ')', 0, 250));
                $objMonitoramentoServicoDTO->setStrUserAgent(substr($_SERVER['HTTP_USER_AGENT'], 0, 250));

                $objMonitoramentoServicoRN = new MonitoramentoServicoRN();
                $objMonitoramentoServicoRN->cadastrar($objMonitoramentoServicoDTO);

            } catch (Throwable $e) {
                try {
                    LogSEI::getInstance()->gravar('Erro monitorando acesso do serviÃ§o.' . "\n" . InfraException::inspecionar($e));
                } catch (Throwable $e) {
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

    private function debugParametros($var)
    {
        $ret = '';
        if (is_array($var)) {
            $arr = $var;
            if (isset($arr['Conteudo']) && $arr['Conteudo'] != null) {
                $arr['Conteudo'] = strlen($arr['Conteudo']) . ' bytes';
            }
            if (isset($arr['ConteudoMTOM']) && $arr['ConteudoMTOM'] != null) {
                $arr['ConteudoMTOM'] = strlen($arr['ConteudoMTOM']) . ' bytes';
            }
            $numItens = count($arr);
            for ($i = 0; $i < $numItens; $i++) {
                $arr[$i] = $this->debugParametros($arr[$i]);
            }
            $ret = print_r($arr, true);
        } elseif (is_object($var)) {
            $obj = clone($var);
            if (isset($obj->Conteudo) && $obj->Conteudo != null) {
                $obj->Conteudo = strlen($obj->Conteudo) . ' bytes';
            }
            if (isset($obj->ConteudoMTOM) && $obj->ConteudoMTOM != null) {
                $obj->ConteudoMTOM = strlen($obj->ConteudoMTOM) . ' bytes';
            }
            $ret = print_r($obj, true);
        } else {
            $ret = $var;
        }
        return $ret;
    }

}
