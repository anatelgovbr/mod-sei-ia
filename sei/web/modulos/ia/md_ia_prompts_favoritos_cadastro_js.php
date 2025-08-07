<script type="text/javascript">
    function inicializar() {
        var acao = '<?= $_GET["acao"] ?>';
        var idPromptFavorito = '<?= $_GET["id_md_ia_interacao_chat"] ?>';
        if (idPromptFavorito == "") {
            var idPromptFavorito = window.top.document.getElementById('hdnIdPromptSelecionado').value;
        }
        $("#hdnIdMdIaInteracaoChat").val(idPromptFavorito);
        carregarPromptFavorito(idPromptFavorito, acao);
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

    function carregarPromptFavorito(idPromptFavorito, acao) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idPromptFavorito;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                if (data["id_prompt_favorito"] != null && acao == 'md_ia_prompts_favoritos_cadastrar') {
                    window.location.href = "<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_prompts_favoritos_alterar'); ?>";
                } else if (data["id_prompt_favorito"] != "" && acao == 'md_ia_prompts_favoritos_alterar') {
                    $("#selGrupoPromptsFav").val(data["id_grupo_favorito"]);
                    $("#txaDescricaoPrompt").val(data["descricao_prompt"]);
                    $("#hdnIdMdIaPromptsFavoritos").val(data["id_prompt_favorito"]);
                    $("#frmNovoPromptFavorito").css("display", "block");
                } else {
                    $("#frmNovoPromptFavorito").css("display", "block");
                }
            }
        });
    }
</script>