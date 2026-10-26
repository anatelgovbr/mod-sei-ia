<?php require_once "md_ia_funcoes_comuns_js.php"; ?>
<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    function inicializar() {
        var idPromptGaleriaPrompt = window.top.document.getElementById('hdnIdPromptSelecionado').value;
        if (idPromptGaleriaPrompt != '' && $("#hdnIdMdIaGaleriaPrompts").val() == '') {
            $("#hdnIdMdIaGaleriaPrompts").val(idPromptGaleriaPrompt);
            carregarPromptAssistente(idPromptGaleriaPrompt, {
                path: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
                seletorFormulario: '#frmPublicarGaleriaPrompt'
            });
            window.top.document.getElementById('hdnIdPromptSelecionado').value = "";
        } else {
            $("#frmPublicarGaleriaPrompt").css("display", "block");
        }
    }

    function validarCadastro(event) {
        $("#divMsg").hide();
        if (document.getElementById('selGrupoGaleriaPrompts').value <= 0) {
            alert('Informe o Grupo.');
            document.getElementById('selGrupoGaleriaPrompts').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txaDescricaoPrompt').value) == '') {
            alert('Informe a Descrição do Prompt.');
            document.getElementById('txaDescricaoPrompt').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txaPrompt').value) == '') {
            alert('Informe o Prompt.');
            document.getElementById('txaPrompt').focus();
            return false;
        }

        return true;
    }
</script>