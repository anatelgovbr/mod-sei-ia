<script type="text/javascript">
    function inicializar() {
        pesquisarDocumentosApi();
        infraEfeitoTabelas(true);
        $(".idRanking").on("mouseup", function() {
            let campoVazio = false;
            $("#frmMdIaPesquisaDocumento .hdnAproved").each(function(index) {
                if($(this).val() == "") {
                    campoVazio = true;
                }
            });
            if(campoVazio) {
                alert ("Não é permitida a reordenação antes de avaliar todos os processos.");
            } else {
                $("#tabela_ordenada tbody").sortable("enable");
            }
        });
        $('#tabela_ordenada tbody').sortable({
            update: function (event, ui) {
                $(this).children().each(function (index) {
                    if ($(this).attr('data-position') != (index + 1)) {
                        $(this).attr('data-position', (index + 1)).addClass('updated');
                        var Input = $(this).closest('tr').find('td.idRanking').find('input');
                        Input.val((index + 1));
                        $(this).closest('tr').find('td.idRanking').html((index + 1) + '<i class="gg-arrows-v mr-2"></i><input type="hidden" id="' + Input.attr("id") + '" name="' + Input.attr("name") + '" value="' + Input.val() + '" />');
                    }
                });
                submeterDadosPesquisaDocumentoViaAjax();
                ;
            },
            helper: function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            handle: ".idRanking"
        });
        $("#tabela_ordenada tbody").sortable("disable");
        $('body').on('click', '.btn_thumbs', function () {
            if ($(this).hasClass("up") && $(this).parent("div").find(".down")) {
                $(this).parent("div").find(".down").removeClass("active");
                $(this).parent("div").find(".up").addClass("active");
                $(this).parent("div").find(".hdnAproved").val("1");
            } else {
                $(this).parent("div").find(".up").removeClass("active");
                $(this).parent("div").find(".down").addClass("active");
                $(this).parent("div").find(".hdnAproved").val("0");
            }
            submeterDadosPesquisaDocumentoViaAjax();
        });
        var animateButton = function (e) {
            e.preventDefault;
            //reset animation
            e.target.classList.remove('animate');
            e.target.classList.add('animate');
            setTimeout(function () {
                e.target.classList.remove('animate');
            }, 700);
        };

        var bubblyButtons = document.getElementsByClassName("bubbly-button");

        for (var i = 0; i < bubblyButtons.length; i++) {
            bubblyButtons[i].addEventListener('click', animateButton, false);
        }

    }
    function pesquisarDocumentosApi() {
        enviaPostApiRecomendacaoDocumento(function(result) {
            $("#resultado_processamento").html($.parseJSON(result));
        });
    }
    function enviaPostApiRecomendacaoDocumento(callback) {
        var form = window.top.document.getElementById("ifrVisualizacao").contentWindow.document.getElementById("frmMdIaPesquisaDocumentos");
        var result = {};
        var arrayAuxiliar = [];
        $(form).find(":input").each(function (index, element) {
            var name = $(element).attr('name');
            var value = $(element).val();
            if($(element).attr('type') == "checkbox" && $(element).is(":checked")) {
                arrayAuxiliar.push(value);
            } else {
                result[name] = value;
            }

        });
        result["ckbTipoDocumentoChecked"] = arrayAuxiliar;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_pesquisa_documentos_api_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: result, // Enviando o JSON com o nome de itens
            async: false,
            success: function (data) {
                callback(data);
            },
            error: function (err) {
                $("#divMsg > div > label").html("Ocorreu um erro ao consultar a API de recomendação de documentos.");
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
                callback(false);
            }
        });
    }

    function submeterDadosPesquisaDocumentoViaAjax() {
        var form = $("#frmMdIaPesquisaDocumento");
        var result = {};

        form.find(":input").each(function (index, element) {
            var name = $(element).attr('name');
            var value = $(element).val();
            result[name] = value;
        });
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_pesquisa_documento_cadastrar_ajax'); ?>', //selecionando o endereço que iremos acessar no backend
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: result, // Enviando o JSON com o nome de itens
            success: function () {
                console.log('Dados enviados');
            },
            error: function (err) {
                //Em caso de erro
                console.log('Dados não enviados');
            }
        }).done(function () {
            //Finalizando todos os passos da operação de AJAX
            console.log('Dados enviados');
        });
    }
</script>