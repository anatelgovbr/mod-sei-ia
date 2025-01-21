<script type="text/javascript">
    function inicializar() {

        <? if ($bolOk) { ?>

        var sel = window.top.document.getElementsByTagName('iframe')[0].contentWindow.document.getElementById("selGrupoPromptsFav");
        if(!sel) {
            var sel = window.top.document.getElementsByTagName('iframe')[1].contentWindow.document.getElementById("selGrupoPromptsFav");
        }
        infraSelectAdicionarOption(sel, '<?= PaginaSEI::tratarHTML($objMdIaGrupoPromptsFavDTO->getStrNomeGrupo()) ?>', '<?= $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() ?>');
        infraSelectSelecionarItem(sel, '<?= $objMdIaGrupoPromptsFavDTO->getNumIdMdIaGrupoPromptsFav() ?>');
        self.setTimeout($(window.top.document).find('div[id^=divInfraSparklingModalClose]').last().click(), 200);

        <? } else { ?>

        if ('<?= $_GET['acao'] ?>' == 'md_ia_grupo_prompts_fav_cadastrar' || '<?= $_GET['acao'] ?>' == 'md_ia_grupo_prompts_fav_alterar') {
            document.getElementById('txtNomeGrupo').focus();
        } else if ('<?= $_GET['acao'] ?>' == 'md_ia_grupo_prompts_fav_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();
        <? } ?>
    }

    function validarCadastro(event) {
        if (infraTrim(document.getElementById('txtNomeGrupo').value) == '') {
            alert('Informe o Nome.');
            document.getElementById('txtNomeGrupo').focus();
            return false;
        }
        verificarExistenciaGrupoPromptFavorito(event, document.getElementById('txtNomeGrupo').value);
        return true;
    }

    function OnSubmitForm(event) {
        return validarCadastro(event);
    }

    function verificarExistenciaGrupoPromptFavorito(event, nomeGrupoPromptFavorito) {
        var dadosMensagem = {};
        dadosMensagem["nomeGrupoPromptFavorito"] = nomeGrupoPromptFavorito;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_consultar_grupo_prompt_favorito_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            async: false,
            data: dadosMensagem, // Enviando o JSON com o nome de itens
            success: function (data) {
                if(data["existeGrupoFavorito"] == true) {
                    event.preventDefault();
                    rolar_para('#divMsg');
                    $("#divMsg > div > label").html("Já existe um Grupo de Prompt Favorito com este nome.");
                    $("#divMsg > div").addClass("alert-danger");
                    $("#divMsg").show();
                    document.getElementById('txtNomeGrupo').focus();
                    return false;
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }
    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }
</script>