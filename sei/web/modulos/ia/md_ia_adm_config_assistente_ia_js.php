<script type="text/javascript">

    var objOperacao = {
        operacao: null,
        validado: false
    };

    function inicializar() {
        carregarComponenteUsuarios();
        validarWebService();
    }

    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        const posicao = $(elemento).offset().top - 100;
        $("#divInfraAreaTelaD").animate({
            scrollTop: posicao
        }, 500);
    }

    function OnSubmitForm(event) {
        return validarCadastro(event);
    }

    function validarCadastro(event) {
        $("#divMsg").hide();
        if (infraTrim(document.getElementById('selMetodoRequisicao').value) == '') {
            alert('Informe o LLM Ativo.');
            document.getElementById('selMetodoRequisicao').focus();
            return false;
        }
        const radioSelecionado = document.querySelector("input[name='rdnExibirFuncionalidade']:checked");

        if($("#hdnConectadoAPI").val() == 0 && radioSelecionado.value === "S") {
            validarWebService();
        }
        return true;
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
    function exibirFuncionalidade() {
        if (infraTrim(document.getElementById('selMetodoRequisicao').value) == '') {
            alert('Para Exibir a Funcionalidade é obrigatório a seleção de um LLM Ativo.');
            document.getElementById('selMetodoRequisicao').focus();
            return false;
        }

        const radioSelecionado = document.querySelector("input[name='rdnExibirFuncionalidade']:checked");

        if (radioSelecionado) {
            if(radioSelecionado.value === "S") {
                validarWebService();
            }
        }
    }
    function validarWebService() {
        $("#divMsg").hide();
        var params = {};
        params["idIntegracao"] = 2;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_busca_dados_integracao'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: params, // Enviando o JSON com o nome de itens
            async: false,
            success: function (data) {
                let params = {
                    urlServico: data["operacaoWsdl"],
                    tipoWs: 'REST',
                    definirServico: 'true',
                    tipoRequisicao: 'GET',
                    retorno: 'JSON',
                    funcionalidade: 2
                };
                buscarOperacoeWs(params);
            },
            error: function (err) {
                callback("Ocorreu um erro ao verificar se o elemento já foi cadastrado.");
            }
        });
    }

    function buscarOperacoeWs(parametros) {
        let path = "<?= $strLinkValidarWsdl ?>";
        $.ajax({
            type: "POST",
            url: path,
            dataType: 'xml',
            async: false,
            data: parametros,
            beforeSend: function () {
                //infraExibirAviso(false);
            },
            success: function (result) {
                montaOperacao(result);
            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {
                //infraAvisoCancelar();
            }
        });
    }

    function montaOperacao(result) {
        if ($(result).find('success').text() != 'true') {
            $("#hdnConectadoAPI").val(0);
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("Não foi possível conectar a API do SEI IA. Verifique os dados da integração no Mapeamento de Integrações.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            const radioParaSelecionar = document.querySelector("input[name='rdnExibirFuncionalidade'][value='N']");
            radioParaSelecionar.checked = true; // Define este botão como selecionado
        } else {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("Conectado a API do SEI IA com sucesso.");
            $("#divMsg > div").addClass("alert-success");
            $("#divMsg").show();
            $("#hdnConectadoAPI").val(1);
        }
    }


</script>