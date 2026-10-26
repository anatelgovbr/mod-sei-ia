<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 22/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/



$strItensSelFormaEntrega = '';
$strAjudaTamanhoMaximoPermitido = '';

try {
    require_once dirname(__FILE__) . '../../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->verificarSelecao('md_ia_adm_ext_permitida_selecionar');

    $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
    $strDesabilitar = '';
    $arrComandos = array();
    $id_md_ia_adm_ext_permitida = PaginaSEI::GET('id_md_ia_adm_ext_permitida') ?: PaginaSEI::POST('hdnIdMdIaAdmExtPermitida');
    $id_md_ia_adm_ext_permitida = is_numeric($id_md_ia_adm_ext_permitida) ? (int)$id_md_ia_adm_ext_permitida : null;
    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $numTamMbDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');
    $strItensSelFormaEntrega = MdIaAdmExtPermitidaINT::montarSelectFormaEntrega('null', '&nbsp;', '');

    switch ($_GET['acao']) {
        case 'md_ia_adm_ext_permitida_cadastrar':
            $strTitulo = 'Novo Extensão Permitidas';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdIaAdmExtPermitida" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdIaAdmExtPermitidaDTO->setStrExtensao(PaginaSEI::POST('txtExtensao'));
            $objMdIaAdmExtPermitidaDTO->setStrSinPermitidaProtocolo(PaginaSEI::getInstance()->getCheckbox(PaginaSEI::POST('chkSinPermitidaProtocolo')));
            $objMdIaAdmExtPermitidaDTO->setNumFormaEntregaArquivo(PaginaSEI::POST('selFormaEntregaArquivo'));
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoProtocolo(PaginaSEI::POST('txtDescTratamentoProtocolo'));
            $objMdIaAdmExtPermitidaDTO->setStrSinPermitidaAvulso(PaginaSEI::getInstance()->getCheckbox(PaginaSEI::POST('chkSinPermitidaAvulso')));
            $objMdIaAdmExtPermitidaDTO->setStrDescTratamentoAvulso(PaginaSEI::POST('txtDescTratamentoAvulso'));
            $objMdIaAdmExtPermitidaDTO->setNumTamanhoMaximoPermitido(PaginaSEI::POST('txtTamanhoMaximoPermitido'));

            if (isset($_POST['sbmCadastrarMdIaAdmExtPermitida'])) {
                try {
                    $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
                    $objMdIaAdmExtPermitidaDTO = $objMdIaAdmExtPermitidaRN->cadastrar($objMdIaAdmExtPermitidaDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Extensão Permitidas "' . $objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida() . '" cadastrado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao') . PaginaSEI::getInstance()->montarAncora($objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_adm_ext_permitida_alterar':
            $strTitulo = 'Alterar Extensão Permitidas';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdIaAdmExtPermitida" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $objMdIaAdmExtPermitidaDTO->retTodos();
            $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
            $objMdIaAdmExtPermitidaDTO->setNumIdMdIaAdmExtPermitida($id_md_ia_adm_ext_permitida);
            $objMdIaAdmExtPermitidaDTO = $objMdIaAdmExtPermitidaRN->consultar($objMdIaAdmExtPermitidaDTO);

            if (isset($_GET['id_md_ia_adm_ext_permitida'])) {
                if ($objMdIaAdmExtPermitidaDTO === null) {
                    throw new InfraException("Registro não encontrado.");
                }
                $strItensSelFormaEntrega = MdIaAdmExtPermitidaINT::montarSelectFormaEntrega('null', '&nbsp;', $objMdIaAdmExtPermitidaDTO->getNumFormaEntregaArquivo());
            } else {
                $objMdIaAdmExtPermitidaDTO->setNumTamanhoMaximoPermitido(PaginaSEI::POST('txtTamanhoMaximoPermitido'));
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao') . PaginaSEI::getInstance()->montarAncora($objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdIaAdmExtPermitida'])) {
                try {
                    $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
                    $objMdIaAdmExtPermitidaRN->alterar($objMdIaAdmExtPermitidaDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Extensão Permitidas "' . $objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida() . '" alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao') . PaginaSEI::getInstance()->montarAncora($objMdIaAdmExtPermitidaDTO->getNumIdMdIaAdmExtPermitida())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_ia_adm_ext_permitida_consultar':
            $strTitulo = 'Consultar Extensão Permitidas';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao') . PaginaSEI::getInstance()->montarAncora($id_md_ia_adm_ext_permitida)) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
            $objMdIaAdmExtPermitidaDTO->setNumIdMdIaAdmExtPermitida($id_md_ia_adm_ext_permitida);
            $objMdIaAdmExtPermitidaDTO->retTodos();
            $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
            $objMdIaAdmExtPermitidaDTO = $objMdIaAdmExtPermitidaRN->consultar($objMdIaAdmExtPermitidaDTO);
            if ($objMdIaAdmExtPermitidaDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            $strItensSelFormaEntrega = MdIaAdmExtPermitidaINT::montarSelectFormaEntrega('null', '&nbsp;', $objMdIaAdmExtPermitidaDTO->getNumFormaEntregaArquivo());
            break;

        default:
            throw new InfraException("Ação '" . PaginaSEI::GET('acao') . "' não reconhecida.");
    }
    $strMsg = 'Na interação com arquivos avulsos os arquivos serão sempre entregues por download.';

    $strLimiteConsiderar = (string)$objMdIaAdmExtPermitidaDTO->getStrLimiteConsiderar();
    $arrInfoLimite = MdIaAdmExtPermitidaRN::calcularLimiteTamanhoMaximoPermitido($numTamMbDocExterno, $strLimiteConsiderar);

    if ($arrInfoLimite['parametro_valido']) {
        $numLimiteEfetivo = $arrInfoLimite['limite_maximo'];
        $strMotivoLimite = $arrInfoLimite['motivo'];
        $strAjudaTamanhoMaximoPermitido = 'Para esta extensão, o valor informado deve ser entre 1 e ' . $numLimiteEfetivo . 'Mb. \n';
        $strAjudaTamanhoMaximoPermitido .= 'Motivo do limite máximo: ' . $strMotivoLimite . ' \n';
    } else {
        $strAjudaTamanhoMaximoPermitido = 'Não foi possível calcular o limite máximo porque o parâmetro SEI_TAM_MB_DOC_EXTERNO está inválido. Contate a administração do sistema.';
    }
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . ($strTitulo ?? false));
PaginaSEI::getInstance()->montarStyle();
?>
<link rel="stylesheet" type="text/css" href="modulos/ia/css/md_ia_comum.css?<?= PaginaSEI::getInstance()->getNumVersao() ?>" />
<link rel="stylesheet" type="text/css" href="modulos/ia/css/md_ia_adm_ext_permitida_cadastro.css?<?= PaginaSEI::getInstance()->getNumVersao() ?>" />
<?php
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo ?? false, 'onload="inicializar();"');
?>
<form id="frmMdIaAdmExtPermitidaCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao') . '&acao_origem=' . PaginaSEI::GET('acao')) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos ?? false);
    PaginaSEI::getInstance()->abrirAreaDados('auto');
    ?>
    <div id="divMsg" style="display: none">
        <div class="alert" role="alert">
            <label></label>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label id="lblExtensao" for="txtExtensao" accesskey="" class="infraLabelObrigatorio">Extensão:</label>
                <input type="text" id="txtExtensao" name="txtExtensao" readonly class="infraText  form-control" value="<?= PaginaSEI::tratarHTML($objMdIaAdmExtPermitidaDTO->getStrExtensao()) ?>" onkeypress="return infraMascaraTexto(this,event,20);" maxlength="20" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <div id="divSinConteudoVetorizavel" class="infraDivCheckbox">
                    <input type="checkbox" id="chkSinConteudoVetorizavel" name="chkSinConteudoVetorizavel" class="infraCheckbox" onclick="return false;" onkeydown="return false;" <?= PaginaSEI::getInstance()->setCheckbox($objMdIaAdmExtPermitidaDTO->getStrSinConteudoVetorizavel()) ?> tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                    <label id="lblSinConteudoVetorizavel" for="chkSinConteudoVetorizavel" accesskey="" class="infraLabelCheckbox">Conteúdo Vetorizável</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label id="lblFormaEntregaArquivo" for="selFormaEntregaArquivo" accesskey="" class="infraLabelObrigatorio">Forma de Entrega do Arquivo na citação de Protocolo de Documentos:</label>
                <select id="selFormaEntregaArquivo" readonly name="selFormaEntregaArquivo" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strItensSelFormaEntrega ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group" style="margin-bottom: 5px;">
                <div id="divSinPermitidaProtocolo" class="infraDivCheckbox">
                    <input type="checkbox" id="chkSinPermitidaProtocolo" name="chkSinPermitidaProtocolo" class="infraCheckbox" onclick="return false;" onkeydown="return false;" <?= PaginaSEI::getInstance()->setCheckbox($objMdIaAdmExtPermitidaDTO->getStrSinPermitidaProtocolo()) ?> tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                    <label id="lblSinPermitidaProtocolo" for="chkSinPermitidaProtocolo" accesskey="" class="infraLabelCheckbox">Aceita em Protocolo de Documentos</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label id="lblDescTratamentoProtocolo" for="txtDescTratamentoProtocolo" accesskey="" class="infraLabelOpcional">Descrição do Tratamento (Protocolo):</label>
                <textarea id="txtDescTratamentoProtocolo" readonly name="txtDescTratamentoProtocolo" class="infraTextArea form-control" maxlength="255" rows="6" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoProtocolo()) ?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <div id="divSinPermitidaAvulso" class="infraDivCheckbox">
                    <input type="checkbox" id="chkSinPermitidaAvulso" name="chkSinPermitidaAvulso" class="infraCheckbox" onclick="return false;" onkeydown="return false;" <?= PaginaSEI::getInstance()->setCheckbox($objMdIaAdmExtPermitidaDTO->getStrSinPermitidaAvulso()) ?> tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                    <label id="lblSinPermitidaAvulso" for="chkSinPermitidaAvulso" accesskey="" class="infraLabelCheckbox">Aceita em Arquivos Avulsos</label>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="form-group">
                <label id="lblTamanhoMaximoPermitido" for="txtTamanhoMaximoPermitido" accesskey="" class="infraLabelOpcional">Tamanho Máximo para Arquivos Avulsos(Mb):</label>
                <a href="javascript:void(0);" id="ancAjuda" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= PaginaSEI::montarTitleTooltip($strAjudaTamanhoMaximoPermitido) ?>><img src="<?= PaginaSEI::getInstance()->getIconeAjuda() ?>" class="infraImg" /></a>
                <input type="text" id="txtTamanhoMaximoPermitido" name="txtTamanhoMaximoPermitido" onkeypress="return infraMascaraNumero(this, event)" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdIaAdmExtPermitidaDTO->getNumTamanhoMaximoPermitido()) ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label id="lblDescTratamentoAvulso" for="txtDescTratamentoAvulso" accesskey="" class="infraLabelOpcional">Descrição do Tratamento (Avulso):</label>
                <textarea id="txtDescTratamentoAvulso" readonly name="txtDescTratamentoAvulso" class="infraTextArea form-control" maxlength="255" rows="6" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($objMdIaAdmExtPermitidaDTO->getStrDescTratamentoAvulso()) ?></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" id="hdnIdMdIaAdmExtPermitida" name="hdnIdMdIaAdmExtPermitida"
        value="<?= $id_md_ia_adm_ext_permitida ?>" />
    <?php
    PaginaSEI::getInstance()->fecharAreaDados();
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos ?? false);
    ?>
</form>
<?php
require_once "md_ia_adm_ext_permitida_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
