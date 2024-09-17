<script type="text/javascript">

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_ia_adm_integracao_selecionar') {
            infraReceberSelecao();
            document.querySelector('#btnFecharSelecao').focus();
        } else {
            document.querySelector('#btnFechar').focus();
        }
        infraEfeitoTabelas(true);
    }

    <? if ($bolAcaoDesativar){ ?>
    function acaoDesativar(id, desc) {
        if (confirm("Confirma desativação de Integração \"" + desc + "\"?")) {
            document.querySelector('#hdnInfraItemId').value = id;
            document.querySelector('#frmMdIaAdmIntegracaoLista').action = '<?=$strLinkDesativar?>';
            document.querySelector('#frmMdIaAdmIntegracaoLista').submit();
        }
    }
    function acaoDesativacaoMultipla(){
        if (document.getElementById('hdnInfraItensSelecionados').value==''){
            alert('Nenhuma Integração selecionada.');
            return;
        }
        if (confirm("Confirma desativação das Integrações selecionadas?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmIntegracaoLista').action='<?=$strLinkDesativar?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoReativar){ ?>
    function acaoReativar(id, desc) {
        if (confirm("Confirma reativação de Integração \"" + desc + "\"?")) {
            document.querySelector('#hdnInfraItemId').value = id;
            document.querySelector('#frmMdIaAdmIntegracaoLista').action = '<?=$strLinkReativar?>';
            document.querySelector('#frmMdIaAdmIntegracaoLista').submit();
        }
    }
    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Integração selecionada.');
            return;
        }
        if (confirm("Confirma reativação das Integrações selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdIaAdmIntegracaoLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclusão da Integração \"" + desc + "\"?")) {
            document.querySelector('#hdnInfraItemId').value = id;
            document.querySelector('#frmMdIaAdmIntegracaoLista').action = '<?=$strLinkExcluir?>';
            document.querySelector('#frmMdIaAdmIntegracaoLista').submit();
        }
    }
    function acaoExclusaoMultipla(){
        if (document.getElementById('hdnInfraItensSelecionados').value==''){
            alert('Nenhum Documento Relevante selecionado.');
            return;
        }
        if (confirm("Confirma exclusão das Integrações selecionados?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmIntegracaoLista').action='<?=$strLinkExcluir?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    function acionarNovo() {
        <?php if ($strBloquearNovoCadastro): ?>
        alert('Todas as integrações do SEI IA já foram mapeadas.');
        <?php else: ?>
        location.href = "<?= $btnLinkNovo ?>";
        <?php endif; ?>
    }

</script>