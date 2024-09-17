<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 15/09/2023 - criado por sabino.colab
 *
 * Vers�o do Gerador de C�digo: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaMensagemINT extends InfraINT
{

    public static $MSG_IA_01 = 'Informe o campo @VALOR1@.';
    public static $MSG_IA_02 = 'O Tipo de Integra��o "SOAP" ainda n�o est� dispon�vel nesta vers�o.';
    public static $MSG_IA_03 = 'Indique como Dado Restrito a chave de autentica��o definida no Header.';
    public static $MSG_IA_04 = 'O Conte�do de Autentica��o � de preenchimento obrigat�rio.';
    public static $MSG_IA_05 = '@VALOR1@ j� consta na lista.';

    public static function getMensagem($msg, $arrParams = null)
    {
        $isPersonalizada = count(explode('@VALOR', self::$MSG_IA_05)) > 1;

        if ($isPersonalizada && !is_null($arrParams)) {
            $msgPersonalizada = self::setMensagemPadraoPersonalizada($msg, $arrParams);
            return $msgPersonalizada;
        }

        return $msg;
    }

    public static function setMensagemPadraoPersonalizada($msg, $arrParametros = null)
    {
        if (!is_array($arrParametros)) {
            $arrParametros = array($arrParametros);
        }

        if ($msg != '') {
            $arrSubstituicao = array();

            foreach ($arrParametros as $key => $param) {
                $vl = $key + 1;
                $arrSubstituicao[] = '@VALOR' . $vl . '@';
            }
            $msgRetorno = str_replace($arrSubstituicao, $arrParametros, $msg);
            return $msgRetorno;
        }

        return '';
    }

}
