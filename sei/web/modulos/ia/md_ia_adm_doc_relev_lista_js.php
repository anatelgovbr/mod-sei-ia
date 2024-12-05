<script>
    function inicializar() {
        $("#divMsg").hide();
        infraEfeitoTabelas(true);
        <? if($_POST["selAplicabilidade"]) { ?>
        retornaTiposDocumentosCadastrados();
        <?php } ?>
    }

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
        $("#divMsg").hide();
        if (confirm("Confirma exclusão do Documento Relevante \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdIaAdmDocRelevLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmMdIaAdmDocRelevLista').submit();
        }
    }
    function acaoExclusaoMultipla(){
        $("#divMsg").hide();
        if (document.getElementById('hdnInfraItensSelecionados').value==''){
            alert('Nenhum Documento Relevante selecionado.');
            return;
        }
        if (confirm("Confirma exclusão dos Documentos Relevantes selecionados?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmDocRelevLista').action='<?=$strLinkExcluir?>';
            document.getElementById('frmMdIaAdmDocRelevLista').submit();
        }
    }
    <?php } ?>
    <? if ($bolAcaoDesativar){ ?>
    function acaoDesativar(id,desc){
        $("#divMsg").hide();
        if (confirm("Confirma desativação do Documento Relevante \""+desc+"\"?")){
            document.getElementById('hdnInfraItemId').value=id;
            document.getElementById('frmMdIaAdmDocRelevLista').action='<?=$strLinkDesativar?>';
            document.getElementById('frmMdIaAdmDocRelevLista').submit();
        }
    }
    function acaoDesativacaoMultipla(){
        $("#divMsg").hide();
        if (document.getElementById('hdnInfraItensSelecionados').value==''){
            alert('Nenhum Documento Relevante selecionado.');
            return;
        }
        if (confirm("Confirma desativação dos Documentos Relevantes selecionados?")){
            document.getElementById('hdnInfraItemId').value='';
            document.getElementById('frmMdIaAdmDocRelevLista').action='<?=$strLinkDesativar?>';
            document.getElementById('frmMdIaAdmDocRelevLista').submit();
        }
    }
    <? } ?>
    <? if ($bolAcaoReativar){ ?>
    function acaoReativar(id,desc){
        $("#divMsg").hide();
        verificarDocumentoExistente(id, function(data) {
            if(data["result"] != "false") {
                event.preventDefault();
                if(data["result"] != "todos") {
                    var mensagem = "Não foi possível reativar o Documento Relevante selecionado, pois já existe um Documento Relevante com situação Ativa para o mesmo Tipo de Documento que abrange <strong>Todos os Tipos de Processos</strong>. <br> Caso ainda queira reativar o Documento Relevante selecionado para Processos Específicos é necessário Desativar o Documento Relevante com o mesmo Tipo de Documento que abrange Todos os Tipos de Processos.";
                } else {
                    var mensagem = "Não foi possível reativar o Documento Relevante <strong>Todos os Tipos de Processos</strong>, pois já existe um Documento Relevante com situação Ativa para o mesmo Tipo de Documento que especifica quais Tipos de Processos Específicos são relevantes. <br> Caso ainda queira reativar o Documento Relevante selecionado para Todos os Tipos de Processos é necessário Desativar o Documento Relevante com o mesmo Tipo de Documento cadastrados com os Tipos de Processos Específicos.";
                }
                rolar_para('#divMsg');
                $("#divMsg > div > label").html(mensagem);
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
            } else {
                if (confirm("Confirma reativação do Documento Relevante \""+desc+"\"?")){
                    document.getElementById('hdnInfraItemId').value=id;
                    document.getElementById('frmMdIaAdmDocRelevLista').action='<?=$strLinkReativar?>';
                    document.getElementById('frmMdIaAdmDocRelevLista').submit();
                }
            }
        });
    }
    function acaoReativacaoMultipla() {
        $("#divMsg").hide();
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Documento Relevante selecionado.');
            return;
        }
        verificarDocumentoExistente(document.getElementById('hdnInfraItensSelecionados').value, function(data) {
            if(data["result"] != "false") {
                event.preventDefault();
                if(data["result"] != "todos") {
                    var mensagem = "Não foi possível reativar o Documento Relevante <strong>"+data["result"]+"</strong>, pois já existe um Documento Relevante com situação Ativa para o mesmo Tipo de Documento que abrange <strong>Todos os Tipos de Processos</strong>. <br> Caso ainda queira reativar o Documento Relevante selecionado para Processos Específicos é necessário Desativar o Documento Relevante com o mesmo Tipo de Documento que abrange Todos os Tipos de Processos.";
                } else {
                    var mensagem = "Não foi possível reativar o Documento Relevante <strong>Todos os Tipos de Processos</strong>, pois já existe um Documento Relevante com situação Ativa para o mesmo Tipo de Documento que especifica quais Tipos de Processos Específicos são relevantes. <br> Caso ainda queira reativar o Documento Relevante selecionado para Todos os Tipos de Processos é necessário Desativar o Documento Relevante com o mesmo Tipo de Documento cadastrados com os Tipos de Processos Específicos.";
                }
               rolar_para('#divMsg');
                $("#divMsg > div > label").html(mensagem);
                $("#divMsg > div").addClass("alert-danger");
                $("#divMsg").show();
            } else {
                if (confirm("Confirma reativação dos Documentos Relevantes selecionados?")) {
                    document.getElementById('hdnInfraItemId').value = '';
                    document.getElementById('frmMdIaAdmDocRelevLista').action = '<?=$strLinkReativar?>';
                    document.getElementById('frmMdIaAdmDocRelevLista').submit();
                }
            }
        });
    }
    <? } ?>
    function retornaTiposDocumentosCadastrados() {
        $("#divMsg").hide();
        if($("#selAplicabilidade").val() != 0) {
            objAjaxIdDocumento = new infraAjaxMontarSelectDependente('selAplicabilidade','selTipoDocumento', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_doc_relev_tipo_documento_cadastrado_ajax'); ?>');
            document.getElementById('selTipoDocumento').innerHTML  = '';

            objAjaxIdDocumento.prepararExecucao = function(){
                return infraAjaxMontarPostPadraoSelect('null','', <?= $_POST['selTipoDocumento'] ?>) + '&aplicabilidade='+document.getElementById('selAplicabilidade').value;
            }
            objAjaxIdDocumento.executar();
        } else {
            objAjaxIdDocumento = new infraAjaxMontarSelect('selTipoDocumento','<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_doc_relev_combobox_tipo_documento_ajax'); ?>');
            objAjaxIdDocumento.prepararExecucao = function(){
                return infraAjaxMontarPostPadraoSelect('null','', <?= $_POST['selTipoDocumento'] ?>);
            }
            objAjaxIdDocumento.executar();
        }
    }
    function retornaTiposProcessosCadastrados(elemento) {
        $("#divMsg").hide();
        if($("#selTipoDocumento").val() != 0) {
            objAjaxIdProcesso = new infraAjaxMontarSelectDependente('selTipoDocumento', 'selTipoProcesso', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_doc_relev_tipo_procedimento_cadastrado_ajax'); ?>');
            document.getElementById('selTipoProcesso').innerHTML = '';

            objAjaxIdProcesso.prepararExecucao = function () {
                return infraAjaxMontarPostPadraoSelect('null', '', <?= $_POST['selTipoProcesso'] ?>) + '&tipoDocumento=' + document.getElementById('selTipoDocumento').value;
            }
            objAjaxIdProcesso.executar();

        } else {
            objAjaxIdProcesso = new infraAjaxMontarSelect('selTipoProcesso','<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_adm_doc_relev_combobox_tipo_processo_ajax'); ?>');
            objAjaxIdProcesso.prepararExecucao = function(){
                return infraAjaxMontarPostPadraoSelect('null','', <?= $_POST['selTipoProcesso'] ?>);
            }
            objAjaxIdProcesso.executar();
        }
    }
    function pesquisar() {
        $("#divMsg").hide();
        document.getElementById('frmMdIaAdmDocRelevLista').action = '<?= $strLinkPesquisar ?>';
        document.getElementById('frmMdIaAdmDocRelevLista').submit();
    }
    function verificarDocumentoExistente(id, callback) {
        $("#divMsg").hide();
        var objeto = new Object();
        objeto["id"] = id;
        $.ajax({
            url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_documento_relevante_validar_reativacao_ajax'); ?>',
            type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
            dataType: "json",//Tipo de dado que será enviado ao servidor
            data: objeto, // Enviando o JSON com o nome de itens
            async: false,
            success: function (data) {
                callback(data);
            },
            error: function (err) {
                callback("Ocorreu um erro ao verificar se o elemento já foi cadastrado.");
            }
        });
    }
    function rolar_para(elemento) {
        $("#divMsg > div").removeClass("alert-warning");
        $("#divMsg > div").removeClass("alert-danger");
        $("#divMsg > div").removeClass("alert-success");
        $("#divInfraAreaTelaD").animate({
            scrollTop: $(elemento).offset().top
        }, 300);
    }
</script>