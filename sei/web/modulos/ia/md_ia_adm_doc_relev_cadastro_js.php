<script>
    function inicializar() {
        infraEfeitoTabelas(true);
        objAutoCompletarTipoProcedimento = new infraAjaxAutoCompletar('hdnIdTpProcesso','txtTpProcesso','<?=$strLinkAjaxTipoProcedimento?>');
        objAutoCompletarTipoProcedimento.limparCampo = true;

        objAutoCompletarTipoProcedimento.prepararExecucao = function(){
            return 'palavras_pesquisa='+document.getElementById('txtTpProcesso').value;
        };

        objAutoCompletarTipoProcedimento.processarResultado = function(id,descricao,complemento){
            if (id!=''){
                var options = document.getElementById('selTpProcesso').options;
                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        alert('Tipo de Processo Específico já consta na lista.');
                        break;
                    }
                }

                if (i==options.length) {
                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }
                    opt = infraSelectAdicionarOption(document.getElementById('selTpProcesso'), descricao, id);
                    objLupaTipoProcedimento.atualizar();
                    opt.selected = true;
                }
            }
        };
        objAjaxIdDocumento = new infraAjaxMontarSelectDependente('selAplicabilidade','selTipoDocumento', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_doc_relev_tipo_documento_ajax'); ?>');
        document.getElementById('selTipoDocumento').innerHTML  = '';

        objAjaxIdDocumento.prepararExecucao = function(){
            return infraAjaxMontarPostPadraoSelect('null','',<?= $idProcedimento ?>) + '&aplicabilidade='+document.getElementById('selAplicabilidade').value;
        }
        objLupaTipoProcedimento = new infraLupaSelect('selTpProcesso','hdnIdTpProcesso','<?= $strLinkTipoProcedimentoSelecao ?>');

        if ('<?=$_GET['acao']?>' == 'md_ia_adm_doc_relev_cadastrar') {
            document.getElementById('selAplicabilidade').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_ia_adm_doc_relev_consultar') {
            infraDesabilitarCamposAreaDados();
            retornaTiposDocumentos();
            trocarTipoProcesso();
            $("#sbmAdicionarPercentualRelevanciaSegmento").hide();
        } else {
            document.getElementById('btnCancelar').focus();
            retornaTiposDocumentos();
            trocarTipoProcesso();
        }
    }

    function validarCadastro(event) {
        $("#divMsg").hide();

        if (infraTrim(document.getElementById('selAplicabilidade').value) == '') {
            alert('Informe a Aplicabilidade.');
            document.getElementById('selAplicabilidade').focus();
            return false;
        }

        if (infraTrim(document.getElementById('selTipoDocumento').value) == '') {
            alert('Informe o Tipo de Documento.');
            document.getElementById('selTipoDocumento').focus();
            return false;
        }

        if (!$("input[name='rdnRelevante']:checked").length) {
            alert('Informe se é Relevante para todos Tipos de Processo ou Relevante apenas para Tipos de Processos Específicos');
            document.getElementById('rdnRelevanteTodosProcessos').focus();
            return false;
        }

        if ($("[name='rdnRelevante']:checked").val() == "1" && $("#hdnIdTpProcesso").val() == "") {
            alert('Informe os Tipos de Processos Específicos');
            document.getElementById('txtTpProcesso').focus();
            return false;
        }
        verificarDocumentoExistente("S", function(data) {
            if (data["result"] != "false") {
                event.preventDefault();
                var mensagem = "";
                $.each(data, function (index, element) {
                    if (element != "" && $("#hdnIdTpProcesso").val() == "" && mensagem == "") {
                        mensagem = "O Tipo de Documento combinado com a Aplicabilidade selecionados já foram cadastrados com o Tipo de Processo Específicos listados abaixo. <br> Para cadastrar um novo Documento Relevante para Todos os Tipos de Processo, antes deve Desativar os Documentos Relevantes que possuem a mesma Aplicabilidade, o mesmo Tipo de Documento com os seguintes Tipos de Processos Específicos.";
                        mensagem += "<br> - " + element;
                    } else if (element == "" && $("#hdnIdTpProcesso").val() == "" && mensagem == "") {
                        mensagem = "O Tipo de Documento combinado com a Aplicabilidade selecionados já foram cadastrados para todos os Tipos de Processos.";
                    } else if (element != "" && $("#hdnIdTpProcesso").val() != "" && mensagem == "") {
                        mensagem = "O Tipo de Documento combinado com a Aplicabilidade selecionados já foram cadastrados com o Tipo de Processo Específicos listados abaixo. <br> Para cadastrar um novo Documento Relevante para os Tipos de Processos Específicos listados abaixo, antes deve Desativar os Documentos Relevantes que possuem a mesma Aplicabilidade, o mesmo Tipo de Documento com os seguintes Tipos de Processos Específicos.";
                        mensagem += "<br> - " + element;
                    } else if (element == "" && $("#hdnIdTpProcesso").val() != "" && mensagem == "") {
                        mensagem = "O Tipo de Documento combinado com a Aplicabilidade selecionados já foram cadastrados para todos os Tipos de Processos. <br> Para cadastrar um novo Documento Relevante para os Tipos de Processos Específicos, antes deve Desativar os Documentos Relevantes que possuem a mesma Aplicabilidade, o mesmo Tipo de Documento e seja Relevante para todos os Tipos de Processo.";
                    } else {
                        if (element != "") {
                            mensagem += "<br> - " + element;
                        }
                    }
                });
                rolar_para('#divMsg');
                $("#divMsg > div > label").html(mensagem);
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
            } else {
                verificarDocumentoExistente("N", function(data) {
                    if (data["result"] != "false") {
                        event.preventDefault();
                        var mensagem = data["result"];
                        rolar_para('#divMsg');
                        $("#divMsg > div > label").html(mensagem);
                        $("#divMsg > div").addClass("alert-danger");
                        $("#divMsg").show();
                    }
                });
            }
        });
    }
    function verificarDocumentoExistente(ativo, callback) {
        var form = $("#frmMdIaAdmDocRelevCadastro");
        var result = {};

        form.find(":input").each(function (index, element) {
            var name = $(element).attr('name');
            var value = $(element).val();
            result[name] = value;
        });
        let resultado;
        if(ativo == "S") {
            var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_documento_relevante_validar_ajax'); ?>';
        } else {
            var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_documento_relevante_validar_desativados_ajax'); ?>';
        }
        $.ajax({
            url: url,
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: result, // Enviando o JSON com o nome de itens
            async: false,
            success: function (data) {
                callback(data);
            },
            error: function (err) {
                callback("Ocorreu um erro ao verificar se o elemento já foi cadastrado.");
            }
        });
    }
    function OnSubmitForm(event) {
        if(!$("#hdnIdMdIaAdmDocRelev").val()) {
            return validarCadastro(event);
        } else {
            return true;
        }
    }
    function retornaTiposDocumentos() {
        objAjaxIdDocumento.executar();
        if($("#selAplicabilidade").val() == "I") {
            $("#segmentoDocumento").show();
        } else {
            $("#segmentoDocumento").hide();
        }
    }
    function trocarTipoProcesso(campo) {
        if($(campo).val() == 0) {
            $("#tipoProcessoEspecifico").hide();
        } else {
            $("#tipoProcessoEspecifico").show();
        }
    }
    function removerPercRelevanciaSegmento(id) {
        var descricaoSegmento = $("#"+id).attr("segmento");
        var peso_segmento = $("#"+id).attr("peso_segmento");
        linhaAtualizada = removerItemHiddenPercRelevSegmentos($("#hdnTbPercRelevSegmento").val(), id);
        linhaAtualizada = linhaAtualizada.substring(0, linhaAtualizada.length - 1);
        $("#hdnTbPercRelevSegmento").val(linhaAtualizada);
        $("#"+id).remove();
        $("option[value='']").remove();
        $('#selSegmento').append($('<option>', {
            value: id,
            text : descricaoSegmento
        }));
        var somaPesosAdicionados = $("#hdnPesoAdicionadoTabela").val();
        $("#hdnPesoAdicionadoTabela").val((somaPesosAdicionados - peso_segmento));
    }
    function adicionarPercentualRelevanciaSegmento() {
        $("#divMsg").hide();
        var percentualRelevanciaAdicionar = $("#txtPercRelevSegmentoAdicionar").val();
        var somaPesosAdicionados = $("#hdnPesoAdicionadoTabela").val();
        if($("#hdnCampoEdicao").val() != "") {
            var contadorSegmento = $("#hdnCampoEdicao").val();
        } else {
            var contadorSegmento = $("#hdnContadorSegmento").val();
            contadorSegmento = parseInt(contadorSegmento) + 1;
        }
        var itemJaAdicionado = false;
        itemAdicionar = $("#txtSegmentoDocumento");
        $('#tbPercRelevSegmento > tbody > tr').each(function (index, element) {
            if($(element).attr("segmento") == itemAdicionar.val() && ($("#hdnCampoEdicao").val() == "")) {
                rolar_para('#divMsg');
                $("#divMsg > div > label").html("O segmento informado já consta na lista.");
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
                itemJaAdicionado = true;
                return false;
            }
        });
        if (itemJaAdicionado === true) {
            return false;
        }
        if(percentualRelevanciaAdicionar < 1) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("O Percentual de Relevância deve ser maior que zero.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            return false;
        }

        if ($("#hdnCampoEdicao").val()) {
            var peso_segmento = $("#" + $("#hdnCampoEdicao").val()).attr("peso_segmento");
        } else {
            var peso_segmento = 0;
        }
        totalPercentualRelevancia = parseInt(percentualRelevanciaAdicionar) + parseInt(somaPesosAdicionados) - parseInt(peso_segmento);
        if(totalPercentualRelevancia > 100) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("A soma do Percentual de Relevância dos Segmentos dos Documentos não pode exceder 100%");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            return false;
        }
        linhaAtualizada = removerItemHiddenPercRelevSegmentos($("#hdnTbPercRelevSegmento").val(), $("#hdnCampoEdicao").val());
        linhaAtualizada += contadorSegmento+"±"+itemAdicionar.val()+"±"+percentualRelevanciaAdicionar;
        $("#hdnTbPercRelevSegmento").val(linhaAtualizada);
        var linhaTabela = montarLinhaTabela(itemAdicionar, percentualRelevanciaAdicionar, contadorSegmento);
        if($("#hdnCampoEdicao").val() != "") {
            $("#"+$("#hdnCampoEdicao").val()).remove();
            $("#hdnCampoEdicao").val("");
        }
        $("#tbPercRelevSegmento").append(linhaTabela);
        $("#hdnContadorSegmento").val(contadorSegmento);
        $("#hdnPesoAdicionadoTabela").val(totalPercentualRelevancia);
        $("#txtPercRelevSegmentoAdicionar").val("");
        $("#txtSegmentoDocumento").val("");
    }
    function editarPercRelevanciaSegmento(id) {
        $("#hdnCampoEdicao").val("");
        var peso_segmento = $("#"+id).attr("peso_segmento");
        var descricaoSegmento = $("#"+id).attr("segmento")
        $("#txtSegmentoDocumento").val(descricaoSegmento);
        $("#txtPercRelevSegmentoAdicionar").val(peso_segmento);
        $("#hdnCampoEdicao").val(id);
    }
    function atualizarPercRelevSegmentos() {
        $("#txtPercRelevSegmentos").val((100 - parseInt($("#txtPercRelevContDoc").val())));
    }
    function removerItemHiddenPercRelevSegmentos(hdnTbPercRelevSegmento, itemRemover) {
        var linhas = hdnTbPercRelevSegmento.split('¥');
        var linhaAtualizada = "";
        linhas.forEach(function(linha) {
            var colunas = linha.split('±');
            if(colunas[0] != itemRemover) {
                colunas.forEach(function (coluna) {
                    linhaAtualizada += coluna+"±";
                });
                linhaAtualizada = linhaAtualizada.substring(0, linhaAtualizada.length - 1);
                linhaAtualizada += "¥";
            }
        });
        return linhaAtualizada;
    }
    function montarLinhaTabela(itemAdicionar, percentualRelevanciaAdicionar, contadorSegmento) {
        var linhaTabela = "<tr id='"+contadorSegmento+"' peso_segmento='"+percentualRelevanciaAdicionar+"' segmento='"+itemAdicionar.val()+"'>";
        linhaTabela += "<td>"+itemAdicionar.val()+"</td>";
        linhaTabela +=  "<td>"+percentualRelevanciaAdicionar+"%</td>";
        linhaTabela +=  "<td>";
        linhaTabela +=  "<a onclick='editarPercRelevanciaSegmento("+contadorSegmento+")'><img src=' <?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/alterar.svg' title='Alterar Percentual de Relevância do Segmento' alt='Alterar Percentual de Relevância do Segmento' class='infraImg' /></a>";
        linhaTabela +=  "<a onclick='removerPercRelevanciaSegmento("+contadorSegmento+")'><img src='<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/excluir.svg' title='Excluir Percentual de Relevância do Segmento' alt='Excluir Percentual de Relevância do Segmento' class='infraImg' /></a>";
        linhaTabela +=  "</td>";
        linhaTabela +=  "</tr>";
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