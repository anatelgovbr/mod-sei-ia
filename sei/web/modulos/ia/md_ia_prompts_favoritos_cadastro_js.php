<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    function inicializar() {
        var idPromptFavorito = window.top.document.getElementById('hdnIdPromptSelecionado').value;
        if (idPromptFavorito != '' && $("#hdnIdMdIaPromptsFavoritos").val() == '') {
            $("#hdnIdMdIaPromptsFavoritos").val(idPromptFavorito);
            carregarPromptAssistente(idPromptFavorito);
            window.top.document.getElementById('hdnIdPromptSelecionado').value = "";
        } else {
            $("#frmNovoPromptFavorito").css("display", "block");
        }
    }

    function cadastrarGrupoPromptsFav() {
        infraAbrirJanelaModal('<?= $strLinkNovoGrupoPromptsFav ?>', 700, 300);
    }

    function OnSubmitForm(event) {
        return validarCadastro(event);
    }

    function validarCadastro(event) {
        $("#divMsg").hide();
        if (document.getElementById('selGrupoPromptsFav').value <= 0) {
            alert('Informe o Grupo.');
            document.getElementById('selGrupoPromptsFav').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txaDescricaoPrompt').value) == '') {
            alert('Informe a Descrição do Prompt.');
            document.getElementById('txaDescricaoPrompt').focus();
            return false;
        }

        return true;
    }

    function carregarPromptAssistente(idPromptGaleriaPrompt) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idPromptGaleriaPrompt;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                mensagem = decodeHtmlEntitiesPergunta(data["pergunta"]);
                mensagem = getTextForTextarea(mensagem);
                $("#txaPrompt").val(mensagem);
                $("#frmNovoPromptFavorito").css("display", "block");
            }
        });
    }
</script>