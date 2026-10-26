<script type="text/javascript">
    function inicializar() {
        if ('<?= PaginaSEI::GET('acao') ?>' === 'md_ia_adm_ext_permitida_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas(true);
    }

    function acaoDesativar(id, desc) {
        if (confirm('Confirma desativação da Extensão de Arquivo Permitida \"' + desc + '\"?')) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkDesativar ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }

    function acaoDesativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Extensão Permitidas selecionado.');
            return;
        }
        if (confirm('Confirma desativação dos Extensões Permitidas selecionados?')) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkDesativar ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }

    function acaoReativar(id, desc) {
        if (confirm('Confirma reativação da Extensão de Arquivo Permitida \"' + desc + '\"?')) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkReativar ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }

    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Extensão Permitidas selecionado.');
            return;
        }
        if (confirm('Confirma reativação dos Extensões Permitidas selecionados?')) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkReativar ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }

    function acaoExcluir(id, desc) {
        if (confirm('Confirma exclusão da Extensão de Arquivo Permitida \"' + desc + '\"?')) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkExcluir ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Extensão Permitidas selecionado.');
            return;
        }
        if (confirm('Confirma exclusão dos Extensões Permitidas selecionados?')) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdIaAdmExtPermitidaLista').action = '<?= $strLinkExcluir ?? false ?>';
            document.getElementById('frmMdIaAdmExtPermitidaLista').submit();
        }
    }
</script>