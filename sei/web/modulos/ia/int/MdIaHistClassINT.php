<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/12/2023 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaHistClassINT extends InfraINT
{
	public function existeHistorico($idProcedimento, $idObjetivo)
    {
        $objMdIaHistClassDTO = new MdIaHistClassDTO();
        $objMdIaHistClassDTO->setNumIdProcedimento($idProcedimento);
        $objMdIaHistClassDTO->setNumIdMdIaAdmObjetivoOds($idObjetivo);
        $objMdIaHistClassDTO->retNumIdMdIaHistClass();
        $arrMdIaHistClassDTO = (new MdIaHistClassRN())->listar($objMdIaHistClassDTO);

        return count($arrMdIaHistClassDTO) > 0;
    }
}
