<?php

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Classe responsÃ¡vel por gerenciar a autenticaÃ§Ã£o e geraÃ§Ã£o de tokens OAuth2
 * para conexÃ£o direta com a Google Cloud Platform (GCP) no mÃ³dulo SEI-IA.
 */
class MdIaGcpAuthRN extends InfraRN
{
    private static $tokenCache = null;
    private static $tokenExpiration = 0;

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Retorna um Access Token vÃ¡lido da GCP (utiliza cache em memÃ³ria enquanto vÃ¡lido)
     *
     * @param string|null $serviceAccountJson Key JSON da Service Account
     * @param bool $usarMetadataServer Se deve tentar obter via Metadata Server da GCP
     * @return string Access Token Bearer
     * @throws InfraException
     */
    public function obterAccessTokenGcp($serviceAccountJson = null, $usarMetadataServer = false)
    {
        $agora = time();

        // Retorna o token em cache se ainda for vÃ¡lido (margem de 60s)
        if (self::$tokenCache !== null && ($agora < (self::$tokenExpiration - 60))) {
            return self::$tokenCache;
        }

        if ($usarMetadataServer) {
            $tokenData = $this->obterTokenViaMetadataServer();
        } else if (!empty($serviceAccountJson)) {
            $tokenData = $this->obterTokenViaServiceAccountJson($serviceAccountJson);
        } else {
            throw new InfraException('Nenhuma credencial GCP informada (Service Account JSON ou Metadata Server).');
        }

        self::$tokenCache = $tokenData['access_token'];
        self::$tokenExpiration = $agora + intval($tokenData['expires_in']);

        return self::$tokenCache;
    }

    /**
     * ObtÃ©m token de acesso via Service Account JSON usando JWT assinado com RS256
     */
    private function obterTokenViaServiceAccountJson($jsonStr)
    {
        $saData = json_decode($jsonStr, true);
        if (!$saData || empty($saData['private_key']) || empty($saData['client_email'])) {
            throw new InfraException('JSON da Service Account GCP invÃ¡lido ou incompleto.');
        }

        $now = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $claimSet = [
            'iss' => $saData['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $saData['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedClaimSet = $this->base64UrlEncode(json_encode($claimSet));
        $signatureInput = $encodedHeader . '.' . $encodedClaimSet;

        $binarySignature = '';
        $success = openssl_sign($signatureInput, $binarySignature, $saData['private_key'], 'SHA256');

        if (!$success) {
            throw new InfraException('Falha ao assinar o JWT da Service Account GCP com OpenSSL.');
        }

        $encodedSignature = $this->base64UrlEncode($binarySignature);
        $jwt = $signatureInput . '.' . $encodedSignature;

        $tokenUri = $saData['token_uri'] ?? 'https://oauth2.googleapis.com/token';

        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $tokenUri,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new InfraException("Erro ao obter Access Token da GCP (HTTP $httpCode): " . $response);
        }

        $responseData = json_decode($response, true);
        if (!$responseData || empty($responseData['access_token'])) {
            throw new InfraException('Resposta de token da GCP invÃ¡lida: ' . $response);
        }

        return $responseData;
    }

    /**
     * ObtÃ©m token de acesso diretamente do GCP Metadata Server
     */
    private function obterTokenViaMetadataServer()
    {
        $metadataUrl = 'http://169.254.169.254/computeMetadata/v1/instance/service-accounts/default/token';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $metadataUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Metadata-Flavor: Google'
            ],
            CURLOPT_TIMEOUT => 5
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new InfraException("Erro ao consultar GCP Metadata Server (HTTP $httpCode): " . $response);
        }

        $responseData = json_decode($response, true);
        if (!$responseData || empty($responseData['access_token'])) {
            throw new InfraException('Resposta do GCP Metadata Server invÃ¡lida.');
        }

        return $responseData;
    }

    private function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
