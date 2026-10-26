<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 22/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmExtPermitidaINT extends InfraINT
{

    const FORMA_ENTREGA_FEED_SOLR = '0';
    const FORMA_ENTREGA_DOWNLOAD = '1';
    const FORMA_ENTREGA_AMBAS = '2';
    const FORMA_NAO_ENTREGUE = '3';

    public static function getArrFormaEntrega()
    {
        return array(
            self::FORMA_ENTREGA_FEED_SOLR => 'Feed do Solr',
            self::FORMA_ENTREGA_DOWNLOAD => 'Download do Arquivo',
            self::FORMA_ENTREGA_AMBAS => 'Feed do Solr e Download do Arquivo',
            self::FORMA_NAO_ENTREGUE => 'Não é permitida a citação como Protocolo de Documento'
        );
    }

    public static function montarSelectFormaEntrega($strCampoVazio = 'null', $strValorCampoVazio = '&nbsp;', $strSelecionado = '')
    {
        $strItensSelect = '';

        if ($strCampoVazio != 'null') {
            $strItensSelect .= '<option value="">' . $strValorCampoVazio . '</option>';
        }

        $arrFormaEntrega = self::getArrFormaEntrega();

        foreach ($arrFormaEntrega as $strValor => $strLabel) {
            $strSel = ((string)$strSelecionado === (string)$strValor) ? ' selected="selected"' : '';
            $strItensSelect .= '<option value="' . $strValor . '"' . $strSel . '>' . PaginaSEI::tratarHTML($strLabel) . '</option>';
        }

        return $strItensSelect;
    }
}
