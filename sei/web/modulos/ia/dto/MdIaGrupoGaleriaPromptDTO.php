<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 13/03/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.45
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaGrupoGaleriaPromptDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_ia_grupo_galeria_prompt';
    }

    /**
     * @throws InfraException
     */
    public function montar(): void
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaGrupoGaleriaPrompt', 'id_md_ia_grupo_galeria_prompt');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'NomeGrupo', 'nome_grupo');

        $this->configurarPK('IdMdIaGrupoGaleriaPrompt', InfraDTO::$TIPO_PK_NATIVA);
    }
}
