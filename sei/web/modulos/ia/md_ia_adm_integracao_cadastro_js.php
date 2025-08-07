<script type="text/javascript">
    var isTelaAcaoAlterar = document.querySelector('#hdnTipoAcao').value == 'alterar' ? true : false;
    var isTpIntegOrigem = document.querySelector('[name="rdnTpIntegracao"]').value;
    var msgDef = '<?= MdIaMensagemINT::getMensagem(MdIaMensagemINT::$MSG_IA_01) ?>';

    var objOperacao = {
        operacao: null,
        validado: false
    };

    /* *****************************
		* Codigo Jquery
		* ******************************/
    $('[name="rdnTpIntegracao"]').change(function () {
        if ($(this).val() == "<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SEM_AUTENTICACAO ?>") {
            $('.dvConteudo').hide();
            $('.selREST,.selSOAP').hide();
        } else {
            if ($(this).val() == "<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_REST ?>") {
                $('.dvConteudo').show();
                $('.selREST').show();
                $('.selSOAP').hide();

                if (isTelaAcaoAlterar && isTpIntegOrigem == "<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SEM_AUTENTICACAO ?>") validarMapear();
            } else {
                alert('<?= MdIaMensagemINT::$MSG_IA_02 ?>');
                return false;
                $('.dvConteudo').hide();
                $('.selREST').hide();
                $('.selSOAP').hide();
            }
        }
    });

    $('#selMetodoRequisicao').change(function () {
        if ($(this).val() != <?= MdIaAdmIntegracaoRN::$REQUISICAO_POST ?>) {
            alert('O Método da Requisição "' + $('option:selected', this).text() + '" ainda não está disponível nesta versão.');
            $(this).val(<?= MdIaAdmIntegracaoRN::$REQUISICAO_POST ?>);
            return false;
        }
    });

    $('#selFormato').change(function () {
        if ($(this).val() != <?= MdIaAdmIntegracaoRN::$FORMATO_JSON ?>) {
            alert('O Formato do Retorno da Operação "' + $('option:selected', this).text() + '" ainda não está disponível nesta versão.');
            $(this).val(<?= MdIaAdmIntegracaoRN::$FORMATO_JSON ?>);
            return false;
        }
    });

    /* *****************************
    * Codigo Javascript
    * ******************************/
    function inicializar() {
        infraEfeitoTabelas(true);
        alterarFuncionalidade();

        switch (document.querySelector('#hdnTipoAcao').value) {
            case 'cadastrar':
                document.querySelector('#txtNome').focus();
                break;
            case 'consultar':
                infraDesabilitarCamposAreaDados();
                $('.btnFormulario').hide();
                break;
        }

        // verifica se tem que exibir o input Conteúdo de Autenticação:
        if (document.querySelector('#selMetodoAutenticacao').value == <?= MdIaAdmIntegracaoRN::$AUT_BODY_TOKEN ?>) {
            $('#rowTokenAut').show();
        }
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function validarCadastro() {
        let tpInteg = document.querySelector('input[name="rdnTpIntegracao"]:checked');
        let func = document.querySelector('#selFuncionalidade').value;

        if (infraTrim(document.querySelector('#selFuncionalidade').value) == '') {
            alert(setMensagemPersonalizada(msgDef, ['Funcionalidade']));
            document.querySelector('#selFuncionalidade').focus();
            return false;
        }

        if (infraTrim(document.querySelector('#txtNome').value) == '') {
            alert(setMensagemPersonalizada(msgDef, ['Nome']));
            document.querySelector('#txtNome').focus();
            return false;
        }
        if (infraTrim(document.querySelector('#txtUrlServico').value) == '') {
            alert(setMensagemPersonalizada(msgDef, ['URL do Endpoint de Autenticação']));
            document.querySelector('#txtUrlServico').focus();
            return false;
        }
        if (tpInteg == null) {
            alert(setMensagemPersonalizada(msgDef, ['Tipo de Integração']));
            return false;
        } else if (tpInteg.value == '<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_REST ?>') {
            if (infraTrim(document.querySelector('#txtUrlServico').value) == '') {
                alert(setMensagemPersonalizada(msgDef, ['URL WebService']));
                document.querySelector('#txtUrlServico').focus();
                return false;
            }

            // preenchimento das combos entrada/saida que sao obrigatorios
            let nmCampo = `.obrigatorio${func}`;
            let valid = true;
            const itens = document.querySelectorAll(nmCampo);

            if (itens.length > 0) {
                itens.forEach((v, i) => {
                    if (v.value == '') {
                        v.focus();
                        alert('Campo obrigatório não preenchido.');
                        valid = false;
                        return;
                    }
                });

                if (!valid) return false;
            }
        } else if (tpInteg.value == '<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SOAP ?>') {
            alert('<?= MdIaMensagemINT::$MSG_IA_02 ?>');
            return false;
        }

        return true;
    }

    function validarWebService() {
        let tpInteg = document.querySelectorAll('input[name="rdnTpIntegracao"]:checked');
        let params = {
            urlServico: document.querySelector('#txtUrlServico').value,
            tipoWs: '',
            definirServico: '',
            versaoSoap: '',
            tipoRequisicao: '',
            retorno: '',
            funcionalidade: $("#selFuncionalidade").val()
        };

        if (tpInteg[0].value == "<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_REST ?>") {
            params.tipoWs = 'REST';
            params.tipoRequisicao = 'GET';
            params.retorno = 'JSON';
            params.definirServico = true;
        } else {
            params.tipoWs = 'SOAP';
            params.versaoSoap = document.querySelector('#selVersaoSOAP').value;
            params.retorno = 'XML';
        }
        buscarOperacoeWs(params);
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
        //valida se a operacao informada existe no arquivo .Json
        $.each($(result).find('operacao'), function (key, value) {
            objOperacao.validado = true;
        });

        if (!objOperacao.validado) return objOperacao.validado;

        if ($(result).find('success').text() != 'true') {
            if ($(result).find('erros').length)
                alert($(result).find('erro').attr('descricao'));
            else
                alert($(result).find('msg').text());
        }
    }

    function validarMapear() {
        $("#divMsg").hide();
        if (infraTrim(document.querySelector('#txtUrlServico').value) == '') {
            alert(setMensagemPersonalizada(msgDef, ['URL do Endpoint da Operação']));
            document.querySelector('#txtUrlServico').focus();
            return false;
        }
        var ultimoCaractere = $("#txtUrlServico").val().slice(-1);
        if(ultimoCaractere == ":" || ultimoCaractere == "/") {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("URL do Endpoint de Autenticação inválido.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            document.querySelector('#txtUrlServico').focus();
            return false;
        }
        let urlServico = document.querySelector('#txtUrlServico').value;
        let arrUrlServico = urlServico.split('/');

        objOperacao.operacao = arrUrlServico.pop();
        objOperacao.validado = false;

        //executa consulta no arquivo .json, definido no campo: URL Definição do Serviço
        validarWebService();

        if (objOperacao.validado === false) {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("Não foi possível conectar a API do SEI IA.");
            $("#divMsg > div").addClass("alert-danger");
            $("#divMsg").show();
            return false;
        } else {
            rolar_para('#divMsg');
            $("#divMsg > div > label").html("Conectado a API do SEI IA com sucesso.");
            $("#divMsg > div").addClass("alert-success");
            $("#divMsg").show();
        }
    }
    function alterarFuncionalidade() {
        if ($("#selFuncionalidade").val() == <?= MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL ?> || $("#selFuncionalidade").val() == <?= MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTERFACE_LLM ?>) {
            $("input[name='rdnTpIntegracao']").each(function() {
                if(this.value != '<?= MdIaAdmIntegracaoRN::$SEI_IA_TP_INTEGRACAO ?>') {
                    this.disabled = true;
                } else {
                    this.disabled = false;
                }
            });
            $("#selMetodoRequisicao > option").each(function() {
                if(this.value != <?= MdIaAdmIntegracaoRN::$SEI_IA_REQUISICAO ?>) {
                    this.disabled = true;
                } else {
                    this.disabled = false;
                }
            });
            $("#selMetodoAutenticacao > option").each(function() {
                if(this.value != <?= MdIaAdmIntegracaoRN::$SEI_IA_AUT ?>) {
                    this.disabled = true;
                } else {
                    this.disabled = false;
                }
            });
            $("#selFormato > option").each(function() {
                if(this.value != <?= MdIaAdmIntegracaoRN::$SEI_IA_FORMATO ?>) {
                    this.disabled = true;
                } else {
                    this.disabled = false;
                }
            });
        }
    }
    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }

    function setMensagemPersonalizada(msg, arrParams){
        var padraoSetMsg = '@VALOR$NUMERO$@';

        for(var i = 0; i < arrParams.length; i++){
            var numero = i + 1;
            var campoNome = padraoSetMsg.replace('$NUMERO$', numero);
            var campoSubst = $.trim(arrParams[i]);
            msg = msg.replace(campoNome, campoSubst);
        }

        return msg;
    }

    function emitirAlerta(){
        var msg = 'Atenção: Este campo é crítico para o funcionamento do Assistente de IA. É mantido exclusivamente pelo script de atualização do Módulo.\n\nNunca altere manualmente esse campo!'
        alert(msg);
    }
</script>