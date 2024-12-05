<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 13/07/2023 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.2
 */

try {
    require_once dirname(__FILE__) . '../../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('md_ia_adm_doc_relev_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_ia_adm_doc_relev_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                $arrObjMdIaAdmDocRelevDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                    $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($arrStrIds[$i]);
                    $arrObjMdIaAdmDocRelevDTO[] = $objMdIaAdmDocRelevDTO;

                    $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
                    $objMdIaAdmSegDocRelevRN->excluirRelacionamento($arrStrIds[$i]);

                }
                $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                $objMdIaAdmDocRelevRN->excluir($arrObjMdIaAdmDocRelevDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_ia_adm_doc_relev_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                $arrObjMdIaAdmDocRelevDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                    $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($arrStrIds[$i]);
                    $objMdIaAdmDocRelevDTO->setStrSinAtivo("N");
                    $objMdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                    $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                    $objMdIaAdmDocRelevRN->alterar($objMdIaAdmDocRelevDTO);
                }

                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_ia_adm_doc_relev_reativar':
            $strTitulo = 'Reativar Documentos Relevantes';
            if ($_GET['acao_confirmada'] == 'sim') {
                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                    $arrObjMdIaAdmDocRelevDTO = array();
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
                        $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($arrStrIds[$i]);
                        $objMdIaAdmDocRelevDTO->setStrSinAtivo("S");
                        $objMdIaAdmDocRelevDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());

                        $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
                        $objMdIaAdmDocRelevRN->alterar($objMdIaAdmDocRelevDTO);
                    }
                    PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
                die;
            }
            break;


        case 'md_ia_adm_doc_relev_listar':
            $strTitulo = 'Documentos Relevantes';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_cadastrar');
    $bolAcaoPesquisar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_pesquisar');


    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }

    if ($bolAcaoPesquisar) {
        $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&acao_retorno=md_ia_adm_doc_relev_listar'));
        $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    }
    $objMdIaAdmDocRelevDTO = new MdIaAdmDocRelevDTO();
    $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();

    if ($_GET['acao'] == 'md_ia_adm_doc_relev_reativar') {
        //Lista somente inativos
        $objMdIaAdmDocRelevDTO->setBolExclusaoLogica(false);
        $objMdIaAdmDocRelevDTO->setStrSinAtivo('N');
    }

    $objMdIaAdmDocRelevDTO->retStrSinAtivo();
    $objMdIaAdmDocRelevDTO->retStrNomeSerie();
    $objMdIaAdmDocRelevDTO->retDthAlteracao();
    $objMdIaAdmDocRelevDTO->retStrAplicabilidade();
    $objMdIaAdmDocRelevDTO->retNumIdMdIaAdmDocRelev();
    $objMdIaAdmDocRelevDTO->retStrNomeTipoProcedimento();

    if (isset($_POST['txtSegmentoDocumento']) && !empty($_POST['txtSegmentoDocumento'])) {
        $objMdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
        $objMdIaAdmSegDocRelevDTO->retNumIdMdIaAdmDocRelev();
        $objMdIaAdmSegDocRelevDTO->setStrSegmentoDocumento('%' . $_POST['txtSegmentoDocumento'] . '%', InfraDTO::$OPER_LIKE);
        $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();
        $arrObjMdIaAdmSegDocRelevDTO = $objMdIaAdmSegDocRelevRN->listar($objMdIaAdmSegDocRelevDTO);

        foreach ($arrObjMdIaAdmSegDocRelevDTO as $objMdIaAdmSegDocRelevDTO) {
            $arrayIdsMdIaAdmSegDocRelevDTO[] = $objMdIaAdmSegDocRelevDTO->getNumIdMdIaAdmDocRelev();
        }
        $objMdIaAdmDocRelevDTO->setNumIdMdIaAdmDocRelev($arrayIdsMdIaAdmSegDocRelevDTO, InfraDTO::$OPER_IN);
    }

    if (isset($_POST['selAplicabilidade']) && !empty($_POST['selAplicabilidade'])) {
        $objMdIaAdmDocRelevDTO->setStrAplicabilidade($_POST['selAplicabilidade']);
    }

    if (isset($_POST['selTipoDocumento']) && !empty($_POST['selTipoDocumento']) && $_POST['selTipoDocumento'] > 0) {
        $objMdIaAdmDocRelevDTO->setNumIdSerie($_POST['selTipoDocumento']);
    }
    if(!empty($_POST)) {
        if($_POST['selTipoProcesso'] > 0) {
            $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento($_POST['selTipoProcesso']);
        } elseif($_POST['selTipoProcesso'] == "") {
            $objMdIaAdmDocRelevDTO->setNumIdTipoProcedimento(null);
        }
    }
    if (isset($_POST['selAtivo']) && !empty($_POST['selAtivo'])) {
        $objMdIaAdmDocRelevDTO->setStrSinAtivo($_POST['selAtivo']);
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdIaAdmDocRelevDTO, 'IdMdIaAdmDocRelev', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdIaAdmDocRelevDTO);

    $objMdIaAdmDocRelevRN = new MdIaAdmDocRelevRN();
    $arrObjMdIaAdmDocRelevDTO = $objMdIaAdmDocRelevRN->listar($objMdIaAdmDocRelevDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdIaAdmDocRelevDTO);

    /** @var MdIaAdmDocRelevDTO[] $arrObjMdIaAdmDocRelevDTO */

    $numRegistros = count($arrObjMdIaAdmDocRelevDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'md_ia_adm_doc_relev_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_ia_adm_doc_relev_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_excluir');
            $bolAcaoDesativar = true;
        } else {
            $bolAcaoReativar = true;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_ia_adm_doc_relev_desativar');
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_desativar&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_excluir&acao_origem=' . $_GET['acao']);
        }

        $strResultado = '';
        $strSumarioTabela = 'Tabela de Documentos Relevantes.';
        $strCaptionTabela = 'Documentos Relevantes';

        $strResultado .= '<table width="100%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmDocRelevDTO, 'Aplicabilidade', 'Aplicabilidade', $arrObjMdIaAdmDocRelevDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmDocRelevDTO, 'Tipo de Documento', 'NomeSerie', $arrObjMdIaAdmDocRelevDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdIaAdmDocRelevDTO, 'Tipo de Processo', 'NomeTipoProcedimento', $arrObjMdIaAdmDocRelevDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh"> Segmento do Documento</th> '."\n";
        $strResultado .= '<th class="infraTh"> Data/Hora da Última Alteração:</th> '."\n";
        $strResultado .= '<th class="infraTh">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';

        $objMdIaAdmSegDocRelevRN = new MdIaAdmSegDocRelevRN();

        for ($i = 0; $i < $numRegistros; $i++) {

            if ($arrObjMdIaAdmDocRelevDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }
            $strResultado .= $strCssTr;

            if ($arrObjMdIaAdmDocRelevDTO[$i]->getStrAplicabilidade() == "I") {
                $aplicabilidade = "Interno";
            } else {
                $aplicabilidade = "Externo";
            }


            $objMdIaAdmSegDocRelevDTO = new MdIaAdmSegDocRelevDTO();
            $objMdIaAdmSegDocRelevDTO->retStrSegmentoDocumento();
            $objMdIaAdmSegDocRelevDTO->retNumPercentualRelevancia();
            $objMdIaAdmSegDocRelevDTO->setNumIdMdIaAdmDocRelev($arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev());
            $arrObjMdIaAdmSegDocRelevDTO = $objMdIaAdmSegDocRelevRN->listar($objMdIaAdmSegDocRelevDTO);
            $segmentoDocumentos = "";

            foreach ($arrObjMdIaAdmSegDocRelevDTO as $segmentoDocumento) {
                $segmentoDocumentos .= $segmentoDocumento->getStrSegmentoDocumento() . ':' . $segmentoDocumento->getNumPercentualRelevancia() . '; ';
            }
            if ($arrObjMdIaAdmDocRelevDTO[$i]->getStrNomeTipoProcedimento() == "") {
                $tipoProcedimento = "Todos os Tipos de Processos";
            } else {
                $tipoProcedimento = $arrObjMdIaAdmDocRelevDTO[$i]->getStrNomeTipoProcedimento();
            }

            if ($bolCheck) {
                $strResultado .= '<td valign="top" style="vertical-align: middle;">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev(), $arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev()) . '</td>';
            }

            $strResultado .= '<td>' . PaginaSEI::tratarHTML($aplicabilidade) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmDocRelevDTO[$i]->getStrNomeSerie()) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($tipoProcedimento) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($segmentoDocumentos) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdIaAdmDocRelevDTO[$i]->getDthAlteracao()) . '</td>';
            $strResultado .= '<td align="center">';

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&documento_relevante=' . $arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeConsultar() . '" title="Consultar Documento Relevante" alt="Consultar Documento Relevante" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_adm_doc_relev_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&documento_relevante=' . $arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Documento Relevante" alt="Alterar Documento Relevante" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = "";
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev());
            }
            if ($bolAcaoExcluir) {
                $strId = $arrObjMdIaAdmDocRelevDTO[$i]->getNumIdMdIaAdmDocRelev();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($aplicabilidade . " - " . $arrObjMdIaAdmDocRelevDTO[$i]->getStrNomeSerie() . " - " . $tipoProcedimento);
            }

            if ($bolAcaoDesativar || $bolAcaoReativar) {
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($aplicabilidade. " - ".$arrObjMdIaAdmDocRelevDTO[$i]->getStrNomeSerie()." - ".$tipoProcedimento));
                if ($bolAcaoDesativar && $arrObjMdIaAdmDocRelevDTO[$i]->getStrSinAtivo() == 'S') {
                    $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg" title="Desativar Documento Relevante" alt="Desativar Documento Relevante" class="infraImg" /></a>&nbsp;';
                } else {
                    $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg" title="Reativar Documento Relevante" alt="Reativar Documento Relevante" class="infraImg" /></a>&nbsp;';
                }
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeExcluir() . '" title="Excluir Documento Relevante" alt="Excluir Documento Relevante" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    $strItensSelTiposDocumentos = MdIaAdmDocRelevINT::retornaComboboxTipoDocumento($_POST);
    $strItensSelTiposProcessos = MdIaAdmDocRelevINT::retornaComboboxTipoProcessos($_POST);

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
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdIaAdmDocRelevLista" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?

        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        ?>
        <div id="divMsg">
            <div class="alert" role="alert">
                <label></label>
            </div>
        </div>
        <div class="infraAreaDados" id="divInfraAreaDados">
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 mb-2">
                    <div class="form-group">
                        <label id="lblAplicabilidade" for="selAplicabilidade" accesskey="o" class="infraLabelOpcional">Aplicabilidade:</label>
                        <select class="infraSelect form-control" name="selAplicabilidade" id="selAplicabilidade"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"
                                onchange="retornaTiposDocumentosCadastrados()">
                            <option value="0">Selecione uma opção</option>
                            <option value="I" <?php if ($_POST['selAplicabilidade'] == "I") {
                                echo "selected";
                            } ?>>Interno
                            </option>
                            <option value="E" <?php if ($_POST['selAplicabilidade'] == "E") {
                                echo "selected";
                            } ?>>Externo
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label id="lblTipoDocumento" for="selTipoDocumento" accesskey="o" class="infraLabelOpcional">Tipo
                            de Documento:</label>
                        <select class="infraSelect form-control" name="selTipoDocumento" id="selTipoDocumento"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <?=$strItensSelTiposDocumentos?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label id="lblTipoProcesso" for="selTipoProcesso" accesskey="o" class="infraLabelOpcional">Tipo
                            de Processo:</label>
                        <select class="infraSelect form-control" name="selTipoProcesso" id="selTipoProcesso"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <?=$strItensSelTiposProcessos?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8">
                    <label id="lblSegmentoDocumento" for="txtSegmentoDocumento" accesskey="m"
                           class="infraLabelOpcional">Segmento do Documento:</label>
                    <input type="text" id="txtSegmentoDocumento" name="txtSegmentoDocumento"
                           class="infraText form-control"
                           value="<?= PaginaSEI::tratarHTML($_POST['txtSegmentoDocumento']) ?>" style="height: calc(1.5em + .4rem + 4px)"/>

                    <input type="submit" style="visibility: hidden;"/>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label id="lblAtivo" for="selAtivo" accesskey="o" class="infraLabelOpcional">Ativo:</label>
                        <select class="infraSelect form-control" name="selAtivo" id="selAtivo"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <option value="">Todos</option>
                            <option value="S"<?php if ($_POST['selAtivo'] == "S") {
                                echo "selected";
                            } ?>>Ativo</option>
                            <option value="N"<?php if ($_POST['selAtivo'] == "N") {
                                echo "selected";
                            } ?>>Inativo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <?
                    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                    //PaginaSEI::getInstance()->montarAreaDebug();
                    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
                    ?>
                </div>
            </div>
        </div>
    </form>
<?
require_once "md_ia_adm_doc_relev_lista_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
