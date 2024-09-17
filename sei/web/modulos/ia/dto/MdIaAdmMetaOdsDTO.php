<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmMetaOdsDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_meta_ods';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetaOds', 'id_md_ia_adm_meta_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmObjetivoOds', 'id_md_ia_adm_objetivo_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'Ordem', 'ordem');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'IdentificacaoMeta', 'identificacao_meta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'DescricaoMeta', 'descricao_meta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinForteRelacao', 'sin_forte_relacao');

        $this->configurarPK('IdMdIaAdmMetaOds', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmObjetivoOds', 'md_ia_adm_objetivo_ods', 'id_md_ia_adm_objetivo_ods');
    }
}
