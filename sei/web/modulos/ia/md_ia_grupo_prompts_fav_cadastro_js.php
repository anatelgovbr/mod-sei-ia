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

    function validarCadastroRI0013() {
        if (infraTrim(document.getElementById('txtNomeGrupo').value) == '') {
            alert('Informe o Nome.');
            document.getElementById('txtNomeGrupo').focus();
            return false;
        }
        return true;
    }

    function OnSubmitForm() {
        return validarCadastroRI0013();
    }
</script>