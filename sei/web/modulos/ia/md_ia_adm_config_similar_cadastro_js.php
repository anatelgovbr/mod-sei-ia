<script type="text/javascript">

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_ia_adm_config_similar_cadastrar') {
            document.getElementById('txtIdMdIaAdmConfigSimilar').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_ia_adm_config_similar_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas(true);
        removerItensSelectMetadado();
    }

    function validarCadastro() {
        $("#divMsg").hide();
        if (infraTrim(document.getElementById('txtQtdProcessListagem').value) == '') {
            alert('Informe a Quantidade de Resultados a serem listados.');
            document.getElementById('txtQtdProcessListagem').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtOrientacoesGerais').value) == '') {
            alert('Informe as Orientações Gerais.');
            document.getElementById('txtOrientacoesGerais').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtPercRelevContDoc').value) == '') {
            alert('Informe o Percentual de Relevância do Conteúdo dos Documentos.');
            document.getElementById('txtPercRelevContDoc').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtPercRelevMetadados').value) == '') {
            alert('Informe o Percentual de Relevância dos Metadados.');
            document.getElementById('txtPercRelevMetadados').focus();
            return false;
        }
        if (document.getElementById('txtQtdProcessListagem').value < 1 || document.getElementById('txtQtdProcessListagem').value > 15) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("A Quantidade de Resultados a serem listados deve ser maior que 1 e menor que 15.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            document.getElementById('txtQtdProcessListagem').focus();
            return false;
        }
        if (document.getElementById('txtPercRelevContDoc').value < 1) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("O Percentual de Relevância do Conteúdo dos Documentos não pode ser zero.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            document.getElementById('txtPercRelevContDoc').focus();
            return false;
        }
        if (document.getElementById('hdnPesoAdicionadoTabela').value != 100) {
            alert('A soma dos percentuais dos metadados deve ser igual a 100.');
            document.getElementById('txtMetadado').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function removerItensSelectMetadado() {
        var hdnTbPercRelevMetadado = $("#hdnTbPercRelevMetadado").val();
        var linhas = hdnTbPercRelevMetadado.split('¥');
        linhas.forEach(function (linha) {
            coluna = linha.split('±');
            $("option[value='" + coluna[0] + "']").remove();
        });
    }

    function adicionarPercentualRelevanciaMetadado() {
        var percentualRelevanciaAdicionar = $("#txtPercRelevMetadadoAdicionar").val();
        var somaPesosAdicionados = $("#hdnPesoAdicionadoTabela").val();
        itemAdicionar = $("#txtMetadado");
        var peso_metadado = $("#" + $("#hdnMetadado").val()).attr("peso_metadado");
        totalPercentualRelevancia = parseInt(percentualRelevanciaAdicionar) + parseInt(somaPesosAdicionados) - parseInt(peso_metadado);
        linhaAtualizada = removerItemHiddenPercRelevMetadados($("#hdnTbPercRelevMetadado").val(), $("#hdnMetadado").val());
        linhaAtualizada += $("#hdnMetadado").val() + "±" + itemAdicionar.val() + "±" + percentualRelevanciaAdicionar;
        $("#hdnTbPercRelevMetadado").val(linhaAtualizada);
        $("#" + $("#hdnMetadado").val()).remove();
        var linhaTabela = montarLinhaTabela(itemAdicionar, percentualRelevanciaAdicionar, $("#hdnMetadado").val());
        $("#tbPercRelevMetadado").append(linhaTabela);
        $("#hdnPesoAdicionadoTabela").val(totalPercentualRelevancia);
        $("#txtMetadado").val("");
        $("#hdnMetadado").val("");
        $("#txtPercRelevMetadadoAdicionar").val("");
        $('#txtPercRelevMetadadoAdicionar').prop("disabled", true);
        $('#sbmAdicionarPercentualRelevanciaMetadado').prop("disabled", true);
    }

    function editarPercRelevanciaMetadado(id) {
        var peso_metadado = $("#" + id).attr("peso_metadado");
        var descricaoMetadado = $("#" + id).attr("descricao_metadado");
        $("#txtMetadado").html("");
        $('#txtMetadado').val(descricaoMetadado);
        $('#hdnMetadado').val(id);
        $("#txtPercRelevMetadadoAdicionar").val(peso_metadado);
        $('#txtPercRelevMetadadoAdicionar').prop("disabled", false);
        $('#sbmAdicionarPercentualRelevanciaMetadado').prop("disabled", false);
    }

    function atualizarPercRelevMetadados() {
        var percRelevContDoc = $("#txtPercRelevContDoc").val();
        if(percRelevContDoc == "") {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("O Percentual de Relevância do Conteúdo dos Documentos não pode ser vazio.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            percRelevContDoc = 0;
        } else {
            $("#divMsg").hide();
        }
        $("#txtPercRelevMetadados").val((100 - parseInt(percRelevContDoc)));
    }

    function removerItemHiddenPercRelevMetadados(hdnTbPercRelevMetadado, itemRemover) {
        var linhas = hdnTbPercRelevMetadado.split('¥');
        var linhaAtualizada = "";
        linhas.forEach(function (linha) {
            var colunas = linha.split('±');
            if (colunas[0] != itemRemover) {
                colunas.forEach(function (coluna) {
                    linhaAtualizada += coluna + "±";
                });
                linhaAtualizada = linhaAtualizada.substring(0, linhaAtualizada.length - 1);
                linhaAtualizada += "¥";
            }
        });
        return linhaAtualizada;
    }

    function montarLinhaTabela(itemAdicionar, percentualRelevanciaAdicionar, idItemAdicionar) {
        var dataAtual = new Date();
        var dia = ("0" + dataAtual.getDate()).slice(-2)
        var mes = ("0" + (dataAtual.getMonth() + 1)).slice(-2);
        var ano = dataAtual.getFullYear();
        var horas = ("0" + dataAtual.getHours()).slice(-2);
        var minutos = ("0" + dataAtual.getMinutes()).slice(-2);
        var segundos = ("0" + dataAtual.getSeconds()).slice(-2);
        var linhaTabela = "<tr id='" + idItemAdicionar + "' descricao_metadado='" + itemAdicionar.val() + "' peso_metadado='" + percentualRelevanciaAdicionar + "'>";
        linhaTabela += "<td>" + itemAdicionar.val() + "</td>";
        linhaTabela += "<td>" + percentualRelevanciaAdicionar + "%</td>";
        linhaTabela += "<td>" + dia + "/" + mes + "/" + ano + " " + horas + ":" + minutos + ":" + segundos + "</td>";
        linhaTabela += "<td class='text-center'>";
        linhaTabela += "<a onclick='editarPercRelevanciaMetadado(" + idItemAdicionar + ")'><img src=' <?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/alterar.svg' title='Alterar Percentual de Relevância do Metadado' alt='Alterar Percentual de Relevância do Metadado' class='infraImg' /></a>";
        linhaTabela += "</td>";
        linhaTabela += "</tr>";
        return linhaTabela;
    }
    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }
</script>