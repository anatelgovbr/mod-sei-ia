<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/03/2024 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmMetaOdsINT extends InfraINT
{
    public static function salvarConfiguracaoMetas($dados)
    {
        (new MdIaAdmMetaOdsRN())->salvarConfiguracaoMetas($dados);

        return json_encode(array("result" => "true"));
    }
}
