<script>
    function atualizarTamanhoMaximo() {
        var chk = document.getElementById('chkSinPermitidaAvulso');
        var txt = document.getElementById('txtTamanhoMaximoPermitido');
        if (chk.checked) {
            txt.disabled = false;
            txt.classList.remove('infraTextDisabled');
        } else {
            txt.disabled = true;
            txt.value = '';
            txt.classList.add('infraTextDisabled');
        }
    }

    function inicializar() {
        if ('<?= PaginaSEI::GET('acao') ?>' === 'md_ia_adm_ext_permitida_cadastrar') {
            document.getElementById('txtExtensao').focus();
        } else if ('<?= PaginaSEI::GET('acao') ?>' === 'md_ia_adm_ext_permitida_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        document.getElementById('chkSinPermitidaAvulso').addEventListener('change', atualizarTamanhoMaximo);
        infraEfeitoTabelas(true);
        atualizarTamanhoMaximo();
    }

    function validarCadastro() {
        if (infraTrim(document.getElementById('txtTamanhoMaximoPermitido').value) == '' && document.getElementById('chkSinPermitidaAvulso').checked) {
            alert('Informe Tamanho máximo permitido.');
            document.getElementById('txtTamanhoMaximoPermitido').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }
</script>