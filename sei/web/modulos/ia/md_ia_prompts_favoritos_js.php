<script src="modulos/ia/lib/purify/purify.js"></script>
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


    function safe_tags(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }


    function decodeHtmlEntities(encodedString) {

        // 1) Decodifica entidades HTML
        const tmp = document.createElement('textarea');
        tmp.innerHTML = encodedString;
        let decoded = tmp.value;

        // 2) Escapa ONLY tags desconhecidas
        decoded = decoded.replace(
            /<\/?([a-zA-Z][\w-]*)\b([^>]*)>/g,
            (match, tagName) => {
                const el = document.createElement(tagName);
                if (el instanceof HTMLUnknownElement) {
                    return match
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }
                return match;
            }
        );

        // 3) Parseia o HTML ?protegido? e remove scripts/atributos XSS,
        //    mas agora PRESERVA o onclick
        const doc = new DOMParser().parseFromString(decoded, 'text/html');
        (function walk(node) {
            let child = node.firstChild;
            while (child) {
                const next = child.nextSibling;
                if (child.nodeType === Node.ELEMENT_NODE) {
                    if (['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED'].includes(child.tagName)) {
                        node.removeChild(child);
                    } else {
                        Array.from(child.attributes).forEach(attr => {
                            const name = attr.name.toLowerCase();
                            const value = attr.value;

                            // ? MANTÉM onclick
                            if (name === 'onclick') {
                                return;
                            }
                            // remove qualquer outro on*  
                            if (/^on/.test(name)) {
                                child.removeAttribute(attr.name);
                            }
                            // remove javascript:? em href/src
                            else if (/^javascript:/i.test(value)) {
                                child.removeAttribute(attr.name);
                            }
                        });
                        walk(child);
                    }
                }
                child = next;
            }
        })(doc.body);

        // 4) Sanitiza com DOMPurify, permitindo explicitamente onclick
        const sanitized = DOMPurify.sanitize(doc.body.innerHTML, {
            ADD_ATTR: ['onclick']
        });

        // 5) Conserta possíveis &amp;gt;
        var resposta = sanitized.replace(/&amp;gt;/g, '>');
        return resposta;
    }

    function cleanUpBreaks(html) {
        return html
            // 1) remove <br> antes ou depois de tags de bloco, incluindo <table>, <thead>, <tbody>, <tr>, <th>, <td>
            .replace(
                /(<\/?(?:h[1-6]|p|ul|ol|li|table|thead|tbody|tr|th|td)[^>]*>)[ \t\r\n]*(?:<br\s*\/?>[ \t\r\n]*)+/gi,
                '$1'
            )
            // 2) remove <br> sobrando no fim do documento
            .replace(/(?:<br\s*\/?>[ \t\r\n]*)+$/gi, '');
    }


    function getTextForTextarea(sanitizedHtml) {
        // converte eventuais <br> em \n
        let text = sanitizedHtml.replace(/<br\s*\/?>/gi, '\n');
        // opcional: se quiser preservar tabs
        //text = text.replace(/&nbsp;/g, '\t');

        // aí sim tira o resto das tags, mas mantendo \n intactos
        const tmp = document.createElement('div');
        tmp.innerHTML = text;
        return tmp.textContent; // contém as quebras de linha reais
    }

    function carregarPromptFavorito(idPromptFavorito) {
        var dadosMensagem = {};
        dadosMensagem["IdMdIaPromptsFavoritos"] = idPromptFavorito;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_prompt_favorito_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json", //Tipo de dado que será enviado ao servidor
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function(data) {
                mensagem = decodeHtmlEntities(data["prompt"]);
                mensagem = getTextForTextarea(mensagem);
                //mensagem = safe_tags(mensagem);
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