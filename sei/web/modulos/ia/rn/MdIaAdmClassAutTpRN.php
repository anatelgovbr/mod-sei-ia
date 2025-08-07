<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 21/05/2025 - criado por mayconhenry@gmail.com
 *
 * Versão do Gerador de Código: 1.45.1
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmClassAutTpRN extends InfraRN
{
  protected function inicializarObjInfraIBanco()
  {
      return BancoSEI::getInstance();
  }

  private function validarNumIdMdIaAdmMetaOds(MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO, InfraException $objInfraException): void
  {
    if (InfraString::isBolVazia($objMdIaAdmClassAutTpDTO->getNumIdMdIaAdmMetaOds())) {
      $objInfraException->adicionarValidacao(' não informad');
    }
  }

  private function validarNumIdTipoProcedimento(MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO, InfraException $objInfraException): void
  {
    if (InfraString::isBolVazia($objMdIaAdmClassAutTpDTO->getNumIdTipoProcedimento())) {
      $objInfraException->adicionarValidacao(' não informad');
    }
  }

  /**
   * @param MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO
   * @return MdIaAdmClassAutTpDTO
   * @throws InfraException
   */
  protected function cadastrarControlado(MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO)
  {
    try {
//      SessaoIA::getInstance()->validarPermissao('md_ia_adm_meta_ods_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdIaAdmMetaOds($objMdIaAdmClassAutTpDTO, $objInfraException);
      $this->validarNumIdTipoProcedimento($objMdIaAdmClassAutTpDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdIaAdmClassAutTpBD = new MdIaAdmClassAutTpBD($this->getObjInfraIBanco());
      return $objMdIaAdmClassAutTpBD->cadastrar($objMdIaAdmClassAutTpDTO);

    } catch (Exception $e) {
      throw new InfraException('Erro cadastrando .', $e);
    }
  }

  /**
   * @param MdIaAdmClassAutTpDTO[] $arrObjMdIaAdmClassAutTpDTO
   * @return void
   * @throws InfraException
   */
  protected function excluirControlado(array $arrObjMdIaAdmClassAutTpDTO): void
  {
    try {
//      SessaoIA::getInstance()->validarPermissao('md_ia_adm_meta_ods_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaAdmClassAutTpBD = new MdIaAdmClassAutTpBD($this->getObjInfraIBanco());
      foreach ($arrObjMdIaAdmClassAutTpDTO as $objMdIaAdmClassAutTpDTO) {
        $objMdIaAdmClassAutTpBD->excluir($objMdIaAdmClassAutTpDTO);
      }

    } catch (Exception $e) {
      throw new InfraException('Erro excluindo .', $e);
    }
  }

  /**
   * @param MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO
   * @return MdIaAdmClassAutTpDTO[]
   * @throws InfraException
   */
  protected function listarConectado(MdIaAdmClassAutTpDTO $objMdIaAdmClassAutTpDTO)
  {
    try {
//      SessaoIA::getInstance()->validarPermissao('md_ia_adm_class_aut_tp_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdIaAdmClassAutTpBD = new MdIaAdmClassAutTpBD($this->getObjInfraIBanco());
      return $objMdIaAdmClassAutTpBD->listar($objMdIaAdmClassAutTpDTO);


    } catch (Exception $e) {
      throw new InfraException('Erro listando .', $e);
    }
  }

}
