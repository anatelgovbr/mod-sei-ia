<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/12/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaClassificacaoOdsDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_classificacao_ods';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaClassificacaoOds', 'id_md_ia_classificacao_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdProcedimento', 'id_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmObjetivoOds', 'id_md_ia_adm_objetivo_ods');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaTipoUltimoUsuario', 'sta_tipo_ultimo_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');

        $this->configurarPK('IdMdIaClassificacaoOds', InfraDTO::$TIPO_PK_NATIVA);

    }
}
