<script type="text/javascript">
    function inicializar() {
        if (window.top.document.getElementById("ifrVisualizacao") != null) {
            var telaConsulta = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('hdntelaConsulta').value;
        } else if (window.top.document.getElementById("ifrConteudoVisualizacao") != null) {
            var telaConsulta = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('hdntelaConsulta').value;
        } else {
            var telaConsulta = window.top.document.getElementById("hdntelaConsulta").value;
        }
        if (telaConsulta != "true") {
            $("#divTituloModal .botoes").show();
        }
        carregarObjetivo(function(data) {
            $("#conteudoObjetivo").html(data);
            infraCriarCheckboxRadio("infraCheckbox", "infraCheckboxDiv", "infraCheckboxLabel", "infraCheckbox", "infraCheckboxInput");
            $(".itemSugeridoIa .infraCheckboxInput").each(function() {
                $(this).prop("disabled", true);
            });
        });

        $(".itemSugeridoUE .infraCheckboxInput").each(function() {
            $(this).prop("disabled", true);
        });

        $(".infraCheckboxInput").change(function() {
            var idCheckbox = this.id;
            var valCheckbox = this.value;
            var isChecked = $(this).prop("checked");
            idCheckbox = idCheckbox.split("chkInfraItem");
            var selecionadosAnteriormente = $("#hdnHistoricoSelecionados").val();
            var itensPreSelecionados = selecionadosAnteriormente ? selecionadosAnteriormente.split(",") : [];
            if (!$("#txaRacional_" + idCheckbox[1]).is(".sugeridoIa")) {
                if (!itensPreSelecionados.includes(valCheckbox)) {
                    if ($("#txaRacional_" + idCheckbox[1]).prop("disabled")) {
                        $("#txaRacional_" + idCheckbox[1]).prop("disabled", false);
                    } else {
                        $("#txaRacional_" + idCheckbox[1]).prop("disabled", true);
                    }
                } else {
                    if ($("#txaRacional_" + idCheckbox[1]).prop("disabled")) {
                        $("#txaRacional_" + idCheckbox[1]).prop("disabled", false);
                        $("#alertRacional_" + idCheckbox[1]).css("display", "block");
                    } else {
                        if (isChecked) {
                            $("#alertRacional_" + idCheckbox[1]).css("display", "none");
                        } else {
                            $("#alertRacional_" + idCheckbox[1]).css("display", "block");
                        }
                    }
                }
            }
            $("#txaRacional_" + idCheckbox[1]).val("");
        });
        $(window.top.document).find(".sparkling-modal-close").mouseup(function() {
            if ($("#hdnAlteracoesRealizadas").val() == "1") {
                if (window.top.document.getElementById("ifrVisualizacao") != null) {
                    window.top.document.getElementById("ifrVisualizacao").src = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']) ?>";
                } else {
                    window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById("ifrVisualizacao").src = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']) ?>";
                }
            }
        });
        /*$(document).keyup(function(e) {
            alert("entra");
            if (e.keyCode === 27)  {
                if($("#hdnAlteracoesRealizadas").val() == "1") {
                    window.top.document.getElementById("ifrVisualizacao").src = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']) ?>";
                }
            }
        });*/
        $('body').on('click', '.btn_thumbs', function() {
            if ($(this).hasClass("up") && $(this).parent("div").find(".down")) {
                $(this).parent("div").find(".down").removeClass("active");
                $(this).parent("div").find(".up").addClass("active");
                $(this).parent("div").find(".hdnAproved").val("1");
                $(this).parent().parent().parent().find(".infraCheckboxDiv").find(".infraCheckboxInput").prop("checked", true);
            } else {
                $(this).parent("div").find(".up").removeClass("active");
                $(this).parent("div").find(".down").addClass("active");
                $(this).parent("div").find(".hdnAproved").val("0");
                $(this).parent().parent().parent().find(".infraCheckboxDiv").find(".infraCheckboxInput").prop("checked", false);
            }
            infraSelecionarItens($(this).parent().parent().parent().find(".infraCheckboxDiv").find(".infraCheckboxInput")[0], 'Infra');
        });

        // Caso não tenha historico para exibir o botao nao e apresentado
        if (!document.getElementById("divHistoricoOds")) {
            document.getElementById("btnHistorico").style.display = "none";
        }
    }

    function carregarObjetivo(callback) {
        if (window.top.document.getElementById("ifrVisualizacao") != null) {
            var idObjetivo = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('hdnIdSelecaoObjetivo').value;
            var telaConsulta = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('hdntelaConsulta').value;
            var idProcedimento = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('hdnIdProcedimento').value;
        } else if (window.top.document.getElementById("ifrConteudoVisualizacao") != null) {
            var idObjetivo = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('hdnIdSelecaoObjetivo').value;
            var telaConsulta = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('hdntelaConsulta').value;
            var idProcedimento = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('hdnIdProcedimento').value;
        } else {
            var idObjetivo = window.top.document.getElementById("hdnIdSelecaoObjetivo").value;
            var telaConsulta = window.top.document.getElementById("hdntelaConsulta").value;
        }

        var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_ods_consultar_objetivo_procedimento_ajax'); ?>';
        var result = {};
        if (idObjetivo != "") {
            result["idObjetivo"] = idObjetivo;
            result["telaConsulta"] = telaConsulta;
            result["idProcedimento"] = idProcedimento;
            result["forteRelacao"] = document.getElementById('forte-relacao').value === 'true';

            $.ajax({
                url: url,
                type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
                dataType: "json", //Tipo de dado que será enviado ao servidor
                data: result, // Enviando o JSON com o nome de itens
                async: false,
                success: function(data) {
                    callback(data);
                },
                error: function(err) {
                    callback("Ocorreu um erro ao consultar o objetivo.");
                }
            });
        } else {
            console.log("Deu erro ao carregar o objetivo");
        }
    }

    function salvarClassificacaoOds() {
        var error = false;
        var racionais = {};
        $(".infraCheckboxInput").each(function() {
            var idCheckbox = this.id;
            idCheckbox = idCheckbox.split("chkInfraItem");
            if ($("#hdnLike_" + idCheckbox[1]).val() == "") {
                alert("Você deve confirmar ou não as sugestões de classificação existentes antes de salvar.");
                error = true;
                return false;
            }
            if (!$("#txaRacional_" + idCheckbox[1]).prop("disabled")) {
                if ($("#txaRacional_" + idCheckbox[1]).val() == "") {
                    alert("Você deve preencher o racional antes de prosseguir.");
                    error = true;
                    return false;
                }
                var name = "txaRacional_" + this.value;
                var value = $("#txaRacional_" + idCheckbox[1]).val();
                racionais[name] = encodeURIComponent(value);
            }
        });
        if (error === true) {
            return false;
        }
        if ($("#hdnSugestaoIa").val() == "S") {
            var confirmar = confirm('Atenção, na tela consta pelo menos uma sugestão de classificação recente do SEI IA.\n' +
                '\n' +
                'Deseja continuar e confirmar a classificação?');
            if (confirmar == false) {
                return;
            }
        }
        $("#divMsg").hide();
        var result = {};
        result["hdnInfraItensSelecionados"] = $("#hdnInfraItensSelecionados").val();
        result["hdnHistoricoSelecionados"] = $("#hdnHistoricoSelecionados").val();
        result["hdnItensSugeridos"] = $("#hdnItensSugeridos").val();
        result["hdnIdObjetivo"] = $("#hdnIdObjetivo").val();
        result["hdnIdProcedimento"] = $("#hdnIdProcedimento").val();
        result["hdnIdSugestaoIa"] = $("#hdnIdSugestaoIa").val();
        result["racionais"] = racionais;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_cadastrar_classificacao_ods_ajax'); ?>', //selecionando o endereço que iremos acessar no backend
            type: 'POST',
            dataType: "json",
            data: result,
            success: function(retorno) {
                if (retorno.result == "true") {
                    atualizarListaOdsONU();
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html("Classificação realizada com sucesso.");
                    $("#divMsg > div").addClass("alert-success");
                    $("#divMsg").show();
                    $("#hdnHistoricoSelecionados").val(result["hdnInfraItensSelecionados"]);
                    $("#hdnAlteracoesRealizadas").val("1");

                    setTimeout(function() {
                        //fecharModal();
                    }, 1500);

                } else {
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html(retorno.mensagem);
                    $("#divMsg > div").addClass("alert-danger");
                    $("#divMsg").show();
                }

            },
            error: function(err) {
                rolar_para('#divMsg');
                $("#divMsg > div > label").html("Ocorreu um erro ao realizar a Classificação.");
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
            }
        });
    }

    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }

    function fecharModal() {
        /* if ($("#hdnAlteracoesRealizadas").val() == "1") {
             if (window.top.document.getElementById("ifrVisualizacao") != null) {
                 window.top.document.getElementById("ifrVisualizacao").src = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']) ?>";
             } else {
                 window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById("ifrVisualizacao").src = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento=' . $_GET['id_procedimento']) ?>";
             }
         }*/
        $(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();
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

    function exibirHistorico() {
        document.getElementById("divHistoricoOds").style.display = "";
        document.getElementById("btnMetas").style.display = "";
        document.getElementById("tabela_ordenada").style.display = "none";
        document.getElementById("btnSalvar").style.display = "none";
        document.getElementById("btnHistorico").style.display = "none";
        document.getElementById("btnFechar").style.display = "none";
        document.getElementById("divForteRelacao").style.display = "none";
        $('#divTituloModal').find('h4').html('Histórico das Metas');
    }

    function exibirMetas() {
        document.getElementById("divHistoricoOds").style.display = "none";
        document.getElementById("tabela_ordenada").style.display = "";
        document.getElementById("btnMetas").style.display = "none";
        document.getElementById("btnSalvar").style.display = "";
        document.getElementById("btnHistorico").style.display = "";
        document.getElementById("btnFechar").style.display = "";
        document.getElementById("divForteRelacao").style.display = "";
        $('#divTituloModal').find('h4').html('Metas dos Objetivos de Desenvolvimento Sustentável da ONU');
    }

    function atualizarListaOdsONU() {
        var input = {};
        input["id_procedimento"] = $("#hdnIdProcedimento").val();
        input["filtrar_forte_relacao"] = isSwitchAtivo();
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_lista_ods_onu_ajax'); ?>',
            type: 'POST',
            dataType: "json",
            data: input,
            success: function(resultado) {
                if (window.top.document.getElementById("ifrVisualizacao") != null) {
                    var telaOdsOnu = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('telaOdsOnu');
                    window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('btn-checkbox').checked = input["filtrar_forte_relacao"];
                } else if (window.top.document.getElementById("ifrConteudoVisualizacao") != null) {
                    var telaOdsOnu = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('telaOdsOnu');
                    window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document.getElementById('btn-checkbox').checked = input["filtrar_forte_relacao"];
                } else {
                    var telaOdsOnu = window.top.document.getElementById("telaOdsOnu");
                    window.top.document.getElementById('btn-checkbox').checked = input["filtrar_forte_relacao"];
                }
                $(telaOdsOnu).html(resultado);
                fecharModal();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function isSwitchAtivo() {
        const checkbox = document.getElementById('btn-checkbox');
        return checkbox.checked;
    }

    function atualizarListaObjetivos(obj){
        var trMetas = document.querySelectorAll("#tabela_ordenada > tbody > tr");
        if (obj.checked) {
            var arrIds = document.getElementById("arr-metas-forte-relacao").value.split(',');
            trMetas.forEach(function(tr) {
                if(tr.id && !arrIds.includes(tr.id)){
                    tr.style.display = "none";
                }
            });
        } else {
            trMetas.forEach(function(tr) {
                tr.style.display = "";
            });
        }
    }
</script>