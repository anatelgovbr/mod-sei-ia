<script>
    if (typeof jQuery === 'undefined') {
        var s = document.createElement('script');
        s.src = "/infra_js/jquery/jquery-3.7.0.min.js";
        document.head.appendChild(s);
    }
</script>

<script type="text/javascript">
    <?php require_once dirname(__FILE__) . '/lib/gpt-tokenizer/cl100k_base.js'; ?>
</script>
<script src="modulos/ia/lib/highlight.js/highlight.min.js"></script>
<script src="modulos/ia/lib/popper/popper.min.js"></script>
<script src="modulos/ia/lib/popper/tippy.js"></script>
<script src="modulos/ia/lib/marked/marked.min.js"></script>
<script src="modulos/ia/lib/bootstrap@4.6.2/bootstrap.bundle.min.js"></script>
<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {


        // --- INICIO DO BLOCO DRAG AND DROP ---

        // 1. Previne comportamento padrão em todo o documento (impede abrir nova guia)
        $(document).on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $(document).on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        // 2. Configura a área de drop (usando a área do chat widget-content)
        var $dropZone = $('.widget-content');

        // Efeito visual quando arrasta o arquivo para dentro (opcional)
        $dropZone.on('dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('background-color', '#f1f8ff'); // Leve azul para indicar área ativa
            $(this).css('border', '2px dashed #2494da');
        });

        $dropZone.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Verifica se realmente saiu da área principal e não entrou num filho
            if (e.target === this) {
                $(this).css('background-color', '');
                $(this).css('border', 'none');
            }
        });

        // 3. O evento DROP real - Aqui capturamos o arquivo
        $dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Remove estilos visuais de destaque
            $(this).css('background-color', '');
            $(this).css('border', 'none');

            // Pega os arquivos do evento de drop (dataTransfer)
            var files = e.originalEvent.dataTransfer.files;

            if (files && files.length > 0) {
                // Percorremos os arquivos que foram soltos
                for (let i = 0; i < files.length; i++) {
                    const arquivo = files[i];

                    // --- AQUI ENTRA A VALIDAÇÃO ---
                    if (!validarArquivo(arquivo)) {
                        continue;
                    }

                    if (!validarTamanhoTotalArquivos(arquivosSelecionados.concat([arquivo]))) {
                        continue;
                    }

                    arquivosSelecionados.push(arquivo);
                }

                // Atualiza os chips na tela
                renderizarListaArquivos();

                // Foca no campo de mensagem após soltar
                $('#mensagem').focus();
            }
        });

        $('.send-message-input').on('keydown input paste', function(event) {
            // ENTER sem Shift ? enviar
            if (event.type === 'keydown' && event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                setTimeout(() => {
                    controlaPreenchimentoCampoMensagem();
                    enviarMensagem();
                    controlaTamanhoInputDigitacao();
                }, 500);
                return;
            }
            if (event.type === 'paste') {
                event.preventDefault();
                inserirTextoColadoMensagem(event.originalEvent.clipboardData.getData('text/plain'));
            }
            // qualquer outra mudança no valor (inclui paste)
            if (event.type === 'input') {
                destacarReferenciasSimplesMensagem();
                controlaPreenchimentoCampoMensagem();
                controlaTamanhoInputDigitacao();
            }
        });

        $('[name=star]').click(function() {
            var valueis = $(this).attr('id-message');
            alert(valueis);
        });

        tippy(".iconeOrientacoesGerais", {
            content: "Orientações Gerais",
        });

        tippy(".iconeConfiguracoes", {
            content: "Configurações",
        });

        tippy("#expandirAssistente", {
            content: "Expandir Assistente",
        });

        tippy("#reduzirAssistente", {
            content: "Reduzir Assistente",
        });

        tippy("#alternarPainelTopicos", {
            content: "Recolher menu lateral",
        });

        tippy(".chat-open", {
            content: "Inteligência Artificial",
        });

        tippy(".close-chat", {
            content: "Minimizar",
        });

        tippy("#botaoEnviarMensagem", {
            content: "Enviar Mensagem",
        });

        tippy("#botaoPromptFavorito", {
            content: "Prompts Favoritos",
        });

        tippy("#botaoGaleriaPrompt", {
            content: "Galeria de Prompts",
        });

        tippy("#adicionarTopico", {
            content: "Novo Tópico",
        });

        tippy("#galeriaPrompts", {
            content: "Galeria de Prompts",
        });

        tippy("#promptsFavoritos", {
            content: "Prompts Favoritos",
        });

        tippy("#botaoMemoriaTopico", {
            content: "Utilizar Memória do Tópico",
        });
        atualizarTooltipMemoriaTopico();

        tippy("#botaoBuscarWeb", {
            content: "Buscar na Web",
        });

        tippy(".iconeAdicionarTopico", {
            content: "Novo Tópico",
        });

        var painelTopicos = document.getElementById('painelTopicos');
        if (painelTopicos && typeof tippy.delegate === 'function') {
            var painelTopicosEmRolagem = false;
            var temporizadorRolagemTopicos;
            var tooltipTopicoAtivo = null;

            var tooltipTopicoDelegado = tippy.delegate(painelTopicos, {
                target: '.selecionaTopico, .rename, .arquivo',
                content: function(elemento) {
                    if (elemento.classList.contains('rename')) {
                        return 'Renomear Tópico';
                    }
                    if (elemento.classList.contains('arquivo')) {
                        return 'Arquivar Tópico';
                    }
                    return 'Selecionar Tópico';
                },
                delay: [500, 0],
                popperOptions: {
                    strategy: 'fixed'
                },
                onShow: function(instance) {
                    if (painelTopicosEmRolagem) {
                        return false;
                    }
                    tooltipTopicoAtivo = instance;
                },
                onHidden: function(instance) {
                    if (tooltipTopicoAtivo === instance) {
                        tooltipTopicoAtivo = null;
                    }
                }
            });

            painelTopicos.addEventListener('scroll', function() {
                painelTopicosEmRolagem = true;
                tooltipTopicoDelegado.disable();
                if (tooltipTopicoAtivo) {
                    tooltipTopicoAtivo.hide();
                }
                clearTimeout(temporizadorRolagemTopicos);
                temporizadorRolagemTopicos = setTimeout(function() {
                    painelTopicosEmRolagem = false;
                    tooltipTopicoDelegado.enable();
                }, 150);
            }, {
                passive: true
            });
        }

        tippy("#botaoAnexarArquivo", {
            content: "Anexar Arquivos",
        });

        tippy("#botaoModelos", {
            content: "Selecionar Modelo",
        });

        tippy("#botaoEsforcoModelo", {
            content: "Selecionar Esforço do Modelo",
        });

        atualizarRotuloModeloSelecionado();
        atualizarRotuloEsforcoSelecionado();
        setTimeout(() => {
            fecharChat();
            $("#chat_ia").css("display", "block");
        }, 200);
        if (document.getElementById("ifrVisualizacao") || document.getElementById("ifrConteudoVisualizacao")) {
            setInterval(() => {
                reposicionaBotao();
            }, 1000);
        }
    });
    $(document).on('click', '.submenu-item', function() {
        const titulo = $(this).text().trim();
        const mensagem = mensagensSugeridas[titulo] || "";
        $(".submenu-opcoes").hide();
        $("#conversa").html("");
        identificarProtocolo(mensagem);
    });

    function atualizarRotuloModeloSelecionado() {
        const modeloSelecionado = document.querySelector('#menuModelos .modelo-item[aria-checked="true"]');
        if (modeloSelecionado) {
            document.querySelector('#botaoModelos .rotulo-modelos').textContent = modeloSelecionado.querySelector('.rotulo-opcao-selecao').textContent.trim();
        }
    }

    function atualizarRotuloEsforcoSelecionado() {
        const esforcoSelecionado = document.querySelector('#menuEsforcoModelo .esforco-item[aria-checked="true"]');
        if (esforcoSelecionado) {
            document.querySelector('#botaoEsforcoModelo .rotulo-esforco').textContent = esforcoSelecionado.querySelector('.rotulo-opcao-selecao').textContent.trim();
        }
    }

    $(document).on('click', '.opcao-selecao-item', function() {
        const menu = this.closest('.submenu-opcoes');
        const botao = menu.closest('.botao-com-menu').querySelector('[aria-expanded]');
        menu.querySelectorAll('[role="menuitemradio"]').forEach(function(item) {
            item.setAttribute('aria-checked', 'false');
        });
        this.setAttribute('aria-checked', 'true');
        if (this.classList.contains('modelo-item')) {
            atualizarRotuloModeloSelecionado();
        }
        if (this.classList.contains('esforco-item')) {
            atualizarRotuloEsforcoSelecionado();
        }
        menu.style.display = 'none';
        botao.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.botao-com-menu')) {
            document.querySelectorAll('.submenu-opcoes').forEach(function(el) {
                el.style.display = 'none';
                const botao = el.closest('.botao-com-menu').querySelector('[aria-expanded]');
                if (botao) {
                    botao.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });

    const mensagensSugeridas = {
        "Redigir e-mail para encaminhar documento": "Solicite o número do protocolo do documento para o usuário, orientando que deve informar utilizando #\n \nUma vez recebido o número do protocolo, utilize-o para redigir um e-mail usando tom impessoal para dar conhecimento sobre o conteúdo resumido do documento.\n \nReserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Redigir Ofício para dar conhecimento sobre documento": "Solicite o nome do destinatário, seu cargo e entidade.\n" +
            "Solicite o número do protocolo do documento para o usuário, orientando que deve informar utilizando #\n" +
            "\n" +
            "Uma vez recebido o nome do destinatário e o número do protocolo, utilize-o para redigir um Ofício destinado ao destinatário informado pelo usuário para dar conhecimento sobre o conteúdo do documento fornecido. Seguir as orientações do Manual de Redação da Presidência da República do Brasil sobre redação oficial e edição de Ofícios.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Redigir texto jornalístico sobre decisão tomada em documento": "Solicite o número do protocolo do documento para o usuário, orientando que deve informar utilizando #\n" +
            "\n" +
            "Uma vez recebido o número do protocolo, utilize-o para redigir um texto jornalístico para divulgação sobre a decisão tomada a partir do documento fornecido pelo usuário.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Redigir resumo sobre documento com lista de solicitações da requerente": "Solicite o número do protocolo do documento para o usuário, orientando que deve informar utilizando #\n" +
            "\n" +
            "Uma vez recebido o número do protocolo, utilize-o para redigir um resumo sobre o conteúdo do documento junto com uma lista das solicitações da requerente.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Resumir documento destacando as solicitações": "Solicite o número do protocolo do documento para o usuário, orientando que deve informar utilizando #\n" +
            "\n" +
            "Uma vez recebido o número do protocolo, utilize-o para redigir um resumo sobre o conteúdo do documento dando maior destaque aos pontos relacionados com as solicitações da requerente.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Resumir anotações de uma reunião": "Pode resumir as anotações da minha reunião?\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida ou peça para citar o protocolo do documento com as anotações da reunião utilizando # ou colar diretamente as anotações da reunião ou mesmo a transcrição gerada pelo Teams.",

        "Resumir artigo de pesquisa": "Pode resumir um artigo de pesquisa para mim? Preciso de um resumo conciso das principais descobertas e conclusões.\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida ou peça para citar o protocolo do documento com a pesquisa utilizando # ou colar diretamente o texto da correspondente.",

        "Resumir relatório técnico": "Pode resumir um relatório técnico destacando os pontos de encaminhamento?\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida ou peça para citar o protocolo do documento com o relatório técnico utilizando # ou colar diretamente o texto correspondente.",

        "Sugerir revisão sobre uma minuta de notícia": "Como um jornalista corporativo com mais de 20 anos de experiência, deve revisar cuidadosa e aperfeiçoar minuta de notícia.\n" +
            "\n" +
            "A notícia revisada deve sempre ter um título em negrito e uma linha fina abaixo do título em itálico.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no texto da minuta de notícia que o usuário vai fornecer em seguida.\n" +
            "\n" +
            "Solicitar que a minuta de notícia seja fornecida na próxima mensagem, respondendo \"Cole o texto da minuta de notícia para que eu possa revisá-la seguindo as instruções acima, utilizando linguagem simples e ordem direta, aprimorando coesão, clareza e eliminação de redundâncias\"",

        "Sugerir tópicos para uma apresentação": "Pode me ajudar a sugerir tópicos para uma apresentação? Comece perguntando sobre o público-alvo e temática principal da apresentação.",

        "Sugerir insights a partir de uma planilha": "Solicite o número do protocolo do documento com planilha para o usuário, orientando que deve informar utilizando # e que seja sobre uma planilha com dados estruturados.\n" +
            "\n" +
            "Uma vez recebido o número do protocolo da planilha, utilize-o para fazer uma análise, estatísticas descritivas e sugerir insights sobre os seus dados.\n" +
            "\n" +
            "Reserve o tempo necessário para pensar, sem pressa. Não invente nada e se baseie exclusivamente no conteúdo do documento que o usuário vai fornecer em seguida.",

        "Sugerir argumentos a favor de um posicionamento": "Sugerir argumentos a favor de um determinado posicionamento para utilização em um documento técnico. Comece perguntando qual assunto e o posicionamento que se pretende defender.",

        "Faça um plano de viagem à trabalho": "Ajude a planejar uma viagem à trabalho.\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida, como questionar o nome, temática e localização da realização do evento.",

        "Faça um plano estratégico para meu órgão": "Ajude a elaborar um plano estratégico para meu órgão.\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida para obter informações necessárias para elaboração adequada de planos estratégicos.",

        "Faça um plano tático para meu órgão": "Ajude a elaborar um plano tático para meu órgão.\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida para obter informações necessárias para elaboração adequada de planos táticos.",

        "Faça um plano de comunicação para meu órgão": "Ajude a elaborar um plano de comunicação para meu órgão.\n" +
            "Se precisar de mais informações, faça outra pergunta em seguida para obter informações necessárias para elaboração adequada de planos de comunicação.",

        "Ajude a depurar o meu código": "Pode me ajudar a depurar o meu código? Vou compartilhar um trecho dele com você.",

        "Ajude a escrever uma função": "Pode me ajudar a escrever uma função? Tenho os requisitos e preciso de ajuda com a implementação.",

        "Ajude a simplificar o meu código": "Pode me ajudar a simplificar o meu código? Quero refatorá-lo para melhorar a legibilidade e o desempenho.",

        "Ajude a aprender Python": "Pode me ajudar a aprender Python? Comece perguntando sobre meu nível atual de conhecimento de programação."
    };

    const ArrExtensoesAceitas = <?= json_encode((new MdIaConfigAssistenteRN())->listarMapaExtensoesPermitidasUpload()); ?>;
    const ArrIcones = <?= json_encode(MdIaInteracaoArqAvulsoINT::obterMapaIconesPorExtensao(), JSON_UNESCAPED_SLASHES) ?>;
    const TAMANHO_MAXIMO_UPLOAD_TOTAL_MB = 500;

    function exibirOpcoes(botao) {

        const submenu = botao.closest('.botao-com-menu').querySelector('.submenu-opcoes');
        const display = submenu.style.display;

        // Fecha todos os outros
        document.querySelectorAll('.submenu-opcoes').forEach(function(el) {
            el.style.display = 'none';
            const botaoMenu = el.closest('.botao-com-menu').querySelector('[aria-expanded]');
            if (botaoMenu) {
                botaoMenu.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle visibilidade
        if (display === 'block') {
            submenu.style.display = 'none';
            botao.setAttribute('aria-expanded', 'false');
            return;
        }

        submenu.style.display = 'block';
        botao.setAttribute('aria-expanded', 'true');

        //Exibir o posicionamento correto
        const rect = botao.getBoundingClientRect();
        const menuWidth = submenu.offsetWidth;
        const menuHeight = submenu.offsetHeight;
        const spaceRight = window.innerWidth - rect.left;
        const spaceBottom = window.innerHeight - rect.bottom;

        // Ajuste para não sair da tela horizontalmente
        let left = rect.left + window.scrollX;
        if (spaceRight < menuWidth) {
            left = window.innerWidth - menuWidth - 10;
        }

        // Ajuste vertical (opcional: pode inverter para cima se não couber)
        let top = rect.bottom + window.scrollY;
        if (spaceBottom < menuHeight) {
            top = rect.top - menuHeight + window.scrollY;
        }

        submenu.style.left = `${left}px`;
        submenu.style.top = `${top}px`;
    }

    function reposicionaBotao() {
        if (document.getElementById("ifrConteudoVisualizacao")) {
            if (document.getElementById("ifrConteudoVisualizacao").contentWindow.document.getElementById("ifrVisualizacao")) {
                var btnInfraTopo = document.getElementById("ifrConteudoVisualizacao").contentWindow.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("btnInfraTopo");
            }
        } else if (document.getElementById("ifrVisualizacao")) {
            var btnInfraTopo = document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("btnInfraTopo");
        }
        if (btnInfraTopo) {
            btnInfraTopo.style.right = "4.5rem";
            btnInfraTopo.style.bottom = "0.2rem";
        }
    }

    function controlaPreenchimentoCampoMensagem() {
        var mensagemNormalizada = obterTextoMensagem().trim();
        if (mensagemNormalizada !== "" && !$("#botaoEnviarMensagem").hasClass("aguardandoResposta") && !$("#botaoEnviarMensagem").hasClass("apiIndisponivel") && !$("#botaoEnviarMensagem").hasClass("limiteDiarioExcedido")) {
            $("#botaoEnviarMensagem").addClass("habilitadoEnvio");

        } else {
            $("#botaoEnviarMensagem").removeClass("habilitadoEnvio");
        }
    }

    function alterarEstadoVerificacaoPermissoes(verificando) {
        var botao = document.getElementById("botaoEnviarMensagem");
        if (!botao) {
            return;
        }

        botao.classList.toggle("verificandoPermissoes", verificando);
        botao.setAttribute("aria-busy", verificando ? "true" : "false");

        if (botao._tippy) {
            botao._tippy.setContent(verificando ? "Verificando permissões de acesso sobre protocolos" : "Enviar Mensagem");
        }
    }

    function capturaTexto(event, selectedText, tela) {
        document.getElementById("botaoTransferir").style.display = 'none';
        if (selectedText !== '') {
            exibirBotao(event, selectedText, tela)
        }
    }

    function acionarListener() {
        if (document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml')) {
            document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml').contentWindow.document.addEventListener('mouseup', function(event) {
                $("#botaoTransferir").css("display", "none");
                const selectedText = document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml').contentWindow.document.getSelection().toString().trim();
                capturaTexto(event, selectedText, "processo");
            });
        }
    }

    function acionarListenerEditor() {
        document.getElementById("divEditores").onmouseover = function() {
            var classeEditor = document.getElementsByClassName('cke_wysiwyg_frame');
            if (classeEditor) {
                Array.prototype.forEach.call(classeEditor, function(elemento) {
                    elemento.contentWindow.document.addEventListener('mouseup', function(event) {
                        $("#botaoTransferir").css("display", "none");
                        const selectedText = elemento.contentWindow.document.getSelection().toString().trim();
                        capturaTexto(event, selectedText, "editor");
                    });
                });
            }
        }
    }

    // Função para exibir o botão no local do clique
    function exibirBotao(event, selectedText, tela) {
        const botao = document.getElementById("botaoTransferir");
        if (tela == "editor") {
            var x = event.clientX + 30;
            var y = event.clientY - $("#divEditores")[0].scrollTop + 240;
        } else {
            var x = event.clientX + 340;
            var y = event.clientY + 85;
        }

        botao.style.left = `${x}px`;
        botao.style.top = `${y}px`;
        botao.style.display = 'block';

        document.getElementById('textoSelecionado').value = selectedText;
    }

    function transferirTexto(selectedText) {
        definirTextoMensagem(document.getElementById('textoSelecionado').value);
        document.getElementById("botaoTransferir").style.display = 'none';
        document.getElementById('widget-open').checked = true;
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
    }

    // Função para abrir o modal
    function openModal(url) {
        infraAbrirJanelaModal(url,
            1024,
            600);
    }

    function abrirChat() {
        $(".widget-content").css("display", "inherit");
        verificarDisponibilidadeAssistenteIa();
        reduzirAssistente();
        document.getElementById('widget-open').checked = true;
    }

    function fecharChat() {
        if (document.getElementById('widget-open')) {
            document.getElementById('widget-open').checked = false;
        }
    }

    function generateUniqueID() {
        var timestamp = Date.now();
        var random = Math.floor(Math.random() * 1000);
        return timestamp.toString() + random.toString();
    }

    function safe_tags(str) {
        return str.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    var REGEX_POSSIVEL_CITACAO_PROTOCOLO_SEI = <?= json_encode(MdIaConfigAssistenteINT::REGEX_POSSIVEL_CITACAO_PROTOCOLO) ?>;
    var REGEX_CITACAO_PROTOCOLO_SEI = <?= json_encode(MdIaConfigAssistenteINT::REGEX_CITACAO_PROTOCOLO) ?>;

    function criarRegexCitacaoProtocolo(flags = '', ancorarInicio = false) {
        return new RegExp((ancorarInicio ? '^' : '') + REGEX_CITACAO_PROTOCOLO_SEI, flags);
    }

    function aplicarLinksCitacoesMensagem(html, dadosCitacoes) {
        if (!Array.isArray(dadosCitacoes) || dadosCitacoes.length === 0) {
            return html;
        }

        var linksCitacoes = {};
        dadosCitacoes.forEach(function(citacao) {
            if (citacao && citacao["citacaoRealizada"] && citacao["linkAcesso"]) {
                linksCitacoes[citacao["citacaoRealizada"]] = decodeHTML(citacao["linkAcesso"]);
            }
        });

        var template = document.createElement('template');
        template.innerHTML = html;
        var walker = document.createTreeWalker(template.content, NodeFilter.SHOW_TEXT, {
            acceptNode: function(noTexto) {
                return noTexto.parentElement && noTexto.parentElement.closest('a') ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }
        });
        var nosTexto = [];
        while (walker.nextNode()) {
            nosTexto.push(walker.currentNode);
        }

        nosTexto.forEach(function(noTexto) {
            var texto = noTexto.nodeValue;
            var regexCitacao = criarRegexCitacaoProtocolo('g');
            var fragmento = document.createDocumentFragment();
            var ultimaPosicao = 0;
            var correspondencia;
            while ((correspondencia = regexCitacao.exec(texto)) !== null) {
                var linkAcesso = linksCitacoes[correspondencia[0].split('[')[0]];
                if (!linkAcesso) {
                    continue;
                }
                var link = document.createElement('a');
                link.className = 'referencia-sei-link';
                link.target = '_blank';
                link.rel = 'noopener';
                link.href = linkAcesso;
                link.textContent = correspondencia[0];
                fragmento.appendChild(document.createTextNode(texto.substring(ultimaPosicao, correspondencia.index)));
                fragmento.appendChild(link);
                ultimaPosicao = correspondencia.index + correspondencia[0].length;
            }
            if (ultimaPosicao > 0) {
                fragmento.appendChild(document.createTextNode(texto.substring(ultimaPosicao)));
                noTexto.replaceWith(fragmento);
            }
        });
        return template.innerHTML;
    }

    function obterTextoMensagem() {
        var editor = document.getElementById('mensagem');
        if (!editor) {
            return '';
        }

        var copia = editor.cloneNode(true);
        copia.querySelectorAll('[data-citacao-sei]').forEach(function(referencia) {
            referencia.replaceWith(document.createTextNode(referencia.getAttribute('data-citacao-sei')));
        });
        return obterTextoElementoEditavel(copia);
    }

    function obterTextoElementoEditavel(elemento) {
        var texto = '';
        elemento.childNodes.forEach(function(no, indice) {
            if (no.nodeType === Node.TEXT_NODE) {
                texto += no.nodeValue;
                return;
            }
            if (no.nodeName === 'BR') {
                texto += '\n';
                return;
            }

            if (/^(DIV|P)$/.test(no.nodeName) && indice > 0 && !texto.endsWith('\n')) {
                texto += '\n';
            }
            texto += obterTextoElementoEditavel(no);
            if (/^(DIV|P)$/.test(no.nodeName) && indice < elemento.childNodes.length - 1 && !texto.endsWith('\n')) {
                texto += '\n';
            }
        });
        return texto;
    }

    // '\r' renderizado em white-space: pre-wrap impede o navegador de mover o cursor entre linhas com as setas.
    function normalizarQuebrasLinhaMensagem(texto) {
        return String(texto).replace(/\r\n?/g, '\n');
    }

    function definirTextoMensagem(texto) {
        var editor = document.getElementById('mensagem');
        if (!editor) {
            return;
        }
        editor.textContent = normalizarQuebrasLinhaMensagem(texto || '');
        destacarReferenciasSimplesMensagem();
    }

    function obterHtmlVisualMensagem() {
        var editor = document.getElementById('mensagem');
        if (!editor) {
            return '';
        }

        var textoParaHtml = function(texto) {
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        };
        var converterNo = function(no) {
            if (no.nodeType === Node.TEXT_NODE) {
                return textoParaHtml(no.nodeValue).replace(/\r\n?|\n/g, '<br>');
            }
            if (no.nodeName === 'BR') {
                return '<br>';
            }
            if (no.matches && no.matches('.referencia-sei-link')) {
                return '<a class="referencia-sei-link" target="_blank" rel="noopener" href="' + textoParaHtml(no.getAttribute('href')) + '">' + textoParaHtml(no.textContent) + '</a>';
            }

            return converterFilhos(no);
        };

        var converterFilhos = function(elemento) {
            var html = '';
            elemento.childNodes.forEach(function(no, indice) {
                var bloco = /^(DIV|P)$/.test(no.nodeName);
                if (bloco && indice > 0 && !html.endsWith('<br>')) {
                    html += '<br>';
                }
                html += converterNo(no);
                if (bloco && indice < elemento.childNodes.length - 1 && !html.endsWith('<br>')) {
                    html += '<br>';
                }
            });
            return html;
        };

        return converterFilhos(editor);
    }

    function criarReferenciaSei(idProtocolo, prefixo, protocolo, sufixo) {
        var link = document.createElement('a');
        link.className = 'referencia-sei-link';
        link.contentEditable = 'false';
        link.setAttribute('data-citacao-sei', '#' + protocolo);
        link.href = obterLinkDiretoProtocolo(idProtocolo, protocolo);
        link.target = '_blank';
        link.rel = 'noopener';
        link.textContent = '#' + protocolo;

        var fragmento = document.createDocumentFragment();
        fragmento.appendChild(document.createTextNode(prefixo));
        fragmento.appendChild(link);
        fragmento.appendChild(document.createTextNode(sufixo));
        return fragmento;
    }

    function obterLinkDiretoProtocolo(idProtocolo, protocolo) {
        if (/\d{5}\.\d{6}\/\d{4}-\d{2}/.test(protocolo)) {
            return 'controlador.php?acao=procedimento_trabalhar&id_procedimento=' + encodeURIComponent(idProtocolo);
        }

        var idProcedimentoAtual = new URLSearchParams(window.location.search).get('id_procedimento');
        if (idProcedimentoAtual) {
            return 'controlador.php?acao=procedimento_trabalhar&id_procedimento=' + encodeURIComponent(idProcedimentoAtual) + '&id_documento=' + encodeURIComponent(idProtocolo);
        }
        return 'controlador.php?acao=documento_visualizar&id_documento=' + encodeURIComponent(idProtocolo);
    }

    function criarFragmentoTextoMensagem(texto) {
        var fragmento = document.createDocumentFragment();
        var regexLinkSei = /#([^#{}<>\\\r\n]*)\{(\d+)\|([^{}<>()&;\s\\]+)\}([^#<>\\\r\n]*)#/g;
        var ultimaPosicao = 0;
        var correspondencia;

        while ((correspondencia = regexLinkSei.exec(texto)) !== null) {
            fragmento.appendChild(document.createTextNode(texto.substring(ultimaPosicao, correspondencia.index)));
            fragmento.appendChild(criarReferenciaSei(correspondencia[2], correspondencia[1], correspondencia[3], correspondencia[4]));
            ultimaPosicao = correspondencia.index + correspondencia[0].length;
        }
        fragmento.appendChild(document.createTextNode(texto.substring(ultimaPosicao)));
        return fragmento;
    }

    function inserirTextoColadoMensagem(texto) {
        var selecao = window.getSelection();
        if (!selecao.rangeCount) {
            return;
        }

        var intervalo = selecao.getRangeAt(0);
        intervalo.deleteContents();
        var fragmento = criarFragmentoTextoMensagem(normalizarQuebrasLinhaMensagem(texto));
        var ultimoNo = fragmento.lastChild;
        intervalo.insertNode(fragmento);
        if (ultimoNo) {
            intervalo.setStartAfter(ultimoNo);
            intervalo.collapse(true);
            selecao.removeAllRanges();
            selecao.addRange(intervalo);
        }
        document.getElementById('mensagem').normalize();
        destacarReferenciasSimplesMensagem();
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
    }

    function destacarReferenciasSimplesMensagem() {
        var editor = document.getElementById('mensagem');
        if (!editor || !window.CSS || !CSS.highlights || typeof Highlight === 'undefined') {
            return;
        }

        var walker = document.createTreeWalker(editor, NodeFilter.SHOW_TEXT, {
            acceptNode: function(noTexto) {
                return noTexto.parentElement.closest('.referencia-sei-link') ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }
        });
        var intervalos = [];
        var noTexto;
        while ((noTexto = walker.nextNode())) {
            var regexCitacao = criarRegexCitacaoProtocolo('g');
            var correspondencia;
            while ((correspondencia = regexCitacao.exec(noTexto.nodeValue)) !== null) {
                var intervalo = new Range();
                intervalo.setStart(noTexto, correspondencia.index);
                intervalo.setEnd(noTexto, correspondencia.index + correspondencia[0].length);
                intervalos.push(intervalo);
            }
        }
        CSS.highlights.set('referencias-sei-simples', new Highlight(...intervalos));
    }

    function criarIconeTopicoSvg() {
        var template = document.createElement('template');
        template.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg>';
        return template.content.firstElementChild;
    }

    function criarElementoTopico(topico, ativo) {
        var idTopico = parseInt(topico["idTopico"], 10);
        var nomeTopico = topico["nome"] == null ? '' : String(topico["nome"]);
        var classeTopico = ativo ? 'topico active' : 'topico';
        var classeBotao = ativo ? 'nav-link text-left active' : 'nav-link text-left';

        var $topico = $('<div>').addClass(classeTopico).attr({
            onmouseover: 'mostrarAcoesTopico(this)',
            onmouseout: 'ocultarAcoesTopico(this)'
        });

        var $linkSeleciona = $('<a>')
            .addClass('selecionaTopico')
            .on('click', function() {
                selecionaTopico(idTopico);
            });

        var $botao = $('<button>')
            .addClass(classeBotao)
            .attr({
                id: 'topico' + idTopico,
                'data-toggle': 'pill',
                'data-target': 'topico' + idTopico,
                type: 'button',
                role: 'tab',
                'aria-controls': 'topico' + idTopico,
                'aria-selected': 'false'
            });

        $botao.append(criarIconeTopicoSvg());
        $botao.append(document.createTextNode(nomeTopico));

        $linkSeleciona.append($botao);
        $topico.append($linkSeleciona);

        var $acoesTopico = $('<div>').addClass('acoesTopico').attr('id', 'acoesTopico' + idTopico);

        var $acaoArquivar = $('<a>').addClass('arquivo').on('click', function() {
            arquivarTopico(idTopico);
        });
        $acaoArquivar.append('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>');

        var $acaoRenomear = $('<a>').addClass('rename').on('click', function() {
            editarTopico(idTopico);
        });
        $acaoRenomear.append('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>');

        $acoesTopico.append($acaoArquivar, $acaoRenomear);
        $topico.append($acoesTopico);

        return $topico;
    }

    function atualizarControleExpansaoPrompt(elementoPrompt) {
        if (!elementoPrompt) {
            return;
        }

        var estiloPrompt = window.getComputedStyle(elementoPrompt);
        var alturaLinha = parseFloat(estiloPrompt.lineHeight);
        if (isNaN(alturaLinha)) {
            alturaLinha = parseFloat(estiloPrompt.fontSize) * 1.2;
        }

        var alturaLimite = (alturaLinha * 11) + parseFloat(estiloPrompt.paddingTop) + parseFloat(estiloPrompt.paddingBottom);
        var estavaExpandido = elementoPrompt.classList.contains('prompt-usuario-expandido');
        var botao = elementoPrompt.nextElementSibling;
        var ehBotaoExpansao = botao && botao.classList.contains('botao-expansao-prompt');

        elementoPrompt.classList.remove('prompt-usuario-recolhido', 'prompt-usuario-expandido');
        elementoPrompt.style.removeProperty('--altura-prompt-recolhido');
        elementoPrompt.style.removeProperty('max-height');

        if (elementoPrompt.scrollHeight <= alturaLimite + 1) {
            if (ehBotaoExpansao) {
                botao.remove();
            }
            return;
        }

        if (!ehBotaoExpansao) {
            botao = document.createElement('button');
            botao.type = 'button';
            botao.className = 'botao-expansao-prompt';
            botao.setAttribute('aria-controls', elementoPrompt.id);
            botao.addEventListener('click', function() {
                var expandir = elementoPrompt.classList.contains('prompt-usuario-recolhido');
                elementoPrompt.style.setProperty('--altura-prompt-expandido', elementoPrompt.scrollHeight + 'px');
                if (!expandir) {
                    // Retira o "none" inline e forca reflow para a transicao partir da altura atual.
                    elementoPrompt.style.removeProperty('max-height');
                    void elementoPrompt.offsetHeight;
                }
                elementoPrompt.classList.toggle('prompt-usuario-recolhido', !expandir);
                elementoPrompt.classList.toggle('prompt-usuario-expandido', expandir);
                botao.textContent = expandir ? 'Mostrar menos' : 'Mostrar mais';
                botao.setAttribute('aria-expanded', expandir ? 'true' : 'false');
                elementoPrompt.addEventListener('transitionend', function(evento) {
                    if (evento.target === elementoPrompt && evento.propertyName === 'max-height') {
                        if (elementoPrompt.classList.contains('prompt-usuario-expandido')) {
                            elementoPrompt.style.maxHeight = 'none';
                        }
                        controlaTamanhoConteudoChat('', false);
                    }
                }, {
                    once: true
                });
                controlaTamanhoConteudoChat('', false);
            });
            elementoPrompt.insertAdjacentElement('afterend', botao);
        }

        elementoPrompt.style.setProperty('--altura-prompt-recolhido', alturaLimite + 'px');
        elementoPrompt.style.setProperty('--altura-prompt-expandido', elementoPrompt.scrollHeight + 'px');
        elementoPrompt.classList.add(estavaExpandido ? 'prompt-usuario-expandido' : 'prompt-usuario-recolhido');
        if (estavaExpandido) {
            elementoPrompt.style.maxHeight = 'none';
        }
        botao.textContent = estavaExpandido ? 'Mostrar menos' : 'Mostrar mais';
        botao.setAttribute('aria-expanded', estavaExpandido ? 'true' : 'false');
        requestAnimationFrame(function() {
            controlaTamanhoConteudoChat('', false);
        });
    }

    function agendarAtualizacaoPromptsUsuario() {
        if (window.mdIaPromptResizeTimeout) {
            clearTimeout(window.mdIaPromptResizeTimeout);
        }

        window.mdIaPromptResizeTimeout = setTimeout(function() {
            document.querySelectorAll('#conversa .interaction.agent .input').forEach(function(elementoPrompt) {
                atualizarControleExpansaoPrompt(elementoPrompt);
            });
        }, 450);
    }

    function resolveItensEnvioMensagem(mensagem, dadosCitacoes = "", idInteracao = "", favorito = "", dthCadastro, upload = [], mensagemVisual = "") {
        if (mensagemVisual !== "") {
            mensagem = mensagemVisual;
        } else {
            mensagem = decodeHtmlEntitiesPergunta(mensagem);
        }
        mensagem = aplicarLinksCitacoesMensagem(mensagem, dadosCitacoes);
        var divPai = $("#conversa");
        var uniqueID = generateUniqueID();

        // GERA O HTML DOS ARQUIVOS
        var htmlArquivos = gerarHtmlArquivosHistorico(upload);

        var respostaMontada = '' +
            '<div class="interaction agent">' +
            '   <div class="mensagemIdentificador">' +
            '       <div class="iconeIdentificacao">' +
            '           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">' +
            '               <path fill="#dc3545" d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>' +
            '           </svg>' +
            '       </div>' +
            '       <div class="blocoMensagem" style="min-width: 91%">' +
            '           ' + htmlArquivos + ' ' +
            '           <div class="input" id="textoPuro' + uniqueID + '">' + mensagem + '' + '</div>' +
            '           <div class="dthCadastro" style="color:#0000007a">' + dthCadastro + '</div>' +
            '       </div>' +
            '   </div>';

        var iconesGerais = '<div class="acoes_assistente" style="margin-top: -10px">' +
            '<a onclick="copiar(' + uniqueID + ')"><div class="copiar" id="copiarResposta' + uniqueID + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></div></a>';

        var iconeFavoritarChat = "";

        if (idInteracao != "") {
            if (favorito) {
                iconeFavoritarChat = '<a onclick="favoritarChat(' + idInteracao + ')"><div class="promptFavoritado" id="promptFavoritado' + idInteracao + '"><svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1.1-1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></div></a>';
            } else {
                iconeFavoritarChat = '<a onclick="favoritarChat(' + idInteracao + ')"><div class="favoritarPrompt" id="favoritarPrompt' + idInteracao + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1.1-1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></div></a>';
            }
        }

        var iconePublicarGaleriaPrompt = '<a onclick="publicarGaleriaPrompt(' + idInteracao + ')"><div class="publicarGaleriaPrompt" id="publicarGaleriaPrompt' + idInteracao + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg></div></a></div>';

        var perguntaCliente = respostaMontada + iconesGerais + iconeFavoritarChat + iconePublicarGaleriaPrompt;
        divPai.append(perguntaCliente);
        requestAnimationFrame(function() {
            atualizarControleExpansaoPrompt(document.getElementById('textoPuro' + uniqueID));
        });
        document.getElementById('conversa').scrollBy(0, 5000);

        if ($(".widget-content").hasClass("expandido")) {
            $(".send-message-input").height("100");
            expandirAssistente();
        } else {
            $(".send-message-input").height("45");
            reduzirAssistente();
        }
        elementosCopiarResposta = document.getElementById('conversa').querySelectorAll('.copiar:not(.copiarMarkdown)');
        elementosCopiarResposta.forEach(function(elemento) {
            geraTooltip(elemento.id, "Copiar");
        });
        elementosFavoritarPrompt = document.getElementById('conversa').querySelectorAll('.favoritarPrompt');
        elementosFavoritarPrompt.forEach(function(elemento) {
            geraTooltip(elemento.id, "Favoritar Prompt");
        });
        elementosFavoritarPrompt = document.getElementById('conversa').querySelectorAll('.promptFavoritado');
        elementosFavoritarPrompt.forEach(function(elemento) {
            geraTooltip(elemento.id, "Prompt Favorito");
        });
        elementosPublicarPrompt = document.getElementById('conversa').querySelectorAll('.publicarGaleriaPrompt');
        elementosPublicarPrompt.forEach(function(elemento) {
            geraTooltip(elemento.id, "Publicar na Galeria de Prompts");
        });
    }

    function identificarProtocolo(mensagem, mensagemVisual = "") {

        mensagem = (mensagem || "").trim();

        if (mensagem === "") {
            $("#validacaoMensagem").html("Informe uma mensagem para enviar.");
            estadoDeInteracao();
            return false;
        }

        if (!validarArquivosSelecionadosAntesEnvio()) {
            estadoDeInteracao();
            return false;
        }

        let possiveisCitacoes = mensagem.match(new RegExp(REGEX_POSSIVEL_CITACAO_PROTOCOLO_SEI, 'g'));
        let documentos = [];
        if (possiveisCitacoes) {
            possiveisCitacoes = possiveisCitacoes.filter((value, index, array) => array.indexOf(value) == index);
            var citacaoIncorreta = false;
            var indicacaoFolhaIncorreta = false;

            if (possiveisCitacoes.length > 0) {
                possiveisCitacoes.forEach(function(possivelCitacao) {
                    if (possivelCitacao.length < 8) {
                        $("#validacaoMensagem").html('O uso do caractere de "#" é exclusivo para citação de protocolos do SEI, cujo número deve estar colado ao # (no formato, por exemplo #01234567). Assim, a mensagem possui citação de protocolo em formato irregular.');
                        estadoDeInteracao();
                        citacaoIncorreta = true;
                        return false;
                    } else {
                        let padraoColchetes = /\[/;

                        let citacaoValida = possivelCitacao.match(criarRegexCitacaoProtocolo('', true));
                        if (padraoColchetes.test(possivelCitacao)) {
                            if (citacaoValida) {
                                documentos.push(citacaoValida[0]);
                            } else {
                                $("#validacaoMensagem").html('A indicação de intervalo de páginas sobre o Documento Externo está em formato irregular. Deve utilizar o formato [p:p] um intervalo ou [p] para indicar página única: #01234567[10:15] ou #01234567[20].');
                                estadoDeInteracao();
                                indicacaoFolhaIncorreta = true;
                                return false;
                            }
                        } else {
                            if (citacaoValida) {
                                documentos.push(citacaoValida[0]);
                            } else {
                                $("#validacaoMensagem").html('O uso do caractere de "#" é exclusivo para citação de protocolos do SEI, cujo número deve estar colado ao # (no formato, por exemplo #01234567). Assim, a mensagem possui citação de protocolo em formato irregular.');
                                estadoDeInteracao();
                                citacaoIncorreta = true;
                                return false;
                            }
                        }
                    }
                })
            } else {
                $("#validacaoMensagem").html('O uso do caractere de "#" é exclusivo para citação de protocolos do SEI, cujo número deve estar colado ao # (no formato, por exemplo #01234567). Assim, a mensagem possui citação de protocolo em formato irregular.');
                estadoDeInteracao();
                citacaoIncorreta = true;
                return false;
            }

            if (citacaoIncorreta == true) {
                return false;
            }

            if (indicacaoFolhaIncorreta == true) {
                return false;
            }
        }

        $("#validacaoMensagem").html("");

        if (documentos == null || documentos.length === 0) {
            verificaJanelaContexto(mensagem, "", mensagemVisual);
        } else {
            documentos = documentos.filter((value, index, array) => array.indexOf(value) === index);
            let protocolo = {};
            const urlParams = new URLSearchParams(window.location.search);
            protocolo["documento"] = documentos;
            protocolo["acao_origem"] = urlParams.get("acao_origem");
            protocolo["acesso"] = urlParams.get("acesso");
            protocolo["id_procedimento"] = urlParams.get("id_procedimento");
            alterarEstadoVerificacaoPermissoes(true);
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consulta_protocolo_assistente_ia_ajax'); ?>',
                type: 'POST',
                contentType: 'application/json; charset=utf-8', // importante
                dataType: "json",
                async: true,
                data: JSON.stringify({
                    protocolo: protocolo
                }),
                success: function(dadosCitacoes) {
                    if (!Array.isArray(dadosCitacoes)) {
                        if (dadosCitacoes["result"] == "false") {
                            $("#validacaoMensagem").html(dadosCitacoes["mensagem"]);
                        } else {
                            $("#validacaoMensagem").html("Não foi possível validar os protocolos citados. Tente novamente.");
                        }
                        estadoDeInteracao();
                        return;
                    }

                    if (obterTextoMensagem().trim() !== mensagem) {
                        $("#validacaoMensagem").html("A mensagem foi alterada durante a validação. Envie novamente para validar os protocolos atuais.");
                        estadoDeInteracao();
                        return;
                    }

                    verificaJanelaContexto(mensagem, dadosCitacoes, mensagemVisual);
                },
                error: function(err) {
                    console.error(err);
                    $("#validacaoMensagem").html("Não foi possível validar os protocolos citados. Tente novamente.");
                    estadoDeInteracao();
                }
            });
        }
    }

    function estadoDeInteracao() {
        alterarEstadoVerificacaoPermissoes(false);
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        controlaTamanhoConteudoChat();
        controlaPreenchimentoCampoMensagem();
    }

    function enviarMensagem() {
        $("#validacaoMensagem").html("");

        if ($("#botaoEnviarMensagem").hasClass("aguardandoResposta")) {
            return;
        }

        var mensagem = obterTextoMensagem().trim();
        var mensagemVisual = obterHtmlVisualMensagem();

        if (mensagem === "") {
            $("#validacaoMensagem").html("Informe uma mensagem para enviar.");
            $("#botaoEnviarMensagem").removeClass("habilitadoEnvio");
            return;
        }

        if ($("#botaoEnviarMensagem").hasClass("habilitadoEnvio") && !$("#botaoEnviarMensagem").hasClass("aguardandoResposta")) {
            $("#botaoEnviarMensagem").removeClass("habilitadoEnvio");
            $("#botaoEnviarMensagem").addClass("aguardandoResposta");
            identificarProtocolo(mensagem, mensagemVisual);
        }
    }

    function verificaJanelaContexto(mensagem, dadosCitacoes = "", mensagemVisual = "") {

        alterarEstadoVerificacaoPermissoes(false);

        const {
            isWithinTokenLimit,
            encode
        } = GPTTokenizer_cl100k_base

        var text = mensagem;
        var tokenLimit = $("#janelaContexto").val();
        if (dadosCitacoes != "") {
            tokenLimit = tokenLimit * 0.5;
        } else {
            tokenLimit = tokenLimit * 0.8;
        }

        var withinTokenLimit = isWithinTokenLimit(text, tokenLimit);

        if (withinTokenLimit != false) {
            definirTextoMensagem("");

            // --- ALTERAÇÃO AQUI ---
            // 1. Criamos uma cópia simples dos arquivos selecionados formatada 
            // para a função de renderização visual (que espera um objeto com propriedade 'nome')
            var uploadsParaVisualizacao = arquivosSelecionados.map(function(file) {
                return {
                    nome: file.name
                };
            });

            // 2. Passamos 'uploadsParaVisualizacao' como o 6º argumento (upload)
            resolveItensEnvioMensagem(mensagem, dadosCitacoes, null, null, dateTimeAgoraFormatado(), uploadsParaVisualizacao, mensagemVisual);
            // ----------------------

            controlaTamanhoConteudoChat().then(function() {
                // A função resolveResposta enviará os dados reais (FormData) e depois limpará a variável arquivosSelecionados
                resolveResposta(mensagem, dadosCitacoes);
            });
        } else {
            var info = {};
            if (procedimento != "") {
                info["protocolo"] = procedimento[0];
            } else {
                info["protocolo"] = false;
            }
            info["janelaContexto"] = tokenLimit;
            info["tokensEnviados"] = isWithinTokenLimit(text, 99999999);
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_gera_log_excedeu_limite_janela_contexto_ajax'); ?>',
                type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
                dataType: "json", //Tipo de dado que será enviado ao servidor
                data: info,
                async: true,
                error: function(err) {
                    console.log(err);
                }
            }).done(function() {
                $("#validacaoMensagem").html("O texto da mensagem ultrapassou o limite permitido. Reduza o texto e tente novamente.");
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            });
        }
    }

    function apiIndisponivel(divpai, divfilho) {
        $("#botaoEnviarMensagem").removeClass("apiDisponivel");
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="output">Assistente de IA está indisponível no momento. <br>Tente novamente mais tarde.</div></div>';
        divpai.appendChild(divfilho);
        controlaTamanhoConteudoChat();
        controlaPreenchimentoCampoMensagem();
    }

    function limiteDiarioUltrapassado() {
        $("#botaoEnviarMensagem").addClass("limiteDiarioExcedido");
        $("#validacaoMensagem").html('O volume de conteúdo permitido nas interações diárias foi excedido. Tente novamente amanhã, quando o volume de conteúdo permitido para interação terá sido renovado.');
        estadoDeInteracao();
    }

    function insereBoasVindas(divpai, divfilho) {
        divfilho.className = 'interaction client';
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="output">Sou um Assistente de Inteligência Artificial.<br>Como posso te ajudar?</div></div>';
        divpai.appendChild(divfilho);
    }

    function carregandoResposta(divpai) {
        var minWidthBlocoMensagem = $(".widget-content").hasClass("expandido") ? "20%" : "91%";
        var divfilho = document.createElement('div');
        divfilho.className = 'interaction client dots';
        divfilho.setAttribute('data-render-id', generateUniqueID());
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="blocoMensagem blocoMensagemCarregando" style="min-width: ' + minWidthBlocoMensagem + '"><div class="processing-live ativo"><span class="reasoning-spinner" aria-hidden="true"><span class="reasoning-spinner-ring"></span><span class="reasoning-spinner-glyph">&#9889;</span></span><span class="processing-status" style="display:none"></span></div></div></div>';
        divpai.appendChild(divfilho);
        return divfilho;
    }

    function obterIdRenderizacaoMensagem(divfilho) {
        var renderId = divfilho.getAttribute('data-render-id');
        if (!renderId) {
            renderId = divfilho.id ? String(divfilho.id) : generateUniqueID();
            divfilho.setAttribute('data-render-id', renderId);
        }

        return renderId;
    }

    function atualizarIndicadorCarregamento(divfilho, exibir) {
        var processingElement = divfilho.querySelector('.processing-live');
        if (!processingElement) {
            return;
        }

        processingElement.style.display = exibir ? 'inline-flex' : 'none';
        processingElement.classList.toggle('ativo', exibir);
    }

    function sincronizarScrollDuranteStreaming(scrollAnterior) {
        var conversaElement = document.getElementById('conversa');
        if (!conversaElement) {
            return;
        }

        var autoScrollHabilitado = window.mdIaAutoScrollStreamingParcial;
        if (typeof autoScrollHabilitado === 'undefined') {
            autoScrollHabilitado = false;
        }

        var distanciaAteFim = conversaElement.scrollHeight - (conversaElement.scrollTop + conversaElement.clientHeight);
        var usuarioEstaNoFim = distanciaAteFim <= 80;

        var alturaAtualConversa = conversaElement.clientHeight || parseInt(conversaElement.style.height, 10);
        if (alturaAtualConversa && alturaAtualConversa > 0) {
            controleScroll(alturaAtualConversa, false);
        } else {
            controlaTamanhoConteudoChat("", false);
        }

        if (autoScrollHabilitado && usuarioEstaNoFim) {
            requestAnimationFrame(function() {
                conversaElement.scrollTop = conversaElement.scrollHeight;
            });
        } else if (scrollAnterior !== null && typeof scrollAnterior !== 'undefined') {
            // Mantem a posicao de leitura; o usuario controla o scroll durante o streaming
            conversaElement.scrollTop = scrollAnterior;
        }
    }

    function definirEstadoReasoning(reasoningElement, expandido) {
        if (!reasoningElement) {
            return;
        }

        var toggleElement = reasoningElement.querySelector('.reasoning-toggle');
        var labelElement = reasoningElement.querySelector('.reasoning-toggle-label');
        var contentElement = reasoningElement.querySelector('.reasoning-content');

        reasoningElement.setAttribute('data-expandido', expandido ? 'true' : 'false');
        reasoningElement.classList.toggle('expandido', expandido);

        if (toggleElement) {
            toggleElement.setAttribute('aria-expanded', expandido ? 'true' : 'false');
        }

        if (labelElement) {
            labelElement.textContent = expandido ? 'Ocultar processamento interno' : 'Processamento interno';
        }

        if (contentElement) {
            if (expandido) {
                atualizarLarguraPainelReasoning(reasoningElement);
                contentElement.style.display = 'block';
            } else {
                contentElement.style.display = 'none';
                reasoningElement.style.width = '';
                reasoningElement.style.maxWidth = '';
            }
        }

        controlaTamanhoConteudoChat('', false);

        if (expandido && toggleElement) {
            requestAnimationFrame(function() {
                toggleElement.scrollIntoView({
                    block: 'nearest',
                    inline: 'nearest'
                });
            });
        }
    }

    function atualizarLarguraPainelReasoning(reasoningElement) {
        if (!reasoningElement) {
            return;
        }

        var blocoMensagem = reasoningElement.parentElement;
        var outputEl = blocoMensagem ? blocoMensagem.querySelector('.output') : null;
        var larguraOutput = outputEl ? Math.round(outputEl.getBoundingClientRect().width) : 0;
        if (larguraOutput > 0) {
            reasoningElement.style.width = larguraOutput + 'px';
            reasoningElement.style.maxWidth = larguraOutput + 'px';
        }
    }

    function atualizarLarguraPaineisReasoningAbertos() {
        document.querySelectorAll('#conteudoChat .reasoning-live[data-expandido="true"]').forEach(function(reasoningElement) {
            atualizarLarguraPainelReasoning(reasoningElement);
        });
    }

    function agendarAtualizacaoLarguraPaineisReasoningAbertos() {
        if (window.mdIaReasoningResizeTimeout) {
            clearTimeout(window.mdIaReasoningResizeTimeout);
        }

        window.mdIaReasoningResizeTimeout = setTimeout(function() {
            requestAnimationFrame(function() {
                atualizarLarguraPaineisReasoningAbertos();
            });
        }, 450);
    }

    function inicializarPainelReasoning(divfilho) {
        var reasoningElement = divfilho.querySelector('.reasoning-live');
        if (!reasoningElement) {
            return null;
        }

        if (reasoningElement.getAttribute('data-inicializado') === 'true') {
            return reasoningElement;
        }

        var toggleElement = reasoningElement.querySelector('.reasoning-toggle');
        if (toggleElement) {
            toggleElement.addEventListener('click', function() {
                var expandido = reasoningElement.getAttribute('data-expandido') === 'true';
                definirEstadoReasoning(reasoningElement, !expandido);
            });
        }

        reasoningElement.setAttribute('data-inicializado', 'true');
        definirEstadoReasoning(reasoningElement, reasoningElement.getAttribute('data-expandido') === 'true');

        return reasoningElement;
    }

    function formatarMarkdownInteracao(texto, idBase) {
        var txtArea = document.createElement('textarea');
        txtArea.innerHTML = texto;

        return markdownToHTML(txtArea.value, idBase);
    }

    function obterCacheReasoningAssistente() {
        if (!window.mdIaReasoningCache) {
            window.mdIaReasoningCache = {};
        }

        return window.mdIaReasoningCache;
    }

    function armazenarReasoningInteracao(idInteracao, reasoning) {
        if (!idInteracao || !reasoning || reasoning.trim() === '') {
            return;
        }

        obterCacheReasoningAssistente()[String(idInteracao)] = reasoning;
    }

    function obterReasoningInteracao(dadosInteracao, idInteracaoFallback) {
        if (dadosInteracao && dadosInteracao['reasoning'] && dadosInteracao['reasoning'].trim() !== '') {
            return dadosInteracao['reasoning'];
        }

        var idInteracao = null;
        if (dadosInteracao && dadosInteracao['id_interacao']) {
            idInteracao = dadosInteracao['id_interacao'];
        } else if (idInteracaoFallback) {
            idInteracao = idInteracaoFallback;
        }

        if (!idInteracao) {
            return '';
        }

        return obterCacheReasoningAssistente()[String(idInteracao)] || '';
    }

    function obterCacheStatusAssistente() {
        if (!window.mdIaStatusCache) {
            window.mdIaStatusCache = {};
        }
        return window.mdIaStatusCache;
    }

    function armazenarStatusInteracao(idInteracao, status) {
        if (!idInteracao || !status || status.trim() === '') {
            return;
        }
        var cache = obterCacheStatusAssistente();
        var key = String(idInteracao);
        if (!cache[key]) {
            cache[key] = [];
        }
        var statusTrimmed = status.trim();
        if (cache[key].indexOf(statusTrimmed) === -1) {
            cache[key].push(statusTrimmed);
        }
    }

    function obterStatusInteracao(idInteracao) {
        if (!idInteracao) return [];
        return obterCacheStatusAssistente()[String(idInteracao)] || [];
    }

    function armazenarHistoricoStatusInteracao(idInteracao, statuses) {
        if (!idInteracao || !Array.isArray(statuses)) {
            return;
        }

        var cache = obterCacheStatusAssistente();
        var key = String(idInteracao);
        cache[key] = [];

        statuses.forEach(function(status) {
            if (!status || String(status).trim() === '') {
                return;
            }

            var statusTrimmed = String(status).trim();
            if (cache[key].indexOf(statusTrimmed) === -1) {
                cache[key].push(statusTrimmed);
            }
        });
    }

    function atualizarPainelReasoning(divfilho, reasoning, statuses) {
        var reasoningElement = inicializarPainelReasoning(divfilho);
        if (!reasoningElement) {
            return;
        }

        var contentElement = reasoningElement.querySelector('.reasoning-content');
        if (!contentElement) {
            return;
        }

        var listaStatuses = Array.isArray(statuses) ? statuses : [];
        var hasStatuses = listaStatuses.length > 0;
        var hasReasoning = reasoning && reasoning.trim() !== '';

        if (!hasStatuses && !hasReasoning) {
            contentElement.innerHTML = '';
            reasoningElement.style.display = 'none';
            reasoningElement.classList.remove('visivel');
            definirEstadoReasoning(reasoningElement, false);
            return;
        }

        reasoningElement.style.display = 'block';
        reasoningElement.classList.add('visivel');

        var html = '';
        if (hasStatuses) {
            html += '<div class="reasoning-section-label">Processamento Interno</div>';
            html += '<ul class="reasoning-status-list">';
            listaStatuses.forEach(function(s) {
                html += '<li class="reasoning-status-item">' + s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</li>';
            });
            html += '</ul>';
        }
        if (hasStatuses && hasReasoning) {
            html += '<div class="reasoning-section-label">Reflexão interna da IA</div>';
        }
        if (hasReasoning) {
            html += '<div class="reasoning-text">' + formatarMarkdownInteracao(reasoning, obterIdRenderizacaoMensagem(divfilho) + '_reasoning') + '</div>';
        }
        contentElement.innerHTML = html;
    }

    function atualizarStatusExecucao(divfilho, statusExecucao) {
        var statusElement = divfilho.querySelector('.processing-status, .status-live');
        if (!statusElement) {
            return;
        }

        if (statusExecucao && statusExecucao.trim() !== '') {
            statusElement.style.display = statusElement.classList.contains('processing-status') ? 'inline' : 'block';
            statusElement.textContent = statusExecucao;
        } else {
            statusElement.style.display = 'none';
            statusElement.textContent = '';
        }
    }

    function obterResumoReasoningInline(reasoning) {
        if (!reasoning || reasoning.trim() === '') {
            return '';
        }

        var linhas = String(reasoning).split(/\r?\n/);
        var primeiraLinha = linhas[0].trim();

        var proximoParagrafo = '';
        for (var i = 1; i < linhas.length; i++) {
            var linha = linhas[i].trim();
            if (linha !== '') {
                proximoParagrafo = linha;
                break;
            }
        }

        if (proximoParagrafo) {
            var trecho = proximoParagrafo.substring(0, 20);
            return primeiraLinha + ' - ' + trecho + '...';
        }

        return primeiraLinha;
    }

    function renderizarResumoReasoningInline(divfilho, reasoning) {
        var statusElement = divfilho.querySelector('.processing-status');
        var resumoReasoning = obterResumoReasoningInline(reasoning);

        if (!statusElement || !resumoReasoning) {
            return false;
        }

        var idBase = obterIdRenderizacaoMensagem(divfilho) + '_reasoning_inline';
        var htmlResumo = formatarMarkdownInteracao(resumoReasoning, idBase).trim();

        htmlResumo = htmlResumo.replace(/^<p>([\s\S]*)<\/p>$/i, '$1');

        statusElement.style.display = 'inline';
        statusElement.innerHTML = htmlResumo;
        return true;
    }

    function renderizarEstadoCarregamento(divfilho, respostaParcial, statusExecucao, reasoning, idInteracao) {
        // Se há resposta parcial, renderizar no .output
        if (respostaParcial && respostaParcial.trim() !== "") {
            atualizarIndicadorCarregamento(divfilho, false);

            var outputElement = divfilho.querySelector('.output');

            // Criar .output se não existir ainda
            if (!outputElement) {
                var blocoMensagem = divfilho.querySelector('.blocoMensagem');
                if (blocoMensagem) {
                    outputElement = document.createElement('div');
                    outputElement.className = 'output';
                    blocoMensagem.appendChild(outputElement);
                }
            }

            if (outputElement) {
                var conversaElement = document.getElementById('conversa');
                var scrollAnterior = conversaElement ? conversaElement.scrollTop : null;
                var textoBruto = respostaParcial;
                var txtArea = document.createElement("textarea");
                txtArea.innerHTML = textoBruto;

                var textoDecodificado = txtArea.value;

                var idTemporario = obterIdRenderizacaoMensagem(divfilho);
                var textoFormatado = markdownToHTML(textoDecodificado, idTemporario);
                outputElement.innerHTML = textoFormatado + '<span>.</span><span>.</span><span>.</span>';
                insereCitacaoFonte(divfilho);
                sincronizarScrollDuranteStreaming(scrollAnterior);
            }
        } else {
            // Se não há resposta, manter indicadores de carregamento
            atualizarIndicadorCarregamento(divfilho, true);

            if (!renderizarResumoReasoningInline(divfilho, reasoning)) {
                atualizarStatusExecucao(divfilho, statusExecucao);
            }
        }
    }

    function insereLoading() {
        $("#conversa").html("");
        var divPai = document.getElementById("conversa");
        var divfilho = document.createElement('div');
        divfilho.className = 'd-flex justify-content-center align-items-center';
        divfilho.innerHTML = '<img src="../../../infra_css/svg/aguarde.svg" alt="" style="d-inline-block">' +
            '<span>Aguarde...</span>';
        divPai.appendChild(divfilho);
    }

    function verificarDisponibilidadeAssistenteIa() {
        var apiJaIndisponivel = false;
        var apiJaDisponivel = false;
        if ($("#botaoEnviarMensagem").hasClass("apiIndisponivel")) {
            apiJaIndisponivel = true;
        }
        if ($("#botaoEnviarMensagem").hasClass("apiDisponivel")) {
            apiJaDisponivel = true;
        }
        $("#botaoEnviarMensagem").addClass("apiIndisponivel");

        var divpai = document.getElementById("conversa");
        var divfilho = document.createElement('div');
        insereLoading();

        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_assistente_consulta_disponibilidade_ajax'); ?>',
            type: 'GET', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            async: true,
            success: function(data) {
                if (data) {
                    if (data['retornoApi']) {
                        if (data['retornoApi']['status'] == "OK") {
                            $("#botaoEnviarMensagem").removeClass("apiIndisponivel");
                            $("#botaoEnviarMensagem").addClass("apiDisponivel");
                            controlaTamanhoConteudoChat();
                            controlaPreenchimentoCampoMensagem();
                            $("#janelaContexto").val(data['janelaContexto']);
                            listarTopicos();
                        } else {
                            if (!apiJaIndisponivel) {
                                apiIndisponivel(divpai, divfilho);
                            }
                        }
                    } else {
                        if (!apiJaIndisponivel) {
                            apiIndisponivel(divpai, divfilho)
                        }
                    }
                } else {
                    if (!apiJaIndisponivel) {
                        apiIndisponivel(divpai, divfilho)
                    }
                }
            },
            error: function(err) {
                if (!apiJaIndisponivel) {
                    apiIndisponivel(divpai, divfilho)
                }
            }
        });
    }

    function resolveResposta(mensagem, dadosCitacoes) {
        var divpai = document.getElementById("conversa");
        var divfilho = carregandoResposta(divpai);

        // Objeto com as configurações da mensagem (metadados)
        var mensagemUsuario = {};
        mensagemUsuario["text"] = encodeURIComponent(mensagem);
        mensagemUsuario["dadosCitacoes"] = dadosCitacoes;
        mensagemUsuario["topicoTemporario"] = $("#topicoTemporario").val();

        // Flags de funcionalidades
        mensagemUsuario["refletir"] = document.querySelector('.esforco-item[data-refletir="true"]').getAttribute('aria-checked') === 'true';
        mensagemUsuario["buscarWeb"] = $("#botaoBuscarWeb").hasClass("botaoAcaoAssistenteIAAtivado");
        mensagemUsuario["skipMemory"] = !$("#botaoMemoriaTopico").hasClass("botaoAcaoAssistenteIAAtivado");
        const urlParams = new URLSearchParams(window.location.search);
        mensagemUsuario["contextoProtocolo"] = {
            acao_origem: urlParams.get("acao_origem"),
            acesso: urlParams.get("acesso"),
            id_procedimento: urlParams.get("id_procedimento")
        };

        // --- PREPARAÇÃO DO FORM DATA PARA ENVIO DE ARQUIVOS ---
        var formData = new FormData();

        // Adiciona os dados da mensagem como uma string JSON
        formData.append('mensagemUsuario', JSON.stringify(mensagemUsuario));

        var quantidadeArquivosSelecionados = arquivosSelecionados.length;

        // Adiciona cada arquivo selecionado ao FormData
        // Usamos 'arquivos[]' para que o PHP receba como um array
        arquivosSelecionados.forEach(function(arquivo, index) {
            formData.append('arquivos[]', arquivo);
        });

        limparTodosArquivos()

        document.getElementById('conversa').scrollBy(0, 5000);

        if (quantidadeArquivosSelecionados > 0) {
            var rotuloArquivos = quantidadeArquivosSelecionados === 1 ? ' arquivo' : ' arquivos';
            atualizarStatusExecucao(divfilho, 'Enviando ' + quantidadeArquivosSelecionados + rotuloArquivos + ': 0%');
        }

        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink("controlador_ajax.php?acao_ajax=md_ia_assistente_envia_mensagem_ajax"); ?>',
            type: 'POST',
            data: formData,
            processData: false, // Importante: impede que o jQuery tente converter o FormData em string
            contentType: false, // Importante: obriga o navegador a definir o Boundary correto
            dataType: "text",
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();

                if (quantidadeArquivosSelecionados > 0 && xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        if (event.lengthComputable) {
                            var percentual = Math.round((event.loaded / event.total) * 100);
                            atualizarStatusExecucao(divfilho, 'Enviando ' + quantidadeArquivosSelecionados + rotuloArquivos + ': ' + percentual + '%');
                        }
                    });
                    xhr.upload.addEventListener('load', function() {
                        atualizarStatusExecucao(divfilho, 'Arquivos enviados. Preparando resposta...');
                    });
                }

                return xhr;
            },
            success: function(data, textStatus, jqXHR) {
                var dadosResposta = data;
                if (typeof dadosResposta === 'string') {
                    try {
                        dadosResposta = JSON.parse(dadosResposta);
                    } catch (e) {
                        var arquivoPossivelmentePrejudicial = detectarPossivelArquivoPrejudicial(jqXHR, data);
                        var mensagemFalha = 'O servidor retornou uma resposta inválida. Tente novamente.';

                        if (arquivoPossivelmentePrejudicial) {
                            mensagemFalha = 'Os arquivos avulsos não foram enviados. Dentro deles pode ter conteúdo potencialmente prejudicial e foi bloqueado pela camada de segurança da rede do seu órgão.';
                            geraLogBloqueioArquivoPrejudicial(quantidadeArquivosSelecionados);
                        }

                        console.error('Resposta inválida do servidor ao enviar mensagem.', e, data);
                        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="blocoMensagem" style="min-width: 91%"><div class="output badge badge-danger">' + mensagemFalha + '</div></div></div>';
                        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                        return;
                    }
                }

                if (dadosResposta["error"]) {
                    if (dadosResposta["erroCitacao"]) {
                        definirTextoMensagem(mensagem);
                        $("#validacaoMensagem").html(dadosResposta["mensagem"]);
                        estadoDeInteracao();
                        return;
                    }

                    $("#botaoEnviarMensagem").addClass("limiteDiarioExcedido");
                    $("#validacaoMensagem").html(dadosResposta["mensagem"]);
                    listarTopicos();
                } else {
                    if ($("#topicoTemporario").val() == "true") {
                        $("#topicoTemporario").val("false");
                    }

                    // LIMPA OS ARQUIVOS APÓS O ENVIO SUCEDIDO
                    limparTodosArquivos();

                    var idMdIaInteracaoChat = dadosResposta;
                    setTimeout(function() {
                        aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                    }, 500);
                }
            },
            error: function(err) {
                console.error(err);
                divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="blocoMensagem" style="min-width: 91%"><div class="output badge badge-danger">Ocorreu um erro ao enviar a mensagem e os arquivos.</div></div></div>';
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            }
        });
    }

    function detectarPossivelArquivoPrejudicial(jqXHR, respostaTexto) {
        if (!jqXHR) {
            return false;
        }

        var statusHttp = jqXHR.status;
        var tamanhoResposta = respostaTexto ? String(respostaTexto).length : 0;
        var contentType = jqXHR.getResponseHeader ? (jqXHR.getResponseHeader('Content-Type') || '') : '';
        var contentLength = jqXHR.getResponseHeader ? (jqXHR.getResponseHeader('Content-Length') || '') : '';
        var respostaNormalizada = (respostaTexto || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        var retornoVazio = (tamanhoResposta === 0 && (contentLength === '' || contentLength === '0'));
        var retornoHtml = contentType.toLowerCase().indexOf('text/html') !== -1;
        var temMensagemBloqueio = respostaNormalizada.indexOf('por questoes de seguranca, esta operacao no sistema foi bloqueada') !== -1;
        var temCodigoBloqueio = respostaNormalizada.indexOf('codigo de bloqueio') !== -1;
        var temEnderecoBloqueio = respostaNormalizada.indexOf('endereco de bloqueio') !== -1;
        var respostaHtmlWaf = retornoHtml && (temMensagemBloqueio || temCodigoBloqueio || temEnderecoBloqueio);

        return statusHttp === 200 && ((retornoVazio && retornoHtml) || respostaHtmlWaf);
    }

    function geraLogBloqueioArquivoPrejudicial(quantidadeArquivos) {

        var dadosLog = {};
        dadosLog["quantidadeArquivos"] = quantidadeArquivos ? quantidadeArquivos : 0;

        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink("controlador_ajax.php?acao_ajax=md_ia_gera_log_arquivo_prejudicial_ajax"); ?>',
            type: 'POST',
            dataType: 'json',
            data: dadosLog,
            async: true,
            error: function(err) {
                console.log(err);
            }
        });
    }

    function geraTooltip(idElemento, titulo) {
        var elemento = document.getElementById(idElemento);
        if (!elemento) {
            return;
        }

        if (elemento._tippy) {
            elemento._tippy.destroy();
        }

        tippy(elemento, {
            content: titulo,
        });
    }

    function decodeHtmlEntitiesPergunta(encodedString) {
        // 1) Mascara &lt; e &gt; para placeholders
        const masked = encodedString
            .replace(/&lt;/g, '«__LT__»')
            .replace(/&gt;/g, '«__GT__»');

        // 2) Decodifica TUDO (named + numeric) usando um <textarea>
        const textarea = document.createElement('textarea');
        textarea.innerHTML = masked;
        let decoded = textarea.value;

        // 3) Restaura só os placeholders como &lt; e &gt;
        decoded = decoded
            .replace(/«__LT__»/g, '&lt;')
            .replace(/«__GT__»/g, '&gt;');

        // 4) Escapa quaisquer < ou > que ainda apareçam ?ao vivo?
        decoded = decoded
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        decoded = decoded.replace(/\n/g, "<br>");
        decoded = decoded.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
        return decoded;
    }

    function stripHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }

    function insereResposta(data, divfilho, divpai, dthCadastro) {
        var id_mensagem = data['id_mensagem'];
        divfilho.className = 'interaction client';
        divfilho.id = id_mensagem;
        divfilho.setAttribute('data-render-id', String(id_mensagem));
        var mensagem = data['resposta'];
        var mensagemParseada = markdownToHTML(mensagem, id_mensagem);
        mensagem = safe_tags(mensagem);
        mensagem = stripHtml(mensagem);

        var respostaMontada = '' +
            '<div class="mensagemIdentificador">' +
            '   <div class="iconeIdentificacao">' +
            '       <img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/>' +
            '   </div>' +
            '   <div class="blocoMensagem" style="min-width: 91%">' +
            '       <div class="reasoning-live" style="display:none" data-expandido="false"><button type="button" class="reasoning-toggle" aria-expanded="false"><span class="reasoning-toggle-glyph">&#10022;</span><span class="reasoning-toggle-label">Processamento interno</span><span class="reasoning-toggle-arrow">&#8250;</span></button><div class="reasoning-content"></div></div>' +
            '       <div class="output">' + mensagemParseada + '</div>' +
            '       <div class="status-live" style="display:none;color:#0000007a;font-size:12px;margin-top:6px"></div>' +
            '       <div class="dthCadastro" style="color:#0000007a">' + dthCadastro + '</div>' +
            '   </div>' +
            '</div>';

        var iconesGerais = '' +
            '<div class="acoes_assistente" style="margin-top: -10px">' +
            '   <div class="avaliacao_estrelinhas"></div>' +
            '   <a onclick="abrirEstrelinhas(' + id_mensagem + ')"><div class="estrelinha"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8-6.2-3.2L5.8 21 7 14.2 2 9.3l6.9-1Z"/></svg></div></a>' +
            '   <a onclick="copiar(' + id_mensagem + ')"><div class="copiar copiarMarkdown" id="copiarResposta' + id_mensagem + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5Z"/><path d="M14 2v6h6"/><path d="m10 13-2 2 2 2"/><path d="m14 13 2 2-2 2"/></svg></div></a>' +
            '   <a onclick="copiarFormatado(' + id_mensagem + ')"><div class="copiarFormatado" id="copiarRespostaFormatado' + id_mensagem + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></div></a>' +
            '   <pre>' +
            '       <code class="textoPuro language-markdown" data-trim="false" id="textoPuro' + id_mensagem + '">' + mensagem + '</code>' +
            '   </pre>';

        if (document.getElementById("frmEditor")) {
            //var iconesEditor = '<a onclick="transportarEditor(' + timestamp + ')"><div class="transportarEditor" title="Transportar para Editor"></div></a>';
            var iconesEditor = '';
        } else {
            var iconesEditor = '';
        }

        divfilho.innerHTML = respostaMontada + iconesGerais + iconesEditor + '</div>';

        divpai.appendChild(divfilho);
        var reasoning = obterReasoningInteracao(data);
        var statuses = reasoning ? obterStatusInteracao(data['id_interacao']) : [];
        atualizarPainelReasoning(divfilho, reasoning, statuses);

        elementosCopiarResposta = divfilho.querySelectorAll('.copiarCodigo');
        elementosCopiarResposta.forEach(function(elemento) {
            geraTooltip(elemento.id, "Copiar");
        });

        tippy(".estrelinha", {
            content: "Feedback sobre a resposta do Assistente",
        });
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        geraTooltip('copiarResposta' + id_mensagem, "Copiar Markdown Puro");
        geraTooltip('copiarRespostaFormatado' + id_mensagem, "Copiar Formatado");

        insereCitacaoFonte(divfilho);

    }


    // Função que inicializa popovers para os elementos dentro de divfilho
    function insereCitacaoFonte(divfilho) {

        // Inicialização única: helpers e handlers globais
        if (!window._assistente_citacao_init) {
            window._assistente_citacao_init = true;

            // Escapa HTML (evita XSS se o texto vier do servidor)
            window._escapeHtmlAssistente = function(unsafe) {
                if (unsafe == null) return '';
                return String(unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            };

            // handler do clique (substitui seu atual)
            $(document).on('click', '.AssistenteSEIIAfonteResposta', function(e) {
                e.preventDefault();
                var $btn = $(this);
                // fecha os outros
                $('.AssistenteSEIIAfonteResposta').not($btn).popover('hide');

                // tecla: toggle do popover do botão clicado
                $btn.popover('toggle');

                // quando abrir, ajusta o tamanho conforme a classe .widget-content.expandido
                $btn.off('shown.bs.popover._assistente').on('shown.bs.popover._assistente', function() {
                    // pega o popover criado pelo Bootstrap
                    var pop = $btn.data('bs.popover');
                    var $popEl = $('#' + $btn.attr('aria-describedby'));
                    if (!$popEl || $popEl.length === 0) return;

                    // decide os tamanhos conforme estado
                    var isExpanded = $(".widget-content").hasClass("expandido");
                    var maxW = isExpanded ? "500px" : "350px";
                    var maxH = isExpanded ? "181px" : "150px";
                    var maxHBody = isExpanded ? "150px" : "100px";

                    // aplica com !important usando style.setProperty
                    $popEl.each(function() {
                        this.style.setProperty('max-width', maxW, 'important');
                        this.style.setProperty('max-height', maxH, 'important');
                        // corpo do popover
                        var body = this.querySelector('.popover-body');
                        if (body) body.style.setProperty('max-height', maxHBody, 'important');
                    });

                    // força recálculo do popper (posicionamento correto após o resize do popover)
                    try {
                        var inst = pop && (pop._popper || pop._popperInstance || pop._popper);
                        if (inst) {
                            if (typeof inst.scheduleUpdate === 'function') inst.scheduleUpdate();
                            if (typeof inst.update === 'function') inst.update();
                        }
                    } catch (err) {
                        /* ignore */
                    }
                });
            });


            // Delegated: abrir via teclado (Enter / Space)
            $(document).on('keydown', '.AssistenteSEIIAfonteResposta', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).trigger('click');
                }
            });

            // Não deixar clique dentro do popover "fechar" imediatamente (permite seleção)
            $(document).on('mousedown', '.popoverChatIa', function(e) {
                e.stopPropagation();
            });

            // Fechar ao clicar fora
            $(document).on('click', function(e) {
                if ($(e.target).closest('.AssistenteSEIIAfonteResposta').length === 0 &&
                    $(e.target).closest('.popoverChatIa').length === 0) {
                    $('.AssistenteSEIIAfonteResposta').popover('hide');
                }
            });

            // Esc para fechar
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.AssistenteSEIIAfonteResposta').popover('hide');
                }
            });

            $('#conversa').on('scroll._assistenteCitacao', function() {
                $('.AssistenteSEIIAfonteResposta').popover('hide');
            });

            /* === Novos handlers para evitar popovers "órfãos" ao navegar/trocar página === */

            // 1) Fechar ao clicar em links de navegação comuns (ajuste seletores se necessário)
            $(document).on('click', '.sidebar a, .navbar a, .menu a, .main-nav a, .left-sidebar a', function() {
                $('.AssistenteSEIIAfonteResposta').popover('hide');
            });

            // 2) Intercepta pushState / replaceState em SPAs e dispara evento locationchange
            (function(history) {
                var push = history.pushState;
                history.pushState = function() {
                    var ret = push.apply(history, arguments);
                    window.dispatchEvent(new Event('locationchange'));
                    return ret;
                };
                var replace = history.replaceState;
                history.replaceState = function() {
                    var ret = replace.apply(history, arguments);
                    window.dispatchEvent(new Event('locationchange'));
                    return ret;
                };
            })(window.history);

            window.addEventListener('popstate', function() {
                window.dispatchEvent(new Event('locationchange'));
            });
            window.addEventListener('locationchange', function() {
                $('.AssistenteSEIIAfonteResposta').popover('hide');
            });

            // 3) MutationObserver para limpar popovers órfãos quando triggers forem removidos/substituídos
            //    (evita popovers que "voam" para o topo)
            if (!window._assistente_popover_observer) {
                window._assistente_popover_observer = new MutationObserver(function(mutations) {
                    // percorre popovers existentes e verifica se existe trigger associado
                    $('.popoverChatIa').each(function() {
                        var $pop = $(this);
                        var popId = $pop.attr('id');
                        if (!popId) return;

                        // procura trigger que possua aria-describedby apontando para esse popover
                        var $trigger = $('[aria-describedby="' + popId + '"]');

                        // se não existir trigger ou trigger não estiver mais no DOM, removemos o popover
                        if ($trigger.length === 0 || !document.body.contains($trigger[0])) {
                            // tenta esconder com bootstrap caso trigger exista (seguro)
                            if ($trigger.length) {
                                try {
                                    $trigger.popover('hide');
                                } catch (e) {
                                    /* ignore */
                                }
                            }
                            // remove o popover da DOM
                            $pop.remove();
                        }
                    });
                });

                // observa mudanças no body (subtree true pega alterações profundas)
                window._assistente_popover_observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            // 4) fechar popovers ao recarregar / sair da página (opcional, mas útil)
            $(window).on('beforeunload', function() {
                $('.AssistenteSEIIAfonteResposta').popover('hide');
            });
        } // fim init único


        // --- Agora processa os elementos dentro de divfilho ---
        var elementosCopiarResposta = divfilho.querySelectorAll('.AssistenteSEIIAfonteResposta');

        elementosCopiarResposta.forEach(function(elemento) {
            var texto = elemento.getAttribute('title');
            // remove title nativo para evitar tooltip do browser
            elemento.removeAttribute('title');
            if (!texto) return;

            var referencia = elemento.textContent.trim().match(/^\[(\d+)\]$/);
            if (referencia) {
                elemento.textContent = referencia[1];
                elemento.setAttribute('aria-label', 'Referencia ' + referencia[1]);
            }

            // Separar título | restante (suporta múltiplos '|' juntando o resto)
            var partes = texto.split(' | ');
            var titulo = partes[0] || 'Sem título';
            var restante = partsToString(partes);

            // tornar focável para acessibilidade (se ainda não for)
            if (!elemento.hasAttribute('tabindex')) elemento.setAttribute('tabindex', '0');

            // remover popover anterior se houver (recria)
            try {
                $(elemento).popover('dispose');
            } catch (e) {
                /* ignore */
            }

            // decide se há texto
            var temTexto = String(restante).trim().length > 0;

            // escolhe template (quando sem texto, usamos template sem .popover-body)
            var templateComBody = '<div class="popover popoverChatIa" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>';
            // template sem corpo ? só header e seta (evita espaço em branco)
            var templateSoTitulo = '<div class="popover popoverChatIa popoverChatIa-only-title" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3></div>';

            if (temTexto) {
                $(elemento).popover({
                    trigger: 'manual',
                    placement: 'top',
                    html: true,
                    container: '#chat_ia .widget-content',
                    sanitize: true, // seguro: conteúdo é escapado e só usamos <br> para quebras
                    title: '<strong>' + window._escapeHtmlAssistente(titulo) + '</strong>',
                    content: function() {
                        // escapamos e convertemos quebras de linha em <br>
                        return '<div class="quote-text">' + window._escapeHtmlAssistente(restante).replace(/\n/g, '<br>') + '</div>';
                    },
                    template: templateComBody,
                    delay: {
                        show: 200,
                        hide: 100
                    }
                });
            } else {
                // somente título ? sem corpo (template sem body)
                $(elemento).popover({
                    trigger: 'manual',
                    placement: 'top',
                    html: true,
                    container: '#chat_ia .widget-content',
                    sanitize: true,
                    title: '<strong>' + window._escapeHtmlAssistente(titulo) + '</strong>',
                    content: '', // nada no body
                    template: templateSoTitulo,
                    delay: {
                        show: 150,
                        hide: 80
                    }
                });
            }
        });

        // helper: concatena as partes (mantendo ' | ' extras se houver)
        function partsToString(parts) {
            if (!parts || parts.length <= 1) return '';
            return parts.slice(1).join(' | ');
        }
    }

    function insereCritica(data, divfilho, divpai, dthCadastro) {
        var id_mensagem = data['id_mensagem'];
        divfilho.className = 'interaction client';
        divfilho.id = id_mensagem;
        var mensagem = data['resposta'];
        if (mensagem == "") {
            mensagem = "Ocorreu um erro no Assistente de IA.";
        }
        if (data['tipo_critica'] == "error") {
            var classCritica = "badge badge-danger";
        } else {
            var classCritica = "badge badge-warning";
        }
        var respostaMontada = '' +
            '<div class="mensagemIdentificador">' +
            '   <div class="iconeIdentificacao">' +
            '       <img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div>' +
            '       <div class="blocoMensagem" style="min-width: 91%">' +
            '           <div class="output ' + classCritica + '">' + mensagem + '</div>' +
            '           <div class="dthCadastro" style="color:#0000007a">' + dthCadastro + '</div>' +
            '       </div>' +
            '</div>';

        divfilho.innerHTML = respostaMontada;
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        divpai.appendChild(divfilho);
    }

    function aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idMdIaInteracaoChat;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
            type: 'POST',
            dataType: "json",
            data: dadosMensagem,
            success: function(data) {
                var respostaParcial = data.resposta && data.resposta.resposta ? data.resposta.resposta : "";
                armazenarReasoningInteracao(idMdIaInteracaoChat, data['reasoning']);
                armazenarStatusInteracao(idMdIaInteracaoChat, data['status_execucao']);
                renderizarEstadoCarregamento(divfilho, respostaParcial, data['status_execucao'], data['reasoning'], idMdIaInteracaoChat);

                if (data["result"] == "true") {
                    atualizarStatusExecucao(divfilho, null);
                    $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                    listarTopicos(true);
                } else {
                    setTimeout(function() {
                        aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                    }, 500);
                }

            },
            error: function(err) {
                console.log(err);
                divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="output">Ocorreu um erro no Assistente de IA.</div></div>';
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            }
        });
    }

    function transportarEditor(id) {
        CKEDITOR.tools.callFunction(520, this);
        setInterval(function() {
            document.getElementsByClassName("cke_pasteframe")[0].contentWindow.document.body.outerHTML = $("#" + id + " p").html()
        }, 1000);
    }

    function markdownToHTML(html, id_mensagem) {
        var quantidadeBlocoCodigo = 0;

        // 0) Escapa TODAS as tags <exemplo_*> para não serem capturadas pelo passo 1,

        html = html.replace(/```([\s\S]*?)```/g, function(match, p1) {
            quantidadeBlocoCodigo++;

            const linhas = p1.split('\n');
            let primeiraLinha = linhas.shift().trim().toLowerCase() || 'markdown';
            const blocoCodigo = linhas.join('\n');

            // 3) verifica linguagem válida e faz highlight
            const linguagemValida = hljs.getLanguage(primeiraLinha) ?
                primeiraLinha :
                'markdown';

            var blocoCodigoParseado = "";
            if (linguagemValida === 'markdown' || linguagemValida === 'plaintext') {
                blocoCodigoParseado = decodeHTML(blocoCodigo);
            } else {
                // regex que captura:
                // 1) strings duplas:        "?"
                // 2) strings simples:       '?'
                // 3) comentários lineares:  //? ou --?
                // 4) comentários em bloco:  /*?*/
                const pattern = /"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\/\/[^\n]*|--[^\n]*|\/\*[\s\S]*?\*\//g;

                blocoCodigoParseado = blocoCodigo.replace(pattern, matched => {
                    // decodifica apenas o trecho capturado
                    return decodeHTML(matched);
                });
            }
            const codigoEstilizado = hljs.highlight(blocoCodigoParseado, {
                language: linguagemValida
            }).value;

            // 4) monta o HTML (igual ao seu)
            const highlightedFirstLine = '<div class="highlighted">' +
                linguagemValida +
                '</div>';
            return (
                '<div class="code-container"><pre class="code theme-atom-one-light">' +
                '<div class="code-header">' +
                highlightedFirstLine +
                '<div class="code-actions">' +
                '<a onclick="copyCode(' + id_mensagem + quantidadeBlocoCodigo + ')">' +
                '<div class="copiarCodigo copiar" id="copyButton' +
                id_mensagem + quantidadeBlocoCodigo +
                '"></div>' +
                '</a>' +
                '</div>' +
                '</div>' +
                '<div class="code-body" id="code' + id_mensagem + quantidadeBlocoCodigo + '">' +
                '<code>' + codigoEstilizado + '</code>' +
                '</div>' +
                '</pre></div>'
            );
        });

        html = html.replace(/<(\/?exemplo_[\w-]*)>/g, function(_, tagName) {
            return '&lt;' + tagName + '&gt;';
        });

        // === 1) Extrai QUALQUER bloco <TAG>?</TAG> e substitui por placeholder
        const customBlocks = [];
        html = html.replace(/<([A-Za-z][\w-]*)>[\s\S]*?<\/\1>/g, function(match) {
            const idx = customBlocks.length;
            customBlocks.push(match);
            return `@@CUSTOM_BLOCK_${idx}@@`;
        });

        // === 3) Seu inline <code>?</code>
        var codeBlocks = [];
        html = html.replace(/<code>([\s\S]*?)<\/code>/g, function(match, p1) {
            codeBlocks.push(p1);
            return "<code>" + (codeBlocks.length - 1) + "</code>";
        });

        // === 4) Markdown ? HTML com Marked.js
        marked.use({
            breaks: true
        });

        let parsedText = marked.parse(html);

        // === 5) Restaurar inline <code>
        parsedText = parsedText.replace(/<code>(\d+)<\/code>/g, function(match, p1) {
            return "<code>" + codeBlocks[parseInt(p1)] + "</code>";
        });

        // === 6) Restaurar QUALQUER tag customizada
        parsedText = parsedText.replace(/@@CUSTOM_BLOCK_(\d+)@@/g, function(match, p1) {
            return customBlocks[parseInt(p1)];
        });

        return parsedText;
    }

    function decodeHTML(html) {
        const tmp = document.createElement('textarea');
        tmp.innerHTML = html;
        return tmp.value;
    }

    function copyCode(id_mensagem) {
        var codeElement = document.getElementById("code" + id_mensagem);
        var range = document.createRange();
        range.selectNode(codeElement);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();

        document.getElementById("copyButton" + id_mensagem)._tippy.setContent("Copiado");
        document.getElementById("copyButton" + id_mensagem)._tippy.show();
        setTimeout(function() {
            document.getElementById("copyButton" + id_mensagem)._tippy.hide();
            document.getElementById("copyButton" + id_mensagem)._tippy.setContent("Copiar");
        }, 2000);
    }

    function abrirEstrelinhas(id) {
        if ($("#" + id + " .avaliacao_estrelinhas").html() == "") {
            $("#" + id + " .avaliacao_estrelinhas").html('<div class="rating">\n' +
                '<label class="5" for="star5" onclick="pontuarResposta(5, ' + id + ')"></label>\n' +
                '<label class="4" for="star3" onclick="pontuarResposta(4, ' + id + ')"></label>\n' +
                '<label class="3" for="star2" onclick="pontuarResposta(3, ' + id + ')"></label>\n' +
                '<label class="2" for="star2" onclick="pontuarResposta(2, ' + id + ')"></label>\n' +
                '<label class="1" for="star1" onclick="pontuarResposta(1, ' + id + ')"></label>\n' +
                '</div>');
            $("#" + id + " .estrelinha").css("display", "none");
        } else {
            if ($("#" + id + " .rating").css("display") == "none") {
                $("#" + id + " .rating").css("display", "flex");
                $("#" + id + " .estrelinha").css("display", "none");
            }
        }
    }

    function habilitaEstrelinhas(id_mensagem, pontuacao) {
        $("#" + id_mensagem + " .estrelinha").removeClass("pontuado");
        $("#" + id_mensagem + " .estrelinha").removeClass("pontuadoMeiaEstrela");
        $("#" + id_mensagem + " .rating").css("display", "none");
        $("#" + id_mensagem + " .estrelinha").css("display", "block");
        if (pontuacao <= 2) {
            $("#" + id_mensagem + " .estrelinha").addClass("pontuadoMeiaEstrela");
        } else {
            $("#" + id_mensagem + " .estrelinha").addClass("pontuado");
        }
        for (let pontuacaoMensagem = 5; pontuacaoMensagem != 0; pontuacaoMensagem--) {
            if (pontuacaoMensagem <= pontuacao) {
                $("#" + id_mensagem + " ." + pontuacaoMensagem).addClass("marcado");
            } else {
                $("#" + id_mensagem + " ." + pontuacaoMensagem).removeClass("marcado");
            }
        }
    }

    function pontuarResposta(pontuacao, id_mensagem) {

        habilitaEstrelinhas(id_mensagem, pontuacao);

        var feeedbackUsuario = {};
        feeedbackUsuario["id_mensagem"] = id_mensagem;
        feeedbackUsuario["stars"] = pontuacao;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_assistente_enviar_feedback_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: feeedbackUsuario, // Enviando o JSON com o nome de itens
            async: true,
            error: function(err) {
                console.log(err);
            }

        });
    }

    function copiar(id) {
        var conteudo = document.getElementById("textoPuro" + id).innerText;

        // Cria um elemento de texto temporário
        var tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = conteudo;
        document.body.appendChild(tempInput);

        // Seleciona e copia o conteúdo do texto
        tempInput.select();
        document.execCommand("copy");

        // Remove o elemento de texto temporário
        document.body.removeChild(tempInput);

        document.getElementById("copiarResposta" + id)._tippy.setContent("Copiado");
        document.getElementById("copiarResposta" + id)._tippy.show();
        setTimeout(function() {
            document.getElementById("copiarResposta" + id)._tippy.hide();
            document.getElementById("copiarResposta" + id)._tippy.setContent("Copiar Markdown Puro");
        }, 2000);
    }

    function copiarFormatado(id) {
        var mensagemElement = document.getElementById(String(id));
        var elementoResposta = mensagemElement ? mensagemElement.querySelector('.output') : null;
        if (!elementoResposta) {
            return;
        }

        var conteudoFormatado = elementoResposta.cloneNode(true);
        conteudoFormatado.querySelectorAll('.AssistenteSEIIAfonteResposta').forEach(function(referencia) {
            referencia.remove();
        });

        var idBotao = 'copiarRespostaFormatado' + id;
        var confirmarCopia = function() {
            document.getElementById(idBotao)._tippy.setContent("Copiado");
            document.getElementById(idBotao)._tippy.show();
            setTimeout(function() {
                document.getElementById(idBotao)._tippy.hide();
                document.getElementById(idBotao)._tippy.setContent("Copiar Formatado");
            }, 2000);
        };

        var copiarPorSelecao = function() {
            var temporario = document.createElement('div');
            temporario.style = 'position:absolute;left:-1000px;top:-1000px';
            temporario.appendChild(conteudoFormatado.cloneNode(true));
            document.body.appendChild(temporario);

            var range = document.createRange();
            range.selectNodeContents(temporario);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            var copiado = document.execCommand('copy');
            window.getSelection().removeAllRanges();
            temporario.remove();

            if (copiado) {
                confirmarCopia();
            }
        };

        if (navigator.clipboard && window.ClipboardItem) {
            navigator.clipboard.write([new ClipboardItem({
                'text/html': new Blob([conteudoFormatado.innerHTML], {
                    type: 'text/html'
                }),
                'text/plain': new Blob([conteudoFormatado.innerText], {
                    type: 'text/plain'
                })
            })]).then(confirmarCopia, copiarPorSelecao);
            return;
        }

        copiarPorSelecao();
    }

    function expandirAssistente() {
        var larguraTela = Math.min(parseInt($(window).width()), window.screen.width || parseInt($(window).width()));
        var larguraRealChat = larguraTela - 30;
        $(".widget-content").css({
            "width": larguraRealChat + "px"
        });
        $("#reduzirAssistente").css({
            "display": "inline-flex"
        });
        $("#expandirAssistente").css({
            "display": "none"
        });
        $(".send-message-input").css({
            "width": "100%"
        });
        $(".send-message-input").css("overflow-y", "");
        $('.send-message-input').attr('rows', 5);
        $(".widget-content").addClass('expandido');
        $(".widget-content").removeClass('reduzido');
        $(".send-message-input").height("100");
        $(".send-message-input").css({
            "min-height": "122px"
        });
        $(".send-message-input").css({
            "max-height": "216px"
        });
        $("#assistenteReduzido").css({
            "display": "none"
        });
        $("#assistenteExpandido").css({
            "display": "block"
        });
        $("#painelTopicos").css({
            "display": "block"
        });
        $("#conteudoChat").css({
            "width": $(".widget-content").hasClass("painel-topicos-recolhido") ? "calc(100% - 56px)" : "87%"
        });
        $("#memoriaTopico").css({
            "display": "block"
        });
        $("#buscarWeb").css({
            "display": "block"
        });
        $("#anexarArquivo").css({
            "display": "block"
        });
        $("#conteudoChat .blocoMensagem").css({
            "min-width": "20%"
        });
        controlaTamanhoConteudoChat("expandido");
        if (larguraTela <= 576) {
            setTimeout(function() {
                if ($(".widget-content").hasClass("expandido")) {
                    controlaTamanhoConteudoChat("expandido");
                }
            }, 450);
        }
        agendarAtualizacaoLarguraPaineisReasoningAbertos();
        agendarAtualizacaoPromptsUsuario();
    }

    function alternarPainelTopicos() {
        var widgetContent = document.querySelector('#chat_ia .widget-content');
        var botao = document.getElementById('alternarPainelTopicos');
        if (!widgetContent || !botao) {
            return;
        }

        var recolhido = widgetContent.classList.toggle('painel-topicos-recolhido');
        var descricaoBotao = recolhido ? 'Expandir menu lateral' : 'Recolher menu lateral';
        botao.setAttribute('aria-expanded', recolhido ? 'false' : 'true');
        botao.setAttribute('aria-label', descricaoBotao);
        if (botao._tippy) {
            botao._tippy.setContent(descricaoBotao);
        }
        controlaTamanhoConteudoChat('expandido', false);
        agendarAtualizacaoLarguraPaineisReasoningAbertos();
        agendarAtualizacaoPromptsUsuario();
    }

    function reduzirAssistente() {
        var larguraTela = Math.min(parseInt($(window).width()), window.screen.width || parseInt($(window).width()));
        if (larguraTela < 576) {
            $(".widget-content").css({
                "width": "360px"
            });
        } else {
            $(".widget-content").css({
                "width": "451px"
            });
        }

        $("#reduzirAssistente").css({
            "display": "none"
        });
        $("#expandirAssistente").css({
            "display": "inline-flex"
        });
        $(".widget-content").removeClass('expandido');
        $(".widget-content").addClass('reduzido');
        $('.send-message-input').attr('rows', 1);
        $(".send-message-input").css("height", "30px");
        $(".send-message-input").css({
            "min-height": "30px"
        });
        $(".send-message-input").css({
            "max-height": "72px"
        });
        $("#assistenteExpandido").css({
            "display": "none"
        });
        $("#assistenteReduzido").css({
            "display": "block"
        });
        $("#painelTopicos").css({
            "display": "none"
        });
        $("#conteudoChat").css({
            "width": "100%"
        });
        $("#memoriaTopico").css({
            "display": "none"
        });
        $("#buscarWeb").css({
            "display": "none"
        });
        $("#anexarArquivo").css({
            "display": "none"
        });
        $("#conteudoChat .blocoMensagem").css({
            "min-width": "91%"
        });

        controlaTamanhoConteudoChat();
        agendarAtualizacaoLarguraPaineisReasoningAbertos();
        agendarAtualizacaoPromptsUsuario();
    }

    function controlaTamanhoConteudoChat(display = "", permitirAutoScroll = true) {
        if (display == "") {
            if ($(".widget-content").hasClass("expandido")) {
                var display = "expandido";
            } else {
                var display = "reduzido";
            }
        }
        if (display == "expandido") {
            if (document.getElementById('navInfraBarraNavegacao')) {
                var reducaoAlturaChat = (document.getElementById('navInfraBarraNavegacao').clientHeight) + 30;
            } else if (document.getElementsByClassName('infra-editor__documento').length > 0) {
                var reducaoAlturaChat = 138;
            } else {
                var reducaoAlturaChat = 83;
            }
        } else {
            if (document.getElementById("ifrVisualizacao") && document.getElementById('navInfraBarraNavegacao') && document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("divArvoreAcoes")) {
                var reducaoAlturaChat = (document.getElementById('navInfraBarraNavegacao').clientHeight) + document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("divArvoreAcoes").clientHeight + 62;
            } else if (document.getElementsByClassName('infra-editor__documento').length > 0) {
                var reducaoAlturaChat = 170;
            } else if (document.getElementById('navInfraBarraNavegacao')) {
                var reducaoAlturaChat = (document.getElementById('navInfraBarraNavegacao').clientHeight) + 74;
            } else {
                var reducaoAlturaChat = 125;
            }
        }
        var alturaRealChat = parseInt($(window).height()) - parseInt(reducaoAlturaChat);
        $(".widget-content").css({
            "height": alturaRealChat + "px"
        });
        var alturaConteudoChat;
        var larguraTela = Math.min(parseInt($(window).width()), window.screen.width || parseInt($(window).width()));
        if (display == "expandido" && larguraTela <= 576) {
            alturaConteudoChat = document.querySelector('.widget-container-dialog').clientHeight - document.getElementById('lugarInteracaoUsuario').clientHeight - document.querySelector('.wrapperMensagens').clientHeight - 28;
        } else {
            alturaConteudoChat = alturaRealChat - document.getElementsByClassName('widget-title')[0].clientHeight - document.getElementById('lugarInteracaoUsuario').clientHeight - 56;
        }
        var larguraPainelTopicos = document.getElementById('painelTopicos').clientWidth;
        var larguraConteudoChat = document.getElementById('conteudoChat').clientWidth;
        var larguraTotal = parseInt(larguraPainelTopicos) + parseInt(larguraConteudoChat);
        if ($(window).width() < larguraTotal) {
            var larguraDesejada = (parseInt(larguraTotal) - parseInt($(window).width())) + 212;
            $("#conteudoChat").css({
                "width": larguraDesejada
            });
        }
        controleScroll(alturaConteudoChat, permitirAutoScroll);
        return Promise.resolve("Success");
    }

    function controleScroll(alturaConteudoChat, permitirAutoScroll = true) {
        let somaTamanhoDiv = 0;
        $('#chat_ia .interaction').css('margin', '7px 0');
        $('#conversa > .interaction').each(function() {
            somaTamanhoDiv += $(this).outerHeight(true);
        });
        if (somaTamanhoDiv > alturaConteudoChat) {
            $("#conversa").css({
                "display": "block"
            });
            $("#conversa").css({
                "overflow-y": "scroll"
            });
            $("#conversa").css({
                "height": alturaConteudoChat + "px"
            });
            $("#chat_ia .interaction").css({
                "margin": "15px 0"
            });
            if (permitirAutoScroll) {
                document.getElementById('conversa').scrollBy(0, 10000);
            }
            return false;
        } else {
            $("#conversa").css({
                "display": "flex"
            });
            $("#conversa").css({
                "overflow-y": "auto"
            });
            $("#conversa").css({
                "height": alturaConteudoChat + "px"
            });
            $("#chat_ia .interaction").css({
                "margin": "7px 0"
            });
            if (permitirAutoScroll) {
                document.getElementById('conversa').scrollBy(0, 10000);
            }
            return false;
        }
        if (permitirAutoScroll) {
            document.getElementById('conversa').scrollBy(0, 5000);
        }
    }

    function controlaTamanhoInputDigitacao() {
        var inputDigitacao = $(".send-message-input")[0];
        var scrollHeight = inputDigitacao.scrollHeight;
        var padding = 20;
        if ($(".widget-content").is(".reduzido")) {
            var alturaMaxima = 72;
            var alturaAnterior = inputDigitacao.offsetHeight;
            inputDigitacao.style.height = 'auto';
            scrollHeight = inputDigitacao.scrollHeight;
            inputDigitacao.style.height = Math.min(scrollHeight, alturaMaxima) + 'px';
            inputDigitacao.style.overflowY = scrollHeight > alturaMaxima ? 'auto' : 'hidden';
            if (inputDigitacao.offsetHeight !== alturaAnterior) {
                controlaTamanhoConteudoChat();
            }
        } else {
            if ($(".send-message-input").height() < (scrollHeight - padding) && $(".send-message-input").height() < 190) {
                $(".send-message-input").height(scrollHeight - padding);
                controlaTamanhoConteudoChat();
            }
        }
    }

    function adicionarTopico() {
        $("#conversa").html("");
        controlaActive();
        $("#topicoTemporario").val("true");
        $("#adicionarTopico").removeClass('active');
        var divpai = document.getElementById("conversa");
        var divfilho = document.createElement('div');
        $("#conversa").html("");
        definirTextoMensagem("");
        $("#botaoMemoriaTopico").addClass('botaoAcaoAssistenteIAAtivado');
        atualizarTooltipMemoriaTopico();
        $("#botaoBuscarWeb").removeClass('botaoAcaoAssistenteIAAtivado');
        insereCards(divpai);
        insereBoasVindas(divpai, divfilho);
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
        controlaTamanhoConteudoChat();
    }

    function insereCards(divpai) {
        var divfilho = document.createElement('div');
        divfilho.innerHTML = '<?= MdIaConfigAssistenteINT::htmlBotoesNovoChatChat() ?>';
        divpai.appendChild(divfilho);
    }

    function retornaNomeMesAmigavel(mesAntes) {
        // Criando um objeto Date para a data atual
        var dataAtual = new Date();

        // Subtraindo um mês
        dataAtual.setMonth(dataAtual.getMonth() - mesAntes);

        // Obtendo o número do mês (0 a 11, onde 0 é janeiro e 11 é dezembro)
        var ultimoMesNumero = dataAtual.getMonth();

        // Convertendo para o nome do mês (opcional)
        var meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        var ultimoMesNome = meses[ultimoMesNumero];
        return ultimoMesNome;
    }

    function nomeAmigavelPeriodo(periodo) {
        var dataAtual = new Date(); // Cria um objeto Date para a data atual
        var anoAtual = dataAtual.getFullYear(); // Obtém o ano atual

        switch (periodo) {
            case "hoje":
                return "Hoje";
                break;
            case "ontem":
                return "Ontem";
                break;
            case "ultimos7dias":
                return "Últimos 7 dias"
                break;
            case "ultimos30dias":
                return "Últimos 30 dias"
                break;
            case "ultimoMes":
                return retornaNomeMesAmigavel(1)
                break;
            case "penultimoMes":
                return retornaNomeMesAmigavel(2)
                break;
            case "antepenultimoMes":
                return retornaNomeMesAmigavel(3)
                break;
            case "anoAtual":
                return anoAtual;
                break;
            case "ultimoAno":
                return (anoAtual - 1);
                break;
            default:
                return "Mais Antigos";
                break;
        }
    }

    function listarTopicos(preservarScrollConversa = false) {
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_listar_topicos'); ?>',
            type: 'GET', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            success: function(data) {
                var topicoAtivo = "";
                if (data["budgetTokens"]["extrapolouLimiteTokens"]) {
                    limiteDiarioUltrapassado();
                }

                if (data["topicos"] != false) {
                    $("#listagemTopicos").html("");
                    var divPai = $("#listagemTopicos");
                    // Ordem definida dos períodos
                    var ordemPeriodos = ['hoje', 'ontem', 'ultimos7dias', 'ultimos30dias', 'ultimoMes', 'penultimoMes', 'antepenultimoMes', 'anoAtual', 'ultimoAno', 'maisAntigos'];

                    // Iterar sobre os períodos na ordem correta
                    ordemPeriodos.forEach(function(periodo) {
                        // Verificar se o período existe nos dados
                        if (data["topicos"].hasOwnProperty(periodo)) {
                            var topicos = data["topicos"][periodo]; // Array de tópicos dentro deste período
                            var nomeTopico = nomeAmigavelPeriodo(periodo);

                            // Adicionar o título da seção do período
                            divPai.append('<div class="section-title">' + nomeTopico + '</div>');

                            // Iterar sobre os tópicos dentro deste período
                            topicos.forEach(function(topico) {
                                if (topico["ativo"] == false) {
                                    divPai.append(criarElementoTopico(topico, false));
                                } else {
                                    topicoAtivo = topico["idTopico"];
                                    divPai.append(criarElementoTopico(topico, true));
                                }
                            });
                        }
                    });
                } else {
                    $("#listagemTopicos").html("");
                }
                selecionaTopico(topicoAtivo, false, preservarScrollConversa);
                $('#listagemTopicos').animate({
                    scrollTop: 0
                }, "slow");
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function mostrarAcoesTopico(elemento) {
        var acoesTopico = elemento.querySelector('.acoesTopico');
        acoesTopico.classList.add('acoesVisiveis');
    }

    function ocultarAcoesTopico(elemento) {
        var acoesTopico = elemento.querySelector('.acoesTopico');
        acoesTopico.classList.remove('acoesVisiveis');
    }

    function selecionaTopico(id = "", limparCampos = true, preservarScrollConversa = false) {
        if (id) {
            controlaActive(id);
        }
        var scrollConversaAnterior = preservarScrollConversa ? document.getElementById('conversa').scrollTop : null;
        insereLoading();
        var dadosTopico = {};
        dadosTopico["id"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_selecionar_topico'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            async: false,
            data: dadosTopico, // Enviando o JSON com o nome de itens
            success: function(data) {
                var divpai = document.getElementById("conversa");
                var divfilho = document.createElement('div');
                $("#conversa").html("");
                if (limparCampos) {
                    definirTextoMensagem("");
                    $("#botaoMemoriaTopico").addClass('botaoAcaoAssistenteIAAtivado');
                    atualizarTooltipMemoriaTopico();
                    $("#botaoBuscarWeb").removeClass('botaoAcaoAssistenteIAAtivado');
                }
                if (data != "") {
                    insereBoasVindas(divpai, divfilho);
                    data.forEach(function(interacao) {
                        armazenarHistoricoStatusInteracao(interacao["id_interacao"], interacao["status_execucao_historico"]);
                        resolveItensEnvioMensagem(interacao["pergunta"], interacao["dadosCitacoes"], interacao["id_interacao"], interacao["favorito"], interacao['dth_cadastro'], interacao['upload']);
                        var divfilho = document.createElement('div');

                        if (interacao["status_requisicao"] == "200" && interacao["resposta"] != "") {

                            insereResposta(interacao, divfilho, divpai, interacao['dth_cadastro']);

                            if (interacao["feedback"] >= '1') {
                                abrirEstrelinhas(interacao["id_mensagem"]);
                                habilitaEstrelinhas(interacao["id_mensagem"], interacao["feedback"]);
                            }

                        } else if (interacao["status_requisicao"] !== "" && interacao["status_requisicao"] !== null && typeof interacao["status_requisicao"] !== "undefined" && (interacao["status_requisicao"] != "200" || interacao["tempo_execucao"] > 0)) {

                            insereCritica(interacao, divfilho, divpai, interacao['dth_cadastro']);

                        } else {
                            if (divfilho.parentNode == divpai && divfilho.innerHTML == "") {
                                divpai.removeChild(divfilho);
                            }

                            var divLoading = carregandoResposta(divpai);
                            renderizarEstadoCarregamento(divLoading, interacao["resposta"], null, interacao["reasoning"], interacao["id_interacao"]);

                            setTimeout(function() {
                                aguardandoResposta(interacao["id_interacao"], divLoading, divpai);
                            }, 500);
                        }
                    });
                } else {
                    insereCards(divpai);
                    insereBoasVindas(divpai, divfilho);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
        if (id != "") {
            $("#topicoTemporario").val("false");
        } else {
            $("#topicoTemporario").val("true");
        }
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
        controlaTamanhoConteudoChat();
        if ($(".widget-content").hasClass("expandido")) {
            expandirAssistente();
        } else {
            reduzirAssistente();
        }
        if (scrollConversaAnterior !== null) {
            document.getElementById('conversa').scrollTop = scrollConversaAnterior;
        }
    }

    function controlaActive(id = "") {
        // Remove a classe "active" de todos os botões
        var botoes = document.querySelectorAll('.nav-link');
        botoes.forEach(function(botao) {
            botao.classList.remove('active');
            botao.parentNode.parentNode.classList.remove('active');
        });
        if (id != "") {
            // Adiciona a classe "active" apenas ao botão clicado
            var botaoClicado = document.getElementById('topico' + id);
            botaoClicado.classList.add('active');
            botaoClicado.parentNode.parentNode.classList.add('active');
        }
    }

    function editarTopico(id) {

        // Seleciona o botão original
        var botaoOriginal = $("#topico" + id);

        // Obtém os atributos do botão original
        var atributos = botaoOriginal.prop("attributes");
        var atributosObj = {};
        $.each(atributos, function() {
            if (this.specified) {
                atributosObj[this.name] = this.value;
            }
        });

        // Obtém o conteúdo do botão original
        var conteudo = botaoOriginal.text();

        $("#acoesTopico" + id).addClass('topicoEmEdicao');

        // Cria um input editável com os mesmos atributos do botão original
        var inputEditavel = $("<input>").attr(atributosObj).attr({
            "type": "text",
            "value": conteudo,
            "maxlength": 80 // Adiciona o limite de caracteres
        });

        // Substitui o botão pelo input
        botaoOriginal.replaceWith(inputEditavel);

        // Adiciona um evento de input para monitorar o número de caracteres digitados
        inputEditavel.on("input", function() {
            if (this.value.length > 80) {
                this.value = this.value.slice(0, 80); // Limita o número de caracteres
            }
        });

        // Adiciona um evento para restaurar o botão quando o input perder o foco
        inputEditavel.blur(function() {
            // Obtém o valor do input
            var novoNomeTopico = $(this).val();

            var dadosTopico = {};
            dadosTopico["id_topico"] = id;
            dadosTopico["nome_topico"] = novoNomeTopico;
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_renomear_topico'); ?>',
                type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
                dataType: "json", //Tipo de dado que será enviado ao servidor
                data: dadosTopico, // Enviando o JSON com o nome de itens
                error: function(err) {
                    console.log(err);
                }
            });
            // Cria um novo botão com o valor atualizado e os mesmos atributos
            var novoBotao = $("<button>").addClass("nav-link text-left").attr(atributosObj);
            novoBotao.append(criarIconeTopicoSvg());
            novoBotao.append(document.createTextNode(novoNomeTopico));
            // Substitui o input pelo botão
            $(this).replaceWith(novoBotao);
            $("#acoesTopico" + id).removeClass('topicoEmEdicao');
        });

        inputEditavel.trigger("focus");
        inputEditavel[0].setSelectionRange(conteudo.length, conteudo.length);
    }

    function arquivarTopico(id) {
        var dadosTopico = {};
        dadosTopico["id_topico"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_arquivar_topico'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosTopico, // Enviando o JSON com o nome de itens
            success: function(data) {
                $("#conversa").html("");
                listarTopicos();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function favoritarChat(idPrompt) {
        $("#hdnIdPromptSelecionado").val(idPrompt);
        infraAbrirJanelaModal("<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_cadastrar') ?>",
            900,
            700, false);
    }

    function publicarGaleriaPrompt(idPrompt) {
        $("#hdnIdPromptSelecionado").val(idPrompt);
        infraAbrirJanelaModal("<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_cadastrar') ?>",
            1200,
            800, false);
    }

    function galeriaPrompts() {
        infraAbrirJanelaModal("<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_galeria_prompts_selecionar&tipo_selecao=2') ?>",
            1200,
            800, false);
    }

    function atualizarTooltipMemoriaTopico() {
        var botao = document.getElementById('botaoMemoriaTopico');
        if (!botao) {
            return;
        }

        var descricaoBotao = botao.classList.contains('botaoAcaoAssistenteIAAtivado') ?
            'Desativar Memória do Tópico' : 'Utilizar Memória do Tópico';
        botao.setAttribute('aria-label', descricaoBotao);
        if (botao._tippy) {
            botao._tippy.setContent(descricaoBotao);
        }
    }

    function ativaBotaoAcao(botao) {
        if (!botao.classList.contains('botaoAcaoAssistenteIAAtivado')) {
            botao.classList.add('botaoAcaoAssistenteIAAtivado');
        } else {
            botao.classList.remove('botaoAcaoAssistenteIAAtivado');
        }
        if (botao.id === 'botaoMemoriaTopico') {
            atualizarTooltipMemoriaTopico();
        }
    }

    function dateTimeAgoraFormatado() {
        const d = new Date();

        const pad = num => String(num).padStart(2, '0');

        const dia = pad(d.getDate());
        const mes = pad(d.getMonth() + 1);
        const ano = d.getFullYear();
        const hora = pad(d.getHours());
        const min = pad(d.getMinutes());
        const seg = pad(d.getSeconds());

        return `${dia}/${mes}/${ano} ${hora}:${min}:${seg}`;
    }

    function getTextForTextarea(sanitizedHtml) {
        // converte eventuais <br> em \n
        let text = sanitizedHtml.replace(/<br\s*\/?>/gi, '\n');

        // Usa textarea para decodificar entidades HTML sem interpretar tags como HTML
        // Ex: <Manual TCU ? Estimativas> não é removido como seria num div
        const tmp = document.createElement('textarea');
        tmp.innerHTML = text;
        return tmp.value; // contém as quebras de linha reais e tags literais intactas
    }

    // 1. Variável global para armazenar os arquivos
    var arquivosSelecionados = [];

    // --- INICIALIZAÇÃO DE EVENTOS (Coloque isso dentro do seu document.ready ou DOMContentLoaded) ---
    document.addEventListener("DOMContentLoaded", function() {

        // EVENTO CTRL+V (PASTE)
        const campoMensagem = document.getElementById('mensagem');
        if (campoMensagem) {
            document.getElementById('mensagem').addEventListener('paste', function(e) {
                var items = (e.clipboardData || e.originalEvent.clipboardData).items;

                for (var index in items) {
                    var item = items[index];
                    if (item.kind === 'file' && item.type.indexOf('image') !== -1) {
                        var blob = item.getAsFile();

                        // Para prints colados, o navegador muitas vezes não dá nome.
                        // Se você quiser evitar colar o MESMO print em sequência:
                        var arquivo = new File([blob], 'print_' + Date.now() + '.png', {
                            type: blob.type
                        });

                        if (!validarArquivo(arquivo)) {
                            continue;
                        }

                        if (!validarTamanhoTotalArquivos(arquivosSelecionados.concat([arquivo]))) {
                            continue;
                        }

                        e.preventDefault();
                        arquivosSelecionados.push(arquivo);

                    }
                }
                renderizarListaArquivos();
            });
        }
    });

    // 2. Função disparada pelo botão de anexo (input file)
    function processarArquivosSelecionados(input) {
        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const arquivo = input.files[i];

                // AQUI ENTRA A VALIDAÇÃO
                if (arquivoJaExiste(arquivo)) {
                    alert("O arquivo '" + arquivo.name + "' já foi adicionado.");
                    continue; // Pula para o próximo arquivo do loop sem adicionar
                }

                if (!validarArquivo(arquivo)) {
                    continue;
                }

                if (!validarTamanhoTotalArquivos(arquivosSelecionados.concat([arquivo]))) {
                    continue;
                }

                arquivosSelecionados.push(arquivo);
            }
            renderizarListaArquivos();
        }
        input.value = "";
    }

    // 3. Função que desenha os "chips" na tela (ESTILO CHATGPT)
    function renderizarListaArquivos() {
        const container = document.getElementById('containerListaArquivos');
        if (!container) return;

        container.innerHTML = '';

        if (arquivosSelecionados.length === 0) {
            container.style.display = 'none';
            // Remove margens para garantir que não ocupará 1px sequer
            container.style.margin = '0';
            container.style.padding = '0';
        } else {
            container.style.display = 'flex';
            // Restaura as margens apenas quando houver arquivos
            container.style.marginTop = '10px';
            container.style.marginBottom = '5px';

            arquivosSelecionados.forEach((arquivo, index) => {
                // ... seu código de criação dos chips/ícones ...
            });
        }

        arquivosSelecionados.forEach((arquivo, index) => {
            const chipDiv = document.createElement('div');
            chipDiv.classList.add('file-chip'); // Classe base que deve ter overflow: visible no CSS

            const isImage = arquivo.type.startsWith('image/');

            if (isImage) {
                // --- VISUAL DE IMAGEM ---
                chipDiv.classList.add('chip-tipo-imagem');

                // Div interna para a imagem (para o CSS aplicar border-radius na foto sem cortar o botão X)
                const imgPreview = document.createElement('div');
                imgPreview.classList.add('image-preview-box');

                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(arquivo);

                chipDiv.appendChild(imgPreview);

            } else {
                // --- VISUAL DE DOCUMENTO ---
                chipDiv.classList.add('chip-tipo-doc');

                const icone = obterIconePorNomeArquivo(arquivo.name);
                const extensao = arquivo.name.split('.').pop().toLowerCase();
                let iconClass = 'default-doc';

                chipDiv.innerHTML = `
                    <div class="doc-icon-box default-doc">
                        <img src="${icone}">
                    </div>
                    <div class="doc-info">
                        <span class="doc-name" title="${arquivo.name}">${arquivo.name}</span>
                        <span class="doc-ext">${extensao.toUpperCase()} File</span>
                    </div>
                `;
            }

            // Botão de remover (O "X" que flutua na quina)
            const btnRemover = document.createElement('button');
            btnRemover.innerHTML = '×';
            btnRemover.type = 'button';
            btnRemover.classList.add('btn-remover-arquivo');
            btnRemover.onclick = function() {
                removerArquivo(index);
            };

            chipDiv.appendChild(btnRemover);
            container.appendChild(chipDiv);
        });

        // Chama sua função existente para reajustar o layout do chat
        if (typeof controlaTamanhoConteudoChat === "function") {
            controlaTamanhoConteudoChat();
        }
    }

    // 4. Função para remover arquivo
    function removerArquivo(index) {
        arquivosSelecionados.splice(index, 1);
        renderizarListaArquivos();
    }

    // 5. Limpar tudo após o envio
    function limparTodosArquivos() {
        arquivosSelecionados = [];
        renderizarListaArquivos();
    }

    function arquivoJaExiste(novoArquivo) {
        return arquivosSelecionados.some(arquivo =>
            arquivo.name === novoArquivo.name &&
            arquivo.size === novoArquivo.size &&
            arquivo.lastModified === novoArquivo.lastModified
        );
    }

    function validarArquivo(arquivo) {
        const ext = arquivo.name.toLowerCase().split('.').pop();

        // 1) extensão permitida?
        if (!Object.prototype.hasOwnProperty.call(ArrExtensoesAceitas, ext)) {
            const extensoesPermitidas = Object.keys(ArrExtensoesAceitas).join(', ');
            alert(`A extensão "${ext.toUpperCase()}" não é permitida. As extensões permitidas são: ${extensoesPermitidas}.`);
            return false;
        }

        // 2) tamanho por extensão
        const limiteMb = ArrExtensoesAceitas[ext];
        if (limiteMb && limiteMb > 0) {
            const limiteBytes = limiteMb * 1024 * 1024;
            if (arquivo.size > limiteBytes) {
                alert(`Arquivo "${arquivo.name}" com tamanho acima do permitido. O limite de tamanho para a extensão ${ext.toUpperCase()} é de ${limiteMb} MB.`);
                return false;
            }
        }

        return true;
    }

    function validarArquivosSelecionadosAntesEnvio() {
        for (let i = 0; i < arquivosSelecionados.length; i++) {
            if (!validarArquivo(arquivosSelecionados[i])) {
                return false;
            }
        }

        return validarTamanhoTotalArquivos(arquivosSelecionados);
    }

    function validarTamanhoTotalArquivos(arquivos) {
        const tamanhoTotalBytes = arquivos.reduce(function(total, arquivo) {
            return total + arquivo.size;
        }, 0);
        const tamanhoMaximoBytes = TAMANHO_MAXIMO_UPLOAD_TOTAL_MB * 1024 * 1024;

        if (tamanhoTotalBytes > tamanhoMaximoBytes) {
            alert(`O tamanho total dos arquivos excede o limite de ${TAMANHO_MAXIMO_UPLOAD_TOTAL_MB} MB por mensagem.`);
            return false;
        }

        return true;
    }

    function gerarHtmlArquivosHistorico(uploads) {
        if (!uploads || !Array.isArray(uploads) || uploads.length === 0) {
            return "";
        }

        let html = '<div class="container-arquivos-historico">';

        uploads.forEach(function(item) {

            let nomeArquivo = null;

            if (typeof item === 'string') {
                nomeArquivo = item;
            } else if (item instanceof File) {
                nomeArquivo = item.name;
            } else if (typeof item === 'object' && item.nome) {
                nomeArquivo = item.nome;
            }

            if (!nomeArquivo) return;

            const extensao = nomeArquivo.split('.').pop().toLowerCase();
            const icone = obterIconePorNomeArquivo(nomeArquivo);

            html += `
                <div class="file-chip-historico">
                    <div class="doc-icon-box default-doc">
                        <img src="${icone}" alt="Arquivo" />
                    </div>
                    <div class="doc-info">
                        <span class="doc-name" title="${nomeArquivo}">
                            ${nomeArquivo}
                        </span>
                        <span class="doc-ext">${extensao.toUpperCase()} File</span>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        return html;
    }

    function obterIconePorNomeArquivo(nomeArquivo) {
        if (!nomeArquivo || !ArrIcones) {
            return ArrIcones.default;
        }

        const ext = nomeArquivo.split('.').pop().toLowerCase();
        return ArrIcones.mapa[ext] || ArrIcones.default;
    }
</script>