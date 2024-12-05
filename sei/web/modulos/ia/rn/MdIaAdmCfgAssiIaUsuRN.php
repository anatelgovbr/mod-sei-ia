<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 27/03/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmCfgAssiIaUsuRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaAdmConfigAssistIA(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmCfgAssiIaUsuDTO->getNumIdMdIaAdmConfigAssistIA())) {
            $objInfraException->adicionarValidacao(' não informad.');
        }
    }

    private function validarNumIdUsuario(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaAdmCfgAssiIaUsuDTO->getNumIdUsuario())) {
            $objMdIaAdmCfgAssiIaUsuDTO->setNumIdUsuario(null);
        }
    }

    protected function cadastrarControlado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_cfg_assi_ia_usu_cadastrar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaAdmConfigAssistIA($objMdIaAdmCfgAssiIaUsuDTO, $objInfraException);
            $this->validarNumIdUsuario($objMdIaAdmCfgAssiIaUsuDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmCfgAssiIaUsuBD->cadastrar($objMdIaAdmCfgAssiIaUsuDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Usuário Configuração Assistente IA.', $e);
        }
    }

    protected function alterarControlado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_cfg_assi_ia_usu_alterar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaAdmCfgAssiIaUsuDTO->isSetNumIdMdIaAdmConfigAssistIA()) {
                $this->validarNumIdMdIaAdmConfigAssistIA($objMdIaAdmCfgAssiIaUsuDTO, $objInfraException);
            }
            if ($objMdIaAdmCfgAssiIaUsuDTO->isSetNumIdUsuario()) {
                $this->validarNumIdUsuario($objMdIaAdmCfgAssiIaUsuDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
            $objMdIaAdmCfgAssiIaUsuBD->alterar($objMdIaAdmCfgAssiIaUsuDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Usuário Configuração Assistente IA.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_cfg_assi_ia_usu_excluir', __METHOD__, $arrObjMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaAdmCfgAssiIaUsuDTO); $i++) {
                $objMdIaAdmCfgAssiIaUsuBD->excluir($arrObjMdIaAdmCfgAssiIaUsuDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Usuário Configuração Assistente IA.', $e);
        }
    }

    protected function consultarConectado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());

            /** @var MdIaAdmCfgAssiIaUsuDTO $ret */
            $ret = $objMdIaAdmCfgAssiIaUsuBD->consultar($objMdIaAdmCfgAssiIaUsuDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Usuário Configuração Assistente IA.', $e);
        }
    }

    protected function listarConectado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());

            /** @var MdIaAdmCfgAssiIaUsuDTO[] $ret */
            $ret = $objMdIaAdmCfgAssiIaUsuBD->listar($objMdIaAdmCfgAssiIaUsuDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Usuários Configuração Assistente IA.', $e);
        }
    }

    protected function contarConectado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO)
    {
        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
            $ret = $objMdIaAdmCfgAssiIaUsuBD->contar($objMdIaAdmCfgAssiIaUsuDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Usuários Configuração Assistente IA.', $e);
        }
    }

    protected function cadastrarRelacionamentoConectado($arrUsuarios)
    {
        $idIaAdmConfigAssistIA = $arrUsuarios[1];
        foreach ($arrUsuarios[0] as $usuarios) {
            if ($usuarios[0] != "") {
                $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();
                $objMdIaAdmCfgAssiIaUsuDTO->setNumIdMdIaAdmConfigAssistIA($idIaAdmConfigAssistIA);
                $objMdIaAdmCfgAssiIaUsuDTO->setNumIdUsuario($usuarios[0]);
                $this->cadastrar($objMdIaAdmCfgAssiIaUsuDTO);
            }
        }
    }

    protected function excluirRelacionamentoConectado($idMdIaAdmConfigAssistIa)
    {
        $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();
        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdUsuario();
        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdMdIaAdmCfgAssiIaUsu();
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdMdIaAdmConfigAssistIA($idMdIaAdmConfigAssistIa);
        $this->excluir($this->listar($objMdIaAdmCfgAssiIaUsuDTO));
    }
    /*
      protected function desativarControlado($arrObjMdIaAdmCfgAssiIaUsuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_cfg_assi_ia_usu_desativar', __METHOD__, $arrObjMdIaAdmCfgAssiIaUsuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmCfgAssiIaUsuDTO);$i++){
            $objMdIaAdmCfgAssiIaUsuBD->desativar($arrObjMdIaAdmCfgAssiIaUsuDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Usuário Configuração Assistente IA.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaAdmCfgAssiIaUsuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_cfg_assi_ia_usu_reativar', __METHOD__, $arrObjMdIaAdmCfgAssiIaUsuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaAdmCfgAssiIaUsuDTO);$i++){
            $objMdIaAdmCfgAssiIaUsuBD->reativar($arrObjMdIaAdmCfgAssiIaUsuDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Usuário Configuração Assistente IA.',$e);
        }
      }

      protected function bloquearControlado(MdIaAdmCfgAssiIaUsuDTO $objMdIaAdmCfgAssiIaUsuDTO){
        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_ia_adm_config_assist_ia_consultar', __METHOD__, $objMdIaAdmCfgAssiIaUsuDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaAdmCfgAssiIaUsuBD = new MdIaAdmCfgAssiIaUsuBD($this->getObjInfraIBanco());
          $ret = $objMdIaAdmCfgAssiIaUsuBD->bloquear($objMdIaAdmCfgAssiIaUsuDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Usuário Configuração Assistente IA.',$e);
        }
      }

     */
}
