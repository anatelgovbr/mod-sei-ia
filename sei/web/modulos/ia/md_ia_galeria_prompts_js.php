<?php require_once "md_ia_funcoes_comuns_js.php"; ?>
<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    window.mdIaFecharModalPadrao = {
        fecharTodos: true
    };
    window.mdIaTratarDigitacaoFormId = 'frmGaleriaPrompts';
    window.mdIaInicializarSelecaoConfig = {
        acaoAtual: '<?= $_GET['acao'] ?>',
        acaoSelecao: 'md_ia_galeria_prompts_selecionar'
    };

    var inicializar = inicializarPromptSelecaoPadrao;

    function carregarPromptGaleriaPrompt(idPrompt) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaGaleriaPrompts"] = idPrompt;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_prompt_galeria_prompts_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                mensagem = getTextForTextarea(data["prompt"]).replace(/\r\n?/g, '\n');
                var campo = window.top.document.getElementById('mensagem');

                campo.textContent = mensagem;
                campo.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                $(window.top.document).find('div[id^=divInfraSparklingModalClose]').get().reverse().forEach(function(element) {
                    $(element).click();
                    campo.focus();
                });
            }
        });
    }

    window.mdIaTransportarItemHandler = carregarPromptGaleriaPrompt;
    <?php if ($bolAcaoDesativar) { ?>

        function acaoDesativar(id, desc) {
            if (confirm("Confirma desativação do Prompt Publicado \"" + desc + "\"?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkDesativar ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }

        function acaoDesativacaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhuma Prompt Publicado selecionada.');
                return;
            }
            if (confirm("Confirma desativação dos Prompts Publicados selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkDesativar ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }
    <?php } ?>

    <?php if ($bolAcaoReativar) { ?>

        function acaoReativar(id, desc) {
            if (confirm("Confirma reativação do Prompt Publicado \"" + desc + "\"?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkReativar ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }

        function acaoReativacaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhuma Prompt Publicado selecionada.');
                return;
            }
            if (confirm("Confirma reativação dos Prompts Publicados selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkReativar ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }
    <?php } ?>
    <?php if ($bolAcaoExcluir) { ?>

        function acaoExcluir(id, desc) {
            if (confirm("Confirma exclusão da Galeria de Prompts?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkExcluir ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }

        function acaoExclusaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhum Prompt selecionado.');
                return;
            }
            if (confirm("Confirma exclusão dos Prompts da Galeria de Prompts?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmGaleriaPrompts').action = '<?= $strLinkExcluir ?>';
                document.getElementById('frmGaleriaPrompts').submit();
            }
        }
    <?php } ?>
</script>