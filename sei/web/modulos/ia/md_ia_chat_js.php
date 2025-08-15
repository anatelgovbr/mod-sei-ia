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
        /* document.getElementById("botaoTransferir").style.display = 'none';
         if(document.getElementById("ifrVisualizacao")) {
             document.getElementById("ifrVisualizacao").onload = function () {
                 var funcaoAcionada = false;
                 if (document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml')) {
                     document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml').onmouseover = function () {
                         if (!funcaoAcionada) {
                             funcaoAcionada = true;
                             acionarListener();
                         }
                     };
                 }
             };
         } else if(document.getElementById("frmEditor")) {
             acionarListenerEditor();
         }*/
        $('.send-message-input').on('keydown input', function(event) {
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
            // qualquer outra mudança no valor (inclui paste)
            if (event.type === 'input') {
                setTimeout(() => {
                    controlaPreenchimentoCampoMensagem();
                    controlaTamanhoInputDigitacao();
                }, 500);
            }
        });

        $('[name=star]').click(function() {
            var valueis = $(this).attr('id-message');
            alert(valueis);
        });

        tippy(".iconeOrientacoesGerais", {
            content: "Orientações Gerais",
        });

        tippy(".titleGaleriaPrompt", {
            content: "Galeria de Prompts",
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
            content: "Promps Favoritos",
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

        tippy("#titleBuscarWeb", {
            content: "Buscar na Web",
        });

        tippy("#botaoRefletir", {
            content: "Pensar antes de responder",
        });

        tippy(".iconeAdicionarTopico", {
            content: "Novo Tópico",
        });
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

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.botao-com-menu')) {
            document.querySelectorAll('.submenu-opcoes').forEach(function(el) {
                el.style.display = 'none';
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

    function exibirOpcoes(botao) {

        const submenu = botao.closest('.botao-com-menu').querySelector('.submenu-opcoes');
        const display = submenu.style.display;

        // Fecha todos os outros
        document.querySelectorAll('.submenu-opcoes').forEach(el => el.style.display = 'none');

        // Toggle visibilidade
        if (display === 'block') {
            submenu.style.display = 'none';
            return;
        }

        submenu.style.display = 'block';

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
        if ($("#mensagem").val() != "" && !$("#botaoEnviarMensagem").hasClass("aguardandoResposta") && !$("#botaoEnviarMensagem").hasClass("apiIndisponivel") && !$("#botaoEnviarMensagem").hasClass("limiteDiarioExcedido")) {
            $("#botaoEnviarMensagem").addClass("habilitadoEnvio");

        } else {
            $("#botaoEnviarMensagem").removeClass("habilitadoEnvio");
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
        document.getElementById("mensagem").value = document.getElementById('textoSelecionado').value;
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

    function resolveItensEnvioMensagem(mensagem, dadosCitacoes = "", idInteracao = "", favorito = "", dthCadastro) {

        mensagem = decodeHtmlEntitiesPergunta(mensagem);
        if (dadosCitacoes != "" && dadosCitacoes != null) {
            if (Array.isArray(dadosCitacoes)) {
                dadosCitacoes.forEach((citacao) => {
                    mensagem = mensagem.replace(citacao["citacaoRealizada"], "<a target='_blank' href='" + citacao["linkAcesso"] + "'>" + citacao["citacaoRealizada"] + "</a>");
                });
            }
        }
        var divPai = $("#conversa");
        var uniqueID = generateUniqueID();

        var respostaMontada = '' +
            '<div class="interaction agent">' +
            '   <div class="mensagemIdentificador">' +
            '       <div class="iconeIdentificacao">' +
            '           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">' +
            '               <path fill="#dc3545" d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>' +
            '           </svg>' +
            '       </div>' +
            '       <div class="blocoMensagem" style="min-width: 91%">' +
            '           <div class="input" id="textoPuro' + uniqueID + '">' + mensagem + '' + '</div>' +
            '           <div class="dthCadastro" style="color:#0000007a">' + dthCadastro + '</div>' +
            '       </div>' +
            '   </div>';

        var iconesGerais = '<div class="acoes_assistente" style="margin-top: -10px">' +
            '<a onclick="copiar(' + uniqueID + ')"><div class="copiar" id="copiarResposta' + uniqueID + '"></div></a>';

        var iconeFavoritarChat = "";

        if (idInteracao != "") {
            if (favorito) {
                iconeFavoritarChat = '<a onclick="favoritarChat(' + idInteracao + ')"><div class="promptFavoritado" id="promptFavoritado' + idInteracao + '"></div></a>';
            } else {
                iconeFavoritarChat = '<a onclick="favoritarChat(' + idInteracao + ')"><div class="favoritarPrompt" id="favoritarPrompt' + idInteracao + '"></div></a>';
            }
        }

        var iconePublicarGaleriaPrompt = '<a onclick="publicarGaleriaPrompt(' + idInteracao + ')"><div class="publicarGaleriaPrompt" id="publicarGaleriaPrompt' + idInteracao + '"></div></a></div>';

        var perguntaCliente = respostaMontada + iconesGerais + iconeFavoritarChat + iconePublicarGaleriaPrompt;
        divPai.append(perguntaCliente);
        document.getElementById('conversa').scrollBy(0, 5000);

        if ($(".widget-content").hasClass("expandido")) {
            $(".send-message-input").height("100");
            expandirAssistente();
        } else {
            $(".send-message-input").height("45");
            reduzirAssistente();
        }
        elementosCopiarResposta = document.getElementById('conversa').querySelectorAll('.copiar');
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

    function identificarProtocolo(mensagem) {

        let possiveisCitacoes = mensagem.match(/#[^\s#]+/g);
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

                        if (padraoColchetes.test(possivelCitacao)) {
                            let verificaColchete = /#[0-9]+\[[0-9]+(:[0-9]+)?]/;
                            if (verificaColchete.test(possivelCitacao)) {
                                documentos.push(possivelCitacao.match(verificaColchete)[0]);
                            } else {
                                $("#validacaoMensagem").html('A indicação de intervalo de páginas sobre o Documento Externo está em formato irregular. Deve utilizar o formato [p:p] um intervalo ou [p] para indicar página única: #01234567[10:15] ou #01234567[20].');
                                estadoDeInteracao();
                                indicacaoFolhaIncorreta = true;
                                return false;
                            }
                        } else {
                            let regexValido = /#[0-9]+[^[\s]*?(?=\s|$|\n|[.,;:?!)](?![^.,;:?!)]))/g;
                            if (regexValido.test(possivelCitacao)) {
                                documentos.push(possivelCitacao.match(regexValido)[0]);
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
        $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "none");
        $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "block");

        if (documentos == null || documentos.length === 0) {
            verificaJanelaContexto(mensagem);
        } else {
            documentos = documentos.filter((value, index, array) => array.indexOf(value) === index);
            let protocolo = {};
            const urlParams = new URLSearchParams(window.location.search);
            protocolo["documento"] = documentos;
            protocolo["acao_origem"] = urlParams.get("acao_origem");
            protocolo["acesso"] = urlParams.get("acesso");
            protocolo["id_procedimento"] = urlParams.get("id_procedimento");
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consulta_protocolo_assistente_ia_ajax'); ?>',
                type: 'POST',
                dataType: "json",
                data: protocolo,
                async: false,
                success: function(dadosCitacoes) {
                    if (!Array.isArray(dadosCitacoes)) {
                        if (dadosCitacoes["result"] == "false") {
                            $("#validacaoMensagem").html(dadosCitacoes["mensagem"]);
                            estadoDeInteracao();
                        }
                    } else {
                        var mensagem = $("#mensagem").val();
                        verificaJanelaContexto(mensagem, dadosCitacoes)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    }

    function estadoDeInteracao() {
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        controlaTamanhoConteudoChat();
        controlaPreenchimentoCampoMensagem();
        $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "block");
        $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "none");
    }

    function enviarMensagem() {
        if ($("#botaoEnviarMensagem").hasClass("habilitadoEnvio") && !$("#botaoEnviarMensagem").hasClass("aguardandoResposta")) {
            $("#botaoEnviarMensagem").removeClass("habilitadoEnvio");
            $("#botaoEnviarMensagem").addClass("aguardandoResposta");
            var mensagem = $("#mensagem").val();
            identificarProtocolo(mensagem);
        }
    }

    function verificaJanelaContexto(mensagem, dadosCitacoes = "", ) {

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
            $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "block");
            $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "none");
            $("#mensagem").val("");
            resolveItensEnvioMensagem(mensagem, dadosCitacoes, null, null, dateTimeAgoraFormatado());
            controlaTamanhoConteudoChat().then(function() {
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
                success: function(data) {
                    console.log(data);
                },
                error: function(err) {
                    console.log(err);
                }
            }).done(function() {
                $("#validacaoMensagem").html("O texto da mensagem ultrapassou o limite permitido. Reduza o texto e tente novamente.");
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "block");
                $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "none");
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
        var divfilho = document.createElement('div');
        divfilho.className = 'interaction client dots';
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="output">Aguarde<span>.</span><span>.</span><span>.</span></div></div>';
        divpai.appendChild(divfilho);
        return divfilho;
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
        var mensagemUsuario = {};
        mensagemUsuario["text"] = encodeURIComponent(mensagem);
        mensagemUsuario["dadosCitacoes"] = dadosCitacoes;
        mensagemUsuario["topicoTemporario"] = $("#topicoTemporario").val();
        document.getElementById('conversa').scrollBy(0, 5000);
        if ($("#botaoRefletir").hasClass("botaoAcaoAssistenteIAAtivado")) {
            mensagemUsuario["refletir"] = true;
        } else {
            mensagemUsuario["refletir"] = false;
        }
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_assistente_envia_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: mensagemUsuario, // Enviando o JSON com o nome de itens
            success: function(data) {
                if (data["error"]) {
                    $("#botaoEnviarMensagem").addClass("limiteDiarioExcedido");
                    $("#validacaoMensagem").html(data["mensagem"]);
                    listarTopicos();
                } else {
                    if ($("#topicoTemporario").val() == "true") {
                        $("#topicoTemporario").val("false");
                        listarTopicos();
                    } else {
                        var idMdIaInteracaoChat = data;
                        setTimeout(function() {
                            aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                        }, 5000);
                    }
                }
            },
            error: function(err) {
                console.log(err);
                divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/></div><div class="output">Ocorreu um erro no Assistente de IA.</div></div>';
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            }
        });
    }

    function geraTooltip(idElemento, titulo) {
        tippy("#" + idElemento, {
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

    function decodeHtmlEntities(encodedString) {

        // 1) Decodifica entidades HTML
        const tmp = document.createElement('textarea');
        tmp.innerHTML = encodedString;
        let decoded = tmp.value;

        // 2) Escapa ONLY tags desconhecidas
        decoded = decoded.replace(
            /<\/?([a-zA-Z][\w-]*)\b([^>]*)>/g,
            (match, tagName) => {
                const el = document.createElement(tagName);
                if (el instanceof HTMLUnknownElement) {
                    return match
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }
                return match;
            }
        );

        // 3) Parseia o HTML ?protegido? e remove scripts/atributos XSS,
        //    mas agora PRESERVA o onclick
        const doc = new DOMParser().parseFromString(decoded, 'text/html');
        (function walk(node) {
            let child = node.firstChild;
            while (child) {
                const next = child.nextSibling;
                if (child.nodeType === Node.ELEMENT_NODE) {
                    if (['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED'].includes(child.tagName)) {
                        node.removeChild(child);
                    } else {
                        Array.from(child.attributes).forEach(attr => {
                            const name = attr.name.toLowerCase();
                            const value = attr.value;

                            // ? MANTÉM onclick
                            if (name === 'onclick') {
                                return;
                            }
                            // remove qualquer outro on*  
                            if (/^on/.test(name)) {
                                child.removeAttribute(attr.name);
                            }
                            // remove javascript:? em href/src
                            else if (/^javascript:/i.test(value)) {
                                child.removeAttribute(attr.name);
                            }
                        });
                        walk(child);
                    }
                }
                child = next;
            }
        })(doc.body);

        // 4) Sanitiza com DOMPurify, permitindo explicitamente onclick
        const sanitized = DOMPurify.sanitize(doc.body.innerHTML, {
            ADD_ATTR: ['onclick']
        });

        // 5) Conserta possíveis &amp;gt;
        var resposta = sanitized.replace(/&amp;gt;/g, '>');
        resposta = resposta.replace(/\n/g, "<br>");
        resposta = resposta.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
        resposta = cleanUpBreaks(resposta);
        return resposta;
    }

    function cleanUpBreaks(html) {
        return html
            // 1) remove <br> antes ou depois de tags de bloco, incluindo <table>, <thead>, <tbody>, <tr>, <th>, <td>
            .replace(
                /(<\/?(?:h[1-6]|p|ul|ol|li|table|thead|tbody|tr|th|td)[^>]*>)[ \t\r\n]*(?:<br\s*\/?>[ \t\r\n]*)+/gi,
                '$1'
            )
            // 2) remove <br> sobrando no fim do documento
            .replace(/(?:<br\s*\/?>[ \t\r\n]*)+$/gi, '');
    }

    function insereResposta(data, divfilho, divpai, dthCadastro) {
        var id_mensagem = data['id_mensagem'];
        divfilho.className = 'interaction client';
        divfilho.id = id_mensagem;
        var mensagem = data['resposta'];
        var mensagemParseada = markdownToHTML(mensagem, id_mensagem);
        mensagem = safe_tags(mensagem);

        var respostaMontada = '' +
            '<div class="mensagemIdentificador">' +
            '   <div class="iconeIdentificacao">' +
            '       <img src="modulos/ia/imagens/md_ia_icone.svg?' + <?= Icone::VERSAO ?> + '"/>' +
            '   </div>' +
            '   <div class="blocoMensagem" style="min-width: 91%">' +
            '       <div class="output">' + mensagemParseada + '</div>' +
            '       <div class="dthCadastro" style="color:#0000007a">' + dthCadastro + '</div>' +
            '   </div>' +
            '</div>';

        var iconesGerais = '' +
            '<div class="acoes_assistente" style="margin-top: -10px">' +
            '   <div class="avaliacao_estrelinhas"></div>' +
            '   <a onclick="abrirEstrelinhas(' + id_mensagem + ')"><div class="estrelinha"></div></a>' +
            '   <a onclick="copiar(' + id_mensagem + ')"><div class="copiar" id="copiarResposta' + id_mensagem + '"></div></a>' +
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

        elementosCopiarResposta = divfilho.querySelectorAll('.copiarCodigo');
        elementosCopiarResposta.forEach(function(elemento) {
            geraTooltip(elemento.id, "Copiar");
        });

        tippy(".estrelinha", {
            content: "Feedback sobre a resposta do Assistente",
        });
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        geraTooltip('copiarResposta' + id_mensagem, "Copiar");

        insereCitacaoFonte(divfilho);

    }

    function insereCitacaoFonte(divfilho) {
        // Seleciona os elementos
        elementosCopiarResposta = divfilho.querySelectorAll('.AssistenteSEIIAfonteResposta');

        // Aplica o popover nos elementos encontrados
        elementosCopiarResposta.forEach(function(elemento) {
            var texto = elemento.getAttribute('title');
            elemento.removeAttribute('title'); // Remove o atributo title original
            if (!texto) return; // Evita erro caso não tenha título

            let partes = texto.split(' | ');
            let titulo = partes[0] || 'Sem título';
            let restante = partes[1] || '';

            // Adiciona os atributos diretamente no elemento
            //elemento.setAttribute('title', titulo);
            //elemento.setAttribute('data-content', restante);
            elemento.setAttribute('data-content', titulo);

            // Inicializa o popover do Bootstrap
            $(elemento).popover({
                trigger: 'focus', // Ativado ao clicar (pode mudar para hover se quiser)
                placement: 'top', // Posição do popover
                html: true, // Permite HTML dentro do popover
                delay: {
                    show: 500,
                    hide: 100
                } // Atraso na exibição
            });
        });
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
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                if (data["result"] == "true") {
                    $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                    listarTopicos();
                } else {
                    setTimeout(function() {
                        aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                    }, 5000);
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
            success: function(data) {
                console.log(data);
            },
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
            document.getElementById("copiarResposta" + id)._tippy.setContent("Copiar");
        }, 2000);
    }

    function expandirAssistente() {
        var larguraRealChat = parseInt($(window).width()) - 30;
        $(".widget-content").css({
            "width": larguraRealChat + "px"
        });
        $("#reduzirAssistente").css({
            "display": "inline"
        });
        $("#expandirAssistente").css({
            "display": "none"
        });
        $(".send-message-input").css({
            "width": "100%"
        });
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
            "width": "85%"
        });
        $("#buscarWeb").css({
            "display": "block"
        });
        $("#refletir").css({
            "display": "block"
        });
        $("#conteudoChat .blocoMensagem").css({
            "min-width": "20%"
        });
        controlaTamanhoConteudoChat("expandido");
    }

    function reduzirAssistente() {
        $(".widget-content").css({
            "width": "360px"
        });
        $("#reduzirAssistente").css({
            "display": "none"
        });
        $("#expandirAssistente").css({
            "display": "inline"
        });
        $(".widget-content").removeClass('expandido');
        $(".widget-content").addClass('reduzido');
        $(".send-message-input").height("45");
        $(".send-message-input").css({
            "min-height": "67px"
        });
        $(".send-message-input").css({
            "max-height": "120px"
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
        $("#buscarWeb").css({
            "display": "none"
        });
        $("#refletir").css({
            "display": "none"
        });
        $("#conteudoChat .blocoMensagem").css({
            "min-width": "91%"
        });
        controlaTamanhoConteudoChat();
    }

    function controlaTamanhoConteudoChat(display = "") {
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
            } else {
                var reducaoAlturaChat = 83;
            }
        } else {
            if (document.getElementById("ifrVisualizacao") && document.getElementById('navInfraBarraNavegacao') && document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("divArvoreAcoes")) {
                var reducaoAlturaChat = (document.getElementById('navInfraBarraNavegacao').clientHeight) + document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("divArvoreAcoes").clientHeight + 101;
            } else if (document.getElementById('navInfraBarraNavegacao')) {
                var reducaoAlturaChat = (document.getElementById('navInfraBarraNavegacao').clientHeight) + 147;
            } else {
                var reducaoAlturaChat = 250;
            }
        }
        var alturaRealChat = parseInt($(window).height()) - parseInt(reducaoAlturaChat);
        $(".widget-content").css({
            "height": alturaRealChat + "px"
        });
        var alturaConteudoChat = alturaRealChat - document.getElementsByClassName('widget-title')[0].clientHeight - document.getElementById('lugarInteracaoUsuario').clientHeight - 56;
        var alturaPainelTopico = alturaRealChat - document.getElementsByClassName('widget-title')[0].clientHeight - 70;
        $("#listagemTopicos").css({
            "height": alturaPainelTopico + "px"
        });
        var larguraPainelTopicos = document.getElementById('painelTopicos').clientWidth;
        var larguraConteudoChat = document.getElementById('conteudoChat').clientWidth;
        var larguraTotal = parseInt(larguraPainelTopicos) + parseInt(larguraConteudoChat);
        if ($(window).width() < larguraTotal) {
            var larguraDesejada = (parseInt(larguraTotal) - parseInt($(window).width())) + 150;
            $("#conteudoChat").css({
                "width": larguraDesejada
            });
        }
        controleScroll(alturaConteudoChat);
        return Promise.resolve("Success");
    }

    function controleScroll(alturaConteudoChat) {
        let somaTamanhoDiv = 0;
        $('.interaction').each(function() {
            somaTamanhoDiv = parseFloat(somaTamanhoDiv) + parseFloat($(this).height() + parseFloat(30));
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
            document.getElementById('conversa').scrollBy(0, 10000)
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
            document.getElementById('conversa').scrollBy(0, 10000);
            return false;
        }
        document.getElementById('conversa').scrollBy(0, 5000);
    }

    function controlaTamanhoInputDigitacao() {
        var scrollHeight = $(".send-message-input")[0].scrollHeight;
        var padding = 20;
        if ($(".widget-content").is(".reduzido")) {
            if ($(".send-message-input").height() < (scrollHeight - padding) && $(".send-message-input").height() < 96) {
                $(".send-message-input").height(scrollHeight - padding);
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
        $("#mensagem").val("");
        $("#botaoRefletir").removeClass('botaoAcaoAssistenteIAAtivado');
        $("#titleBuscarWeb").removeClass('botaoAcaoAssistenteIAAtivado');
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

    function listarTopicos() {
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
                                // Renderizar o tópico
                                if (topico["ativo"] == false) {
                                    divPai.append('<div class="topico" onmouseover="mostrarAcoesTopico(this)" onmouseout="ocultarAcoesTopico(this)"><a onclick="selecionaTopico(' + topico["idTopico"] + ')" class="selecionaTopico"><button class="nav-link text-left" id="topico' + topico["idTopico"] + '" data-toggle="pill" data-target="topico' + topico["idTopico"] + '" type="button" role="tab" aria-controls="topico' + topico["idTopico"] + '" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M160 368c26.5 0 48 21.5 48 48v16l72.5-54.4c8.3-6.2 18.4-9.6 28.8-9.6H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16V352c0 8.8 7.2 16 16 16h96zm48 124l-.2 .2-5.1 3.8-17.1 12.8c-4.8 3.6-11.3 4.2-16.8 1.5s-8.8-8.2-8.8-14.3V474.7v-6.4V468v-4V416H112 64c-35.3 0-64-28.7-64-64V64C0 28.7 28.7 0 64 0H448c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H309.3L208 492z"/></svg>' + topico["nome"] + '</button></a><div class="acoesTopico" id="acoesTopico' + topico["idTopico"] + '"><a class="arquivo" onclick="arquivarTopico(' + topico["idTopico"] + ')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M121 32C91.6 32 66 52 58.9 80.5L1.9 308.4C.6 313.5 0 318.7 0 323.9V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V323.9c0-5.2-.6-10.4-1.9-15.5l-57-227.9C446 52 420.4 32 391 32H121zm0 64H391l48 192H387.8c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7H195.8c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7H73L121 96z"/></svg></a><a class="rename" onclick="editarTopico(' + topico["idTopico"] + ')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg></a></div></div>');
                                } else {
                                    topicoAtivo = topico["idTopico"];
                                    divPai.append('<div class="topico active" onmouseover="mostrarAcoesTopico(this)" onmouseout="ocultarAcoesTopico(this)"><a onclick="selecionaTopico(' + topico["idTopico"] + ')" class="selecionaTopico"><button class="nav-link text-left active" id="topico' + topico["idTopico"] + '" data-toggle="pill" data-target="topico' + topico["idTopico"] + '" type="button" role="tab" aria-controls="topico' + topico["idTopico"] + '" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M160 368c26.5 0 48 21.5 48 48v16l72.5-54.4c8.3-6.2 18.4-9.6 28.8-9.6H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16V352c0 8.8 7.2 16 16 16h96zm48 124l-.2 .2-5.1 3.8-17.1 12.8c-4.8 3.6-11.3 4.2-16.8 1.5s-8.8-8.2-8.8-14.3V474.7v-6.4V468v-4V416H112 64c-35.3 0-64-28.7-64-64V64C0 28.7 28.7 0 64 0H448c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H309.3L208 492z"/></svg>' + topico["nome"] + '</button></a><div class="acoesTopico" id="acoesTopico' + topico["idTopico"] + '"><a class="arquivo" onclick="arquivarTopico(' + topico["idTopico"] + ')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M121 32C91.6 32 66 52 58.9 80.5L1.9 308.4C.6 313.5 0 318.7 0 323.9V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V323.9c0-5.2-.6-10.4-1.9-15.5l-57-227.9C446 52 420.4 32 391 32H121zm0 64H391l48 192H387.8c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7H195.8c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7H73L121 96z"/></svg></a><a class="rename" onclick="editarTopico(' + topico["idTopico"] + ')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"><path d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg></a></div></div>');
                                }
                            });
                        }
                    });
                } else {
                    $("#listagemTopicos").html("");
                }
                selecionaTopico(topicoAtivo, false);
                tippy(".rename", {
                    content: "Renomear Tópico",
                });

                tippy(".arquivo", {
                    content: "Arquivar Tópico",
                });

                tippy(".selecionaTopico", {
                    content: "Selecionar Tópico",
                });
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

    function selecionaTopico(id = "", limparCampos = true) {
        if (id) {
            controlaActive(id);
        }
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
                    $("#mensagem").val("");
                    $("#botaoRefletir").removeClass('botaoAcaoAssistenteIAAtivado');
                    $("#titleBuscarWeb").removeClass('botaoAcaoAssistenteIAAtivado');
                }
                if (data != "") {
                    insereBoasVindas(divpai, divfilho);
                    data.forEach(function(interacao) {
                        resolveItensEnvioMensagem(interacao["pergunta"], interacao["dadosCitacoes"], interacao["id_interacao"], interacao["favorito"], interacao['dth_cadastro']);
                        var divfilho = document.createElement('div');
                        if (interacao["resposta"] != "" || interacao["status_requisicao"] > 0) {
                            if (interacao["status_requisicao"] == "200" && interacao["resposta"] != "") {
                                insereResposta(interacao, divfilho, divpai, interacao['dth_cadastro']);
                                if (interacao["feedback"] >= '1') {
                                    abrirEstrelinhas(interacao["id_mensagem"]);
                                    habilitaEstrelinhas(interacao["id_mensagem"], interacao["feedback"]);
                                }
                            } else {
                                insereCritica(interacao, divfilho, divpai, interacao['dth_cadastro']);
                            }
                        } else {
                            carregandoResposta(divpai);
                            setTimeout(function() {
                                aguardandoResposta(interacao["id_interacao"], divfilho, divpai);
                            }, 5000);
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
            var iconeTopico = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 512 512\"><path d=\"M160 368c26.5 0 48 21.5 48 48v16l72.5-54.4c8.3-6.2 18.4-9.6 28.8-9.6H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16V352c0 8.8 7.2 16 16 16h96zm48 124l-.2 .2-5.1 3.8-17.1 12.8c-4.8 3.6-11.3 4.2-16.8 1.5s-8.8-8.2-8.8-14.3V474.7v-6.4V468v-4V416H112 64c-35.3 0-64-28.7-64-64V64C0 28.7 28.7 0 64 0H448c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H309.3L208 492z\"/></svg>";
            var novoConteudo = iconeTopico + $(this).val();

            var dadosTopico = {};
            dadosTopico["id_topico"] = id;
            dadosTopico["nome_topico"] = $(this).val();
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_renomear_topico'); ?>',
                type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
                dataType: "json", //Tipo de dado que será enviado ao servidor
                data: dadosTopico, // Enviando o JSON com o nome de itens
                success: function(data) {
                    console.log("ok");
                },
                error: function(err) {
                    console.log(err);
                }
            });
            // Cria um novo botão com o valor atualizado e os mesmos atributos
            var novoBotao = $("<button>").addClass("nav-link text-left").html(novoConteudo).attr(atributosObj);
            // Substitui o input pelo botão
            $(this).replaceWith(novoBotao);
            $("#acoesTopico" + id).removeClass('topicoEmEdicao');
        });
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

    function ativaBotaoAcao(botao) {
        if (!botao.classList.contains('botaoAcaoAssistenteIAAtivado')) {
            botao.classList.add('botaoAcaoAssistenteIAAtivado');
        } else {
            botao.classList.remove('botaoAcaoAssistenteIAAtivado');
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
</script>