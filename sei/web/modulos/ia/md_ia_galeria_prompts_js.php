<script src="modulos/ia/lib/purify/purify.js"></script>
<script type="text/javascript">
    function inicializar() {

        //infraOcultarMenuSistemaEsquema();

        if ('<?= $_GET['acao'] ?>' == 'md_ia_galeria_prompts_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }

    function fecharModal() {
        $(window.top.document).find('div[id^=divInfraSparklingModalClose]').get().reverse().forEach(function(element) {
            $(element).click();
        });
    }

    function carregarPromptGaleriaPrompt(idPrompt) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaGaleriaPrompts"] = idPrompt;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_prompt_galeria_prompts_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                mensagem = decodeHtmlEntitiesPergunta(data["prompt"]);
                mensagem = getTextForTextarea(mensagem);

                window.top.document.getElementById('mensagem').value = mensagem;
                // Seleciona o campo no top document
                var campo = window.top.document.querySelector('#mensagem');

                // Cria um evento keydown para a tecla Shift (keyCode 16)
                var evento = new KeyboardEvent('keydown', {
                    key: 'Shift',
                    keyCode: 16,
                    which: 16,
                    bubbles: true
                });

                // Dispara o evento keydown no campo
                campo.dispatchEvent(evento);
                $(window.top.document).find('div[id^=divInfraSparklingModalClose]').get().reverse().forEach(function(element) {
                    $(element).click();
                    campo.focus();
                });
            }
        });
    }

    function infraTransportarItem(n) {
        carregarPromptGaleriaPrompt(n);
    }

    function tratarDigitacao(ev) {
        if (infraGetCodigoTecla(ev) == 13) {
            document.getElementById('frmGaleriaPrompts').submit();
        }
        return true;
    }
    <? if ($bolAcaoDesativar) { ?>

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

    <? if ($bolAcaoReativar) { ?>

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
    <? if ($bolAcaoExcluir) { ?>

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
    <? } ?>
</script>