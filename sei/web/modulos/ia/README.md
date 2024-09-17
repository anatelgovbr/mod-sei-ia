# Módulo SEI Inteligência Artificial

## Requisitos
- Requisito Mínimo é o SEI 4.0.12 instalado/atualizado - Não é compatível com versões anteriores e em versões mais recentes é necessário conferir antes se possui compatibilidade.
   - Verificar valor da constante de versão no arquivo /sei/web/SEI.php ou, após logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- Antes de executar os scripts de instalação/atualização, o usuário de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.
- Os códigos-fonte do Módulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a versão mais recente: [https://github.com/anatelgovbr/mod-sei-ia/releases](https://github.com/anatelgovbr/mod-sei-ia/releases "Clique e acesse")
- Se já tiver instalado a versão principal com a execução dos scripts de banco do módulo no SEI e no SIP, então basta sobrescrever os códigos e não precisa executar os scripts de banco novamente.
   - Atualizações apenas de código são identificadas com o incremento apenas do terceiro dígito da versão (p. ex. v4.1.1, v4.1.2) e não envolve execução de scripts de banco.

## Procedimentos para Instalação
1. Fazer backup dos bancos de dados do SEI e do SIP.
2. Carregar no servidor os arquivos do módulo nas pastas correspondentes nos servidores do SEI e do SIP.
   - **Caso se trate de atualização de versão anterior do Módulo**, antes de copiar os códigos-fontes para a pasta "/sei/web/modulos/ia", é necessário excluir os arquivos anteriores pré existentes na mencionada pasta, para não manter arquivos de códigos que foram renomeados ou descontinuados.
3. Editar o arquivo "/sei/config/ConfiguracaoSEI.php", tomando o cuidado de usar editor que não altere o charset do arquivo, para adicionar a referência à classe de integração do módulo e seu caminho relativo dentro da pasta "/sei/web/modulos" na array 'Modulos' da chave 'SEI':

		'SEI' => array(
			...
			'Modulos'=>array(
				'IaIntegracao' => 'ia',
				),
			),

4. Antes de seguir para os próximos passos, é importante conferir se o Módulo foi corretamente declarado no arquivo "/sei/config/ConfiguracaoSEI.php". Acesse o menu **Infra > Módulos** e confira se consta a linha correspondente ao Módulo, pois, realizando os passos anteriores da forma correta, independente da execução do script de banco, o Módulo já deve ser reconhecido na tela aberta pelo menu indicado.
5. Rodar o script de banco "/sip/scripts/sip_atualizar_versao_modulo_ia.php" em linha de comando no servidor do SIP, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sip/scripts/sip_atualizar_versao_modulo_ia.php > atualizacao_ia_sip.log

6. Rodar o script de banco "/sei/scripts/sei_atualizar_versao_modulo_ia.php" em linha de comando no servidor do SEI, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sei/scripts/sei_atualizar_versao_modulo_ia.php > atualizacao_modulo_ia_sei.log

7. **IMPORTANTE**: Na execução dos dois scripts de banco acima, ao final deve constar o termo "FIM", o "TEMPO TOTAL DE EXECUÇÃO" e a informação de que a instalação/atualização foi realizada com sucesso na base de dados correspondente (SEM ERROS). Do contrário, o script não foi executado até o final e algum dado não foi inserido/atualizado no respectivo banco de dados, devendo recuperar o backup do banco e repetir o procedimento.
   - Constando ao final da execução do script as informações indicadas, pode logar no SEI e SIP e verificar no menu **Infra > Parâmetros** dos dois sistemas se consta o parâmetro "VERSAO_MODULO_IA" com o valor da última versão do módulo.
8. Em caso de erro durante a execução do script, verificar (lendo as mensagens de erro e no menu Infra > Log do SEI e do SIP) se a causa é algum problema na infraestrutura local ou ajustes indevidos na estrutura de banco do core do sistema. Neste caso, após a correção, deve recuperar o backup do banco pertinente e repetir o procedimento, especialmente a execução dos scripts de banco indicados acima.
	- Caso não seja possível identificar a causa, entrar em contato com: Nei Jobson - neijobson@anatel.gov.br
9. Após a execução com sucesso, com um usuário com permissão de Administrador no SEI, seguir os passos dispostos no tópico "Orientações Negociais" mais abaixo.

## Orientações Negociais
1. Imediatamente após a instalação com sucesso, com usuário com permissão de "Administrador" do SEI, acessar os menus de administração do Módulo pelo seguinte caminho: Administração > Inteligência Artificial. Somente com tudo parametrizado adequadamente será possível o uso do módulo.
2. O script de banco do SIP já cria todos os Recursos e Menus e os associam automaticamente ao Perfil "Básico" ou ao Perfil "Administrador".
	- Independente da criação de outros Perfis, os recursos indicados para o Perfil "Básico" ou "Administrador" devem manter correspondência com os Perfis dos Usuários internos que utilizarão o Módulo e dos Usuários Administradores do Módulo.
	- Tão quanto ocorre com as atualizações do SEI, versões futuras deste Módulo continuarão a atualizar e criar Recursos e associá-los apenas aos Perfis "Básico" e "Administrador".
	- Todos os recursos do Módulo iniciam pelo prefixo **"md_ia_"**.
3. Funcionalidades do Módulo SEI Correios:
	- 3.1. Administração:
		- Inteligência Artificial > Configurações de Similaridade:
			- Definir se a funcionalidade "Processos Similares" será exibida.
            - Definir a quantidade de processos a serem listados na funcionalidade "Processos Similares", sendo o mínimo 1 e o máximo 15. O valor padrão é 5.
            - Definir as orientações que serão exibidas na tela do SEI IA na seção da funcionalidade "Processos Similares".
            - Definir o percentual de relevância do conteúdo dos Documentos, o valor deve ser maior que zero e não pode exceder 100%. O valor padrão é 70%.
            - Definir o percentual de relevância dos Metadados, o valor deve ser maior que zero e não pode exceder 100%. O valor padrão é 100%.
            - Definir os metadados e seu percentual de relevância, o sistema obriga manter o valor de 100% nessa distribuição de percentuais. O percentual de distribuição é sobre o valor do que já foi definido no campo "Percentual de Relevância dos Metadados" Por padrão já são cadastrados na instalação 7 tipos de metadados com seus valores padrões.
        - Inteligência Artificial > Configurações do Assistente IA:
            - Definir se a funcionalidade "Assistente IA" será exibida.
            - Definir as orientações que serão exibidas no ícone de ajuda no "Assistente IA".
            - Definir o Limite Geral de Tokens que um usuário pode utilizar por dia (milhões de tokens).
            - Caso seja necessário você pode definir um Limite maior de tokens para Usuários específicos.
            - Definir o LLM que deseja utilizar.
            - Definir o Prompt System para o LLM.
        - Inteligência Artificial > Documentos Relevantes:
            - Parametrizar quais tipos de documentos serão considerados relevantes para a funcionalidade de "Processos Similares". 
        - Inteligência Artificial > Mapeamento das Integrações:
            - Parametrizar a URL do Endpoint de Autenticação da funcionalidade de "API Interna de interface entre SEI IA e LLM de IA Generativa".
            - Parametrizar a URL do Endpoint de Autenticação da funcionalidade de "Autenticação junto à Solução de Inteligência Artificial do SEI". 
        - Inteligência Artificial > Objetivos de Desenvolvimento Sustentável da ONU:
            - Definir se a funcionalidade "Objetivos de Desenvolvimento Sustentável da ONU" será exibida.
            - Definir se a funcionalidade "Objetivos de Desenvolvimento Sustentável da ONU" será exibida para classificação por Usuários Externos.
            - Definir se irá exi
            - Definir as orientações que serão exibidas no ícone de ajuda no "Assistente IA".
            - Definir o Limite Geral de Tokens que um usuário pode utilizar por dia (milhões de tokens).
            - Caso seja necessário você pode definir um Limite maior de tokens para Usuários específicos.
            - Definir o LLM que deseja utilizar.
            - Definir o Prompt System para o LLM.        
	- 3.2. Unidade de Expedição:
		- Expedição pelos Correios:
			- Gerar PLP: 
				- Tela onde lista as solicitações de expedições realizadas pelos Usuários e gera a PLP(pré-lista de postagem), sendo possível selecionar o "Formato de Expedição do Objeto" e visualizar a "Solicitação de Expedição" cadastrada.
			- Expedir PLP:
				- Lista as PLPs(pré-lista de postagem) geradas para expedição e realiza o "Expedir PLP".
					- Antes de "Concluir a Expedição da PLP" e possível Imprimir os Documentos, Envelopes, ARs e Voucher da PLP.
			- Consultar PLPs Geradas:
				- Tela onde lista as PLPs Geradas e visualiza o detalhamento é sendo possível Imprimir os Documentos, Envelopes, ARs e Voucher da PLP.
			- Processamento de Retorno de AR:
				- Tela onde Lista o Processamento de Retorno de AR e realiza o processamento em lote.
			- ARs Pendentes de Retorno:
				- Tela onde lista os ARs Pendentes de Retorno e "Gerar Documento de Cobrança" vinculado aos dias em atraso do processo.
	- 3.3 Relatórios:
		- Correios:
			- Expedições Solicitadas pela Unidade:
				- Tela onde lista as Expedições Solicitadas pela Unidade.
	- 3.4. Usuários:
		- Iniciar Processo > Ofício > Solicitar Expedição pelos Correios:
			- Solicitar Expedição pelos Correios:
				- Após iniciar um Processo e vincular um documento do tipo "Ofício" é realizar a assinatura do documento será exibido o icone "Solicitar Expedição pelos Correios".
				- Na tela de "Solicitar Expedição pelos Correios" é possível alterar os dados dos "Documentos Expedidos" e preencher o "Formato de Expedição dos Documentos" é incluir uma "Observação".
