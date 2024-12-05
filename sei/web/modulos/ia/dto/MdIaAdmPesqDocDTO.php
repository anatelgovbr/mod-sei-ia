<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 28/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmPesqDocDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_pesq_doc';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmPesqDoc', 'id_md_ia_adm_pesq_doc');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'QtdProcessListagem', 'qtd_process_listagem');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OrientacoesGerais', 'orientacoes_gerais');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeSecao', 'nome_secao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinExibirFuncionalidade', 'sin_exibir_funcionalidade');

        $this->configurarPK('IdMdIaAdmPesqDoc', InfraDTO::$TIPO_PK_NATIVA);

    }
}
