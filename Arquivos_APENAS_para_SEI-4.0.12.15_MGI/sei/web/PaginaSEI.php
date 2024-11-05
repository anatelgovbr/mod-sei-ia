<?
/*
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 12/11/2007 - criado por MGA
 *
 */

require_once dirname(__FILE__).'/SEI.php';


class PaginaSEI extends InfraPaginaEsquema3
{
  private static $instance = null;
  private $bolArvore = false;
  private $bolD3 =   false;
  private $bolAcoesSistema = true;
  private static $strMenu = null;

  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new PaginaSEI();
    }
    return self::$instance;
  }

  public function __construct()
  {
    SeiINT::validarHttps();
    parent::__construct();
  }

  public function getStrNomeSistema()
  {
    return ConfiguracaoSEI::getInstance()->getValor('PaginaSEI', 'NomeSistema');
  }

  public function isBolProducao()
  {
    return ConfiguracaoSEI::getInstance()->getValor('SEI', 'Producao');
  }

  public function validarHashTabelas(){
    return true;
  }

  public function getStrLogoSistema(){
    $logoSufix = $this->getStrEsquemaCores() == self::$ESQUEMA_DSGOV ? '-azul' : '';    
    $strRet = '<img class="d-none d-lg-inline-block" src="svg/sei_barra'.$logoSufix.'.svg" title="Sistema Eletrônico de Informações - Versão ' . SEI_VERSAO . '"/>';
    $strRet .= '<img class="d-inline-block d-lg-none" src="svg/sei_barra'.$logoSufix.'.svg" title="Sistema Eletrônico de Informações - Versão ' . SEI_VERSAO . ' Distribuído por ' . $strDistribuidoPor . '"/>';
    if (($strComplemento = ConfiguracaoSEI::getInstance()->getValor('PaginaSEI', 'NomeSistemaComplemento',false))!=null){
      $strRet .= '<span class="infraTituloLogoSistema">'.$strComplemento.'</span>';
    }
    return $strRet;
  }

  public function getDiretoriosIconesMenu()
  {
    global $SEI_MODULOS;
    $arr = array('menu');
    foreach($SEI_MODULOS as $objModulo){
      if (($strDir = $objModulo->executar('obterDiretorioIconesMenu'))!=null){
        $arr[] = $strDir;
      }
    }
    return $arr;
  }

  public function fecharBody() {
    echo '</div>' . "\n" . //infraAreaTelaD
      '</div>' . "\n" . //infraAreaTela
      '</div>' . "\n" . //infraAreaGlobal

      '<input type="hidden" id="hdnInfraPrefixoCookie" name="hdnInfraPrefixoCookie" value="' . $this->getStrPrefixoCookie() . '" />' . "\n" .
      '<div id="infraDivImpressao" class="infraImpressao"></div>' . "\n";

      echo '<div id="infraDivBootstrap-xs" class="d-none d-xs-block"></div>'."\n".
      '<div id="infraDivBootstrap-sm" class="d-none d-sm-block"></div>'."\n".
      '<div id="infraDivBootstrap-md" class="d-none d-md-block"></div>'."\n".
      '<div id="infraDivBootstrap-lg" class="d-none d-lg-block"></div>'."\n";

    $this->montarMensagens();
    echo '</body>' . "\n";
  }

  public function getStrMenuSistema()
  {
    global $SEI_MODULOS;

    if (self::$strMenu === null) {

      $strMenu = parent::montarMenuSessao('Principal');

      if (($strLogo = ConfiguracaoSEI::getInstance()->getValor('PaginaSEI', 'LogoMenu', false)) != null) {
        $strMenu = str_replace("<!--LOGO-->" ,$strLogo,$strMenu);
      }

      $strModulos = "";
      foreach($SEI_MODULOS as $objModulo){
        if (($strMenuModulo = $objModulo->executar('adicionarElementoMenu', $_GET['acao']))!=null){
          $strModulos .= $strMenuModulo;
        }
      }
      if($strModulos != ""){
        $strMenu = str_replace("<!--MODULOS-->" ,$strModulos,$strMenu);
      }

      self::$strMenu = $strMenu;
    }

    return self::$strMenu;
  }

  public function getArrStrAcoesSistema()
  {
    global $SEI_MODULOS;

    $arrStrAcoes = array();

    // $arrStrAcoes[] = $this->montarLinkMenuTexto();

    if ($this->bolAcoesSistema) {
      $strAcoesIcones = '';

      if (SessaoSEI::getInstance()->verificarPermissao('protocolo_pesquisa_rapida')) {

        $arrStrAcoes[] =' <div class="nav-item px-1 media d-flex py-md-0 ">
                 <form class="form-inline align-self-center w-100" id="frmProtocoloPesquisaRapida" method="post" action="'.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=protocolo_pesquisa_rapida').'">
                  <div class="input-group">
                    <input type="text" id="txtPesquisaRapida" name="txtPesquisaRapida" class="form-control" placeholder="Pesquisar..." style="font-size:.8rem;height:24px;width:190px;border:0;" tabindex="' . $this->getProxTabBarraSistema() . '" />
                    <span class="input-group-btn">
                      <span id="spnInfraUnidade" class="btn infraAcaoBarraConjugada">
                      <i class="fa-solid fa-search" onclick="document.getElementById(\'frmProtocoloPesquisaRapida\').submit();"></i>
                      </span>
                    </span>
                  </div>
                 </form>
             </div >           
          ';
      }

      $strAcoesIcones .= ''.$this->montarSelectUnidades().'';

      foreach($SEI_MODULOS as $objModulo){
        if (($arrAcoesModulo = $objModulo->executar('montarIconeSistema'))!=null){
          foreach ($arrAcoesModulo as $strAcaoModulo){
            $strAcoesIcones .= '<div class="nav-item d-flex infraAcaoBarraSistema">'.$strAcaoModulo.'</div>';
          }
        }
      }

      if (SessaoSEI::getInstance()->verificarPermissao('procedimento_controlar')) {
        $strAncora = '';
        if (isset($_GET['acao_origem']) && $_GET['acao_origem'] == 'procedimento_controlar' && isset($_GET['id_procedimento'])) {
          $strAncora = self::montarAncora($_GET['id_procedimento']);
        }
        $strAcoesIcones .= '
          <div class="nav-item d-flex infraAcaoBarraSistema">
            <a class="align-self-center  d-none d-md-block" id="lnkControleProcessos" href="#" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&reset=1'.$strAncora).'\'" title="Controle de Processos" tabindex="'.$this->getProxTabBarraSistema().'">
              <img src="svg/controle_processos_barra.svg" class="infraImg" title="Controle de Processos" />
            </a>            
            <span title="Controle de Processos"  class=" nav-link d-flex d-md-none" >
              <img src="svg/controle_processos_barra.svg" class="infraImg" title="Controle de Processos" />
              <a class="align-self-center text-white pl-1"  href="'.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&reset=1'.$strAncora).'" title="Controle de Processos" tabindex="' . $this->getProxTabBarraSistema() . '" >                
                Controle de Processos
              </a>
            </span>
          </div >';
      }
      if (SessaoSEI::getInstance()->verificarPermissao('novidade_mostrar')) {
        $strAcoesIcones .= '
          <div class="nav-item d-flex infraAcaoBarraSistema">
            
            <a class="align-self-center  d-none d-md-block"  target="_blank" href="'.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=novidade_mostrar&mostrar_todas=1').'" title="Novidades" tabindex="' . $this->getProxTabBarraSistema() . '">
              <i class="fa fa-exclamation-circle" title="Novidades"></i>
            </a>
            
            <span title="Novidades"  class=" nav-link   d-flex d-md-none" >
               <i class="fa fa-exclamation-circle" title="Novidades"></i>
               <a class="align-self-center text-white pl-1"  target="_blank" href="'.SessaoSEI::getInstance()->assinarLink("controlador.php?acao=novidade_mostrar&mostrar_todas=1").'" title="Novidades" tabindex="' . $this->getProxTabBarraSistema() . '">
                Novidades
               </a>
            </span>
         </div >';
      }
      $strAcoesIcones .= $this->montarLinkUsuario();
      $strAcoesIcones .= $this->montarLinkConfiguracao();
      $strAcoesIcones .= parent::montarLinkSair(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=sair'));
      $arrStrAcoes[] = $strAcoesIcones;
    }

    return $arrStrAcoes;
  }


  public function getObjInfraSessao()
  {
    return SessaoSEI::getInstance();
  }

  public function getObjInfraLog()
  {
    return LogSEI::getInstance();
    //return null;
  }

  public function setBolArvore($bolArvore)
  {
    $this->bolArvore = $bolArvore;
    if ($this->bolArvore) {
      $this->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    }
  }

  public function isBolArvore()
  {
    return $this->bolArvore;
  }

  public function abrirHead($strAtributos = '')
  {
    parent::abrirHead($strAtributos);
    SeiINT::montarHeaderFavicon('favicon');
  }

  public function montarLinkMenu()
  {
    return '';
  }

  public function getBolMontarIconeMenu(){
    return true;
  }

  public function montarBotaoVoltarExcecao()
  {
    if ($this->isBolArvore()) {
      return '';
    } else {
      return parent::montarBotaoVoltarExcecao();
    }
  }

  public function montarBotaoFecharExcecao()
  {
    if ($this->isBolArvore()) {
      return '';
    } else {
      return parent::montarBotaoFecharExcecao();
    }
  }


  public function montarBarraComandosSuperior($arrComandos) {
    array_unshift($arrComandos, '<a id="ancVoltarArvoreSuperior" href="javascript:seiVoltarArvoreProcesso()" class="btn" style="padding: 0px; float: left;display: none;" title="Voltar para a Árvore do Processo" tabindex="'.InfraPagina::$TAB_INI_BARRA_COMANDOS_SUPERIOR.'">
          <img src="'.$this->getDiretorioSvgGlobal() .'/voltar.svg" width="32" height="32">
        </a>');

    parent::montarBarraComandosSuperior($arrComandos);
  }

  public function montarBarraComandosInferior($arrComandos, $bolForcarMontagem = false) {
    array_unshift($arrComandos, '<a id="ancVoltarArvoreInferior" href="javascript:seiVoltarArvoreProcesso()" class="btn" style="padding: 0px; float: left;display: none;" title="Voltar para a Árvore do Processo" tabindex="'.InfraPagina::$TAB_INI_BARRA_COMANDOS_SUPERIOR.'">
          <img src="'.$this->getDiretorioSvgGlobal() .'/voltar.svg" width="32" height="32">
        </a>');

    parent::montarBarraComandosInferior($arrComandos, $bolForcarMontagem);
  }

  public function getArrStrAcoesSistemaMovel(){
    $arrStrAcoes = array();
    // $arrStrAcoes[] = $this->montarLinkMenuTexto(true);
    $arrStrAcoes[] = $this->montarSelectUnidades(true);
    return $arrStrAcoes;
  }

  public function montarJavaScript(){
    parent::montarJavaScript();
    echo '<script type="text/javascript" charset="iso-8859-1" src="js/sei.js?' . $this->getNumVersao() . '"></script>'."\n";
  }

  public function obterTiposMensagemExibicao()
  {
    return self::$TIPO_MSG_AVISO | self::$TIPO_MSG_ERRO;
  }

   public function getNumVersao(){
     return str_replace(' ','-',SEI_VERSAO . '-'.parent::getNumVersao());
   }

  public function adicionarJQuery(){
    return true;
  }

  public function setBolD3($bolD3){
    $this->bolD3 = $bolD3;
  }

  public function adicionarD3(){
    return $this->bolD3;
  }

  public function setBolAcoesSistema($bolAcoesSistema){
    $this->bolAcoesSistema = $bolAcoesSistema;
  }

  public function getStrSiglaOrgao(){
    return  $this->getObjInfraSessao()->getStrSiglaOrgaoUnidadeAtual() ;
  }

  public function getStrTextoBarraSuperior(){
    $strOrgaoTopo = ConfiguracaoSEI::getInstance()->getValor('PaginaSEI','OrgaoTopoJanela',false,'S');
    if ($strOrgaoTopo=='S') {
      return $this->getObjInfraSessao()->getStrDescricaoOrgaoSistema();
    }else if ($strOrgaoTopo=='U') {
      return $this->getObjInfraSessao()->getStrDescricaoOrgaoUsuario();
    }
    return null;
  }

  public function permitirXHTML(){
    return false;
  }

  public function abrirBody($strLocalizacao = '', $strAtributos = ''){
    parent::abrirBody($strLocalizacao, $strAtributos);
    global $SEI_MODULOS;
    foreach ($SEI_MODULOS as $seiModulo) {
      if (($ret = $seiModulo->executar('montarBotaoChatIA'))!=null){
          echo $ret;
      }
    }
    $this->montarBarraChaveAcessoInsegura();    
  }

  function montarBarraChaveAcessoInsegura(){
    if ($this->getTipoPagina()==self::$TIPO_PAGINA_COMPLETA || $this->getTipoPagina()==self::$TIPO_PAGINA_SEM_MENU) {
      if ($this->isBolProducao() && ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'ChaveAcesso', false) === CHAVE_ACESSO_PADRAO){
        echo '<div id="divInfraBarraSeguranca" class="infraBarraSeguranca"><span>ATENÇÃO: Chave de acesso padrão da base não deve ser utilizada em produção. Favor gerar novas chaves através menu Sistemas/Listar no SIP</span></div>';
      }
    }
  }
  
  public static function montarTitleTooltip($strTexto, $strTitulo = ''){

    $ret = '';

    if (SessaoSEI::getInstance()->getStrSinAcessibilidade()=='S'){
      if ($strTitulo!='' && $strTexto!=''){
        $ret = 'title="'.str_replace("\n",'&#13;',self::tratarHTML($strTitulo)).'&#13;'.str_replace("\n",'&#13;',self::tratarHTML($strTexto)).'" ';
      }else if ($strTitulo!=''){
        $ret = 'title="'.str_replace("\n",'&#13;',self::tratarHTML($strTitulo)).'" ';
      }else if ($strTexto!=''){
        $ret = 'title="'.str_replace("\n",'&#13;',self::tratarHTML($strTexto)).'" ';
      }
    }

    if ($strTitulo!=''){
      $ret .= 'onmouseover="return infraTooltipMostrar(\''.self::tratarHTML(self::formatarParametrosJavaScript($strTexto)).'\',\''.self::tratarHTML(self::formatarParametrosJavaScript($strTitulo)) . '\');"';
    }else{
      $ret .= 'onmouseover="return infraTooltipMostrar(\''.self::tratarHTML(self::formatarParametrosJavaScript($strTexto)).'\');"';
    }

    $ret .= ' onmouseout="return infraTooltipOcultar();"';

    $ret = str_replace('&amp;lt;hr&amp;gt;','<hr>', $ret);

    return $ret;
  }

  public static function getParametroRandom(){
    return 'seiRandom='.uniqid();
  }

  public function obterTipoJanelaLupas(){
    return self::$INFRA_LUPA_MODAL;
  }
}
