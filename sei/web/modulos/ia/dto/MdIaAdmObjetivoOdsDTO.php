<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmObjetivoOdsDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_objetivo_ods';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmObjetivoOds', 'id_md_ia_adm_objetivo_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmOdsOnu', 'id_md_ia_adm_ods_onu');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeOds', 'nome_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'DescricaoOds', 'descricao_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'IconeOds', 'icone_ods');

        $this->configurarPK('IdMdIaAdmObjetivoOds', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmOdsOnu', 'md_ia_adm_ods_onu', 'id_md_ia_adm_ods_onu');
    }
}
