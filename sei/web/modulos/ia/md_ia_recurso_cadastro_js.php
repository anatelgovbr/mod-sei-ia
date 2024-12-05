<script type="text/javascript">
    function inicializar() {
        infraEfeitoTabelas(true);
        $("#divMsgProcessosSimilares").hide();
        $("#divMsgPesquisaDocumento").hide();
        $(".idRanking").on("mouseup", function() {
            <?php
                if($exibirRacional != "1") {
            ?>
                let campoVazio = false;
                $("#frmMdIaSimilaridadeCadastro .hdnAproved").each(function(index) {
                    if($(this).val() == "") {
                        campoVazio = true;
                    }
                });
                if(campoVazio) {
                    alert ("Não é permitida a reordenação antes de avaliar todos os processos.");
                } else {
                    $("#tabela_ordenada tbody").sortable("enable");
                }
            <?php
                } else {
            ?>
                $("#tabela_ordenada tbody").sortable("enable");
            <?php
                }
            ?>
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
                <?php
                    if($exibirRacional != "1") {
                ?>
                 submeterDadosViaAjax();
                <?php
                    }
                ?>
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
            <?php
                if($exibirRacional != "1") {
            ?>
                submeterDadosViaAjax($(this));
            <?php
                }
            ?>
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
        <?php
        if ($objMdIaAdmPesqDocDTO->getStrSinExibirFuncionalidade() == "S") {
        ?>
        objLupaDocumento = new infraLupaSelect('selDocumento','hdnIdDocumento','<?= $strLinkDocumentoSelecao ?>');
        <?php
        }
        ?>
        $("#txtTextoPesquisa").keyup(function(){
            var content = $("#txtTextoPesquisa").val(); //content is now the value of the text box
            var words = content.split(/\s+/); //words is an array of words, split by space
            var num_words = words.length; //num_words is the number of words in the array
            var max_limit=512;
            $("#divMsgPesquisaDocumento").hide();
            if(num_words>max_limit){
                var lastIndex = content.lastIndexOf(" ");
                $("#txtTextoPesquisa").val(content.substring(0, lastIndex));
                exibeMensagem("#divMsgPesquisaDocumento", "alert-danger", "A quantidade máxima de palavras suportadas no Texto para Pesquisa é de 512 palavras.");
                $("#txtTextoPesquisa").focus();
                return false;
            }
        });
    }
    function erroProcessosSimilares(elemento) {
        <?php
            if($exibirRacional == "1") {
        ?>
                exibeMensagem("#divMsgProcessosSimilares", "alert-danger", "Ocorreu um erro ao realizar a Avaliação de Similaridade entre Processos.");
        <?php
            } else {
        ?>
                elemento.removeClass("active");
                $("#tabela_ordenada tbody" ).sortable( "cancel" );
        <?php
            }
        ?>
    }
    function submeterDadosViaAjax(elemento) {
        $("#divMsgProcessosSimilares").hide();
        <?php
        if($exibirRacional == "1") {
        ?>
            event.preventDefault();
            if (!validarCamposObrigatoriosLikeDeslike()) {
                return false;
            }
        <?php
            }
        ?>
        var form = $("#frmMdIaSimilaridadeCadastro");
        var result = {};

        form.find(":input").each(function (index, element) {
            var name = $(element).attr('name');
            var value = $(element).val();
            result[name] = value;
        });
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_similaridade_cadastrar_ajax'); ?>', //selecionando o endereço que iremos acessar no backend
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: result, // Enviando o JSON com o nome de itens
            success: function (data) {
                if(data != "Feedbacks salvos com sucesso.") {
                    erroProcessosSimilares(elemento);
                } else {
                    <?php
                    if($exibirRacional == "1") {
                    ?>
                    exibeMensagem("#divMsgProcessosSimilares", "alert-success", "Avaliação de Similaridade entre Processos realizada com sucesso.");
                    <?php
                    }
                    ?>
                }
            },
            error: function (err) {
                erroProcessosSimilares(elemento);
            }
        }).done(function () {
        //Finalizando todos os passos da operação de AJAX
            console.log('Dados enviados');
        });
    }
    function pesquisarDocumentos() {
        $("#divMsgPesquisaDocumento").hide();
        var form = $("#frmMdIaPesquisaDocumentos");
        if(form.find("input[name='ckbTipoDocumento[]']:checked").length > 0) {
            if($("#txtTextoPesquisa").val() == "" && $("#hdnIdDocumento").val() == "") {
                exibeMensagem("#divMsgPesquisaDocumento", "alert-danger", "É necessário preencher o Texto para Pesquisa ou selecionar Documentos para montar a pesquisa.");
            } else {
                infraAbrirJanelaModal("<?= $strLinkPesquisaDocumento ?>",
                    1200,
                    550);
            }
        } else {
            exibeMensagem("#divMsgPesquisaDocumento", "alert-danger", "É obrigatório escolher pelo menos um Tipo de Documento Alvo para a pesquisa.");
        }
    }
    function validarCamposObrigatoriosLikeDeslike() {
        $("#divMsgProcessosSimilares").hide();
        var camposObrigatorios = document.getElementsByClassName('hdnAproved');
        var todosPreenchidos = true;
        for (var i = 0; i < camposObrigatorios.length; i++) {
            if (todosPreenchidos) {
                if (camposObrigatorios[i].value == "") {
                    todosPreenchidos = false;
                    exibeMensagem("#divMsgProcessosSimilares", "alert-danger", "É necessário avaliar todas as recomendações de similaridade antes de prosseguir.");
                    camposObrigatorios[i].focus();
                }
            }
        }

        return todosPreenchidos;
    }
    function exibeMensagem(elemento, classe, mensagem) {
        $(elemento+" > div").removeClass("alert-warning");
        $(elemento+" > div").removeClass("alert-danger");
        $(elemento+" > div").removeClass("alert-success");
        $(elemento+" > div > label").html(mensagem);
        $(elemento+" > div").addClass(classe);
        $(elemento).show();
    }
    function consultarObjetivoOds(idObjetivo) {
        $("#hdnIdSelecaoObjetivo").val(idObjetivo);
        infraAbrirJanelaModal("<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo_procedimento&id_procedimento='.$_GET['id_procedimento']) ?>",
            1200,
            1000, false);
    }

</script>