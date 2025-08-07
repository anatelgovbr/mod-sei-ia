<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 21/05/2025 - criado por mayconhenry@gmail.com
 *
 * Versão do Gerador de Código: 1.45.1
 **/


require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmClassAutTpINT extends InfraINT
{
    public static function apagarListaTipoProcessoMeta($idMdIaAdmMetaOds)
    {
        $objMdIaAdmClassAutTpDTO = new MdIaAdmClassAutTpDTO();
        $objMdIaAdmClassAutTpDTO->setNumIdMdIaAdmMetaOds($idMdIaAdmMetaOds);
        $objMdIaAdmClassAutTpDTO->retNumIdMdIaAdmClassAutTp();
        $arrObjMdIaAdmClassAutTpDTO = (new MdIaAdmClassAutTpRN())->listar($objMdIaAdmClassAutTpDTO);
        (new MdIaAdmClassAutTpRN())->excluir($arrObjMdIaAdmClassAutTpDTO);
    }

    public static function cadastrarListaTipoProcessoMeta($idMdIaAdmMetaOds, $arrTiposProcessosMetas)
    {
        if($arrTiposProcessosMetas[$idMdIaAdmMetaOds]){
            foreach ($arrTiposProcessosMetas[$idMdIaAdmMetaOds] as $idTiposProcessosMetas){
                $objMdIaAdmClassAutTpDTO = new MdIaAdmClassAutTpDTO();
                $objMdIaAdmClassAutTpDTO->setNumIdMdIaAdmMetaOds($idMdIaAdmMetaOds);
                $objMdIaAdmClassAutTpDTO->setNumIdTipoProcedimento($idTiposProcessosMetas);
                (new MdIaAdmClassAutTpRN())->cadastrar($objMdIaAdmClassAutTpDTO);
            }
        }
    }
}
