<script type="text/javascript">
    function inicializar() {
        if ('<?= $_GET['acao'] ?>' == 'md_ia_grupo_prompts_fav_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }

    <? if ($bolAcaoExcluir) { ?>
    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclusão do Grupo de Prompts Favoritos \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmGrupoPrompsFavoritosLista').action = '<?= $strLinkExcluir ?>';
            document.getElementById('frmGrupoPrompsFavoritosLista').submit();
        }
    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Grupo de Prompts Favoritos selecionado.');
            return;
        }
        if (confirm("Confirma exclusão dos Grupos de Acompanhamento selecionados?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmGrupoPrompsFavoritosLista').action = '<?= $strLinkExcluir ?>';
            document.getElementById('frmGrupoPrompsFavoritosLista').submit();
        }
    }
    <? } ?>
</script>