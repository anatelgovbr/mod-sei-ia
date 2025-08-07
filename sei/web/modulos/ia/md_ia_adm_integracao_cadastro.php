<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 ** 14/09/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    // Links para consulta Ajax
    $strLinkValidarWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_ia_integracao_busca_operacao_ajax');

    // Instancia classes RN e DTO
    $objMdIaAdmIntegracaoDTO = new MdIaAdmIntegracaoDTO();

    $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();

    // Variaveis globais
    $strDesabilitar = '';
    $strTipoAcao = '';
    $isRest = false;
    $arrDados = [];
    $arrConfig = ['hab_soap' => false, 'hab_rest' => false];
    $tpFuncionalidade = null;
    $vlrSelOperacao = '';
    $arrFuncionalidadesCadastradas = null;

    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_ia_adm_integracao_cadastrar':
            $strTipoAcao = 'cadastrar';
            $strTitulo = 'Novo Mapeamento de Integração';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdIaAdmIntegracao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegracao(null);
            $objMdIaAdmIntegracaoDTO->setStrNome($_POST['txtNome']);
            $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegFuncion($_POST['selFuncionalidade']);
            $objMdIaAdmIntegracaoDTO->setStrTipoIntegracao($_POST['rdnTpIntegracao']);
            $objMdIaAdmIntegracaoDTO->setNumMetodoAutenticacao($_POST['selMetodoAutenticacao']);
            $objMdIaAdmIntegracaoDTO->setNumMetodoRequisicao($_POST['selMetodoRequisicao']);
            $objMdIaAdmIntegracaoDTO->setNumFormatoResposta($_POST['selFormato']);
            $objMdIaAdmIntegracaoDTO->setStrVersaoSoap($_POST['selVersaoSoap']);
            $objMdIaAdmIntegracaoDTO->setStrUrlWsdl($_POST['txtUrlDefServico']);
            $objMdIaAdmIntegracaoDTO->setStrOperacaoWsdl($_POST['txtUrlServico']);
            $objMdIaAdmIntegracaoDTO->setStrSinAtivo('S');

            $strItensSelMdIaIntegFuncionalid = MdIaAdmIntegracaoINT::montarSelectFuncionalidade('null', '&nbsp;', '');

            if (isset($_POST['sbmCadastrarMdIaAdmIntegracao'])) {
                try {
                    $idFunc = $_POST['selFuncionalidade'];
                    $vlrToken = $_POST['txtTokenAut' . $idFunc];
                    $objMdIaAdmIntegracaoDTO->setStrTokenAutenticacao(empty($vlrToken) ? null : MdIaAdmIntegracaoINT::gerenciaDadosRestritos($vlrToken));

                    $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->cadastrar($objMdIaAdmIntegracaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Integração "' . $objMdIaAdmIntegracaoDTO->getStrNome() . '" cadastrada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_ia_adm_integracao=' . $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegracao() . PaginaSEI::getInstance()->montarAncora($objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegracao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_adm_integracao_alterar':
            $strTipoAcao = 'alterar';
            $strTitulo = 'Alterar Mapeamento de Integração';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmIntegracao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_ia_adm_integracao'])) {
                $idIntegracao = $_GET['id_md_ia_adm_integracao'];
            } else {
                $idIntegracao = $_POST["hdnIdMdIaAdmIntegracao"];
            }

            $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegracao($idIntegracao);

            $objMdIaAdmIntegracaoDTO->retStrNome();
            $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
            $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegFuncion();
            $objMdIaAdmIntegracaoDTO->retStrTipoIntegracao();
            $objMdIaAdmIntegracaoDTO->retNumFormatoResposta();
            $objMdIaAdmIntegracaoDTO->retNumMetodoRequisicao();
            $objMdIaAdmIntegracaoDTO->retNumMetodoAutenticacao();
            $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegracao();

            $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
            $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);

            $strItensSelMdIaIntegFuncionalid = MdIaAdmIntegracaoINT::montarSelectNome('null', '&nbsp;', $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion());

            if ($objMdIaAdmIntegracaoDTO == null) {
                throw new InfraException("Registro não encontrado.");
            }

            if (isset($_GET['id_md_ia_adm_integracao'])) {
                // habilta ou nao dados relacionados ao SOAP ou REST
                if ($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == 'SO') $arrConfig['hab_soap'] = true;
                if ($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == 'RE') $isRest = $arrConfig['hab_rest'] = true;

                $tpFuncionalidade = $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion();
            } else {
                if ($objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion() == MdIaAdmIntegFuncionRN::$ID_FUNCIONALIDADE_INTELIGENCIA_ARTIFICIAL) {
                    $objMdIaAdmIntegracaoDTO->setStrOperacaoWsdl($_POST['txtUrlServico']);
                }
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegracao())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';


            if ($tpFuncionalidade == "1" || $tpFuncionalidade == "2") {
                $disabledSeiIa = "disabled";
            }

            if (isset($_POST['sbmAlterarMdIaAdmIntegracao'])) {
                try {
                    $idFunc = $_POST['selFuncionalidade'];
                    $vlrToken = $_POST['txtTokenAut' . $idFunc] == MdIaAdmIntegracaoRN::$INFO_RESTRITO
                        ? $_POST['hdnTokenAut' . $idFunc]
                        : $_POST['txtTokenAut' . $idFunc];

                    $objMdIaAdmIntegracaoDTO->setStrTokenAutenticacao(empty($vlrToken) ? null : MdIaAdmIntegracaoINT::gerenciaDadosRestritos($vlrToken));
                    $objMdIaAdmIntegracaoDTO->setStrOperacaoWsdl($_POST['txtUrlServico']);
                    $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
                    $objMdIaAdmIntegracaoRN->alterar($objMdIaAdmIntegracaoDTO);
                    (new MdIaAdmUrlIntegracaoINT())->atualizarCadastroUrls($_POST);
                    PaginaSEI::getInstance()->adicionarMensagem('Integração "' . $objMdIaAdmIntegracaoDTO->getStrNome() . '" alterada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegracao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            $strCadastroUrls = (new MdIaAdmIntegracaoINT())->recuperarGridUrls($_GET['id_md_ia_adm_integracao']);
            break;

        case 'md_ia_adm_integracao_consultar':
            $strTipoAcao = 'consultar';
            $strTitulo = 'Consultar Mapeamento de Integração';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_md_ia_adm_integracao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
            $objMdIaAdmIntegracaoDTO->setNumIdMdIaAdmIntegracao($_GET['id_md_ia_adm_integracao']);
            $objMdIaAdmIntegracaoDTO->setBolExclusaoLogica(false);
            $objMdIaAdmIntegracaoDTO->retStrNome();
            $objMdIaAdmIntegracaoDTO->retStrOperacaoWsdl();
            $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegFuncion();
            $objMdIaAdmIntegracaoDTO->retStrTipoIntegracao();
            $objMdIaAdmIntegracaoDTO->retNumFormatoResposta();
            $objMdIaAdmIntegracaoDTO->retNumMetodoRequisicao();
            $objMdIaAdmIntegracaoDTO->retNumMetodoAutenticacao();
            $objMdIaAdmIntegracaoDTO->retNumIdMdIaAdmIntegracao();
            $objMdIaAdmIntegracaoRN = new MdIaAdmIntegracaoRN();
            $objMdIaAdmIntegracaoDTO = $objMdIaAdmIntegracaoRN->consultar($objMdIaAdmIntegracaoDTO);

            $strItensSelMdIaIntegFuncionalid = MdIaAdmIntegracaoINT::montarSelectNome('null', '&nbsp;', $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion());

            // habilta ou nao dados relacionados ao SOAP ou REST
            if ($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == 'SO') $arrConfig['hab_soap'] = true;
            if ($objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == 'RE') $isRest = $arrConfig['hab_rest'] = true;

            $tpFuncionalidade = $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegFuncion();

            if ($objMdIaAdmIntegracaoDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
//require 'md_ia_geral_css.php';

PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdIaAdmIntegracaoCadastro" method="post" onsubmit="return OnSubmitForm();"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    //PaginaSEI::getInstance()->montarAreaValidacao();
    PaginaSEI::getInstance()->abrirAreaDados();
    ?>
    <div id="divMsg">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-12 col-md-10">
            <label id="lblFuncionalidade" for="Funcionalidade" class="infraLabelObrigatorio">Funcionalidade:</label>
            <select id="selFuncionalidade" name="selFuncionalidade" class="infraSelect form-control"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" onChange="alterarFuncionalidade()" <?= $disabledSeiIa ?>>
                <?= $strItensSelMdIaIntegFuncionalid ?>
            </select>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-12 col-md-10">
            <label id="lblNome" for="txtNome" class="infraLabelObrigatorio">Nome:</label>
            <input type="text" id="txtNome" name="txtNome" class="infraText form-control"
                value="<?= PaginaSEI::tratarHTML($objMdIaAdmIntegracaoDTO->getStrNome()); ?>"
                onkeypress="return infraMascaraTexto(this,event,100);" maxlength="100"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $disabledSeiIa ?> />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-12 col-lg-12 mb-2">
            <label id="lblTipoIntegracao" class="infraLabelObrigatorio">Tipo de Integração:</label>
            <div id="divRadiosTpIntegracao">
                <div class="form-check-inline">
                    <div class="infraRadioDiv">
                        <input type="radio" name="rdnTpIntegracao" id="rdnTpSemIntegracao"
                            value="<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SEM_AUTENTICACAO ?>"
                            class="infraRadioInput"
                            <?= $objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SEM_AUTENTICACAO ? 'checked' : '' ?> <?= $disabledSeiIa ?>>
                        <label class="infraRadioLabel" for="rdnTpSemIntegracao"></label>
                    </div>
                    <label id="lblSemIntegracao" name="lblSemIntegracao" for="rdnTpSemIntegracao"
                        class="infraLabelOpcional infraLabelRadio">Sem Integração</label>
                </div>

                <div class="form-check-inline">
                    <div class="infraRadioDiv">
                        <input type="radio" name="rdnTpIntegracao" id="rdnTpIntegracaoSoap"
                            value="<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SOAP ?>" class="infraRadioInput"
                            <?= $objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == MdIaAdmIntegracaoRN::$TP_INTEGRACAO_SOAP ? 'checked' : '' ?> <?= $disabledSeiIa ?>>
                        <label class="infraRadioLabel" for="rdnTpIntegracaoSoap"></label>
                    </div>
                    <label id="lblIntegracaoSoap" name="lblIntegracaoSoap" for="rdnTpIntegracaoSoap"
                        class="infraLabelOpcional infraLabelRadio">SOAP</label>
                </div>

                <div class="form-check-inline">
                    <div class="infraRadioDiv">
                        <input type="radio" name="rdnTpIntegracao" id="rdnTpIntegracaoRest"
                            value="<?= MdIaAdmIntegracaoRN::$TP_INTEGRACAO_REST ?>" class="infraRadioInput"
                            <?= $objMdIaAdmIntegracaoDTO->getStrTipoIntegracao() == MdIaAdmIntegracaoRN::$TP_INTEGRACAO_REST ? 'checked' : '' ?> <?= $disabledSeiIa ?>>
                        <label class="infraRadioLabel" for="rdnTpIntegracaoRest"></label>
                    </div>
                    <label id="lblIntegracaoRest" name="lblIntegracaoRest" for="rdnTpIntegracaoRest"
                        class="infraLabelOpcional infraLabelRadio">REST</label>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-10 col-lg-3 mb-2 selSOAP" <?= $arrConfig['hab_soap'] == true ? '' : 'style="display: none;"' ?>>
            <label id="lblVersaoSOAP" class="infraLabelObrigatorio">Versão SOAP:</label>
            <select id="selVersaoSOAP" name="selVersaoSOAP" class="infraSelect form-control"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <option value="">Selecione</option>
                <option value="1.2">1.2</option>
                <option value="1.1">1.1</option>
            </select>
        </div>

        <div class="col-sm-12 col-md-10 col-lg-3 mb-2 selREST" <?= $arrConfig['hab_rest'] ? '' : 'style="display: none;"' ?>>
            <label id="lblMetodoRequisicao" class="infraLabelObrigatorio">Método da Requisição:</label>
            <select id="selMetodoRequisicao" name="selMetodoRequisicao" class="infraSelect form-control"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $disabledSeiIa ?>>
                <?= MdIaAdmIntegracaoINT::montarSelectMetodoRequisicao(PaginaSEI::tratarHTML($objMdIaAdmIntegracaoDTO->getNumMetodoRequisicao())) ?>
            </select>
        </div>

        <div class="col-sm-12 col-md-10 col-lg-4 mb-2 selREST" <?= $arrConfig['hab_rest'] ? '' : 'style="display: none;"' ?>>
            <label id="lblMetodoAutenticacao" class="infraLabelObrigatorio">Método de Autenticação:</label>
            <select id="selMetodoAutenticacao" name="selMetodoAutenticacao" class="infraSelect form-control"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $disabledSeiIa ?>>
                <?= MdIaAdmIntegracaoINT::montarSelectMetodoAutenticacao(PaginaSEI::tratarHTML($objMdIaAdmIntegracaoDTO->getNumMetodoAutenticacao())) ?>
            </select>
        </div>

        <div class="col-sm-12 col-md-10 col-lg-3 selREST" <?= $arrConfig['hab_rest'] ? '' : 'style="display: none;"' ?>>
            <label id="lblFormato" class="infraLabelObrigatorio">Formato do Retorno da Operação:</label>
            <select id="selFormato" name="selFormato" class="infraSelect form-control"
                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= $disabledSeiIa ?>>
                <?= MdIaAdmIntegracaoINT::montarSelectFormato(PaginaSEI::tratarHTML($objMdIaAdmIntegracaoDTO->getNumFormatoResposta())) ?>
            </select>
        </div>

    </div>

    <div class="dvConteudo"
        style="width:100%; <?= ($arrConfig['hab_soap'] || $arrConfig['hab_rest']) ? '' : 'display: none' ?>">
        <div class="row mb-2">
            <div class="col-sm-12 col-md-10">
                <label id="lblUrlServico" for="txtUrlServico" class="infraLabelObrigatorio">URL do Endpoint de Autenticação:</label>
                <img id="imgDefServico" align="top" alt="Ícone de Ajuda"
                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" class="infraImg"
                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Informe a URL com o domínio do Servidor de Soluções de IA do ambiente correspondente, conforme manual indicado no README do do repositório do Servidor de Soluções de IA.

Deve utilizar o protocolo HTTPS na URL e não pode finalizar a URL com barra (/) nem informação de porta. Informar apenas o hostname do Servidor de Soluções de IA instalado no órgão para o ambiente correspondente.

Exemplo de URL valida: https://hostname_docker_solucao_sei_ia_do_ambiente', 'Ajuda') ?> />
                <div class="input-group">
                    <input type="text" id="txtUrlServico" name="txtUrlServico" class="infraText form-control mr-2"
                        value="<?= PaginaSEI::tratarHTML($objMdIaAdmIntegracaoDTO->getStrOperacaoWsdl()); ?>"
                        onkeypress="return infraMascaraTexto(this,event,100);"
                        maxlength="100"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                    <button type="button" class="infraButton btnFormulario" accesskey="v" onclick="validarMapear()">
                        <span class="infraTeclaAtalho">V</span>alidar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <? if ($strCadastroUrls != '') { ?>
        <div class="row">
            <div class="col-12 col-xl-10">
                <label id="lblNome" for="txtNome" class="infraLabelObrigatorio">Cadastro de URL's:</label>
            </div>

            <div class="col-12 col-xl-10">
                <? PaginaSEI::getInstance()->montarAreaTabela($strCadastroUrls, 1); ?>
            </div>
        </div>
    <? } ?>

    <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
    <input type="hidden" id="hdnIdMdIaAdmIntegracao" name="hdnIdMdIaAdmIntegracao"
        value="<?= $objMdIaAdmIntegracaoDTO->getNumIdMdIaAdmIntegracao() ?>" />
    <input type="hidden" id="hdnTipoAcao" value="<?= $strTipoAcao ?>">
    <input type="hidden" id="hdnIsRest" value="<?= $isRest ? 's' : 'n' ?>">

    <?
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>

<?
require 'md_ia_adm_integracao_cadastro_js.php';

PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
