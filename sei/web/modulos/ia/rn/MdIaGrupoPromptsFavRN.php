<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/10/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaGrupoPromptsFavRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarStrNomeGrupo(MdIaGrupoPromptsFavDTO $objMdIaGrupoPromptsFavDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdIaGrupoPromptsFavDTO->getStrNomeGrupo())) {
            $objMdIaGrupoPromptsFavDTO->setStrNomeGrupo(null);
        } else {
            $objMdIaGrupoPromptsFavDTO->setStrNomeGrupo(trim($objMdIaGrupoPromptsFavDTO->getStrNomeGrupo()));

            if (strlen($objMdIaGrupoPromptsFavDTO->getStrNomeGrupo()) > 80) {
                $objInfraException->adicionarValidacao(' possui tamanho superior a 80 caracteres.');
            }
        }
    }

    protected function cadastrarControlado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrNomeGrupo($arrobjMdIaGrupoPromptsFavDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
            $ret = $objMdIaGrupoPromptsFavBD->cadastrar($arrobjMdIaGrupoPromptsFavDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Grupo de Prompts Favoritos.', $e);
        }
    }

    protected function alterarControlado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($arrobjMdIaGrupoPromptsFavDTO->isSetStrNomeGrupo()) {
                $this->validarStrNomeGrupo($arrobjMdIaGrupoPromptsFavDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
            $objMdIaGrupoPromptsFavBD->alterar($arrobjMdIaGrupoPromptsFavDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Grupo de Prompts Favoritos.', $e);
        }
    }

    protected function excluirControlado($arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if (count($arrobjMdIaGrupoPromptsFavDTO)){

                $arrIdGrupoAcompanhamento = InfraArray::converterArrInfraDTO($arrobjMdIaGrupoPromptsFavDTO,'IdMdIaGrupoPromptsFav');

                $objMdIaPromptsFavoritosDTO = new MdIaPromptsFavoritosDTO();
                $objMdIaPromptsFavoritosDTO->retNumIdMdIaPromptsFavoritos();
                $objMdIaPromptsFavoritosDTO->retStrNomeGrupoFavorito();
                $objMdIaPromptsFavoritosDTO->setNumIdMdIaGrupoPromptsFav($arrIdGrupoAcompanhamento,InfraDTO::$OPER_IN);
                $objMdIaPromptsFavoritosRN = new MdIaPromptsFavoritosRN();
                $arrObjMdIaPromptsFavoritosDTO = $objMdIaPromptsFavoritosRN->listar($objMdIaPromptsFavoritosDTO);

                if(count($arrObjMdIaPromptsFavoritosDTO) > 0) {
                    foreach($arrObjMdIaPromptsFavoritosDTO as $objMdIaPromptsFavoritosDTO) {
                        $objInfraException->adicionarValidacao('Existem Prompts Favoritos associados ao grupo "'.$objMdIaPromptsFavoritosDTO->getStrNomeGrupoFavorito().'".');
                        break;
                    }
                }
            }

            $objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrobjMdIaGrupoPromptsFavDTO); $i++) {
                $objMdIaGrupoPromptsFavBD->excluir($arrobjMdIaGrupoPromptsFavDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Grupo de Prompts Favoritos.', $e);
        }
    }

    protected function consultarConectado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());

            /** @var MdIaGrupoPromptsFavDTO $ret */
            $ret = $objMdIaGrupoPromptsFavBD->consultar($arrobjMdIaGrupoPromptsFavDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Grupo de Prompts Favoritos.', $e);
        }
    }

    protected function listarConectado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());

            /** @var MdIaGrupoPromptsFavDTO[] $ret */
            $ret = $objMdIaGrupoPromptsFavBD->listar($arrobjMdIaGrupoPromptsFavDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Grupos Favoritos.', $e);
        }
    }

    protected function contarConectado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO)
    {
        try {

            SessaoSEI::getInstance()->validarPermissao('md_ia_adm_config_assist_ia_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
            $ret = $objMdIaGrupoPromptsFavBD->contar($arrobjMdIaGrupoPromptsFavDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Grupos Favoritos.', $e);
        }
    }
    /*
      protected function desativarControlado($arrobjMdIaGrupoPromptsFavDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_grupo_prompts_fav_desativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrobjMdIaGrupoPromptsFavDTO);$i++){
            $objMdIaGrupoPromptsFavBD->desativar($arrobjMdIaGrupoPromptsFavDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro desativando Grupo de Prompts Favoritos.',$e);
        }
      }

      protected function reativarControlado($arrobjMdIaGrupoPromptsFavDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_grupo_prompts_fav_reativar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
          for($i=0;$i<count($arrobjMdIaGrupoPromptsFavDTO);$i++){
            $objMdIaGrupoPromptsFavBD->reativar($arrobjMdIaGrupoPromptsFavDTO[$i]);
          }

        }catch(Exception $e){
          throw new InfraException('Erro reativando Grupo de Prompts Favoritos.',$e);
        }
      }

      protected function bloquearControlado(MdIaGrupoPromptsFavDTO $arrobjMdIaGrupoPromptsFavDTO){
        try {

          SessaoSEI::getInstance()->validarPermissao('md_ia_grupo_prompts_fav_consultar');

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdIaGrupoPromptsFavBD = new MdIaGrupoPromptsFavBD($this->getObjInfraIBanco());
          $ret = $objMdIaGrupoPromptsFavBD->bloquear($arrobjMdIaGrupoPromptsFavDTO);

          return $ret;
        }catch(Exception $e){
          throw new InfraException('Erro bloqueando Grupo de Prompts Favoritos.',$e);
        }
      }

     */
}
