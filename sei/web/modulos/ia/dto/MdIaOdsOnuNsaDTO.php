<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 17/09/2025 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaOdsOnuNsaDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_ia_ods_onu_nsa';
    }

    /**
     * @throws InfraException
     */
    public function montar(): void
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProcedimento', 'id_procedimento');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Cadastro', 'dth_cadastro');


        $this->configurarPK('IdProcedimento', InfraDTO::$TIPO_PK_INFORMADO);
    }
}
