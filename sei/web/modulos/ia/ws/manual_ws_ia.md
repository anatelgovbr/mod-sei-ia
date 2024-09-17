# Manual do Webservice do Módulo Inteligência Artificial

 * Endereço do WSDL: http://[dominio_servidor]/sei/controlador_ws.php?servico=wsia 
 * Recomendado utilizar o software SOAP-UI para testes: http://sourceforge.net/projects/soapui/files/soapui/
 * Todas as operações abaixo somente funcionam se o Serviço correspondente do Sistema indicado possuir pelo menos a operação "Consultar Documentos" no menu Administração > Sistemas.
 * Para fazer uso do Assistente IA é necessário gerar e utilizar a chave do Serviço consultarDocumentoExternoIA a partir do menu Administração > Sistemas > Usuario_IA > Serviços > consultarDocumentoExternoIA.
 
| Observações Gerais |
| ---- |
| Os métodos abaixo documentados somente funcionarão se o Serviço correspondente do Sistema indicado possuir pelo menos a operação "Consultar Documentos" no menu Administração > Sistemas. |

## 1. Consultar Documento Externo IA

### Método “consultarDocumentoExternoIA”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| Chave | Chave do serviço gerada no cadastro de Serviços do SEI no menu Administração > Sistemas > Usuario_IA > Serviços > consultarDocumentoExternoIA. |
| IdDocumento | ID do Documento que deseja consultar.|

| Parâmetros de Saída |  |
| ---- | ---- |
| Mensagem | Status da transação, caso tenha sucesso irá retornar a mensagem "Arquivo enviado com sucesso." |
| NomeDocumento | Nome do Documento o qual foi consultado. |
| Anexo MTOM | O documento será retornado como um anexo utilizando o protocolo MTOM. |

| Observações |
| ---- |
| Para mais informações sobre o protocolo MTOM consulte https://www.w3.org/TR/soap12-mtom/ 
Todas as requisições realizadas neste método são auditadas e estão disponíveis para consulta em Infra > Auditoria. |

### Regras de Negócio:
 * Caso a chave de acesso for inválida o webservice retornará a seguinte mensagem "Chave de Acesso inválida para o serviço [consultarDocumentoExternoIA] do sistema [Usuario_IA].".
 * Caso a operação "Consultar Documento" não estiver sido adicionada ao serviço o webservice retornará a seguinte mensagem "Operação não permitida pois não consta para a integração deste Sistema e Serviço ao menos a operação "Consultar Documento". Entre em contato com a Administração do SEI.".
 * Caso o documento não tiver sido encontrado no SEI retornará a seguinte mensagem "Documento não encontrado.".
 * Caso o documento não seja um documento Externo retornará a seguinte mensagem "Arquivo buscado não é um documento externo.".
