<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 07/11/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaPromptsFavoritosRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdIaPromptsFavoritos(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaPromptsFavoritosDTO->getNumIdMdIaPromptsFavoritos())) {
            $objInfraException->adicionarValidacao('Id Prompt Favorito não informado.');
        }
    }

    private function validarNumIdMdIaGrupoPromptsFav(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaPromptsFavoritosDTO->getNumIdMdIaGrupoPromptsFav())) {
            $objMdIaPromptsFavoritosDTO->setNumIdMdIaGrupoPromptsFav(null);
        }
    }

    private function validarStrDescricaoPrompt(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaPromptsFavoritosDTO->getStrDescricaoPrompt())) {
            $objMdIaPromptsFavoritosDTO->setStrDescricaoPrompt(null);
        } else {
            $objMdIaPromptsFavoritosDTO->setStrDescricaoPrompt(trim($objMdIaPromptsFavoritosDTO->getStrDescricaoPrompt()));

            if (strlen($objMdIaPromptsFavoritosDTO->getStrDescricaoPrompt()) > 500) {
                $objInfraException->adicionarValidacao('Descrição do Prompt possui tamanho superior a 500 caracteres.');
            }
        }
    }

    private function validarDthAlteracao(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaPromptsFavoritosDTO->getDthAlteracao())) {
            $objMdIaPromptsFavoritosDTO->setDthAlteracao(null);
        } else {
            if (!InfraData::validarDataHora($objMdIaPromptsFavoritosDTO->getDthAlteracao())) {
                $objInfraException->adicionarValidacao('Data Alteração inválida.');
            }
        }
    }

    protected function cadastrarControlado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdIaGrupoPromptsFav($objMdIaPromptsFavoritosDTO, $objInfraException);
            $this->validarStrDescricaoPrompt($objMdIaPromptsFavoritosDTO, $objInfraException);
            $this->validarDthAlteracao($objMdIaPromptsFavoritosDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
            $ret = $objMdIaPromptsFavoritosBD->cadastrar($objMdIaPromptsFavoritosDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Prompt Favorito.', $e);
        }
    }

    protected function alterarControlado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdIaPromptsFavoritosDTO->isSetNumIdMdIaPromptsFavoritos()) {
                $this->validarNumIdMdIaPromptsFavoritos($objMdIaPromptsFavoritosDTO, $objInfraException);
            }
            if ($objMdIaPromptsFavoritosDTO->isSetNumIdMdIaGrupoPromptsFav()) {
                $this->validarNumIdMdIaGrupoPromptsFav($objMdIaPromptsFavoritosDTO, $objInfraException);
            }
            if ($objMdIaPromptsFavoritosDTO->isSetStrDescricaoPrompt()) {
                $this->validarStrDescricaoPrompt($objMdIaPromptsFavoritosDTO, $objInfraException);
            }
            if ($objMdIaPromptsFavoritosDTO->isSetDthAlteracao()) {
                $this->validarDthAlteracao($objMdIaPromptsFavoritosDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
            $objMdIaPromptsFavoritosBD->alterar($objMdIaPromptsFavoritosDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Prompt Favorito.', $e);
        }
    }

    protected function excluirControlado($arrObjMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdIaPromptsFavoritosDTO); $i++) {
                $objMdIaPromptsFavoritosBD->excluir($arrObjMdIaPromptsFavoritosDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Prompt Favorito.', $e);
        }
    }

    protected function consultarConectado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());

            /** @var MdIaPromptsFavoritosDTO $ret */
            $ret = $objMdIaPromptsFavoritosBD->consultar($objMdIaPromptsFavoritosDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Prompt Favorito.', $e);
        }
    }

    protected function listarConectado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());

            /** @var MdIaPromptsFavoritosDTO[] $ret */
            $ret = $objMdIaPromptsFavoritosBD->listar($objMdIaPromptsFavoritosDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Prompts Favoritos.', $e);
        }
    }

    protected function contarConectado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
            $ret = $objMdIaPromptsFavoritosBD->contar($objMdIaPromptsFavoritosDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Prompts Favoritos.', $e);
        }
    }
    /*
      protected function desativarControlado($arrObjMdIaPromptsFavoritosDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_prompts_favoritos_desativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaPromptsFavoritosDTO);$i++){
            $objMdIaPromptsFavoritosBD->desativar($arrObjMdIaPromptsFavoritosDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Prompt Favorito.',$e);
        }
      }

      protected function reativarControlado($arrObjMdIaPromptsFavoritosDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_prompts_favoritos_reativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrObjMdIaPromptsFavoritosDTO);$i++){
            $objMdIaPromptsFavoritosBD->reativar($arrObjMdIaPromptsFavoritosDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Prompt Favorito.',$e);
        }
      }

      protected function bloquearControlado(MdIaPromptsFavoritosDTO $objMdIaPromptsFavoritosDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_prompts_favoritos_consultar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaPromptsFavoritosBD = new MdIaPromptsFavoritosBD($this->getObjInfraIBanco());
          $ret = $objMdIaPromptsFavoritosBD->bloquear($objMdIaPromptsFavoritosDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Prompt Favorito.',$e);
        }
      }

     */
}
