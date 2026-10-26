<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 22/04/2026 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.46.4
 **/



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

    PaginaSEI::getInstance()->prepararSelecao('md_ia_adm_ext_permitida_selecionar');

    switch ($_GET['acao']) {
        case 'md_ia_adm_ext_permitida_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaAdmExtPermitidaDTO = array();
                foreach ($arrStrIds as $strId) {
                    $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
                    $objMdIaAdmExtPermitidaDTO->setNumIdMdIaAdmExtPermitida($strId);
                    $arrObjMdIaAdmExtPermitidaDTO[] = $objMdIaAdmExtPermitidaDTO;
                }
                $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();

                $objMdIaAdmExtPermitidaRN->excluir($arrObjMdIaAdmExtPermitidaDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao_origem') . '&acao_origem=' . PaginaSEI::GET('acao')));
            die;

        case 'md_ia_adm_ext_permitida_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaAdmExtPermitidaDTO = array();
                foreach ($arrStrIds as $strId) {
                    $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
                    $objMdIaAdmExtPermitidaDTO->setNumIdMdIaAdmExtPermitida($strId);
                    $objMdIaAdmExtPermitidaDTO->setStrSinAtivo('N'); // Garante que o DTO tem o status de inativo para a operação de desativação
                    $arrObjMdIaAdmExtPermitidaDTO[] = $objMdIaAdmExtPermitidaDTO;
                }
                $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
                $objMdIaAdmExtPermitidaRN->desativar($arrObjMdIaAdmExtPermitidaDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao_origem') . '&acao_origem=' . PaginaSEI::GET('acao')));
            die;

        case 'md_ia_adm_ext_permitida_reativar':
            $strTitulo = 'Reativar Extensões Permitidas';
            if (PaginaSEI::GET('acao_confirmada') !== 'sim') {
                break;
            }
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdIaAdmExtPermitidaDTO = array();
                foreach ($arrStrIds as $strId) {
                    $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
                    $objMdIaAdmExtPermitidaDTO->setNumIdMdIaAdmExtPermitida($strId);
                    $objMdIaAdmExtPermitidaDTO->setStrSinAtivo('S'); // Garante que o DTO tem o status de ativo para a operação de reativação
                    $arrObjMdIaAdmExtPermitidaDTO[] = $objMdIaAdmExtPermitidaDTO;
                }

                $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
                $objMdIaAdmExtPermitidaRN->reativar($arrObjMdIaAdmExtPermitidaDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao_origem') . '&acao_origem=' . PaginaSEI::GET('acao')));
            die;


        case 'md_ia_adm_ext_permitida_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Extensão de Arquivo Permitidas', 'Selecionar Extensões Permitidas');

            //Se cadastrou alguem
            if (PaginaSEI::GET('acao_origem') === 'md_ia_adm_ext_permitida_cadastrar' && is_numeric(PaginaSEI::GET('id_md_ia_adm_ext_permitida'))) {
                PaginaSEI::getInstance()->adicionarSelecionado((int)PaginaSEI::GET('id_md_ia_adm_ext_permitida'));
            }
            break;

        case 'md_ia_adm_ext_permitida_listar':
            $strTitulo = 'Extensões de Arquivos Permitidas';
            break;

        default:
            throw new InfraException("Ação '" . PaginaSEI::GET('acao') . "' não reconhecida.");
    }

    $arrComandos = array();
    if (PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    /*if (PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_listar' || PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_selecionar') {
        $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_cadastrar');
        if ($bolAcaoCadastrar) {
            $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_cadastrar&acao_origem=' . PaginaSEI::GET('acao') . '&acao_retorno=' . PaginaSEI::GET('acao')) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
        }
    }*/

    $objMdIaAdmExtPermitidaDTO = new MdIaAdmExtPermitidaDTO();
    $objMdIaAdmExtPermitidaDTO->retNumIdMdIaAdmExtPermitida();
    $objMdIaAdmExtPermitidaDTO->retStrExtensao();
    $objMdIaAdmExtPermitidaDTO->retStrSinPermitidaProtocolo();
    $objMdIaAdmExtPermitidaDTO->retNumFormaEntregaArquivo();
    $objMdIaAdmExtPermitidaDTO->retStrDescTratamentoProtocolo();
    $objMdIaAdmExtPermitidaDTO->retStrSinPermitidaAvulso();
    $objMdIaAdmExtPermitidaDTO->retStrDescTratamentoAvulso();
    $objMdIaAdmExtPermitidaDTO->retNumTamanhoMaximoPermitido();
    $objMdIaAdmExtPermitidaDTO->retStrSinAtivo();

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaAdmExtPermitidaDTO, 'IdMdIaAdmExtPermitida', InfraDTO::$TIPO_ORDENACAO_ASC);
    //PaginaSEI::getInstance()->prepararPaginacao($objMdIaAdmExtPermitidaDTO);

    $objMdIaAdmExtPermitidaRN = new MdIaAdmExtPermitidaRN();
    $arrObjMdIaAdmExtPermitidaDTO = $objMdIaAdmExtPermitidaRN->listar($objMdIaAdmExtPermitidaDTO);

  //PaginaSEI::getInstance()->processarPaginacao($objMdIaAdmExtPermitidaDTO);

    /** @var MdIaAdmExtPermitidaDTO[] $arrObjMdIaAdmExtPermitidaDTO */

    $numRegistros = count($arrObjMdIaAdmExtPermitidaDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;

        if (PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_alterar');
            $bolAcaoImprimir = false;
            //$bolAcaoGerarPlanilha = false;
            //$bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } elseif (PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
            //$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_excluir');
            $bolAcaoDesativar = false;
        } else {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_alterar');
            $bolAcaoImprimir = true;
            //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
            //$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_ext_permitida_desativar');
        }
        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_desativar&acao_origem=' . PaginaSEI::GET('acao'));
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_reativar&acao_origem=' . PaginaSEI::GET('acao') . '&acao_confirmada=sim');
        }


        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_excluir&acao_origem=' . PaginaSEI::GET('acao'));
        }

        /*
    if ($bolAcaoGerarPlanilha) {
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="P" id="btnGerarPlanilha" value="Gerar Planilha" onclick="infraGerarPlanilhaTabela(\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=infra_gerar_planilha_tabela').'\');" class="infraButton">Gerar <span class="infraTeclaAtalho">P</span>lanilha</button>';
    }
    */

        $strResultado = '';

        $strCaptionTabela = 'Extensões de Arquivos Permitidas';

        $strResultado .= '<table style="width: 99%" class="infraTable">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<thead><tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" style="width: 1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmExtPermitidaDTO, 'Extensão', 'Extensao', $arrObjMdIaAdmExtPermitidaDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">Forma de Entrega do Arquivo na citação de Protocolo de Documentos</th>' . "\n";
        $strResultado .= '<th class="infraTh">Permitida Interação por Protocolo</th>' . "\n";
        //$strResultado .= '<th class="infraTh">Tratamento em Protocolo</th>' . "\n";
        $strResultado .= '<th class="infraTh">Permitida Interação por Arquivos Avulsos</th>' . "\n";
        //$strResultado .= '<th class="infraTh">Tratamento em Arquivos Avulso</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmExtPermitidaDTO, 'Tamanho Máximo para Arquivos Avulsos(Mb)', 'TamanhoMaximoPermitido', $arrObjMdIaAdmExtPermitidaDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">Ações</th>' . "\n";
        $strResultado .= '</tr></thead><tbody>' . "\n";
        $arrFormaEntregaLista = MdIaAdmExtPermitidaINT::getArrFormaEntrega();
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            if ($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td style="vertical-align: center">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaAdmExtPermitidaDTO[$i]->getNumIdMdIaAdmExtPermitida(), $arrObjMdIaAdmExtPermitidaDTO[$i]->getStrExtensao()) . '</td>';
            }
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrExtensao()) . '</td>';
            $strFormaEntrega = (string)$arrObjMdIaAdmExtPermitidaDTO[$i]->getNumFormaEntregaArquivo();
            $strResultado .= '<td>' . PaginaSEI::tratarHTML(isset($arrFormaEntregaLista[$strFormaEntrega]) ? $arrFormaEntregaLista[$strFormaEntrega] : $strFormaEntrega) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrSinPermitidaProtocolo()) . '</td>';
            //$strResultado .= '<td>' . PaginaSEI::tratarHTML(mb_strimwidth($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrDescTratamentoProtocolo(), 0, 100, "...", "ISO-8859-1")) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrSinPermitidaAvulso()) . '</td>';
            //$strResultado .= '<td>' . PaginaSEI::tratarHTML(mb_strimwidth($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrDescTratamentoAvulso(), 0, 100, "...", "ISO-8859-1")) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmExtPermitidaDTO[$i]->getNumTamanhoMaximoPermitido()) . '</td>';
            $strResultado .= '<td style="text-align: center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdIaAdmExtPermitidaDTO[$i]->getStrExtensao());

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_consultar&acao_origem=' . PaginaSEI::GET('acao') . '&id_md_ia_adm_ext_permitida=' . $arrObjMdIaAdmExtPermitidaDTO[$i]->getNumIdMdIaAdmExtPermitida() .  '&acao_retorno=' . PaginaSEI::GET('acao')) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeConsultar() . '" title="Consultar Extensão de Arquivo Permitida" alt="Consultar Extensão de Arquivo Permitida" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_ext_permitida_alterar&acao_origem=' . PaginaSEI::GET('acao') . '&id_md_ia_adm_ext_permitida=' . $arrObjMdIaAdmExtPermitidaDTO[$i]->getNumIdMdIaAdmExtPermitida() . '&acao_retorno=' . PaginaSEI::GET('acao')) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Extensão de Arquivo Permitida" alt="Alterar Extensão de Arquivo Permitida" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdIaAdmExtPermitidaDTO[$i]->getNumIdMdIaAdmExtPermitida();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrExtensao());
            }

            if ($bolAcaoDesativar || $bolAcaoReativar) {

                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdIaAdmExtPermitidaDTO[$i]->getStrExtensao()));
                if ($bolAcaoDesativar && $arrObjMdIaAdmExtPermitidaDTO[$i]->getStrSinAtivo() == 'S') {
                    $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg" title="Desativar Documento Relevante" alt="Desativar Documento Relevante" class="infraImg" /></a>&nbsp;';
                } else {
                    $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg" title="Reativar Documento Relevante" alt="Reativar Documento Relevante" class="infraImg" /></a>&nbsp;';
                }
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Extensão de Arquivo Permitida" alt="Excluir Extensão de Arquivo Permitida" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr></tbody>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if (PaginaSEI::GET('acao') === 'md_ia_adm_ext_permitida_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . PaginaSEI::GET('acao')) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
    }

    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $numTamMbDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');
    $strMsg = 'O tamanho máximo geral para arquivos configurado no sistema é ' . $numTamMbDocExterno . 'Mb.';
    $strMsgArquivosAvulsos = 'Na interação com arquivos avulsos os arquivos serão sempre entregues por download.';
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . ($strTitulo ?? false));
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo ?? false, 'onload="inicializar();"');
?>
<form id="frmMdIaAdmExtPermitidaLista" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::GET('acao') . '&acao_origem=' . PaginaSEI::GET('acao')) ?>">
    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos ?? false);
    PaginaSEI::getInstance()->abrirAreaDados('2em');
    ?>
    <label id="lblAviso" class="infraLabelOpcional"><?= $strMsg ?></label><br>
    <label id="lblAvisoArquivosAvulsos" class="infraLabelOpcional"><?= $strMsgArquivosAvulsos ?></label>
    <?php
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarAreaTabela($strResultado ?? false, $numRegistros ?? false);
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos ?? false);
    ?>
</form>
<?php
require_once "md_ia_adm_ext_permitida_lista_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
