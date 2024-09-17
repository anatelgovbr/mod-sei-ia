<script type="text/javascript">

    function inicializar() {
        carregarComponenteUsuarios();
    }

    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }

    function OnSubmitForm(event) {
        return validarCadastro(event);
    }

    function validarCadastro(event) {
        $("#divMsg").hide();
    }

    function carregarComponenteUsuarios() {
        objLupaUsuario = new infraLupaSelect('selUsuarioTeste', 'hdnIdUsuarios', '<?= $strLinkUsuariosSelecao ?>');

        objAutoCompletarUsuario = new infraAjaxAutoCompletar('hdnIdUsuario', 'txtUsuario', '<?= $strLinkAjaxUsuario ?>');
        objAutoCompletarUsuario.limparCampo = true;
        objAutoCompletarUsuario.tamanhoMinimo = 3;

        objAutoCompletarUsuario.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUsuario').value;
        };

        objAutoCompletarUsuario.processarResultado = function (id, descricao) {
            if (id != '') {
                objLupaUsuario.adicionar(id, descricao, document.getElementById('txtUsuario'));
            }
        };
    }

</script>