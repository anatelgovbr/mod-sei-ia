# Manual do Webservice do Módulo SEI IA

 * Endereço do WSDL: http://[dominio_servidor]/sei/controlador_ws.php?servico=wsia 
 * Recomendado utilizar o software SOAP-UI para testes: http://sourceforge.net/projects/soapui/files/soapui/
 * O script de instalação do módulo cria automaticamente o Sistema "Usuario_IA", o Serviço "consultarDocumentoExternoIA" e adiciona no mencionado Serviço a Operação "Consultar Documentos".
 * Para utilizar o Assistente do SEI IA é necessário gerar a "Chave de Acesso" do mencionado Serviço "consultarDocumentoExternoIA", pelo menu Administração > Sistemas > "Usuario_IA" > Serviços > "consultarDocumentoExternoIA" > botão de ação "Gerar Chave de Acesso".
    - Copiar a chave de acesso gerada para salvar em variável própria "SEI_IAWS_KEY" no arquivo de configuração de ambiente "security.env" no Servidor de Soluções de IA.
 * Esse Webservice é acionado internamente na comunicação entre o SEI e o *Servidor de Soluções de IA*. Não deve ser utilizado para outras finalidades.
 
| Observações Gerais |
| ---- |
| Os métodos abaixo documentados somente funcionarão se o Serviço "consultarDocumentoExternoIA" do Sistema "Usuario_IA" possuir pelo menos a operação "Consultar Documentos" no menu Administração > Sistemas. |

## 1. Consultar Documento Externo IA

### Método “consultarDocumentoExternoIA”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Utilizar "Usuario_IA", criado pelo script de instalação do módulo. |
| IdentificacaoServico | Chave de Acesso gerada sobre o Serviço "consultarDocumentoExternoIA", criado pelo script de instalação do módulo. |
| IdDocumento | ID do Documento que a solução da API interna do Assistente enviará junto com a requisição.|

| Parâmetros de Saída |  |
| ---- | ---- |
| Mensagem | Status da transação, caso tenha sucesso irá retornar a mensagem "Arquivo enviado com sucesso." |
| NomeDocumento | Nome do Documento o qual foi consultado. |
| Anexo MTOM | O documento será retornado como um anexo utilizando o protocolo MTOM. |

| Observações |
| ---- |
| Informações sobre o protocolo MTOM: https://www.w3.org/TR/soap12-mtom/
Todas as requisições realizadas neste método são auditadas e estão disponíveis para consulta em Infra > Auditoria.
Este método somente é acionado pela API interna do Assistente existente no Servidor de Soluções de IA **quando** o usuário realiza citação de Documento Externo com indicação de intervalo de páginas. |

#### Regras de Negócio:
 * Caso a chave de acesso for inválida o webservice retornará a seguinte mensagem "Chave de Acesso inválida para o serviço [consultarDocumentoExternoIA] do sistema [Usuario_IA].".
 * Caso a operação "Consultar Documento" não estiver sido adicionada ao serviço o webservice retornará a seguinte mensagem "Operação não permitida pois não consta para a integração deste Sistema e Serviço ao menos a operação "Consultar Documento". Entre em contato com a Administração do SEI.".
 * Caso o documento não tiver sido encontrado no SEI retornará a seguinte mensagem "Documento não encontrado.".
 * Caso o documento não seja um documento Externo retornará a seguinte mensagem "Arquivo buscado não é um documento externo.".
