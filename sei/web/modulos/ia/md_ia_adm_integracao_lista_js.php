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
        if (confirm("Confirma desativa��o de Integra��o \"" + desc + "\"?")) {
            document.querySelector('#hdnInfraItemId').value = id;
            document.querySelector('#frmMdIaAdmIntegracaoLista').action = '<?=$strLinkDesativar?>';
            document.querySelector('#frmMdIaAdmIntegracaoLista').submit();
        }
    }
    function acaoDesativacaoMultipla(){
        if (document.getElementById('hdnInfraItensSelecionados').value==''){
            alert('Nenhuma Integra��o selecionada.');
            return;
        }
        if (confirm("Confirma desativa��o das Integra��es selecionadas?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmIntegracaoLista').action='<?=$strLinkDesativar?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoReativar){ ?>
    function acaoReativar(id, desc) {
        if (confirm("Confirma reativa��o de Integra��o \"" + desc + "\"?")) {
            document.querySelector('#hdnInfraItemId').value = id;
            document.querySelector('#frmMdIaAdmIntegracaoLista').action = '<?=$strLinkReativar?>';
            document.querySelector('#frmMdIaAdmIntegracaoLista').submit();
        }
    }
    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Integra��o selecionada.');
            return;
        }
        if (confirm("Confirma reativa��o das Integra��es selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdIaAdmIntegracaoLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclus�o da Integra��o \"" + desc + "\"?")) {
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
        if (confirm("Confirma exclus�o das Integra��es selecionados?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmIntegracaoLista').action='<?=$strLinkExcluir?>';
            document.getElementById('frmMdIaAdmIntegracaoLista').submit();
        }
    }
    <? } ?>

    function acionarNovo() {
        <?php if ($strBloquearNovoCadastro): ?>
        alert('Todas as integra��es do SEI IA j� foram mapeadas.');
        <?php else: ?>
        location.href = "<?= $btnLinkNovo ?>";
        <?php endif; ?>
    }

</script>