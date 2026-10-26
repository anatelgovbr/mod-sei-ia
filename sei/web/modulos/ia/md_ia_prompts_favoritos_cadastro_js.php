<?php require_once "md_ia_funcoes_comuns_js.php"; ?>
<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    function inicializar() {
        var idPromptFavorito = window.top.document.getElementById('hdnIdPromptSelecionado').value;
        if (idPromptFavorito != '' && $("#hdnIdMdIaPromptsFavoritos").val() == '') {
            $("#hdnIdMdIaPromptsFavoritos").val(idPromptFavorito);
            carregarPromptAssistente(idPromptFavorito, {
                path: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
                seletorFormulario: '#frmNovoPromptFavorito'
            });
            window.top.document.getElementById('hdnIdPromptSelecionado').value = "";
        } else {
            $("#frmNovoPromptFavorito").css("display", "block");
        }
    }

    function cadastrarGrupoPromptsFav() {
        infraAbrirJanelaModal('<?= $strLinkNovoGrupoPromptsFav ?>', 700, 300);
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
</script>