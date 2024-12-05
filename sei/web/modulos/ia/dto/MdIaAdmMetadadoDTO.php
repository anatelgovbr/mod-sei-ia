<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 06/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmMetadadoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_metadado';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetadado', 'id_md_ia_adm_metadado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Metadado', 'metadado');

        $this->configurarPK('IdMdIaAdmMetadado', InfraDTO::$TIPO_PK_NATIVA);
    }
}
