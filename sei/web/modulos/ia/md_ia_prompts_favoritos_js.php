<?php require_once "md_ia_funcoes_comuns_js.php"; ?>
<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    window.mdIaFecharModalPadrao = {
        fecharTodos: true
    };
    window.mdIaTratarDigitacaoFormId = 'frmPromptsFavoritos';
    window.mdIaInicializarSelecaoConfig = {
        acaoAtual: '<?= $_GET['acao'] ?>',
        acaoSelecao: 'md_ia_prompts_favoritos_selecionar'
    };

    var inicializar = inicializarPromptSelecaoPadrao;

    function carregarPromptFavorito(idPromptFavorito) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaPromptsFavoritos"] = idPromptFavorito;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_prompt_favorito_ajax'); ?>',
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

    window.mdIaTransportarItemHandler = carregarPromptFavorito;

    <?php if ($bolAcaoExcluir) { ?>

        function acaoExcluir(id, desc) {
            if (confirm("Confirma exclusão do Prompt Favorito?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmPromptsFavoritos').action = '<?= $strLinkExcluir ?>';
                document.getElementById('frmPromptsFavoritos').submit();
            }
        }

        function acaoExclusaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhum Prompt Favorito selecionado.');
                return;
            }
            if (confirm("Confirma exclusão dos Prompts Favoritos selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmPromptsFavoritos').action = '<?= $strLinkExcluir ?>';
                document.getElementById('frmPromptsFavoritos').submit();
            }
        }
    <?php } ?>
</script>