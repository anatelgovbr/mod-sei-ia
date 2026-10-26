<script type="text/javascript">
    function rolar_para(elemento, opcoes) {
        var config = opcoes || {};
        var deslocamento = typeof config.deslocamento === 'number' ? config.deslocamento : 0;
        var duracao = typeof config.duracao === 'number' ? config.duracao : 300;

        $("#divMsg > div").removeClass("alert-warning alert-danger alert-success");

        var alvo = $(elemento);
        if (!alvo.length || alvo.offset() == null) {
            return;
        }

        $("#divInfraAreaTelaD").animate({
            scrollTop: alvo.offset().top + deslocamento
        }, duracao);
    }

    function buscarOperacoeWs(parametros, opcoes) {
        var config = opcoes || {};
        var path = config.path || '';
        var onSuccess = typeof config.onSuccess === 'function' ? config.onSuccess : (typeof montaOperacao === 'function' ? montaOperacao : null);
        var onError = typeof config.onError === 'function' ? config.onError : null;

        if (!path) {
            return;
        }

        $.ajax({
            type: "POST",
            url: path,
            dataType: 'xml',
            async: false,
            data: parametros,
            beforeSend: function() {
                //infraExibirAviso(false);
            },
            success: function(result) {
                if (onSuccess) {
                    onSuccess(result);
                }
            },
            error: function(msgError) {
                if (onError) {
                    onError(msgError);
                } else if (typeof msgCommit !== 'undefined') {
                    msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
                }
            },
            complete: function(result) {
                //infraAvisoCancelar();
            }
        });
    }

    function fecharModal(opcoes) {
        var configPadrao = typeof window.mdIaFecharModalPadrao === 'object' ? window.mdIaFecharModalPadrao : {};
        var config = opcoes || configPadrao;
        var fecharTodos = !!config.fecharTodos;
        var botoesFechar = $(window.top.document).find('div[id^=divInfraSparklingModalClose]');

        if (fecharTodos) {
            botoesFechar.get().reverse().forEach(function(elemento) {
                $(elemento).click();
            });
            return;
        }

        botoesFechar.click();
    }

    function infraCriarCheckboxRadio(classInput, classDiv, classLabel, classRemocao, classAdicao) {
        $("." + classInput).each(function(index) {
            var div = $('<div class="' + classDiv + '" ></div>');
            var isVisible = this.style.visibility !== 'hidden' && this.style.display !== 'none';
            if (isVisible) {
                $(this).wrap(div);
            } else {
                $(this).addClass("infraCheckboxRadioSemDiv");
            }

            var id = $(this).attr("id");
            var title = $(this).attr("title");

            var label = $('<label class="' + classLabel + '"></label>');

            if (id != undefined) {
                $(label).attr("for", id);
            }
            if (title != undefined) {
                $(label).attr("title", title);
            }

            $(this).removeClass(classRemocao);
            $(this).addClass(classAdicao);

            label.insertAfter($(this));
        });
    }

    function carregarPromptAssistente(idPromptInteracao, opcoes) {
        var config = opcoes || {};
        var path = config.path || '';
        var seletorTextarea = config.seletorTextarea || '#txaPrompt';
        var seletorFormulario = config.seletorFormulario || '';
        var onSuccess = typeof config.onSuccess === 'function' ? config.onSuccess : null;

        if (!path) {
            return;
        }

        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idPromptInteracao;

        $.ajax({
            url: path,
            type: 'POST',
            dataType: "json",
            data: dadosMensagem,
            success: function(data) {
                var mensagem = decodeHtmlEntitiesPergunta(data["pergunta"]);
                mensagem = getTextForTextarea(mensagem);
                $(seletorTextarea).val(mensagem);

                if (seletorFormulario) {
                    $(seletorFormulario).css("display", "block");
                }

                if (onSuccess) {
                    onSuccess(data, mensagem);
                }
            }
        });
    }

    function inicializarListaPromptSelecao(opcoes) {
        var config = opcoes || {};
        var acaoAtual = config.acaoAtual || '';
        var acaoSelecao = config.acaoSelecao || '';
        var idBotaoFecharSelecao = config.idBotaoFecharSelecao || 'btnFecharSelecao';
        var idBotaoFechar = config.idBotaoFechar || 'btnFechar';

        if (acaoAtual == acaoSelecao) {
            infraReceberSelecao();
            var botaoSelecao = document.getElementById(idBotaoFecharSelecao);
            if (botaoSelecao) {
                botaoSelecao.focus();
            }
        } else {
            var botaoFechar = document.getElementById(idBotaoFechar);
            if (botaoFechar) {
                botaoFechar.focus();
            }
        }

        infraEfeitoTabelas();
    }

    function OnSubmitForm() {
        if (typeof validarCadastro === 'function') {
            return validarCadastro.apply(this, arguments);
        }
        return true;
    }

    function infraTransportarItem(n) {
        if (typeof window.mdIaTransportarItemHandler === 'function') {
            window.mdIaTransportarItemHandler(n);
        }
    }

    function tratarDigitacao(ev) {
        if (infraGetCodigoTecla(ev) == 13 && window.mdIaTratarDigitacaoFormId) {
            var formulario = document.getElementById(window.mdIaTratarDigitacaoFormId);
            if (formulario) {
                formulario.submit();
            }
        }
        return true;
    }

    function inicializarPromptSelecaoPadrao() {
        if (window.mdIaInicializarSelecaoConfig) {
            inicializarListaPromptSelecao(window.mdIaInicializarSelecaoConfig);
        }
    }
</script>