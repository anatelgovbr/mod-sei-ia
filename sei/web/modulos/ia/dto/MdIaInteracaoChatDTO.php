<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 05/04/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaInteracaoChatDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_interacao_chat';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaInteracaoChat', 'id_md_ia_interacao_chat');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaTopicoChat', 'id_md_ia_topico_chat');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMessage', 'id_message');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'TotalTokens', 'total_tokens');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'TempoExecucao', 'tempo_execucao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'StatusRequisicao', 'status_requisicao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Pergunta', 'pergunta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Resposta', 'resposta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'Feedback', 'feedback');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Cadastro', 'dth_cadastro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'InputPrompt', 'input_prompt');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'ProcedimentoCitado', 'procedimento_citado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'LinkAcessoProcedimento', 'link_acesso_procedimento');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaTopicoChatMdIaTopicoChat', 'id_md_ia_topico_chat', 'md_ia_topico_chat');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario', 'md_ia_topico_chat');

        $this->configurarPK('IdMdIaInteracaoChat', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaTopicoChat', 'md_ia_topico_chat', 'id_md_ia_topico_chat', InfraDTO::$TIPO_FK_OPCIONAL);
    }
}
