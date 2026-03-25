<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaDocumentoRN extends InfraRN
{

  public function __construct()
  {
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco()
  {
    return BancoSEI::getInstance();
  }

  protected function listarConectado($parObjDocumentoDTO)
  {
    try {
      $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
      $ret = $objDocumentoBD->listar($parObjDocumentoDTO);

      return $ret;
    } catch (Exception $e) {
      throw new InfraException('Erro listando Documentos.', $e);
    }
  }
}
