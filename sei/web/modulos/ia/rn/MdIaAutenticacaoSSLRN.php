<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 18/06/2026 - criado por sabino.colab
 *
 * RN utilitária para configuração de SSL em conexões cURL do módulo IA.
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAutenticacaoSSLRN extends InfraRN
{
    const CURL_CA_BUNDLE_PATH = __DIR__ . '/../../../../config/mod-ia/seiia.cert.pem';

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private static function recuperarCaminhoCaBundle()
    {
        $caBundle = realpath(self::CURL_CA_BUNDLE_PATH);
        if ($caBundle === false || !is_readable($caBundle)) {
            throw new InfraException('Certificado CA não encontrado ou ilegível: ' . self::CURL_CA_BUNDLE_PATH);
        }

        return $caBundle;
    }

    public static function configurarAutenticacaoSSLPadrao($curl)
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    }

    public static function configurarAutenticacaoSSL($curl)
    {
        self::configurarAutenticacaoSSLPadrao($curl);
        curl_setopt($curl, CURLOPT_CAINFO, self::recuperarCaminhoCaBundle());
    }
}
