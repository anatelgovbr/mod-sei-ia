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

    function getTextForTextarea(sanitizedHtml) {
        // converte eventuais <br> em \n
        let text = sanitizedHtml.replace(/<br\s*\/?>/gi, '\n');
        // opcional: se quiser preservar tabs
        text = text.replace(/&nbsp;/g, '\t');

        // aí sim tira o resto das tags, mas mantendo \n intactos
        const tmp = document.createElement('div');
        tmp.innerHTML = text;
        return tmp.textContent; // contém as quebras de linha reais
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
                mensagem = decodeHtmlEntities(data["pergunta"]);
                mensagem = getTextForTextarea(mensagem);
                //mensagem = safe_tags(mensagem);
                $("#txaPrompt").val(mensagem);
                $("#frmNovoPromptFavorito").css("display", "block");
            }
        });
    }
</script>