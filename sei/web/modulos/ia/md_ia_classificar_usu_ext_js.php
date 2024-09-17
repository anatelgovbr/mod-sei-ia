<script type="text/javascript">
    function inicializar() {

    }

    function atualizarListaObjetivos(){

        var divsObjetivos = document.querySelectorAll("#todos-objetivos > div");
        if (document.getElementById("btn-checkbox").checked) {
            var arrIds = document.getElementById("arr-objetivos-forte-relacao").value.split(',');
            divsObjetivos.forEach(function(div) {
                if(!arrIds.includes(div.id)){
                    div.style.display = "none";
                }
            });
        } else {
            divsObjetivos.forEach(function(div) {
                div.style.display = "";
            });
        }
    }

    function fecharModal() {
        $(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();
    }

    function exibirObjetivos(){
        document.getElementById('step-1').classList.remove('d-none');
        document.getElementById('step-2').classList.add('d-none');
        document.querySelectorAll('.form-stepper-list').forEach(item => {
            item.classList.remove('form-stepper-active');
        });
        document.querySelector(`.form-stepper-list[step="1"]`).classList.add('form-stepper-active');
    }

    function mudarStep(step){

        document.getElementById('step-1').classList.add('d-none');
        document.getElementById('step-2').classList.add('d-none');
        document.getElementById('step-3').classList.add('d-none');

        document.querySelectorAll('.form-stepper-list').forEach(item => {
            item.classList.remove('form-stepper-active');
            item.classList.add('form-stepper-unfinished');
        });

        document.getElementById(`step-${step}`).classList.remove('d-none');
        document.querySelector(`.form-stepper-list[step="${step}"]`).classList.add('form-stepper-active');

        switch (step) {
            case 1:
                atualizarObjetivosAtivos();
                document.getElementById('btnNovaClassificacao').style.display = 'none';
                document.getElementById('btnProsseguir').style.display = 'none';
                break;
            case 2:
                document.getElementById('btnNovaClassificacao').style.display = '';
                document.getElementById('btnProsseguir').style.display = '';
                break;
            case 3:
                document.getElementById('btnNovaClassificacao').style.display = '';
                document.getElementById('btnProsseguir').style.display = 'none';
                break;
        }
    }

    function exibirMetas(idObjetivo){

        mudarStep(2);

        //consultar as metas para exibir na tela
        var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_ods_consultar_metas_usu_ext_ajax'); ?>';
        var params = {};
        params["idObjetivo"] = idObjetivo;
        params["SinForteRelacao"] = document.getElementById("btn-checkbox").checked;
        params["MetasMarcadas"] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
           url: url,
           type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
           dataType: "json",//Tipo de dado que será enviado ao servidor
           data: params, // Enviando o JSON com o nome de itens
           async: false,
           success: function (data) {
               $("#step-2").html(data);
               infraCriarCheckboxRadio("infraCheckbox","infraCheckboxDiv","infraCheckboxLabel","infraCheckbox","infraCheckboxInput");
           },
           error: function (err) {
               callback("Ocorreu um erro ao consultar as metas.");
           }
        });
    }
    function infraCriarCheckboxRadio(classInput,classDiv,classLabel,classRemocao,classAdicao){
        $("."+classInput).each(function (index) {
            var div = $('<div class="'+classDiv+'" ></div>');
            var isVisible = this.style.visibility !== 'hidden' && this.style.display !== 'none';
            if (isVisible) {
                $(this).wrap(div);
            }else{
                $(this).addClass("infraCheckboxRadioSemDiv");
            }

            var id = $(this).attr("id");
            var title = $(this).attr("title");

            var label = $('<label class="'+classLabel+'"></label>');

            if(id != undefined){
                $(label).attr("for",id);
            }
            if(title != undefined){
                $(label).attr("title",title);
            }

            $(this).removeClass(classRemocao);
            $(this).addClass(classAdicao);

            label.insertAfter($(this));
            label.on("click", function() {
                adicionarMetaLista(index);
            });
        });
    }

    function adicionarMetaLista(index){

        var todasAsTR = $('#tabela_ordenada tr').toArray();
        var linhaClicada = todasAsTR[index + 2]
        var idMeta = linhaClicada.querySelector(".infraCheckboxInput").value;
        var identificacao = linhaClicada.querySelector("td:nth-of-type(2)").textContent;
        var descricao = linhaClicada.querySelector("td:nth-of-type(3)").textContent;
        var arrSelecionados = document.getElementById('hdnInfraItensSelecionados').value;

        if(arrSelecionados.indexOf(idMeta) === -1){
            var div = document.getElementById('metas-selecionadas');
            var h5 = document.createElement("h5");
            var texto = identificacao + ' - ' + descricao;
            var textoNode = document.createTextNode(texto.substring(0,100)+'...');
            h5.appendChild(textoNode);
            h5.id = idMeta;
            h5.title = identificacao + ' - ' + descricao;
            h5.style = 'padding-bottom:10px';
            h5.onclick = function() {
                expandirTexto(idMeta);
            };

            // adicionar seta
            var imagem = document.createElement("img");
            imagem.setAttribute("src", "modulos/ia/imagens/sei_seta_direita.png");
            imagem.setAttribute("style", "width:25px; height:20px; margin-right: 5px;");
            h5.insertBefore(imagem, h5.firstChild);
            div.appendChild(h5);

        } else {
            $("#metas-selecionadas h5#"+idMeta).remove();
        }

        atualizarOrdemLista();
    }

    function atualizarOrdemLista() {
        var div = document.getElementById("metas-selecionadas");
        var h5s = div.querySelectorAll("h5");
        var h5Array = Array.from(h5s);

        h5Array.sort(function(a, b) {
            var idA = parseInt(a.id);
            var idB = parseInt(b.id);
            if (idA < idB) {
                return -1;
            }
            if (idA > idB) {
                return 1;
            }
            return 0;
        });

        div.innerHTML = "";

        h5Array.forEach(function(h5) {
            div.appendChild(h5);
        });
    }

    function expandirTexto(idMeta) {
        var todasAsTR = $('#metas-selecionadas h5').toArray();
        todasAsTR.forEach(function (h5){
            if(h5.id == idMeta){
                if(h5.textContent.length === 103){
                    h5.textContent = h5.title;
                    var imagem = document.createElement("img");
                    imagem.setAttribute("style", "width:20px; height:25px; margin-right: 5px;");
                    imagem.setAttribute("src", "modulos/ia/imagens/sei_seta_abaixo.png");
                    h5.insertBefore(imagem, h5.firstChild);
                } else {
                    h5.textContent = h5.title.substring(0,100)+'...';
                    var imagem = document.createElement("img");
                    imagem.setAttribute("style", "width:25px; height:20px; margin-right: 5px;");
                    imagem.setAttribute("src", "modulos/ia/imagens/sei_seta_direita.png");
                    h5.insertBefore(imagem, h5.firstChild);
                }
            }
        });
    }

    function atualizarObjetivosAtivos() {

        var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_ods_consultar_objetivos_selecionados_ajax'); ?>';
        var params = {};

        params['itensSelecionados'] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: params,
            async: false,
            success: function (data) {
                var div = document.getElementById('todos-objetivos');
                var elementos = div.querySelectorAll('.col-2');
                elementos.forEach(function(elemento) {
                    var id = elemento.id;
                    var imagem = elemento.querySelector('img');
                    imagem.classList.add('img-desfoque');
                    if(data.indexOf(parseInt(id)) !== -1){
                        var imagem = elemento.querySelector('img');
                        imagem.classList.remove('img-desfoque');
                    }
                });
            },
            error: function (err) {
                callback("Ocorreu um erro ao consultar Objetivos.");
            }
        });
    }

    function salvarMetasSessao() {

        var url = '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_ods_salvar_metas_selecionadas_sessao_ajax'); ?>';
        var params = {};

        params['itensSelecionados'] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: params,
            async: true,
            error: function (err) {
                callback("Ocorreu um erro ao salvar meta.");
            }
        });
    }

</script>