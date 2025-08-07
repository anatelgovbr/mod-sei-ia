<script type="text/javascript">
    <?php
        $strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_tipo_procedimento_auto_completar');
    ?>

    var objLupaTipoProcessos = [];
    var objAutoCompletarTipoProcesso = null;

    function inicializar() {

        carregarObjetivo(function(data) {
            $("#conteudoObjetivo").html(data);
            infraCriarCheckboxRadio("infraCheckbox","infraCheckboxDiv","infraCheckboxLabel","infraCheckbox","infraCheckboxInput");
        });

        document.querySelectorAll('.row-meta').forEach(function(input) {
            inicializarAutocomplete(input.id);
        });

        document.addEventListener('change', function(event) {
            if (event.target && event.target.matches('input[type="checkbox"].infraCheckboxInput')) {
                const select = document.getElementById('selTipoProcessos_' + event.target.value);
                if (!event.target.checked && select.options.length > 0) {
                    alert('Ao desmarcar a Forte Relação e salvar o formulário os Tipos de Processos cadastrados serão excluídos');
                }
            }
        });

    }

    function inicializarAutocomplete(index) {
        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar(
            'hdnIdTipoProcesso_' + index,
            'txtTipoProcesso_' + index,
            '<?=$strLinkAjaxTipoProcesso?>'
        );
        objAutoCompletarTipoProcesso.limparCampo = true;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso_' + index).value;
        };
        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var sel = document.getElementById('selTipoProcessos_' + index);
                var options = sel.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == id) {
                        alert('Tipo de Processo já consta na lista.');
                        return;
                    }
                }
                var opt = infraSelectAdicionarOption(sel, descricao, id);
                opt.selected = true;
                document.getElementById('txtTipoProcesso_' + index).value = '';
                document.getElementById('txtTipoProcesso_' + index).focus();
            }
        };
        objLupaTipoProcessos[index] = new infraLupaSelect('selTipoProcessos_' + index, 'hdnIdTipoProcesso_' + index, null);
    }

    function carregarObjetivo(callback) {
        if( window.top.document.getElementById("ifrVisualizacao") != null) {
            var idObjetivo = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById('hdnIdSelecaoObjetivo').value;
        } else {
            var idObjetivo = window.top.document.getElementById("hdnIdSelecaoObjetivo").value;
        }

        var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_ods_consultar_objetivo_ajax'); ?>';
        var result = {};
        result["idObjetivo"] = idObjetivo;

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
                callback("Ocorreu um erro ao consultar o Metas do Objetivo.");
            }
        });
    }

    function infraCriarCheckboxRadio(classInput,classDiv,classLabel,classRemocao,classAdicao){
        $("."+classInput).each(function (index) {
            var div = $('<div class="'+classDiv+'" ></div>');
            var isVisible = this.style.visibility !== 'hidden' && this.style.display !== 'none';
            if (isVisible) {
                $(this).wrap(div);
            }else{
                $(this).addClass("infraCheckboxRadioSemDiv");
            }

            var id = $(this).attr("id");
            var title = $(this).attr("title");

            var label = $('<label class="'+classLabel+'"></label>');

            if(id != undefined){
                $(label).attr("for",id);
            }
            if(title != undefined){
                $(label).attr("title",title);
            }

            $(this).removeClass(classRemocao);
            $(this).addClass(classAdicao);

            label.insertAfter($(this));
        });
    }

    function salvarConfiguracaoMetas() {

        $("#divMsg").hide();

        var dadosPost = {};
        dadosPost["hdnInfraItensSelecionados"] = $("#hdnInfraItensSelecionados").val();
        dadosPost["hdnIdObjetivo"] = $("#hdnIdObjetivo").val();
        dadosPost['tipoProcessoMetas'] = lerSelectsTipoProcessoDasMetas();
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_configurar_metas_ajax'); ?>', //selecionando o endereço que iremos acessar no backend
            type: 'POST',
            dataType: "json",
            data: dadosPost,
            success: function (retorno) {

                if(retorno.result == "true") {
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html("Parâmetros salvos com sucesso.");
                    $("#divMsg > div").addClass("alert-success");
                    $("#divMsg").show();
                    $("#hdnHistoricoSelecionados").val(dadosPost["hdnInfraItensSelecionados"]);
                } else {
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html(retorno.mensagem);
                    $("#divMsg > div").addClass("alert-danger");
                    $("#divMsg").show();
                }

                setTimeout(function(){
                    $("#divMsg").fadeOut(300);
                    fecharModal();
                }, 2000);

            },
            error: function (err) {
                //Em caso de erro
                rolar_para('#divMsg');
                $("#divMsg > div > label").html("Ocorreu um erro ao realizar a Classificação.");
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
            }
        });
    }

    function lerSelectsTipoProcessoDasMetas() {
        const resultado = {};

        document.querySelectorAll('select[id^="selTipoProcessos_"]').forEach(select => {
            const id = select.id;
            const indice = id.split('_')[1]; // Extrai o sufixo após "_"

            const values = Array.from(select.options).map(option => option.value);

            resultado[indice] = values;
        });

        return resultado;
    }

    function atualizarVisibilidadeCamposTipoProcesso() {
        const idsSelecionados = document.getElementById('hdnInfraItensSelecionados').value
            .split(',')
            .map(id => id.trim())
            .filter(id => id !== '');


        document.querySelectorAll('.row-meta').forEach(td => {
            const div = td.querySelector('div');
            if (idsSelecionados.includes(td.id)) {
                div.style.display = '';
            } else {
                div.style.display = 'none';
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
        $(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();
    }

</script>