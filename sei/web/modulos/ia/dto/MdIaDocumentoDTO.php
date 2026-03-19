<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 30/07/2008 - criado por mga
 *
 * Versão do Gerador de Código: 1.21.0
 *
 * Versão no CVS: $Id$
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaDocumentoDTO extends InfraDTO
{

    private $numFiltroFkDocumentoConteudo = null;

    public function __construct()
    {
        $this->numFiltroFkDocumentoConteudo = InfraDTO::$FILTRO_FK_ON;
        parent::__construct();
    }

    public function getStrNomeTabela()
    {
        return 'documento';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_DBL,
            'IdDocumento',
            'id_documento'
        );

        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_DBL,
            'IdProcedimento',
            'id_procedimento'
        );

        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie'
        );

        // Todo: Remover depois
        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_STR,
            'SinBloqueado',
            'sin_bloqueado'
        );

        $this->adicionarAtributoTabela(
            InfraDTO::$PREFIXO_STR,
            'StaDocumento',
            'sta_documento'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_STR,
            'EspecificacaoDocumento',
            'd.descricao',
            'protocolo d'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_DBL,
            'IdProtocoloProcedimento',
            'p.id_protocolo',
            'protocolo p'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_STR,
            'StaEstadoProcedimento',
            'p.sta_estado',
            'protocolo p'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_STR,
            'StaEstadoProtocolo',
            'd.sta_estado',
            'protocolo d'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_NUM,
            'IdTipoProcedimentoProcedimento',
            'id_tipo_procedimento',
            'procedimento'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_DTH,
            'ConclusaoProcedimento',
            'dta_conclusao',
            'procedimento'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_STR,
            'NomeAnexo',
            'a.nome',
            'anexo a'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_DTH,
            'InclusaoAnexo',
            'dth_inclusao',
            'anexo a'
        );


        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_STR,
            'SinAtivoAssinatura',
            'sin_ativo',
            'assinatura'
        );

        $this->adicionarAtributoTabelaRelacionada(
            InfraDTO::$PREFIXO_NUM,
            'IdAtividade',
            'id_atividade',
            'assinatura'
        );

        $this->configurarPK('IdDocumento', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdDocumento', 'protocolo d', 'd.id_protocolo');
        $this->configurarFK('IdDocumento', 'anexo a', 'a.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdDocumento', 'assinatura', 'id_documento AND assinatura.sin_ativo = "S"', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdProcedimento', 'protocolo p', 'p.id_protocolo');
        $this->configurarFK('IdProtocoloProcedimento', 'procedimento', 'id_procedimento');
    }
}
