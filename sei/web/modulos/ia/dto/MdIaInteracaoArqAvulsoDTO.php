<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 30/12/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaInteracaoArqAvulsoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_interacao_arq_avulso';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaInteracaoArqAvulso', 'id_md_ia_interacao_arq_avulso');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaInteracaoChat', 'id_md_ia_interacao_chat');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeOriginal', 'nome_original');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeTemporario', 'nome_temporario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Inclusao', 'dth_inclusao');

        $this->configurarPK('IdMdIaInteracaoArqAvulso', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaInteracaoChat', 'md_ia_interacao_chat', 'id_md_ia_interacao_chat');
    }
}
