<?
/**
 * ANATEL
 *
 * 28/06/2024 - criado por Willian Christian - sabino.colab@anatel.gov.br
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';


class IaWS extends MdIaUtilWS
{

    public function getObjInfraLog()
    {
        return LogSEI::getInstance();
    }

    public function cadastrarClassificacaoMonitorado($chave, $idProcedimento, $meta)
    {
        try {

            $objMdIaAdmObjetivoOdsINT = new MdIaAdmObjetivoOdsINT();
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $idUsuario = $objInfraParametro->getValor(MdIaClassificacaoOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA, false);
            $staTipoUsuario = MdIaClassificacaoOdsRN::$USUARIO_IA;

            $retorno = $objMdIaAdmObjetivoOdsINT->classificarOdsWS($idProcedimento, $meta, $idUsuario, $staTipoUsuario);

            return $retorno;

        } catch (Exception $e) {
            throw new InfraException('Erro ao Classificar meta.', $e);
        }
    }

    public function consultarOperacaoConsultarDocumento() {

        $objServicoRN = new ServicoRN();

        $objServicoDTO = new ServicoDTO();
        $objServicoDTO->retNumIdServico();
        $objServicoDTO->setStrIdentificacao("consultarDocumentoExternoIA");
        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);
        if (!is_null($objServicoDTO)) {
            $operacaoServicoDTO = new OperacaoServicoDTO();
            $operacaoServicoRN = new OperacaoServicoRN();

            $operacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_CONSULTAR_DOCUMENTO);
            $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
            $operacaoServicoDTO->retNumIdServico();
            $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

            if (empty($objOperacaoServicoDTO)) {
                throw new InfraException('Operação não permitida pois não consta para a integração deste Sistema e Serviço ao menos a operação "Consultar Documento". Entre em contato com a Administração do SEI.');
            }
        }
    }

    public function consultarDocumentoExternoIAMonitorado($chave, $idDocumento)
    {
        try {

            $this->consultarOperacaoConsultarDocumento();

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->retStrStaProtocolo();
            $objProtocoloDTO->setDblIdProtocolo($idDocumento);
            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

            if (!is_null($objProtocoloDTO)) {
                if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {
                    $objAnexoDTO = new AnexoDTO();
                    $objAnexoDTO->retNumIdAnexo();
                    $objAnexoDTO->retStrNome();
                    $objAnexoDTO->retNumIdAnexo();
                    $objAnexoDTO->retStrHash();
                    $objAnexoDTO->setDblIdProtocolo($idDocumento);
                    $objAnexoDTO->retDblIdProtocolo();
                    $objAnexoDTO->retDthInclusao();
                    $objAnexoDTO->retStrProtocoloFormatadoProtocolo();

                    $objAnexoRN = new AnexoRN();
                    $arrObjAnexoDTO = $objAnexoRN->listarRN0218($objAnexoDTO);

                    if (count($arrObjAnexoDTO) == 1) {

                        AuditoriaSEI::getInstance()->auditar('md_ia_consultar_documento_externo_ia',__METHOD__,$arrObjAnexoDTO);

                        // Exemplo de obtenção do caminho do arquivo (substitua com sua lógica real)
                        $strCaminhoNomeArquivo = $objAnexoRN->obterLocalizacao($arrObjAnexoDTO[0]);

                        // Verificar se o arquivo existe
                        if (!file_exists($strCaminhoNomeArquivo)) {
                            throw new Exception('Arquivo não encontrado.');
                        }

                        // Carregar o conteúdo do arquivo
                        $conteudo = file_get_contents($strCaminhoNomeArquivo);

                        // Criar um SoapVar com o conteúdo do arquivo para MTOM
                        $soapVar = new SoapVar($conteudo, XSD_BASE64BINARY, null, null, null, 'http://www.w3.org/2005/05/xmlmime');

                        // Retornar os dados conforme exigido pelo seu WSDL
                        $resultado = [
                            'Mensagem' => "Arquivo enviado com sucesso.",
                            'NomeDocumento' => $arrObjAnexoDTO[0]->getStrNome(),
                            'Arquivo' => $soapVar
                        ];

                        return $resultado;


                    } else {
                        throw new InfraException('Documento não tem anexo.');
                    }
                } else {
                    throw new InfraException('Arquivo buscado não é um documento externo.');
                }
            } else {
                throw new InfraException('Documento não encontrado.');
            }


        } catch (Exception $e) {
            throw new InfraException('Erro ao consultar documento.', $e);
        }
    }

    public static function buscarArquivoDownload($objAnexoDTO = null, $strContentDisposition = 'inline', $strIdentificacao = null, $dblIdDocumento = null)
    {

        try {

            LimiteSEI::getInstance()->configurarNivel2();

            $strCaminhoNomeArquivo = null;

            $objAnexoRN = new AnexoRN();
            $strCaminhoNomeArquivo = $objAnexoRN->obterLocalizacao($objAnexoDTO);

            return file_get_contents($strCaminhoNomeArquivo);

        } catch (Throwable $e) {

            if (strpos(strtoupper($e->__toString()), 'NO SUCH FILE OR DIRECTORY') !== false || strpos(strtoupper($e->__toString()), 'STAT FAILED FOR') !== false) {
                throw new InfraException('Erro acessando o sistema de arquivos.', $e);
            }

            throw $e;
        }
    }
}

$servidorSoap = new BeSimple\SoapServer\SoapServer( "wsia.wsdl", array ('encoding'=>'ISO-8859-1',
    'soap_version' => SOAP_1_1,
    'attachment_type'=>BeSimple\SoapCommon\Helper::ATTACHMENTS_TYPE_MTOM));
$servidorSoap->setClass ( "IaWS" );

//Só processa se acessado via POST
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $servidorSoap->handle();
}