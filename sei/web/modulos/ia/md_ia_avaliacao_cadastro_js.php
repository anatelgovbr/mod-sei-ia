<script type="text/javascript">
    function inicializar() {
        infraEfeitoTabelas(true);
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

    function validarCamposObrigatoriosLikeDeslike() {
        var camposObrigatorios = document.getElementsByClassName('hdnAproved');
        var todosPreenchidos = true;
        for (var i = 0; i < camposObrigatorios.length; i++) {
            if (todosPreenchidos) {
                if (camposObrigatorios[i].value == "") {
                    todosPreenchidos = false;
                    alert("Informe o campo Avaliação.");
                    camposObrigatorios[i].focus();
                }
            }
        }

        return todosPreenchidos;
    }

    function bloquearBotaoSalvar() {
        var botoes = document.getElementsByClassName('botaoSalvar');
        if (botoes.length > 0) {
            for (var i = 0; i < botoes.length; i++) {
                botoes[i].setAttribute('disabled', 'disabled');
            }
        }
    }

    function OnSubmitForm() {
        if (!validarCamposObrigatoriosLikeDeslike()) {
            return false;
        }
        bloquearBotaoSalvar();
        document.getElementById("frmMdIaAvaliacaoCadastro").submit();
    }
</script>