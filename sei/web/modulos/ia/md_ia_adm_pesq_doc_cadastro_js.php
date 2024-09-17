<script type="text/javascript">

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_ia_adm_pesq_doc_cadastrar') {
            document.getElementById('txtQtdProcessListagem').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_ia_adm_pesq_doc_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas(true);
    }

    function validarCadastro() {
        if (infraTrim(document.getElementById('txtQtdProcessListagem').value) == '') {
            alert('Informe a Quantidade de Resultados a serem listados.');
            document.getElementById('txtQtdProcessListagem').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtOrientacoesGerais').value) == '') {
            alert('Informe a Orientações Gerais.');
            document.getElementById('txtOrientacoesGerais').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtNomeSecao').value) == '') {
            alert('Informe o Nome da Seção na Tela do Usuário.');
            document.getElementById('txtNomeSecao').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }
    function adicionarTipoDocumento() {
        var IdItipoDocumento = $("#selTipoDocumento").val();
        if($("#"+IdItipoDocumento).length) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("O Tipo de Documento selecionado já foi adicionado como Tipo de Documento Alvo da Pesquisa.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            return false;
        }
        var nomeTipoDocumento = $('#selTipoDocumento').find(":selected").text();
        var linhaAtualizada = $("#hdnTbTipoDocumento").val()+"¥"+IdItipoDocumento+"±S";
        $("#hdnTbTipoDocumento").val(linhaAtualizada);
        var linhaTabela = montarLinhaTabela(IdItipoDocumento, nomeTipoDocumento, true);
        $("#tbTipoDocumento").append(linhaTabela);
    }
    function montarLinhaTabela(IdItipoDocumento, nomeTipoDocumento, itemNovo, ativo) {
        var dataAtual = new Date();
        var dia = ("0" + dataAtual.getDate()).slice(-2)
        var mes = ("0" + (dataAtual.getMonth() + 1)).slice(-2);
        var ano = dataAtual.getFullYear();
        var horas = ("0" + dataAtual.getHours()).slice(-2);
        var minutos = ("0" + dataAtual.getMinutes()).slice(-2);
        var segundos = ("0" + dataAtual.getSeconds()).slice(-2);
        if(itemNovo) {
            var linhaTabela = "<tr id='" + IdItipoDocumento + "'>";
        }
        linhaTabela +=  "<td>"+nomeTipoDocumento+"</td>";
        linhaTabela +=  "<td>"+dia+"/"+mes+"/"+ano+" "+horas+":"+minutos+":"+segundos+"</td>";
        linhaTabela +=  "<td>";
        if(itemNovo) {
            linhaTabela +=  "<a onclick='removerTipoDocumento("+IdItipoDocumento+")'><img src=' <?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg' title='Excluir Tipo de Documento' alt='Excluir Tipo de Documento' class='infraImg' /></a>";
        } else if(ativo) {
            linhaTabela +=  "<a onclick='desativarTipoDocumento("+IdItipoDocumento+")'><img src=' <?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/desativar.svg' title='Desativar Tipo de Documento' alt='Desativar Tipo de Documento' class='infraImg' /></a>";
        } else {
            linhaTabela +=  "<a onclick='ativarTipoDocumento("+IdItipoDocumento+")'><img src=' <?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/reativar.svg' title='Reativar Tipo de Documento' alt='Reativar Tipo de Documento' class='infraImg' /></a>";
        }
        linhaTabela +=  "</td>";
        if(itemNovo) {
            linhaTabela += "</tr>";
        }
        return linhaTabela;
    }
    function removerTipoDocumento(IdItipoDocumento) {
        $("#"+IdItipoDocumento).remove();
        linhaAtualizada = removerItemHiddenTipoDocumento($("#hdnTbTipoDocumento").val(), IdItipoDocumento);
        $("#hdnTbTipoDocumento").val(linhaAtualizada);
    }
    function removerItemHiddenTipoDocumento(hdnTbTipoDocumento, itemRemover) {
        var linhas = hdnTbTipoDocumento.split('¥');
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
        linhaAtualizada = linhaAtualizada.substring(0, linhaAtualizada.length - 1);
        return linhaAtualizada;
    }
    function desativarTipoDocumento(IdItipoDocumento) {
        var linhaAtualizada = removerItemHiddenTipoDocumento($("#hdnTbTipoDocumento").val(), IdItipoDocumento);
        linhaAtualizada = linhaAtualizada+"¥"+IdItipoDocumento+"±N";
        var nomeTipoDocumento = $('#'+IdItipoDocumento+' > td:first-child').html();
        $("#hdnTbTipoDocumento").val(linhaAtualizada);
        var linhaTabela = montarLinhaTabela(IdItipoDocumento, nomeTipoDocumento, false, false);
        $("#"+IdItipoDocumento).addClass("trVermelha");
        $("#"+IdItipoDocumento).html(linhaTabela);
    }
    function ativarTipoDocumento(IdItipoDocumento) {
        var linhaAtualizada = removerItemHiddenTipoDocumento($("#hdnTbTipoDocumento").val(), IdItipoDocumento);
        linhaAtualizada = linhaAtualizada+"¥"+IdItipoDocumento+"±S";
        var nomeTipoDocumento = $('#'+IdItipoDocumento+' > td:first-child').html();
        $("#hdnTbTipoDocumento").val(linhaAtualizada);
        var linhaTabela = montarLinhaTabela(IdItipoDocumento, nomeTipoDocumento, false, true);
        $("#"+IdItipoDocumento).html(linhaTabela);
        $("#"+IdItipoDocumento).removeClass("trVermelha");
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