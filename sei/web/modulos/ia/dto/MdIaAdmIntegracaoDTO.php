<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 15/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmIntegracaoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_integracao';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmIntegracao', 'id_md_ia_adm_integracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmIntegFuncion', 'id_md_ia_adm_integ_funcion');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoIntegracao', 'tipo_integracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'MetodoAutenticacao', 'metodo_autenticacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'MetodoRequisicao', 'metodo_requisicao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'FormatoResposta', 'formato_resposta');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'VersaoSoap', 'versao_soap');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TokenAutenticacao', 'token_autenticacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'UrlWsdl', 'url_wsdl');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OperacaoWsdl', 'operacao_wsdl');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeFuncionalidade', 'nome', 'md_ia_adm_integ_funcion');

        $this->configurarPK('IdMdIaAdmIntegracao', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdIaAdmIntegFuncion', 'md_ia_adm_integ_funcion', 'id_md_ia_adm_integ_funcion');

        #$this->configurarExclusaoLogica('SinAtivo', 'N');

    }
}
