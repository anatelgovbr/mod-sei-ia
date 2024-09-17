<script type="text/javascript">
    <?php require_once dirname(__FILE__) . '/lib/gpt-tokenizer/cl100k_base.js'; ?>
</script>
<script src="modulos/ia/lib/highlight.js/highlight.min.js"></script>
<script src="modulos/ia/lib/popper/popper.min.js"></script>
<script src="modulos/ia/lib/popper/tippy.js"></script>
<script src="modulos/ia/lib/marked/marked.min.js"></script>
<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function (event) {
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
        $('.send-message-input').keydown(function (event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                setTimeout(() => {
                    controlaPreenchimentoCampoMensagem();
                    enviarMensagem();
                    controlaTamanhoInputDigitacao();
                }, 500);
            } else {
                setTimeout(() => {
                    controlaPreenchimentoCampoMensagem();
                    controlaTamanhoInputDigitacao();
                }, 500);
            }
        });
        $('[name=star]').click(function () {
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

        tippy(".chat-open", {
            content: "Inteligência Artificial",
        });

        tippy(".close-chat", {
            content: "Minimizar",
        });

        tippy("#botaoEnviarMensagem", {
            content: "Enviar Mensagem",
        });

        tippy("#adicionarTopico", {
            content: "Novo Tópico",
        });

        tippy(".iconeAdicionarTopico", {
            content: "Novo Tópico",
        });
        setTimeout(() => {
            fecharChat();
            $("#chat_ia").css("display", "block");
        }, 200);
        if(document.getElementById("ifrVisualizacao")) {
            document.getElementById("ifrVisualizacao").onload = function () {
                document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("btnInfraTopo").style.right = "6.2rem";
            }
        }
    });

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
            document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml').contentWindow.document.addEventListener('mouseup', function (event) {
                $("#botaoTransferir").css("display", "none");
                const selectedText = document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('ifrArvoreHtml').contentWindow.document.getSelection().toString().trim();
                capturaTexto(event, selectedText, "processo");
            });
        }
    }

    function acionarListenerEditor() {
        document.getElementById("divEditores").onmouseover = function () {
            var classeEditor = document.getElementsByClassName('cke_wysiwyg_frame');
            if (classeEditor) {
                Array.prototype.forEach.call(classeEditor, function (elemento) {
                    elemento.contentWindow.document.addEventListener('mouseup', function (event) {
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
        document.getElementById('widget-open').checked = false;
    }

    function generateUniqueID() {
        var timestamp = Date.now();
        var random = Math.floor(Math.random() * 1000);
        return timestamp.toString() + random.toString();
    }

    function safe_tags(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') ;
    }

    function resolveItensEnvioMensagem(mensagem, procedimento = "", linkAcesso = "") {
        mensagem = safe_tags(mensagem);
        mensagem = mensagem.replace(/\n/g, "<br>");
        mensagem = mensagem.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
        if(Array.isArray(procedimento)) {
            var textoCitacaoProcedimento = procedimento[0];
        } else {
            var textoCitacaoProcedimento = procedimento;
        }
        mensagem = mensagem.replace(textoCitacaoProcedimento, "<a target='_blank' href='" + linkAcesso + "'>" + textoCitacaoProcedimento + "</a>");
        var divPai = $("#conversa");
        var uniqueID = generateUniqueID();

        var respostaMontada = '<div class="interaction agent"><div class="mensagemIdentificador"><div class="iconeIdentificacao"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#dc3545" d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg></div><div class="input" id="textoPuro' + uniqueID + '">' + mensagem + '</div></div>';
        var iconesGerais = '<div class="acoes_assistente">' +
            '<a onclick="copiar(' + uniqueID + ')"><div class="copiar" id="copiarResposta' + uniqueID + '"></div></a></div>';

        var perguntaCliente = respostaMontada + iconesGerais;
        divPai.append(perguntaCliente);
        document.getElementById('conversa').scrollBy(0, 5000);
        $("#mensagem").val("");

        if ($(".widget-content").hasClass("expandido")) {
            $(".send-message-input").height("100");
        } else {
            $(".send-message-input").height("20");
        }
        elementosCopiarResposta = document.getElementById('conversa').querySelectorAll('.copiar');
        elementosCopiarResposta.forEach(function (elemento) {
            geraTooltip(elemento.id, "Copiar");
        });
    }

    function identificarProtocolo(mensagem) {

        let possiveisCitacoes =  mensagem.match(/#\S*(?=\s|$|\n)/g);
        let documentos = [];

        if(possiveisCitacoes) {
            possiveisCitacoes = possiveisCitacoes.filter((value, index, array) => array.indexOf(value) == index);
            var citacaoIncorreta = false;
            var indicacaoFolhaIncorreta = false;

            if (possiveisCitacoes.length > 0) {
                if(possiveisCitacoes.length > 1) {
                    $("#validacaoMensagem").html('Provisoriamente, não é permitida a citação de mais de um protocolo de processo ou de documento na mesma mensagem.');
                    estadoDeInteracao();
                    return false;
                } else {
                    possiveisCitacoes.forEach(function (possivelCitacao) {
                        if(possivelCitacao.length < 8) {
                            $("#validacaoMensagem").html('O uso do caractere de "#" é exclusivo para citação de protocolos do SEI, cujo número deve estar colado ao # (no formato, por exemplo #01234567). Assim, a mensagem possui citação de protocolo em formato irregular.');
                            estadoDeInteracao();
                            citacaoIncorreta = true;
                            return false;
                        } else {
                            let padraoColchetes = /\[/;

                            if (padraoColchetes.test(possivelCitacao)) {
                                let verificaColchete  = /#[0-9]+\[[0-9]+(:[0-9]+)?]/;
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
                }
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

        if(documentos == null || documentos.length === 0 ){
            verificaJanelaContexto(mensagem);
        }else{
            documentos = documentos.filter((value, index, array) => array.indexOf(value) === index);
            if(documentos.length === 1){
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
                    success: function (data) {
                        if (data["result"] == "false") {
                            $("#validacaoMensagem").html(data["mensagem"]);
                            estadoDeInteracao();
                        } else {
                            var mensagem = $("#mensagem").val();
                            verificaJanelaContexto(mensagem, data["idDocumento"], documentos, data["linkAcesso"], data["idProcedimento"], data["relacaoProtocolos"]);
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            }

            if(documentos.length > 1){
                $("#validacaoMensagem").html('Provisoriamente, não é permitida a citação de protocolo de processo ou de mais de um documento na mesma mensagem.');
                estadoDeInteracao();
            }

        }

    }

    function estadoDeInteracao(){
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

    function verificaJanelaContexto(mensagem, idDocumento = "", procedimento = "", linkAcesso = "", idProcedimento = "", relacaoProtocolos = "") {

        const {isWithinTokenLimit, encode} = GPTTokenizer_cl100k_base

        var text = mensagem;
        var tokenLimit = $("#janelaContexto").val();

        if (idDocumento != "") {
            tokenLimit = tokenLimit * 0.5;
        } else {
            tokenLimit = tokenLimit * 0.8;
        }
        var withinTokenLimit = isWithinTokenLimit(text, tokenLimit);
        if (withinTokenLimit != false) {
            $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "block");
            $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "none");
            resolveItensEnvioMensagem(mensagem, procedimento, linkAcesso);
            controlaTamanhoConteudoChat().then(function () {
                resolveResposta(mensagem, idDocumento, procedimento, linkAcesso, idProcedimento, relacaoProtocolos);
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
                dataType: "json",//Tipo de dado que será enviado ao servidor
                data: info,
                async: true,
                success: function (data) {
                    console.log(data);
                },
                error: function (err) {
                    console.log(err);
                }
            }).done(function () {
                $("#validacaoMensagem").html("O texto da mensagem ultrapassou o limite permitido. Reduza o texto e tente novamente.");
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                $(".botaoEnvioMensagem #botaoEnviarMensagem").css("display", "block");
                $(".botaoEnvioMensagem #imgArvoreAguarde").css("display", "none");
            });
        }
    }

    function apiIndisponivel(divpai, divfilho) {
        $("#botaoEnviarMensagem").removeClass("apiDisponivel");
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">Assistente de IA está indisponível no momento. <br>Tente novamente mais tarde.</div></div>';
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
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">Oi, sou um Assistente de Inteligência Artificial.<br>Como posso te ajudar?</div></div>';
        divpai.appendChild(divfilho);
    }

    function carregandoResposta(divpai) {
        var divfilho = document.createElement('div');
        divfilho.className = 'interaction client dots';
        divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">Aguarde<span>.</span><span>.</span><span>.</span></div></div>';
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
            dataType: "json",//Tipo de dado que será enviado ao servidor
            async: true,
            success: function (data) {
                if (data) {
                    if (data['retornoApi']) {

                        if (data['retornoApi']['status'] == "OK") {
                            $("#botaoEnviarMensagem").removeClass("apiIndisponivel");
                            $("#botaoEnviarMensagem").addClass("apiDisponivel");
                            controlaTamanhoConteudoChat();
                            controlaPreenchimentoCampoMensagem();
                            $("#janelaContexto").val(data['janelaContexto']);
                            listarTopicos();
                        }else {
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
            error: function (err) {
                if (!apiJaIndisponivel) {
                    apiIndisponivel(divpai, divfilho)
                }
            }
        });
    }

    function resolveResposta(mensagem, idDocumento, procedimento, linkAcesso, idProcedimento, relacaoProtocolos) {
        var divpai = document.getElementById("conversa");
        var divfilho = carregandoResposta(divpai);
        var mensagemUsuario = {};
        mensagemUsuario["text"] = mensagem;
        mensagemUsuario["procedimento"] = procedimento[0];
        mensagemUsuario["linkAcesso"] = linkAcesso;
        mensagemUsuario["idDocumento"] = idDocumento;
        mensagemUsuario["idProcedimento"] = idProcedimento;
        mensagemUsuario["relacaoProtocolos"] = relacaoProtocolos;
        mensagemUsuario["topicoTemporario"] = $("#topicoTemporario").val();
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_assistente_envia_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: mensagemUsuario, // Enviando o JSON com o nome de itens
            success: function (data) {
                if(data["error"]) {
                    $("#botaoEnviarMensagem").addClass("limiteDiarioExcedido");
                    $("#validacaoMensagem").html(data["mensagem"]);
                    listarTopicos();
                } else {
                    if($("#topicoTemporario").val() == "true") {
                        $("#topicoTemporario").val("false");
                        listarTopicos();
                    } else {
                        var idMdIaInteracaoChat = data;
                        setTimeout(function () {
                            aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                        }, 5000);
                    }
                }
            },
            error: function (err) {
                console.log(err);
                divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">Ocorreu um erro no Assistente de IA.</div></div>';
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            }
        });
    }

    function geraTooltip(idElemento, titulo) {
        tippy("#" + idElemento, {
            content: titulo,
        });
    }

    function insereResposta(data, divfilho, divpai) {
        var id_mensagem = data['id_mensagem'];
        divfilho.className = 'interaction client';
        divfilho.id = id_mensagem;
        var mensagem = data['resposta'];
        var mensagemParseada = markdownToHTML(mensagem, id_mensagem);
        var respostaMontada = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">' + mensagemParseada + '</div></div>';
        var iconesGerais = '<div class="acoes_assistente"><div class="avaliacao_estrelinhas"></div>' +
            '<a onclick="abrirEstrelinhas(' + id_mensagem + ')"><div class="estrelinha"></div></a>' +
            '<a onclick="copiar(' + id_mensagem + ')"><div class="copiar" id="copiarResposta' + id_mensagem + '"></div></a>' +
            '<pre><code class="textoPuro language-markdown" data-trim="false" id="textoPuro' + id_mensagem + '">' + safe_tags(mensagem) + '</code></pre>';
        if (document.getElementById("frmEditor")) {
            //var iconesEditor = '<a onclick="transportarEditor(' + timestamp + ')"><div class="transportarEditor" title="Transportar para Editor"></div></a>';
            var iconesEditor = '';
        } else {
            var iconesEditor = '';
        }

        divfilho.innerHTML = respostaMontada + iconesGerais + iconesEditor + '</div>';

        divpai.appendChild(divfilho);

        elementosCopiarResposta = divfilho.querySelectorAll('.copiarCodigo');
        elementosCopiarResposta.forEach(function (elemento) {
            geraTooltip(elemento.id, "Copiar");
        });

        tippy(".estrelinha", {
            content: "Feedback sobre a resposta do Assistente",
        });
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        geraTooltip('copiarResposta' + id_mensagem, "Copiar");
    }

    function insereCritica(data, divfilho, divpai) {
        var id_mensagem = data['id_mensagem'];
        divfilho.className = 'interaction client';
        divfilho.id = id_mensagem;
        var mensagem = data['resposta'];
        if(mensagem == "") {
            mensagem = "Ocorreu um erro no Assistente de IA.";
        }
        var respostaMontada = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output erro_ia">' + mensagem + '</div></div>';

        divfilho.innerHTML = respostaMontada ;
        $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
        divpai.appendChild(divfilho);
    }
    function aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idMdIaInteracaoChat;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function (data) {
                if (data["result"] == "true") {
                    $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
                    listarTopicos();
                } else {
                    setTimeout(function () {
                        aguardandoResposta(idMdIaInteracaoChat, divfilho, divpai);
                    }, 5000);
                }

            },
            error: function (err) {
                console.log(err);
                divfilho.innerHTML = '<div class="mensagemIdentificador"><div class="iconeIdentificacao"><img src="modulos/ia/imagens/md_ia_icone.svg?' + <?=Icone::VERSAO ?> + '"/></div><div class="output">Ocorreu um erro no Assistente de IA.</div></div>';
                $("#botaoEnviarMensagem").removeClass("aguardandoResposta");
            }
        });
    }

    function transportarEditor(id) {
        CKEDITOR.tools.callFunction(520, this);
        setInterval(function () {
            document.getElementsByClassName("cke_pasteframe")[0].contentWindow.document.body.outerHTML = $("#" + id + " p").html()
        }, 1000);
    }

    function markdownToHTML(html, id_mensagem) {
        var quantidadeBlocoCodigo = 0;
        // Blocos de Código
        html = html.replace(/```([\s\S]*?)```/g, function (match, p1) {
            quantidadeBlocoCodigo++;
            var firstLine = p1.split('\n')[0];
            let linguagensMaisUtilizadas = [
                "JavaScript",
                "Python",
                "Java",
                "C#",
                "C++",
                "TypeScript",
                "PHP",
                "Ruby",
                "Swift",
                "Kotlin",
                "css",
                "html"
            ];
            if (linguagensMaisUtilizadas.includes(firstLine)) {
                var highlightedFirstLine = '<div class="highlighted">' + firstLine + '</div>';
                var codeBlock = p1.replace(firstLine, "");
                blocoCodigo = codeBlock.substring(1);
                var codigoEstilizado = hljs.highlight(blocoCodigo, {language: firstLine}).value;
            } else {
                if (firstLine == "") {
                    firstLine = "markdown";
                }
                var highlightedFirstLine = '<div class="highlighted">' + firstLine + '</div>';
                var codeBlock = p1.replace(firstLine, "");
                blocoCodigo = codeBlock.substring(1);
                var codigoEstilizado = hljs.highlightAuto(blocoCodigo).value;
            }
            return '<div class="code-container"><pre class="code theme-atom-one-light">' +
                '<div class="code-header">' + highlightedFirstLine + '<div class="code-actions">' +
                '<a onclick="copyCode(' + id_mensagem + quantidadeBlocoCodigo + ')"><div class="copiarCodigo copiar" id="copyButton' + id_mensagem + quantidadeBlocoCodigo + '">' +
                '</div></a></div></div><div class="code-body" id="code' + id_mensagem + quantidadeBlocoCodigo + '"><code>' + codigoEstilizado + '</code>' +
                '</div></pre></div>';

        });

        var regex = /<code>([\s\S]*?)<\/code>/g;

        // Substitui os blocos de código pela tag `<code>` e armazena em um array
        var codeBlocks = [];
        var parsedText = html.replace(regex, function (match, p1) {
            codeBlocks.push(p1);
            return "<code>" + (codeBlocks.length - 1) + "</code>";
        });
        // Aplica parsedown usando Marked.js apenas fora dos blocos de código
        marked.use({
            breaks: true,
        });
        parsedText = marked.parse(parsedText);
        // Substitui as tags `<code>` pelo conteúdo dos blocos de código
        html = parsedText.replace(/<code>(\d+)<\/code>/g, function (match, p1) {
            return "<code>" + codeBlocks[parseInt(p1)] + "</code>";
        });

        return html;
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
        setTimeout(function () {
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
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: feeedbackUsuario, // Enviando o JSON com o nome de itens
            async: true,
            success: function (data) {
                console.log(data);
            },
            error: function (err) {
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
        setTimeout(function () {
            document.getElementById("copiarResposta" + id)._tippy.hide();
            document.getElementById("copiarResposta" + id)._tippy.setContent("Copiar");
        }, 2000);
    }

    function expandirAssistente() {
        var larguraRealChat = parseInt($(window).width()) - 30;
        $(".widget-content").css({"width": larguraRealChat + "px"});
        $("#reduzirAssistente").css({"display": "inline"});
        $("#expandirAssistente").css({"display": "none"});
        $(".send-message-input").css({"width": "100%"});
        $('.send-message-input').attr('rows', 5);
        $(".widget-content").addClass('expandido');
        $(".widget-content").removeClass('reduzido');
        $(".send-message-input").height("100");
        $(".send-message-input").css({"min-height": "122px"});
        $(".send-message-input").css({"max-height": "216px"});
        $("#assistenteReduzido").css({"display": "none"});
        $("#assistenteExpandido").css({"display": "block"});
        $("#painelTopicos").css({"display": "block"});
        $("#conteudoChat").css({"width": "85%"});
        controlaTamanhoConteudoChat("expandido");
    }

    function reduzirAssistente() {
        $(".widget-content").css({"width": "360px"});
        $("#reduzirAssistente").css({"display": "none"});
        $("#expandirAssistente").css({"display": "inline"});
        $(".widget-content").removeClass('expandido');
        $(".widget-content").addClass('reduzido');
        $(".send-message-input").height("20");
        $(".send-message-input").css({"min-height": "42px"});
        $(".send-message-input").css({"max-height": "120px"});
        $("#assistenteExpandido").css({"display": "none"});
        $("#assistenteReduzido").css({"display": "block"});
        $("#painelTopicos").css({"display": "none"});
        $("#conteudoChat").css({"width": "100%"});
        controlaTamanhoConteudoChat();
    }

    function controlaTamanhoConteudoChat(display = "") {
        if(display == "") {
            if ($(".widget-content").hasClass("expandido")) {
                var display = "expandido";
            } else {
                var display = "reduzido";
            }
        }
        if(display == "expandido") {
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
        $(".widget-content").css({"height": alturaRealChat + "px"});
        var alturaConteudoChat = alturaRealChat - document.getElementsByClassName('widget-title')[0].clientHeight - document.getElementsByClassName('send-message')[0].clientHeight - 56;
        var alturaPainelTopico = alturaRealChat - document.getElementsByClassName('widget-title')[0].clientHeight - 70;
        $("#listagemTopicos").css({"height": alturaPainelTopico + "px"});
        var larguraPainelTopicos = document.getElementById('painelTopicos').clientWidth;
        var larguraConteudoChat = document.getElementById('conteudoChat').clientWidth;
        var larguraTotal = parseInt(larguraPainelTopicos) + parseInt(larguraConteudoChat);
        if($(window).width() < larguraTotal) {
            var larguraDesejada = (parseInt(larguraTotal) - parseInt($(window).width())) + 150;
            $("#conteudoChat").css({"width": larguraDesejada});
        }
        setTimeout(function () {
            controleScroll(alturaConteudoChat);
        }, 500);
        return Promise.resolve("Success");
    }

    function controleScroll(alturaConteudoChat) {
        let somaTamanhoDiv = 0;
        $('.interaction').each(function () {
            somaTamanhoDiv = parseFloat(somaTamanhoDiv) + parseFloat($(this).height() + parseFloat(30));
        });
        if (somaTamanhoDiv > alturaConteudoChat) {
            $("#conversa").css({"display": "block"});
            $("#conversa").css({"overflow-y": "scroll"});
            $("#conversa").css({"height": alturaConteudoChat + "px"});
            $("#chat_ia .interaction").css({"margin": "15px 0"});
            document.getElementById('conversa').scrollBy(0, 10000)
            return false;
        } else {
            $("#conversa").css({"display": "flex"});
            $("#conversa").css({"overflow-y": "auto"});
            $("#conversa").css({"height": alturaConteudoChat + "px"});
            $("#chat_ia .interaction").css({"margin": "7px 0"});
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
        insereBoasVindas(divpai, divfilho);
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
        controlaTamanhoConteudoChat();
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
        var dataAtual = new Date();    // Cria um objeto Date para a data atual
        var anoAtual = dataAtual.getFullYear();    // Obtém o ano atual

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
            dataType: "json",//Tipo de dado que será enviado ao servidor
            success: function (data) {
                var topicoAtivo = "";
                if(data["budgetTokens"]["extrapolouLimiteTokens"]) {
                    limiteDiarioUltrapassado();
                }

                if(data["topicos"] != false) {
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
                }
                selecionaTopico(topicoAtivo);
                tippy(".rename", {
                    content: "Renomear Tópico",
                });

                tippy(".arquivo", {
                    content: "Arquivar Tópico",
                });

                tippy(".selecionaTopico", {
                    content: "Selecionar Tópico",
                });
                $('#listagemTopicos').animate({ scrollTop: 0 }, "slow");
            },
            error: function (err) {
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

    function selecionaTopico(id = "") {
        if (id) {
            controlaActive(id);
        }
        insereLoading();
        var dadosTopico = {};
        dadosTopico["id"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_selecionar_topico'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            async: false,
            data: dadosTopico, // Enviando o JSON com o nome de itens
            success: function (data) {
                var divpai = document.getElementById("conversa");
                var divfilho = document.createElement('div');
                $("#conversa").html("");
                insereBoasVindas(divpai, divfilho);
                data.forEach(function (interacao) {
                    resolveItensEnvioMensagem(interacao["pergunta"], interacao["procedimento_citado"], interacao["link_acesso"]);
                    var divfilho = document.createElement('div');
                    if(interacao["resposta"] != "" || interacao["status_requisicao"] > 0) {
                        if(interacao["status_requisicao"] == "200" && interacao["resposta"] != "") {
                            insereResposta(interacao, divfilho, divpai);
                            if(interacao["feedback"] >= '1') {
                                abrirEstrelinhas(interacao["id_mensagem"]);
                                habilitaEstrelinhas(interacao["id_mensagem"], interacao["feedback"]);
                            }
                        } else {
                            insereCritica(interacao, divfilho, divpai);
                        }
                    } else {
                        carregandoResposta(divpai);
                        setTimeout(function () {
                            aguardandoResposta(interacao["id_interacao"], divfilho, divpai);
                        }, 5000);
                    }
                });
            },
            error: function (err) {
                console.log(err);
            }
        });
        if(id != "") {
            $("#topicoTemporario").val("false");
        } else {
            $("#topicoTemporario").val("true");
        }
        controlaPreenchimentoCampoMensagem();
        controlaTamanhoInputDigitacao();
        controlaTamanhoConteudoChat();

    }

    function controlaActive(id = "") {
        // Remove a classe "active" de todos os botões
        var botoes = document.querySelectorAll('.nav-link');
        botoes.forEach(function (botao) {
            botao.classList.remove('active');
            botao.parentNode.parentNode.classList.remove('active');
        });
        if(id != "") {
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
        $.each(atributos, function () {
            if (this.specified) {
                atributosObj[this.name] = this.value;
            }
        });

        // Obtém o conteúdo do botão original
        var conteudo = botaoOriginal.text();

        $("#acoesTopico"+id).addClass('topicoEmEdicao');

        // Cria um input editável com os mesmos atributos do botão original
        var inputEditavel = $("<input>").attr(atributosObj).attr({
            "type": "text",
            "value": conteudo,
            "maxlength": 80 // Adiciona o limite de caracteres
        });

        // Substitui o botão pelo input
        botaoOriginal.replaceWith(inputEditavel);

        // Adiciona um evento de input para monitorar o número de caracteres digitados
        inputEditavel.on("input", function () {
            if (this.value.length > 80) {
                this.value = this.value.slice(0, 80); // Limita o número de caracteres
            }
        });

        // Adiciona um evento para restaurar o botão quando o input perder o foco
        inputEditavel.blur(function () {
            // Obtém o valor do input
            var iconeTopico = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 512 512\"><path d=\"M160 368c26.5 0 48 21.5 48 48v16l72.5-54.4c8.3-6.2 18.4-9.6 28.8-9.6H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16V352c0 8.8 7.2 16 16 16h96zm48 124l-.2 .2-5.1 3.8-17.1 12.8c-4.8 3.6-11.3 4.2-16.8 1.5s-8.8-8.2-8.8-14.3V474.7v-6.4V468v-4V416H112 64c-35.3 0-64-28.7-64-64V64C0 28.7 28.7 0 64 0H448c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H309.3L208 492z\"/></svg>";
            var novoConteudo = iconeTopico + $(this).val();

            var dadosTopico = {};
            dadosTopico["id_topico"] = id;
            dadosTopico["nome_topico"] = $(this).val();
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_renomear_topico'); ?>',
                type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
                dataType: "json",//Tipo de dado que será enviado ao servidor
                data: dadosTopico, // Enviando o JSON com o nome de itens
                success: function (data) {
                    console.log("ok");
                },
                error: function (err) {
                    console.log(err);
                }
            });
            // Cria um novo botão com o valor atualizado e os mesmos atributos
            var novoBotao = $("<button>").addClass("nav-link text-left").html(novoConteudo).attr(atributosObj);
            // Substitui o input pelo botão
            $(this).replaceWith(novoBotao);
            $("#acoesTopico"+id).removeClass('topicoEmEdicao');
        });
    }

    function arquivarTopico(id) {
        var dadosTopico = {};
        dadosTopico["id_topico"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_arquivar_topico'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: dadosTopico, // Enviando o JSON com o nome de itens
            success: function (data) {
                $("#conversa").html("");
                listarTopicos();
            },
            error: function (err) {
                console.log(err);
            }
        });
    }
</script>

