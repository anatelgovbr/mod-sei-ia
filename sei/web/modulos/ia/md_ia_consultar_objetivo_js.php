<script type="text/javascript">
    function inicializar() {

        carregarObjetivo(function(data) {
            $("#conteudoObjetivo").html(data);
            infraCriarCheckboxRadio("infraCheckbox","infraCheckboxDiv","infraCheckboxLabel","infraCheckbox","infraCheckboxInput");
        });

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
                bloquearPermitirCassificacaoUsuarioExterno();
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
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_configurar_metas_ajax'); ?>', //selecionando o endereço que iremos acessar no backend
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: dadosPost, // Enviando o JSON com o nome de itens
            success: function (retorno) {

                if(retorno.result == "true") {
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html("Classificação realizada com sucesso.");
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