# Módulo SEI IA

## Requisitos
- Requisito Mínimo é o SEI 4.1.5.
   - Não é compatível com versões anteriores e em versões mais recentes é necessário conferir antes se possui compatibilidade.
   - Verificar valor da constante de versão no arquivo `/sei/web/SEI.php` ou, após logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- **Atenção**: O Módulo SEI IA somente funciona em conjunto com a instalação do [Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia?tab=readme-ov-file "Clique e acesse").
   - Todos os detalhes constantes no [README](https://github.com/anatelgovbr/sei-ia/blob/main/README.md) e no [INSTALL](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md) do *Servidor de Soluçõea de IA* são importantes, nessa ordem e exigindo leitura prévia integral **antes** de iniciar sua instalação.
   - Para instalar o *Servidor de Soluções de IA* é mandatório ter o Módulo SEI IA previamente instalado e configurado no SEI do ambiente correspondente. **Ou seja, antes, instale o módulo no SEI!**
- Antes de executar os scripts de instalação/atualização, o usuário de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.
- Os códigos-fonte do Módulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a versão mais recente: [https://github.com/anatelgovbr/mod-sei-ia/releases](https://github.com/anatelgovbr/mod-sei-ia/releases "Clique e acesse")
- Se já tiver instalado versão principal com a execução dos scripts de banco do módulo no SEI e no SIP, **em versões intermediárias basta sobrescrever os códigos** e não precisa executar os scripts de banco novamente.
   - Atualizações de versões intermediárias são melhorias apenas de código e são identificadas com o incremento somente do terceiro dígito da versão (p. ex. v4.1.1, v4.1.2) e não envolve execução de scripts de banco.

## ⚠️ Configurações Obrigatórias do PHP (Apache e CLI)

Antes de executar qualquer script de instalação ou atualização do Módulo SEI IA, **é obrigatório conferir as configurações do PHP tanto no ambiente Web (Apache/php-fpm) quanto no PHP CLI, já descristas no Manual de Instalação do SEI**.

É comum que o PHP utilizado pelo Apache carregue um `php.ini` diferente do PHP utilizado em linha de comando. Caso as configurações não estejam alinhadas, os scripts poderão falhar ou o módulo poderá apresentar comportamento inesperado.

**Diretivas que devem ser conferidas**

As diretivas PHP abaixo devem ser conferidas se estão em conformidade com o Manual de Instalação do SEI.

| Diretiva               | Valor                | Observação |
|------------------------|----------------------|------------|
| include_path           | /opt/infra/infra_php | Adicionar o diretório da InfraPHP. |
| default_charset        | ISO-8859-1           |            |
| session.gc_maxlifetime | 28800                | Tempo de sessão (ex.: 28800 = 8 horas). |
| short_open_tag         | 1                    |            |
| default_socket_timeout | 60                   |            |
| max_input_vars         | 1000                 |            |
| html_errors            | 0                    |            |
| session.cookie_secure  | 1                    | Opcional. Indica que o cookie de sessão trafegará somente via HTTPS. Antes de ativar, garantir que todos os links do SEI utilizem o prefixo `https://` (intranet, atalhos, acessos externos, integrações, etc.). Caso o usuário clique em um link `http://` estando logado, a sessão será perdida. |

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

## Guia de Inicialização de Conhecimento sobre a Instalação do SEI IA

O SEI IA funciona com dois componentes: o Módulo SEI IA, instalado no SEI, e o Servidor de Soluções de IA, executado em um servidor Linux com Docker. O órgão deve instalar e configurar o módulo no ambiente correspondente antes de instalar o servidor.

O Servidor de Soluções de IA deve ocupar um servidor exclusivo. Não compartilhe esse servidor com outras soluções.

**Leitura inicial**

Leia integralmente o [README do Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia?tab=readme-ov-file) e o seu [Manual de Instalação do Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md) antes de iniciar os trabalhos. Esses documentos detalham requisitos, configurações, implantação, validação, segurança, backup e atualização da solução.

**Materiais para estudo**

- Consulte o [Manual do Usuário do SEI IA](https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub) para conhecer as funcionalidades disponíveis.
- Assista à [Aula de Engenharia de Prompts aplicada ao SEI IA](https://www.youtube.com/watch?v=Q4oIQBLHKXo) para compreender a importância do letramento em IA Generativa.
- Consulte a [apresentação sobre o SEI IA e suas próximas versões](https://www.canva.com/design/DAGqoXRJj2Y/GlvF7VPtDapQIIpa4H0hDA/view).
	- O slide 18 informa a quantidade de instalações já existentes do SEI IA.
	- O slide 19 apresenta o roadmap das próximas versões.
- Veja o [vídeo de demonstração de uso prático](https://www.youtube.com/watch?v=BX3NdqivydA).

**Arquitetura e distribuição**

O SEI IA é o sétimo módulo desenvolvido e distribuído pela Anatel por meio do GitHub. O [Módulo SEI IA](https://github.com/anatelgovbr/mod-sei-ia) e o [Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia) compõem a arquitetura da solução.

**Infraestrutura e comunicação**

O Servidor de Soluções de IA requer, como referência mínima, um servidor Linux com 16 núcleos de 2,10 GHz, 128 GB de memória RAM, Docker Engine versão 27.1.1 ou posterior e Docker Compose versão 2.29 ou posterior. O órgão deve dimensionar o armazenamento de acordo com o volume de processos e documentos do SEI, pois os bancos de dados e os índices crescem com esse volume.
- Veja mais informações sobre os requisitos de hardware e software no [Manual de Instalação do Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia/blob/main/docs/INSTALL.md).

Em ambiente não-produtivo, o órgão pode avaliar uma alocação menor de recursos e desligar o servidor durante períodos sem uso. Ligue o servidor para realizar atualizações e procedimentos de pré-produção. O manual não recomenda o uso do Windows com o Subsistema do Windows para Linux em ambiente de produção.

**Consumo de serviços de inteligência artificial**

A solução utiliza APIs de inteligência artificial como serviço para os modelos de linguagem de grande porte (LLMs) e a geração de embeddings (vetorização).

Apenas como referência, reserve US$ 400 brutos **por mês** na console do Cloud Provider **para cada 1.000 usuários internos**. Esse valor é uma estimativa e não representa o preço final. O contrato pode incluir a taxa do Broker e outros acréscimos.
- O [processo de contratação da Anatel](https://sei.anatel.gov.br/sei/modulos/pesquisa/md_pesq_processo_exibir.php?92AHliMZAlcgWWxm2w2qy-GDVz335h7FYd_mrGCDn9UEi2XStDTUZKOJ_damneHH9mwnSMIjw0l3j23hD04Tsmk90zHN2lc3Yg9C8WB6vIFDMdpaSLfMyGju2GmZCvwD) reúne Documento de Formalização da Demanda (DFD), Estudo Técnico Preliminar (ETP), Termo de Referência (TR) e Informe de Pesquisa de Preços. Esses documentos podem ajudar na contratação do serviço de **Broker MultiCloud** do Serpro, quando o órgão ainda não possui contrato para esse serviço.

O valor reservado contempla, por enquanto, as APIs de inteligência artificial e de geração de embeddings. A estimativa inclui margem para funcionalidades futuras. O consumo real varia conforme o uso da solução **e depende diretamente** das campanhas de letramento, oficinas e capacitações sobre engenharia de prompts.

**Importância da Galeria de Prompts no SEI IA**

É muito importante que o órgão cadastre na **Galeria de Prompts** do SEI IA os prompts avançados indicados em seu [Manual do Usuário](https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub#h.cay75n32gnc8) e crie mais prompts adequados às atividades do órgão.

A Galeria de Prompts iniciando com o máximo de prompts avançados serve como referência e orienta os usuários na criação de prompts mais completos, organizados e eficazes.

Os prompts avançados indicados no Manual do Usuário são versionados no seguinte projeto do GitHub: [https://github.com/anatelgovbr/prompts](https://github.com/anatelgovbr/prompts)

## Orientações Negociais
1. Mais uma vez reforçamos que antes de instalar o [Servidor de Soluções de IA](https://github.com/anatelgovbr/sei-ia?tab=readme-ov-file "Clique e acesse") é mandatório ter o Módulo SEI IA previamente instalado e configurado no SEI do ambiente correspondente.
2. Imediatamente após a instalação com sucesso do Módulo SEI IA no SEI, usuário com permissão de "Administrador" do SEI deve fazer novo login no SEI e acessar os menus de administração do Módulo pelo seguinte caminho: Administração > Inteligência Artificial.
	- Somente com tudo parametrizado na Administração do Módulo será possível seu uso adequado.
 	- A funcionalidade de "Pesquisa de Documentos" (recomendação de documentos similares) somente funcionará depois que configurar pelo menos um Tipo de Documento como Alvo da Pesquisa no menu Administração > Inteligência Artificial > Pesquisa de Documentos (na seção "Tipos de Documentos Alvo da Pesquisa").
3. O script de banco do SIP já cria todos os Recursos e Menus e os associam automaticamente ao Perfil "Básico" ou ao Perfil "Administrador".
	- Independente da criação de outros Perfis, os recursos indicados para o Perfil "Básico" ou "Administrador" devem manter correspondência com os Perfis dos usuários internos que utilizarão o Módulo e dos usuários Administradores.
	- Tão quanto ocorre com as atualizações do SEI, versões futuras deste Módulo continuarão a atualizar e criar Recursos e associá-los apenas aos Perfis "Básico" e "Administrador".
	- Todos os recursos do Módulo iniciam pelo prefixo **"md_ia_"**.
	- **Atenção**: O recurso "md_ia_adm_config_assist_ia_consultar" define quem visualiza o Assistente do SEI IA.
		- Caso o órgão tenha perfil separado para colaboradores, por exemplo "Colaborador (Básico sem Assinatura)", e queira ampliar o uso do Assistente, deve incluir o mencionado recurso no Perfil pertinente.
		- Caso o órgão queira restringir quem pode utilizar o Assistente, precisa retirar o mencionado recurso do Perfil "Básico" e incluir no Perfil pertinente.
		- O custo do uso das APIs é muito baixo e pode ser desnecessário restringir o uso do Assistente.
		- É de responsabilidade do órgão essa avaliação, sobre ampliar ou restringir o uso do Assistente.
4. Acesse o [Manual do Usuário do SEI IA](https://docs.google.com/document/d/e/2PACX-1vRsKljzHcKwRfdW7IcnFA1EHNPIInog9Mqpu58xEFzRMfZ5avrLhYbwUjPkXuTDFKFEPnev4ASJ-5Dm/pub "Clique e acesse") para conhecer suas funcionalidades.

## Erros ou Sugestões
1. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-ia/issues) no repositório do GitHub do módulo se ocorrer erro na execução dos scripts de banco do módulo no SEI ou no SIP acima.
2. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-ia/issues) no repositório do GitHub do módulo se ocorrer erro na operação do módulo.
3. Na abertura da Issue utilizar o modelo **"1 - Reportar Erro"**. 
