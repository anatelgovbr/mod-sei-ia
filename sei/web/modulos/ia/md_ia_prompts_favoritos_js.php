<script type="text/javascript">
    function inicializar() {

        //infraOcultarMenuSistemaEsquema();

        if ('<?= $_GET['acao'] ?>' == 'md_ia_prompts_favoritos_selecionar') {
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

    function carregarPromptFavorito(idPromptFavorito) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaInteracaoChat"] = idPromptFavorito;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_mensagem_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function (data) {
                window.top.document.getElementById('mensagem').value = data["pergunta"];
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
        carregarPromptFavorito(n);
    }

    function tratarDigitacao(ev) {
        if (infraGetCodigoTecla(ev) == 13) {
            document.getElementById('frmPromptsFavoritos').submit();
        }
        return true;
    }

    <? if ($bolAcaoExcluir) { ?>
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
    <? } ?>
</script>