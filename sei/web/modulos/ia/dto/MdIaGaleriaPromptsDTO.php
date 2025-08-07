<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.45
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaGaleriaPromptsDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_ia_galeria_prompts';
    }

    /**
     * @throws InfraException
     */
    public function montar(): void
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaGaleriaPrompts', 'id_md_ia_galeria_prompts');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaGrupoGaleriaPrompt', 'id_md_ia_grupo_galeria_prompt');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Descricao', 'descricao');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Prompt', 'prompt');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Alteracao', 'dth_alteracao');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdIaGrupoGaleriaPromptMdIaGrupoGaleriaPrompt', 'id_md_ia_grupo_galeria_prompt', 'md_ia_grupo_galeria_prompt');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeGrupo', 'nome_grupo', 'md_ia_grupo_galeria_prompt');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidade', 'sigla', 'unidade');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUsuario', 'sigla', 'usuario');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeUsuario', 'nome', 'usuario');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoUnidade', 'descricao', 'unidade');

        $this->configurarPK('IdMdIaGaleriaPrompts', InfraDTO::$TIPO_PK_NATIVA);
        $this->configurarFK('IdMdIaGrupoGaleriaPrompt', 'md_ia_grupo_galeria_prompt', 'id_md_ia_grupo_galeria_prompt');
        $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');
        $this->configurarFK('IdUnidade', 'unidade', 'id_unidade');
    }
}
