<script type="text/javascript">
    function inicializar() {
        if ($('#btn-na-checkbox').is(':checked')) {
            setModeNaoSeAplica(true);
        } else {
            setModeNaoSeAplica(false);
        }

        $('#btn-na-checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                setModeNaoSeAplica(true);
                setAtualizaNaoSeAplica(true);
            } else {
                setModeNaoSeAplica(false);
                setAtualizaNaoSeAplica(false);
            }
        });
    }

    function consultarObjetivoOds(idObjetivo) {
        $("#hdnIdSelecaoObjetivo").val(idObjetivo);
        var Url = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo_procedimento&forte_relacao=false&id_procedimento=' . $_GET['id_procedimento']) ?>"
        if (document.getElementById("btn-checkbox").checked) {
            Url = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo_procedimento&forte_relacao=true&id_procedimento=' . $_GET['id_procedimento'])  ?>"
        }
        infraAbrirJanelaModal(Url, 1200, 1000, false);
    }

    function atualizarListaObjetivos(obj) {
        var divsObjetivos = document.querySelectorAll("#telaOdsOnu > div");
        if (obj.checked) {
            var arrIds = document.getElementById("arr-objetivos-forte-relacao").value.split(',');
            divsObjetivos.forEach(function(div) {
                if (!arrIds.includes(div.id)) {
                    div.style.display = "none";
                }
            });
        } else {
            divsObjetivos.forEach(function(div) {
                div.style.display = "";
            });
        }
    }

    function setModeNaoSeAplica(active) {
        if (active) {
            // ocultar grade e filtro
            $('#telaOdsOnu').hide();
            $('#filterWrapper').hide();
            $('#na_flag').val('1');
            // limpar seleções visuais de ODS (caso exista)
            $('#telaOdsOnu').find('.ods-item.selected').removeClass('selected').attr('aria-pressed', 'false');
        } else {
            $('#telaOdsOnu').show();
            $('#filterWrapper').show();
            $('#na_flag').val('0');
            atualizarListaObjetivos(document.getElementById("btn-checkbox"));
        }
    }

    function setAtualizaNaoSeAplica(active) {
        var dadosOdsOnu = {};
        dadosOdsOnu["idProcedimento"] = <?= $_GET['id_procedimento'] ?>;
        dadosOdsOnu["naoSeAplica"] = active;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_atualiza_nao_se_aplica_ods_onu_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosOdsOnu, // Enviando o JSON com o nome de itens
            success: function(data) {
                $("#divMsg > div > label").html("Salvo com sucesso.");
                $("#divMsg > div").addClass("alert-success");
                $("#divMsg").show();
            }
        });
        setInterval(function() {
            $("#divMsg").fadeOut(400);
        }, 5000);
    }
</script>