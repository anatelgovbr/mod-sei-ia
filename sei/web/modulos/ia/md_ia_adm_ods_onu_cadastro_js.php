<script type="text/javascript">

    function inicializar() {
        document.getElementById('btnCancelar').focus();
        infraEfeitoTabelas(true);
        iniciarAutoCompletarUnidadeSolicitante()
    }

    function validarCadastro() {
        if (infraTrim(document.getElementById('txtOrientacoesGerais').value) == '') {
            alert('Informe a Orientações Gerais.');
            document.getElementById('txtOrientacoesGerais').focus();
            return false;
        }
        if (infraTrim(document.getElementById('hdnIdUnidades').value) == '') {
            alert('É necessário selecionar no minímo uma Unidade para alertar pendência ou divergência.');
            document.getElementById('txtUnidadeAlerta').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }
    function rolar_para(elemento) {
        $("#divMsg > div").removeClass('alert-warning alert-danger alert-success');
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }
    function iniciarAutoCompletarUnidadeSolicitante() {
        objLupaUnidade = new infraLupaSelect('selUnidadeAlerta', 'hdnIdUnidades', '<?= $strUrlUnidadeAlerta ?>');

        objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidadeAlerta', '<?= $strLinkAjaxAutocompletarUnidade ?>');
        objAutoCompletarUnidade.limparCampo = true;
        objAutoCompletarUnidade.tamanhoMinimo = 3;

        objAutoCompletarUnidade.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidadeAlerta').value;
        };

        objAutoCompletarUnidade.processarResultado = function (id, descricao) {
            if (id != '') {
                objLupaUnidade.adicionar(id, descricao, document.getElementById('txtUnidadeAlerta'));
            }
        };
    }
    function consultarObjetivoOds(idObjetivo) {
        $("#hdnIdSelecaoObjetivo").val(idObjetivo);
        infraAbrirJanelaModal("<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_consultar_objetivo') ?>",
            1200,
            1000, false);
    }

    function bloquearPermitirCassificacaoUsuarioExterno() {
        $('input[name="rdnClassificacaoExterno"]').prop('disabled', ($('input[name="rdnExibirFuncionalidade"]:checked').val() == 'S') ? false : true);

        if($('input[name="rdnExibirFuncionalidade"]:checked').val() == 'N'){
            $('input[name="rdnClassificacaoExterno"][value="N"]').prop('checked', true);
        }
    }

    $('body').on('change', 'input[name="rdnExibirFuncionalidade"]', function(){
        bloquearPermitirCassificacaoUsuarioExterno();
    });

</script>