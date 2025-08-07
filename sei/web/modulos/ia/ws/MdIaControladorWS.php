<?php

/**
 * ANATEL
 *
 * 28/06/2024 - criado por Willian Christian - sabino.colab@anatel.gov.br
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaControladorWS extends MdIaUtilWS
{

    public static function downloadArquivoDocumentoExterno()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->downloadArquivoDocumentoExterno($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function consultarDocumento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->consultarDocumento($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function consultarProcesso()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->consultarProcesso($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function gerarHashConteudoDocumento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->gerarHashConteudoDocumento($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function listarTipoDocumento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarTipoDocumento($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function listarSegmentosDocRelevantes()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarSegmentosDocRelevantes($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function listarPercentualRelevanciaMetadados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarPercentualRelevanciaMetadados($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function consultarConteudoDocumento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->consultarConteudoDocumento($_GET);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function processosIndexaveis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarProcessosPendenteIndexacao($_GET);
            http_response_code($response['code']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $IaWs = new IaWS();
            $response = $IaWs->atualizarStatusProcessoIndexado($parametros['IdProcedimento']);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function documentosIndexaveis()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarDocumentosPendenteIndexacao($_GET);
            http_response_code($response['code']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $IaWs = new IaWS();
            $response = $IaWs->atualizarStatusDocumentoIndexado($parametros['IdDocumento']);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function processosIndexadosCancelados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarProcessosIndexadosCancelados($_GET);
            http_response_code($response['code']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $IaWs = new IaWS();
            $response = $IaWs->removerProcessoIndexadoListaCancelados($parametros['IdProcedimento']);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function documentosIndexadosCancelados()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->listarDocumentosIndexadosCancelados($_GET);
            http_response_code($response['code']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $IaWs = new IaWS();
            $response = $IaWs->removerDocumentoIndexadoListaCancelados($parametros['IdDocumento']);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function listarDocumentosRelevantesProcesso()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $response = $IaWs->listarDocumentosRelevantesProcesso($parametros);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function consultarHistoricoTopico()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $parametros = [];
            parse_str($_SERVER['QUERY_STRING'], $parametros);
            $response = $IaWs->consultarHistoricoTopico($parametros['IdTopico']);
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    public static function consultarUltimoIdMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $IaWs = new IaWS();
            $response = $IaWs->consultarUltimoIdMessage();
            http_response_code($response['code']);
        }

        self::printResult($response);
    }

    private function printResult($response)
    {
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}
