<script type="text/javascript">
    function inicializar() {
        if ('<?= $_GET['acao'] ?>' == 'md_ia_adm_integracao_selecionar') {
            infraReceberSelecao();
            document.querySelector('#btnFecharSelecao').focus();
        } else {
            document.querySelector('#btnFechar').focus();
        }
        infraEfeitoTabelas(true);
    }

    function acionarNovo() {
        <?php if ($strBloquearNovoCadastro): ?>
            alert('Todas as integrações do SEI IA já foram mapeadas.');
        <?php else: ?>
            location.href = "<?= $btnLinkNovo ?>";
        <?php endif; ?>
    }
</script>