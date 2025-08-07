<script type="text/javascript">
    function inicializar() {
    }

    function consultarObjetivoOds(idObjetivo) {
        $("#hdnIdSelecaoObjetivo").val(idObjetivo);
        var Url = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo_procedimento&forte_relacao=false&id_procedimento=' . $_GET['id_procedimento']) ?>"
        if (document.getElementById("btn-checkbox").checked) {
            Url = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo_procedimento&forte_relacao=true&id_procedimento=' . $_GET['id_procedimento'])  ?>"
        }
        infraAbrirJanelaModal(Url, 1200, 1000, false);
    }

    function atualizarListaObjetivos(obj){
        var divsObjetivos = document.querySelectorAll("#telaOdsOnu > div");
        if (obj.checked) {
            var arrIds = document.getElementById("arr-objetivos-forte-relacao").value.split(',');
            divsObjetivos.forEach(function(div) {
                if(!arrIds.includes(div.id)){
                    div.style.display = "none";
                }
            });
        } else {
            divsObjetivos.forEach(function(div) {
                div.style.display = "";
            });
        }
    }

</script>