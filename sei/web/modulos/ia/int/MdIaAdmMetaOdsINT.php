<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/03/2024 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmMetaOdsINT extends InfraINT
{
    public static function salvarConfiguracaoMetas($dados)
    {
        $metasForteRelacaoSelecionadas = explode(",", $dados["hdnInfraItensSelecionados"]);

        $objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
        $objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
        $objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["hdnIdObjetivo"]);
        $objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
        $objMdIaAdmMetaOdsDTO->retStrSinForteRelacao();
        $arrObjMdIaAdmMetaOdsDTO = $objMdIaAdmMetaOdsRN->listar($objMdIaAdmMetaOdsDTO);

        foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
            MdIaAdmClassAutTpINT::apagarListaTipoProcessoMeta($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
            $objMdIaAdmMetaOdsDTO->setStrSinForteRelacao('N');
            if (in_array($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $metasForteRelacaoSelecionadas)) {
                $objMdIaAdmMetaOdsDTO->setStrSinForteRelacao('S');
                MdIaAdmClassAutTpINT::cadastrarListaTipoProcessoMeta($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $dados["tipoProcessoMetas"]);
            }
            $objMdIaAdmMetaOdsRN->alterar($objMdIaAdmMetaOdsDTO);
        }

        return json_encode(array("result" => "true"));
    }
}
