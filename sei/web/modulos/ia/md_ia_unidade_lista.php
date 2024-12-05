<?
/**
 * ANATEL
 *
 * 21/12/2023 - criado por sabino.colab
 */
try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(true);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('md_ia_unidade_selecionar_todas');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->salvarCamposPost(array('selOrgao', 'txtSiglaUnidade', 'txtDescricaoUnidade'));

    switch ($_GET['acao']) {
        case 'md_ia_unidade_selecionar_todas':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Unidade', 'Selecionar Unidades');
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    $arrComandos[] = '<button accesskey="P" type="submit" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    if ($_GET['acao'] == 'md_ia_unidade_selecionar_todas') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $objUnidadeRN = new UnidadeRN();
    $objUnidadeDTO = new UnidadeDTO();
    $objUnidadeDTO->retNumIdUnidade();
    $objUnidadeDTO->retStrSigla();
    $objUnidadeDTO->retStrDescricao();
    $objUnidadeDTO->retStrSiglaOrgao();
    $objUnidadeDTO->retStrDescricaoOrgao();

    $numIdOrgao = PaginaSEI::getInstance()->recuperarCampo('selOrgao');
    if ($numIdOrgao !== '') {
        $objUnidadeDTO->setNumIdOrgao($numIdOrgao);
    }

    $strSiglaPesquisa = trim(PaginaSEI::getInstance()->recuperarCampo('txtSiglaUnidade'));
    if ($strSiglaPesquisa !== '') {
        $objUnidadeDTO->setStrSigla($strSiglaPesquisa);
    }

    $strDescricaoPesquisa = PaginaSEI::getInstance()->recuperarCampo('txtDescricaoUnidade');
    if ($strDescricaoPesquisa !== '') {
        $objUnidadeDTO->setStrDescricao($strDescricaoPesquisa);
    }

    $objUnidadeDTO->setOrdStrSigla(InfraDTO::$TIPO_ORDENACAO_ASC);

    $strTermoPesquisa = '&idUnidadeExpedidora=';

    $objUnidadeRN = new UnidadeRN();
    $arrObjUnidadeDTO = $objUnidadeRN->pesquisar($objUnidadeDTO);

    PaginaSEI::getInstance()->prepararPaginacao($objUnidadeDTO);
    PaginaSEI::getInstance()->processarPaginacao($objUnidadeDTO);

    $numRegistros = count($arrObjUnidadeDTO);


    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'md_ia_unidade_selecionar_todas') {
            $bolCheck = true;
        }

        $strResultado = '';

        $strSumarioTabela = 'Tabela de Unidades.';
        $strCaptionTabela = 'Unidades';

        $strResultado .= '<table width="100%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh" width="10%">ID</th>' . "\n";
        $strResultado .= '<th class="infraTh text-center" width="10%">Sigla</th>' . "\n";
        $strResultado .= '<th class="infraTh text-left">Descrição</th>' . "\n";
        $strResultado .= '<th class="infraTh text-center" width="5%">Órgão</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        foreach ($arrObjUnidadeDTO as $i => $item) {

            $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td>' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjUnidadeDTO[$i]->getNumIdUnidade(), UnidadeINT::formatarSiglaDescricao($arrObjUnidadeDTO[$i]->getStrSigla(), $arrObjUnidadeDTO[$i]->getStrDescricao())) . '</td>';
            }

            $strResultado .= '<td align="center">' . $arrObjUnidadeDTO[$i]->getNumIdUnidade() . '</td>';
            $strResultado .= '<td class="text-center">' . PaginaSEI::tratarHTML($arrObjUnidadeDTO[$i]->getStrSigla()) . '</td>';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjUnidadeDTO[$i]->getStrDescricao()) . '</td>';
            $strResultado .= '<td align="center"><a alt="' . PaginaSEI::tratarHTML($arrObjUnidadeDTO[$i]->getStrDescricaoOrgao()) . '" title="' . PaginaSEI::tratarHTML($arrObjUnidadeDTO[$i]->getStrDescricaoOrgao()) . '" class="ancoraSigla">' . PaginaSEI::tratarHTML($arrObjUnidadeDTO[$i]->getStrSiglaOrgao()) . '</a></td>';
            $strResultado .= '<td align="center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjUnidadeDTO[$i]->getNumIdUnidade());

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if ($_GET['acao'] == 'md_ia_unidade_selecionar_todas') {
        $arrComandos[] = '<button type="button" accesskey="C" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $strItensSelOrgao = OrgaoINT::montarSelectSiglaRI1358('', 'Todos', $numIdOrgao);
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
?>

    function inicializar(){
    if ('<?= $_GET['acao'] ?>'=='md_ia_unidade_selecionar_todas'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    }else{
    document.getElementById('btnFechar').focus();
    }

    infraEfeitoTabelas();
    }

<?
/*
  if ($bolAcaoDesativar) {
  function acaoDesativar(id,desc){
  if (confirm("Confirma desativação da Unidade \""+desc+"\"?")){
  document.getElementById('hdnInfraItemId').value=id;
  document.getElementById('frmUnidadeLista').action='<?= $strLinkDesativar ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }

  function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
  alert('Nenhuma Unidade selecionada.');
  return;
  }
  if (confirm("Confirma desativação das Unidades selecionadas?")){
  document.getElementById('hdnInfraItemId').value='';
  document.getElementById('frmUnidadeLista').action='<?= $strLinkDesativar ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }
  }
  if ($bolAcaoReativar) { ?>
  function acaoReativar(id,desc){
  if (confirm("Confirma reativação da Unidade \""+desc+"\"?")){
  document.getElementById('hdnInfraItemId').value=id;
  document.getElementById('frmUnidadeLista').action='<?= $strLinkReativar ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }

  function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
  alert('Nenhuma Unidade selecionada.');
  return;
  }
  if (confirm("Confirma reativação das Unidades selecionadas?")){
  document.getElementById('hdnInfraItemId').value='';
  document.getElementById('frmUnidadeLista').action='<?= $strLinkReativar ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }
  }
  if ($bolAcaoExcluir) { ?>
  function acaoExcluir(id,desc){
  if (confirm("Confirma exclusão da Unidade \""+desc+"\"?")){
  document.getElementById('hdnInfraItemId').value=id;
  document.getElementById('frmUnidadeLista').action='<?= $strLinkExcluir ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }

  function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
  alert('Nenhuma Unidade selecionada.');
  return;
  }
  if (confirm("Confirma exclusão das Unidades selecionadas?")){
  document.getElementById('hdnInfraItemId').value='';
  document.getElementById('frmUnidadeLista').action='<?= $strLinkExcluir ?>';
  document.getElementById('frmUnidadeLista').submit();
  }
  }
  }
 */
?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmUnidadeLista" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . $strTermoPesquisa) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('5em');
        ?>

        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label id="lblOrgao" for="selOrgao" accesskey="o" class="infraLabelOpcional">Órgã<span
                                class="infraTeclaAtalho">o</span>:</label>
                    <select id="selOrgao" name="selOrgao" onchange="this.form.submit();"
                            class="infraSelect form-control"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strItensSelOrgao ?>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label id="lblSiglaUnidade" for="txtSiglaUnidade" class="infraLabelOpcional">Sigla:</label>
                    <input type="text" id="txtSiglaUnidade" name="txtSiglaUnidade" class="infraText form-control"
                           value="<?= PaginaSEI::tratarHTML(PaginaSEI::tratarHTML($strSiglaPesquisa)) ?>" maxlength="15"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label id="lblDescricaoUnidade" for="txtDescricaoUnidade"
                           class="infraLabelOpcional">Descrição:</label>
                    <input type="text" id="txtDescricaoUnidade" name="txtDescricaoUnidade"
                           class="infraText form-control"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                           value="<?= PaginaSEI::tratarHTML($strDescricaoPesquisa) ?>"/>
                </div>
            </div>
        </div>

        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <? PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros); ?>
                </div>
            </div>
        </div>

        <?
        PaginaSEI::getInstance()->montarAreaDebug();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>