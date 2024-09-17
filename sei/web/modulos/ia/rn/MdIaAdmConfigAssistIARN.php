<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmConfigAssistIARN extends InfraRN
{
    public static $LLM_GPT_4_128K_ID   = '6';
    public static $LLM_GPT_4_128K   = 'GPT 4 128K de Contexto';
    public static $LLM_GPT_4_128K_CONTEXTO   = '128000';


    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarStrOrientacoesGerais(MdIaAdmConfigAssistIADTO $objMdIaAdmConfigAssistIADTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigAssistIADTO->getStrOrientacoesGerais())) {
            $objInfraException->adicionarValidacao('Orientações Gerais não informada.');
        } else {
            $objMdIaAdmConfigAssistIADTO->setStrOrientacoesGerais(trim($objMdIaAdmConfigAssistIADTO->getStrOrientacoesGerais()));
        }
    }

    private function validarStrSinExibirFuncionalidade(MdIaAdmConfigAssistIADTO $objMdIaAdmConfigAssistIADTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmConfigAssistIADTO->getStrSinExibirFuncionalidade())) {
            $objInfraException->adicionarValidacao('Exibir Funcionalidade não informado.');
        } else {
            $objMdIaAdmConfigAssistIADTO->setStrSinExibirFuncionalidade(trim($objMdIaAdmConfigAssistIADTO->getStrSinExibirFuncionalidade()));
        }
    }

    protected function alterarControlado(MdIaAdmConfigAssistIADTO $objMdIaAdmConfigAssistIADTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_alterar', __METHOD__, $objMdIaAdmConfigAssistIADTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmConfigAssistIADTO->isSetStrOrientacoesGerais()) {
                $this->validarStrOrientacoesGerais($objMdIaAdmConfigAssistIADTO, $objInfraException);
            }

            if ($objMdIaAdmConfigAssistIADTO->isSetStrSinExibirFuncionalidade()) {
                $this->validarStrSinExibirFuncionalidade($objMdIaAdmConfigAssistIADTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmConfigSimilarBD = new MdIaAdmConfigSimilarBD($this->getObjInfraIBanco());
            $objMdIaAdmConfigSimilarBD->alterar($objMdIaAdmConfigAssistIADTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function consultarConectado(MdIaAdmConfigAssistIADTO $objMdIaAdmConfigAssistIADTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaAdmConfigAssistIADTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmConfigAssistIABD = new MdIaAdmConfigAssistIABD($this->getObjInfraIBanco());

            /** @var MdIaAdmConfigSimilarDTO $ret */
            $ret = $objMdIaAdmConfigAssistIABD->consultar($objMdIaAdmConfigAssistIADTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function cadastrarControlado(MdIaAdmConfigAssistIADTO $objMdIaAdmConfigAssistIADTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_cadastrar', __METHOD__, $objMdIaAdmConfigAssistIADTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrOrientacoesGerais($objMdIaAdmConfigAssistIADTO, $objInfraException);
            $this->validarStrSinExibirFuncionalidade($objMdIaAdmConfigAssistIADTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmConfigAssistIABD = new MdIaAdmConfigAssistIABD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmConfigAssistIABD->cadastrar($objMdIaAdmConfigAssistIADTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }
}
