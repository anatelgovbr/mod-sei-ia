<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaTopicoChatDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_topico_chat';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaTopicoChat', 'id_md_ia_topico_chat');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Cadastro', 'dth_cadastro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeUsuario', 'nome', 'usuario');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUsuario', 'sigla', 'unidade');

        $this->configurarPK('IdMdIaTopicoChat', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdUsuario', 'usuario', 'id_usuario', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->configurarFK('IdUnidade', 'unidade', 'id_unidade', InfraDTO::$TIPO_FK_OPCIONAL);

    }
}
