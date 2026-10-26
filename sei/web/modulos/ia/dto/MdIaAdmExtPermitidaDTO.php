<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 22/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmExtPermitidaDTO extends InfraDTO
{
    public function getStrNomeTabela(): ?string
    {
        return 'md_ia_adm_ext_permitida';
    }

    /**
     * @throws InfraException
     */
    public function montar(): void
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmExtPermitida', 'id_md_ia_adm_ext_permitida');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Extensao', 'extensao');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPermitidaProtocolo', 'sin_permitida_protocolo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'FormaEntregaArquivo', 'forma_entrega_arquivo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'DescTratamentoProtocolo', 'desc_tratamento_protocolo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPermitidaAvulso', 'sin_permitida_avulso');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'DescTratamentoAvulso', 'desc_tratamento_avulso');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'TamanhoMaximoPermitido', 'tamanho_maximo_permitido');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinConteudoVetorizavel', 'sin_conteudo_vetorizavel');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'LimiteConsiderar', 'limite_considerar');

        $this->configurarPK('IdMdIaAdmExtPermitida', InfraDTO::$TIPO_PK_NATIVA);
    }
}
