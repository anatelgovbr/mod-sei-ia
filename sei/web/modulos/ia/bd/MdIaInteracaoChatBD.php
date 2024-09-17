<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaInteracaoChatBD extends InfraBD
{

    private $objInfraIBanco;

    public function __construct(InfraIBanco $objInfraIBanco)
    {
        $this->objInfraIBanco = $objInfraIBanco;
        parent::__construct($objInfraIBanco);
    }

    public function calcularConsumoTokenDiario($idUsuario)
    {
        try {

            $dataInicio = "'".InfraData::formatarDataBanco(InfraData::getStrDataAtual(), true)."'";
            $dataFim = "'".InfraData::formatarDataBanco(InfraData::getStrDataAtual(), false)."'";
            if ($this->objInfraIBanco instanceof InfraOracle) {
                $dataInicio = "TO_DATE(".$dataInicio.", 'YYYY-MM-DD HH24:MI:SS')";
                $dataFim = "TO_DATE(".$dataFim.", 'YYYY-MM-DD HH24:MI:SS')";
            }
            $sql = "SELECT SUM(md_ia_interacao_chat.total_tokens) as total_token_utilizado, md_ia_topico_chat.id_usuario FROM md_ia_interacao_chat
            LEFT OUTER JOIN md_ia_topico_chat ON md_ia_topico_chat.id_md_ia_topico_chat = md_ia_interacao_chat.id_md_ia_topico_chat
            WHERE md_ia_topico_chat.id_usuario = " . $idUsuario . "
            AND md_ia_interacao_chat.dth_cadastro >= " . $dataInicio . "
            AND md_ia_interacao_chat.dth_cadastro <= " . $dataFim;

            $sql .= " GROUP BY md_ia_topico_chat.id_usuario ";
            $rs = $this->getObjInfraIBanco()->consultarSql($sql);
            if (count($rs) == 0) {
                return 0;
            } else {
                return $rs[0]["total_token_utilizado"];
            }
        } catch (Exception $e) {
            throw new InfraException('Erro calculando consumo diário do usuário.', $e);
        }
    }
}
