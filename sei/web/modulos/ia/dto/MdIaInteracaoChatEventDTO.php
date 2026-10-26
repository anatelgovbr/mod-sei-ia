<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 07/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaInteracaoChatEventDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_ia_interacao_chat_event';
    }

    /**
     * @throws InfraException
     */
    public function montar(): void
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaInteracaoChatEvent', 'id_md_ia_interacao_chat_event');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaInteracaoChat', 'id_md_ia_interacao_chat');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoEvento', 'tipo_evento');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Conteudo', 'conteudo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Evento', 'dth_evento');

        $this->configurarPK('IdMdIaInteracaoChatEvent', InfraDTO::$TIPO_PK_NATIVA);
        $this->configurarFK('IdMdIaInteracaoChat', 'md_ia_interacao_chat', 'id_md_ia_interacao_chat', InfraDTO::$TIPO_FK_OBRIGATORIA);
    }
}