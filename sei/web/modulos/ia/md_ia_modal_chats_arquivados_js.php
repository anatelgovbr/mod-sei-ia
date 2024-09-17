<script src="modulos/ia/lib/popper/popper.min.js"></script>
<script src="modulos/ia/lib/popper/tippy.js"></script>
<script type="text/javascript">
    function desarquivarTopico(id) {
        var dadosTopico = {};
        dadosTopico["id_topico"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_desarquivar_topico'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: dadosTopico, // Enviando o JSON com o nome de itens
            success: function (data) {
                window.parent.listarTopicos();
                window.location.reload();
            },
            error: function (err) {
                console.log(err);
            }
        });
    }
</script>