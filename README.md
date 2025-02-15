# Módulo SEI IA

## Requisitos
- Requisito Mínimo é o SEI 4.0.12 (com ajustes em códigos para ativar ponto de extensão de módulo) ou diretamente no SEI 4.1.4.
   - Não é compatível com versões anteriores e em versões mais recentes é necessário conferir antes se possui compatibilidade.
   - Instalar apenas no SEI 4.0.12 ou no SEI 4.1.4 em diante.
      - No SEI 4.0.12, **antes**, sobrescrever os três arquivos do core do SEI disponibilizados na pasta `Arquivos_APENAS_para_SEI-4.0.12`.
      - No SEI 4.0.12.15, **antes**, sobrescrever os três arquivos do core do SEI disponibilizados na pasta `Arquivos_APENAS_para_SEI-4.0.12.15_MGI`.
   - Verificar valor da constante de versão do SEI no arquivo `/sei/web/SEI.php` ou, após logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- **Atenção**: O Módulo SEI IA somente funciona em conjunto com a instalação do [Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia?tab=readme-ov-file "Clique e acesse").
   - Todos os detalhes constantes no [README](https://github.com/anatelgovbr/sei-ia/blob/main/README.md) e no [INSTALL](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md) do *Servidor de Soluçõea de IA* são importantes, nessa ordem e exigindo leitura prévia integral **antes** de iniciar sua instalação.
   - Para instalar o *Servidor de Soluções de IA* é mandatório ter o Módulo SEI IA previamente instalado e configurado no SEI do ambiente correspondente. **Ou seja, antes, instale o módulo no SEI!**
- Antes de executar os scripts de instalação/atualização, o usuário de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.
- Os códigos-fonte do Módulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a versão mais recente: [https://github.com/anatelgovbr/mod-sei-ia/releases](https://github.com/anatelgovbr/mod-sei-ia/releases "Clique e acesse")
- Se já tiver instalado versão principal com a execução dos scripts de banco do módulo no SEI e no SIP, **em versões intermediárias basta sobrescrever os códigos** e não precisa executar os scripts de banco novamente.
   - Atualizações de versões intermediárias são melhorias apenas de código e são identificadas com o incremento somente do terceiro dígito da versão (p. ex. v4.1.1, v4.1.2) e não envolve execução de scripts de banco.

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
9. Após a execução com sucesso, com um usuário com permissão de Administrador no SEI, seguir os passos dispostos no tópico "Orientações Negociais" mais abaixo.

## Orientações Negociais
1. Mais uma vez reforçamos que antes de instalar o [Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia?tab=readme-ov-file "Clique e acesse") é mandatório ter o Módulo SEI IA previamente instalado e configurado no SEI do ambiente correspondente.
2. Imediatamente após a instalação com sucesso do Módulo SEI IA no SEI, usuário com permissão de "Administrador" do SEI deve fazer novo login no SEI e acessar os menus de administração do Módulo pelo seguinte caminho: Administração > Inteligência Artificial.
	- Somente com tudo parametrizado na Administração do Módulo será possível seu uso adequado.
 	- A funcionalidade de "Pesquisa de Documentos" (recomendação de documentos similares) somente funcionará depois que configurar pelo menos um Tipo de Documento como Alvo da Pesquisa no menu Administração > Inteligência Artificial > Pesquisa de Documentos (na seção "Tipos de Documentos Alvo da Pesquisa").
 	- Atenção para o tópico [Ajustar Permissão por IP no Solr do SEI](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md#ajustar-permiss%C3%A3o-por-ip-no-solr-do-sei) no INSTALL, para configurar a permissão de acesso ao Solr do SEI correspondente, e para a criação prévia de usuário de aplicação de SOMENTE LEITURA no banco do SEI correspondente, destacada no passo 6 do tópico [Passos para Instalação](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md#passos-para-instala%C3%A7%C3%A3o).
3. O script de banco do SIP já cria todos os Recursos e Menus e os associam automaticamente ao Perfil "Básico" ou ao Perfil "Administrador".
	- Independente da criação de outros Perfis, os recursos indicados para o Perfil "Básico" ou "Administrador" devem manter correspondência com os Perfis dos usuários internos que utilizarão o Módulo e dos usuários Administradores.
	- Tão quanto ocorre com as atualizações do SEI, versões futuras deste Módulo continuarão a atualizar e criar Recursos e associá-los apenas aos Perfis "Básico" e "Administrador".
	- Todos os recursos do Módulo iniciam pelo prefixo **"md_ia_"**.
	- **Atenção**: O recurso "md_ia_adm_config_assist_ia_consultar" define quem visualiza o Assistente do SEI IA.
		- Caso o órgão tenha perfil separado para colaboradores, por exemplo "Colaborador (Básico sem Assinatura)", e queira ampliar o uso do Assistente, deve incluir o mencionado recurso no Perfil pertinente.
		- Caso o órgão queira restringir quem pode utilizar o Assistente, precisa retirar o mencionado recurso do Perfil "Básico" e incluir no Perfil pertinente.
		- O custo do uso da API do GPT-4o é muito baixo e pode ser desnecessário restringir o uso do Assistente.
		- É de responsabilidade do órgão essa avaliação sobre ampliar ou restringir o uso do Assistente.
4. Acesse o [Manual do Webservice do Módulo SEI IA](https://github.com/anatelgovbr/mod-sei-ia/blob/master/sei/web/modulos/ia/ws/manual_ws_ia.md "Clique e acesse").
	- Esse Webservice é acionado internamente na comunicação entre o SEI e o *Servidor de Soluções de IA*.
	- Não deve ser utilizado para outras finalidades.
5. Acesse o [Manual do Usuário do SEI IA](https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub "Clique e acesse") para conhecer suas funcionalidades.

## Erros ou Sugestões
1. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-ia/issues) no repositório do GitHub do módulo se ocorrer erro na execução dos scripts de banco do módulo no SEI ou no SIP acima.
2. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-ia/issues) no repositório do GitHub do módulo se ocorrer erro na operação do módulo.
3. Na abertura da Issue utilizar o modelo **"1 - Reportar Erro"**.
