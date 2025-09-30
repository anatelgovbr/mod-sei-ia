<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 20/12/2023 - criado por sabino.colab
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaAdmObjetivoOdsINT extends InfraINT
{
	public static function consultarObjetivoProcedimento($dados)
	{

		$teveAlgumaSugestaoIa = "N";
		$itensSugeridos = [];
		$arrayItensMarcados = [];
		$i = 0;

		$objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
		$objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
		$objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
		$objMdIaAdmObjetivoOdsDTO->retStrDescricaoOds();
		$objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();
		$objMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->consultar($objMdIaAdmObjetivoOdsDTO);

		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrDescricaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrSinForteRelacao();
		$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmMetaOdsDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
		$objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
		$arrObjMdIaAdmMetaOdsDTO = $objMdIaAdmMetaOdsRN->listar($objMdIaAdmMetaOdsDTO);

		$html = self::montarHTMLCabecalho($objMdIaAdmObjetivoOdsDTO);

		$html .= self::montarHTMLTopoTabela($dados);

		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {

			$sugestaoIa = '';
			$disabledRacional = "disabled";
			$itemMarcadoAvaliacao = "N";
			$txtRacional = '';
			$hidenForteRelacao = self::forteRelacao(filter_var($dados['forteRelacao'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE), $objMdIaAdmMetaOdsDTO->getStrSinForteRelacao());
			$tr = "<tr id='{$objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds()}' style='{$hidenForteRelacao}'>";
			$itemSugerido = '';

			$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
			$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
			$objMdIaClassMetaOdsDTO->setDblIdProcedimento($dados["idProcedimento"]);
			$objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
			$objMdIaClassMetaOdsDTO->retNumIdMdIaAdmMetaOds();
			$objMdIaClassMetaOdsDTO->retStrRacional();
			$objMdIaClassMetaOdsDTO = (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);

			//SE NÃO HOUVE CLASSIFICACAO NÃO É NECESSARIO VERIFICAR MAIS NADA
			if ($objMdIaClassMetaOdsDTO) {

				$txtRacional = $objMdIaClassMetaOdsDTO->getStrRacional() ? $objMdIaClassMetaOdsDTO->getStrRacional() : '';

				if ($txtRacional != "") {
					$disabledRacional = "";
				}

				//IDENTIFICAR SE A META JÁ FOI CLASSIFICADA DEVIDO TER SIDO ALTERADO POR UM USUARIO INTERNO
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_PADRAO || $objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_AGENDAMENTO) {
					$itemMarcadoAvaliacao = "S";
					$arrayItensMarcados[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
				}

				//IDENTIFICAR SE HOUVE SUGESTAO IA
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_IA) {
					$teveAlgumaSugestaoIa = "S";
					$sugestaoIa = "sugeridoIa";
					$disabledRacional = "";
					$itemSugerido = 'ia';
					$itensSugeridos[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
					$tr = "<tr id='{$objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds()}' class='table-info itemSugeridoIa' style='{$hidenForteRelacao}'>";
				}

				// CASO SEJA SUGERIDO POR UM USUARIO EXTERNO E QUE AINDA NÃO FOI CLASSIFICADO POR UM USUARIO INTERNO
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_EXTERNO) {
					$itemSugerido = 'usuario_externo';
					$tr = "<tr id='{$objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds()}' class='itemSugeridoUE' style='background-color: #ffbf94b5';{$hidenForteRelacao}>";
					$disabledRacional = "";
					$itensSugeridos[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
				}
			}

			//MONTAR ROWS DA TABLELA
			$html .= $tr;
			$html .= self::montarTdCheckbox($i, $objMdIaAdmMetaOdsDTO, $itemMarcadoAvaliacao);
			$html .= self::montarTdIdentificacao($itemSugerido, $i, $objMdIaAdmMetaOdsDTO);
			$html .= self::montarTdDescricao($objMdIaAdmMetaOdsDTO);
			$html .= self::montarTdRacional($sugestaoIa, $i, $disabledRacional, $txtRacional);
			$html .= "</tr>";

			$i++;
		}
		$html .= self::montarHTMLRodapeTabla(count($arrObjMdIaAdmMetaOdsDTO), $dados, $teveAlgumaSugestaoIa, $arrayItensMarcados, $itensSugeridos);
		return mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
	}

	private static function forteRelacao($filtrarForteRelacao, $metaForteRelacao)
	{
		$hidenForteRelacao = '';
		if ($filtrarForteRelacao) {
			$hidenForteRelacao = $metaForteRelacao == 'N' ? 'display: none;' : '';
		}
		return $hidenForteRelacao;
	}

	private static function montarHTMLCabecalho($objMdIaAdmObjetivoOdsDTO)
	{

		$html = "";
		$html .= "<div class='col-12'>";
		$html .= "<div class='p-3 bg-light mb-3'>";
		$html .= "<div class='row'>";
		$html .= "<div class='col-2'>";
		$html .= "<img src='modulos/ia/imagens/Icones_Oficiais_ONU/" . $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() . "'/>";
		$html .= "</div>";
		$html .= "<div class='col-10'>";
		$html .= "<h4> Objetivo " . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . " - " . $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() . "</h4>";
		$html .= "<p>" . $objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds() . "</p>";
		$html .= "</div>";
		$html .= "</div>";
		$html .= "</div>";
		$html .= "</div>";

		$switchChecked = filter_var($_POST['forteRelacao'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 'checked' : '';
		$html .= '<div class="row" style="padding: 0px 20px" id="divForteRelacao">';
		$html .= '    <div class="col-12">';
		$html .= '        <h6 class="font-weight-bold d-flex align-items-center">';
		$html .= '            <label class="switch">';
		$html .= '            <input id="btn-checkbox" type="checkbox" ' . $switchChecked . ' onclick="atualizarListaObjetivos(this)">';
		$html .= '            <span class="slider round"></span>';
		$html .= '            </label>';
		$html .= '                Exibir apenas os que possuem forte relação com o órgão';
		$html .= '        </h6>';
		$html .= '    </div>';
		$html .= '</div>';

		return $html;
	}

	private static function montarHTMLTopoTabela($dados)
	{
		$txtAjuda = "Ao realizar a classificação ou desclassificação de qualquer Meta é obrigatório o preenchimento do campo Racional. Ao realizar a Confirmação ou Não Confirmação de sugestão do SEI IA também é obrigatório o preenchimento do campo Racional.
  
A classificação por especialistas com preenchimento obrigatório de Racional é fase inicial provisória essencial para o desenvolvimento dos algoritmos de IA para ter sugestões adequadas, devendo descrever o raciocínio utilizado e destacar o quê no teor dos documentos fundamentam a escolha da Meta, citando expressamente os protocolos dos documentos utilizados na mencionada fundamentação.";

		$html = self::consultarHistObjetivo($dados);
		$html .= "
            <div class='col-12'>
                <table class='infraTable' id='tabela_ordenada'>
                    <tbody>
                        <tr>
                            <th class='infraTh' width='1%'>" . PaginaSEI::getInstance()->getThCheck() . "</th>
                            <th class='infraTh' width='6%'>Identificação</th>
                            <th class='infraTh text-left' width='59%'>Descrição da Meta</th>";
		if (self::avaliacaoEspecializada()) {
			$html .= "
                            <th class='infraTh text-left' width='35%'>
                                Racional
                                <img align='top' src='/infra_css/imagens/ajuda.gif' name='ajuda' " . PaginaSEI::montarTitleTooltip($txtAjuda, 'Ajuda') . " class='infraImg'/>
                            </th>
                        ";
		}
		$html .= "       </tr>";

		return $html;
	}

	private static function montarTdCheckbox($i, $objMdIaAdmMetaOdsDTO, $itemMarcadoAvaliacao)
	{
		$html = '';
		$html .= "<td valign='top' style='vertical-align: middle;'>
                            " . PaginaSEI::getInstance()->getTrCheck($i, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $itemMarcadoAvaliacao) . "
                        </td>";
		return $html;
	}

	private static function montarTdIdentificacao($itemSugerido, $i, $objMdIaAdmMetaOdsDTO)
	{
		$txtAjudaIa = 'A classificação desta Meta é apenas uma sugestão realizada pela Inteligência Artificial do SEI. Não é, ainda, uma classificação efetiva nessa Meta.

É obrigatório avaliar a sugestão do SEI IA, podendo Confirmar (polegar para cima) ou Não Confirmar (polegar para baixo) a sugestão.

Nos dois casos (Confirmar e Não Confirmar), deve preencher o Racional com os fundamentos da avaliação sobre a sugestão realizada pelo SEI IA.';

		$txtAjudaUE = 'A classificação desta Meta é apenas uma sugestão realizada por Usuário Externo. Não é, ainda, uma classificação efetiva nessa Meta.

É obrigatório avaliar, podendo Confirmar (polegar para cima) ou Não Confirmar (polegar para baixo) a sugestão.

Nos dois casos (Confirmar e Não Confirmar), deve preencher o Racional com os fundamentos da avaliação realizada sobre a sugestão.';
		switch ($itemSugerido) {
			case 'ia':
				return "<td>
                          <div class='rounded-pill p-2 d-flex justify-content-around align-items-center' style='background: #EEE;'>
                              <span class='btn_thumbs up bubbly-button'></span>
                              <span style='color:#BBB'>----</span>
                              <span class='btn_thumbs down bubbly-button'></span>
                              <input type='hidden' class='hdnAproved' id='hdnLike_" . $i . "' name='hdnLike' value=''/>
                          </div>
                          <div style='margin-top: 28px'>
                              " . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "
                              <img src='modulos/ia/imagens/md_ia_icone.svg?"
					. Icone::VERSAO . "' " . PaginaSEI::montarTitleTooltip($txtAjudaIa, 'Ajuda') . "/>
                          </div>
                      </td>";
			case 'usuario_externo':
				return "<td>
                          <div class='rounded-pill p-2 d-flex justify-content-around align-items-center' style='background: #EEE;'>
                              <span class='btn_thumbs up bubbly-button'></span>
                              <span style='color:#BBB'>----</span>
                              <span class='btn_thumbs down bubbly-button'></span>
                              <input type='hidden' class='hdnAproved' id='hdnLike_" . $i . "' name='hdnLike' value=''/>
                          </div>
                          <div style='margin-top: 28px'>
                              " . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "
                              <img style='width:25px; height:25px' src='modulos/ia/imagens/md_ia_usu_externo.svg?"
					. Icone::VERSAO . "' " . PaginaSEI::montarTitleTooltip($txtAjudaUE, 'Ajuda') . "/>
                          </div>
                      </td>";

			default:
				return "<td>" . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "</td>";
		}
	}

	private static function montarTdDescricao($objMdIaAdmMetaOdsDTO)
	{
		$html = "";
		$html .= "<td>" . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta() . "</td>";

		return $html;
	}

	private static function montarTdRacional($sugestaoIa, $i, $disabled, $txtRacional)
	{
		$html = "";
		if (self::avaliacaoEspecializada()) {
			$html .= "<td>
			<div id='alertRacional_" . $i . "' style='border-left: 6px solid red; display: none;'>
				<label class='infraLabelOpcional' style='font-size: 11px;'>
				<span style='color: red; font-weight: bold;font-size: 11px;'>&nbsp;Atenção: </span>
														 É necessário preencher o racional para desclassificar.

				</label>
			</div>
			<textarea class='infraTextArea form-control " . $sugestaoIa . "'
						name='txaRacional' id='txaRacional_" . $i . "'
						rows='3'
						cols='150'
						onkeypress='return infraMascaraTexto(this, event, 4000);'
						maxlength='4000' " . $disabled . ">" . $txtRacional . "</textarea>
		</td>";
		}
		return $html;
	}

	private static  function montarHTMLRodapeTabla($numRegistros, $dados, $teveAlgumaSugestaoIa, $arrayItensMarcados, $itensSugeridos)
	{
		$html = "
                    </tbody>
                </table>
            </div>
            <input type='hidden' id='hdnInfraNroItens' name='hdnInfraNroItens' value='" . $numRegistros . "'>
            <input type='hidden' id='hdnIdObjetivo' name='hdnIdObjetivo' value='" . $dados["idObjetivo"] . "'>
            <input type='hidden' id='hdnIdProcedimento' name='hdnIdProcedimento' value='" . $dados["idProcedimento"] . "'>
            <input type='hidden' id='hdnSugestaoIa' name='hdnSugestaoIa' value='" . $teveAlgumaSugestaoIa . "'>
            <input type='hidden' id='hdnInfraItensSelecionados' name='hdnInfraItensSelecionados' value='" . implode(",", $arrayItensMarcados) . "'>
            <input type='hidden' id='hdnHistoricoSelecionados' name='hdnHistoricoSelecionados' value='" . implode(",", $arrayItensMarcados) . "'>
            <input type='hidden' id='hdnItensSugeridos' name='hdnItensSugeridos' value='" . implode(',', $itensSugeridos) . "'>
            <input type='hidden' id='hdnAlteracoesRealizadas' name='hdnAlteracoesRealizadas'>
        ";

		return $html;
	}

	public static function consultarHistObjetivo($dados)
	{
		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setDblIdProcedimento($dados["idProcedimento"]);
		$objMdIaHistClassDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
		$objMdIaHistClassDTO->retStrNomeUsuario();
		$objMdIaHistClassDTO->retStrDescricaoMeta();
		$objMdIaHistClassDTO->retStrSiglaUnidade();
		$objMdIaHistClassDTO->retStrIdentificacaoMeta();
		$objMdIaHistClassDTO->retDthCadastro();
		$objMdIaHistClassDTO->retStrDescricaoUnidade();
		$objMdIaHistClassDTO->retStrOperacao();
		$objMdIaHistClassDTO->retStrRacional();

		$objMdIaHistClassRN = new MdIaHistClassRN();
		$arrObjMdIaHistClassDTO = $objMdIaHistClassRN->listar($objMdIaHistClassDTO);

		$tabela = '';

		$strCaptionTabela = "Histórico";

		$tabela .= "<div class='col-12' id='divHistoricoOds' style='display: none'>";
		$tabela .= "<table class='infraTable'>";
		$tabela .= "<caption class='infraCaption'>" . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, count($arrObjMdIaHistClassDTO)) . "</caption>";
		$tabela .= "<tbody>";

		$tabela .= "<tr>";
		$tabela .= "<th class='infraTh' width='4%'>Data</th>";
		$tabela .= "<th class='infraTh' width='6%'>Usuário</th>";
		$tabela .= "<th class='infraTh' width='4%' style='align-content: center'>Unidade</th>";
		$tabela .= "<th class='infraTh' width='4%'>Identificação</th>";
		$tabela .= "<th class='infraTh' width='15%'>Descrição da Meta</th>";
		$tabela .= "<th class='infraTh' width='6%'>Operação</th>";
		if (self::avaliacaoEspecializada()) {
			$tabela .= "<th class='infraTh' width='11%'>Racional</th>";
		}
		$tabela .= "<tr/>";

		foreach ($arrObjMdIaHistClassDTO as $objMdIaHistClassDTO) {
			$tabela .= "<tr>";
			$tabela .= "<td style='text-align: center;'>" . $objMdIaHistClassDTO->getDthCadastro() . "</td>";
			$tabela .= "<td style='text-align: center;'>" . $objMdIaHistClassDTO->getStrNomeUsuario() . "</td>";
			$tabela .= '<td style="text-align: center;"><a alt="' . PaginaSEI::tratarHTML($objMdIaHistClassDTO->getStrDescricaoUnidade()) . '" title="' . PaginaSEI::tratarHTML($objMdIaHistClassDTO->getStrDescricaoUnidade()) . '" class="ancoraSigla">' . PaginaSEI::tratarHTML($objMdIaHistClassDTO->getStrSiglaUnidade()) . '</a></td>';
			$tabela .= '<td style="text-align: center;">' . $objMdIaHistClassDTO->getStrIdentificacaoMeta() . '</td>';
			$tabela .= "<td>" . $objMdIaHistClassDTO->getStrDescricaoMeta() . "</td>";
			$tabela .= "<td style='text-align: center;'>" . self::getOperacao($objMdIaHistClassDTO->getStrOperacao()) . "</td>";
			if (self::avaliacaoEspecializada()) {
				$tabela .= "<td>" . $objMdIaHistClassDTO->getStrRacional() . "</td>";
			}
			$tabela .= "<tr/>";
		}

		$tabela .= "</tbody>";
		$tabela .= "</table>";
		$tabela .= "</div>";


		return $tabela;
	}

	public static function autoCompletarTipoProcedimento($strPalavrasPesquisa)
	{

		$objTipoProcedimentoDTO = new TipoProcedimentoDTO();
		$objTipoProcedimentoDTO->retNumIdTipoProcedimento();
		$objTipoProcedimentoDTO->retStrNome();
		$objTipoProcedimentoDTO->setStrNome('%' . $strPalavrasPesquisa . '%', InfraDTO::$OPER_LIKE);
		$objTipoProcedimentoDTO->setNumMaxRegistrosRetorno(50);
		$objTipoProcedimentoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

		$objTipoProcedimentoRN = new TipoProcedimentoRN();

		$arrObjTipoProcedimentoDTO = $objTipoProcedimentoRN->listarRN0244($objTipoProcedimentoDTO);


		return $arrObjTipoProcedimentoDTO;
	}

	public static function consultarObjetivo($dados)
	{

		$arrayItensMarcados = [];

		$objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
		$objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
		$objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
		$objMdIaAdmObjetivoOdsDTO->retStrDescricaoOds();
		$objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();
		$objMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->consultar($objMdIaAdmObjetivoOdsDTO);

		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrDescricaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrSinForteRelacao();
		$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmMetaOdsDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
		$objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
		$arrObjMdIaAdmMetaOdsDTO = $objMdIaAdmMetaOdsRN->listar($objMdIaAdmMetaOdsDTO);

		$strCaptionTabela = "Metas";

		$descricaoObjetivo = "";
		$descricaoObjetivo .= "<div class='col-12'>";
		$descricaoObjetivo .= "<div class='p-3 bg-light mb-3'>";
		$descricaoObjetivo .= "<div class='row'>";
		$descricaoObjetivo .= "<div class='col-2'>";
		$descricaoObjetivo .= "<img src='modulos/ia/imagens/Icones_Oficiais_ONU/" . $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() . "'/>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "<div class='col-10'>";
		$descricaoObjetivo .= "<h4> Objetivo " . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . " - " . $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() . "</h4>";
		$descricaoObjetivo .= "<p>" . $objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds() . "</p>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "</div>";
		$table = "<div class='col-12'>";
		$table .= "<table class='infraTable' id='tabela_ordenada'>";
		$table .= "<tr>";
		$table .= "<caption class='infraCaption'>" . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, count($arrObjMdIaAdmMetaOdsDTO)) . "</caption>";
		$table .= "<th class='infraTh' width='6%'>Identificação</th>";
		$table .= "<th class='infraTh text-left' width='39%'>Descrição da Meta</th>";
		$table .= "<th class='infraTh center' width='15%'>Forte Relação Temática com o Órgão</th>";
		$table .= "<th class='infraTh center' width='30%'>Tipos de Processos para classificação automática</th>";
		$table .= "</tr>";
		$i = 0;
		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			$itemMarcado = $objMdIaAdmMetaOdsDTO->getStrSinForteRelacao() == 'S' ? true : false;
			if ($itemMarcado) {
				$arrayItensMarcados[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
			}

			$strItensSelTipoProcessos = self::recuperarTipoProcessoClassificacaoAutomatica($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
			$exibirTpProcesso = $itemMarcado ? '' : 'display: none';

			$table .= "<tr>";
			$table .= "<td class='text-center'>" . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "</td>";
			$table .= "<td>" . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta() . "</td>";
			$table .= "<td class='text-center' onclick='atualizarVisibilidadeCamposTipoProcesso()'>";
			$table .=  PaginaSEI::getInstance()->getTrCheck($i, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $itemMarcado);
			$table .= '</td>';
			$table .= "<td class='text-left row-meta' id='" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "'>";
			$table .= "    <div style='" . $exibirTpProcesso . "'>";
			$table .= "    <div class='row'>";
			$table .= "        <div class='col-xs-5 col-sm-8 col-md-10 col-lg-8'>";
			$table .= "            <label id='lblTipoProcessos' for='selTipoProcessos' accesskey='' class='infraLabelOpcional'>Tipos de Processos:</label>";
			$table .= "            <input type='text' id='txtTipoProcesso_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' name='txtTipoProcesso_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' class='infraText form-control' tabindex='<?= PaginaSEI::getInstance()->getProxTabDados() ?>'/>";
			$table .= "        </div>";
			$table .= "    </div>";
			$table .= "    <div class='row'>";
			$table .= "        <div class='col-sm-12 col-md-12 col-lg-12 col-xl-12'>";
			$table .= "            <div class='form-group'>";
			$table .= "                <div class='input-group'>";
			$table .= "                    <select id='selTipoProcessos_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' name='selTipoProcessos_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' size='3' multiple='multiple'";
			$table .= "                            class='infraSelect form-control'>";
			$table .=                         $strItensSelTipoProcessos;
			$table .= "                   </select>";
			$table .= "                   <div class='botoes'>";
			$table .= "                       <img id='imgExcluirTipoProcessos' onclick='objLupaTipoProcessos[" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "].remover();'";
			$table .= "                           src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/remover.svg?" . Icone::VERSAO . "'";
			$table .= "                           alt='Remover Tipo de Processo Selecionado'";
			$table .= "                           title='Remover Tipo de Processo Selecionado' class='infraImg'/>";
			$table .= "                       </div>";
			$table .= "                       <input type='hidden' id='hdnIdTipoProcesso_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' name='hdnIdTipoProcesso_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' value=''/>";
			$table .= "                </div>";
			$table .= "            </div>";
			$table .= "        </div>";
			$table .= "        <input type='hidden' id='hdnTipoProcessos_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' name='hdnTipoProcessos_" . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . "' value=''/>";
			$table .= "    </div>";
			$table .= "    </div>";
			$table .= "</td>";
			$table .= "</tr>";
			$i++;
		}
		$table .= "</table>";
		$table .= "</div>";
		$table .= "<input type='hidden' id='hdnInfraItensSelecionados' name='hdnInfraItensSelecionados' value='" . implode(",", $arrayItensMarcados) . "'>";
		$table .= "<input type='hidden' id='hdnIdObjetivo' name='hdnIdObjetivo' value='" . $dados["idObjetivo"] . "'>";

		$historico = self::consultarHistObjetivo($dados);
		$html = $descricaoObjetivo . $historico . $table;
		return mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
	}

	private static function recuperarTipoProcessoClassificacaoAutomatica($IdMdIaAdmMetaOds)
	{
		$MdIaAdmClassAutTpDTO = new MdIaAdmClassAutTpDTO();
		$MdIaAdmClassAutTpDTO->setNumIdMdIaAdmMetaOds($IdMdIaAdmMetaOds);
		$MdIaAdmClassAutTpDTO->retNumIdTipoProcedimento();
		$MdIaAdmClassAutTpDTO->retStrNomeTipoProcedimento();
		$arrMdIaAdmClassAutTpDTO = (new MdIaAdmClassAutTpRN())->listar($MdIaAdmClassAutTpDTO);

		$strItensSelTipoProcessos = "";

		foreach ($arrMdIaAdmClassAutTpDTO as $MdIaAdmClassAutTpDTO) {
			$strItensSelTipoProcessos .= "<option value='" . $MdIaAdmClassAutTpDTO->getNumIdTipoProcedimento() .  "'>" . $MdIaAdmClassAutTpDTO->getStrNomeTipoProcedimento() . "</option>";
		}

		return $strItensSelTipoProcessos;
	}

	public static function avaliacaoEspecializada()
	{
		$objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
		$objMdIaAdmOdsOnuDTO->retStrSinExibirAvaliacao();
		$objMdIaAdmOdsOnuDTO = (new MdIaAdmOdsOnuRN())->consultar($objMdIaAdmOdsOnuDTO);

		return $objMdIaAdmOdsOnuDTO->getStrSinExibirAvaliacao() == 'S' ? true : false;
	}

	private static function getOperacao($operacao)
	{
		switch ($operacao) {
			case MdIaHistClassRN::$OPERACAO_INSERT:
				return MdIaHistClassRN::$OPERACAO_INSERT_DESC;
			case MdIaHistClassRN::$OPERACAO_DELETE:
				return MdIaHistClassRN::$OPERACAO_DELETE_DESC;
			case MdIaHistClassRN::$OPERACAO_CONFIRMACAO:
				return MdIaHistClassRN::$OPERACAO_CONFIRMACAO_DESC;
			case MdIaHistClassRN::$OPERACAO_NAO_CONFIRMACAO:
				return MdIaHistClassRN::$OPERACAO_NAO_CONFIRMACAO_DESC;
			case MdIaHistClassRN::$OPERACAO_SOBRESCRITA:
				return MdIaHistClassRN::$OPERACAO_SOBRESCRITA_DESC;
			case MdIaHistClassRN::$OPERACAO_ATUALIZACAO:
				return MdIaHistClassRN::$OPERACAO_ATUALIZACAO_DESC;
		}
	}

	public static function classificarOdsWS($idProcedimento, $meta, $idUsuario, $staTipoUsuario)
	{
		// Recupera a Meta para utilizar na Classificação
		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->setStrIdentificacaoMeta($meta);
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$objMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->consultar($objMdIaAdmMetaOdsDTO);

		if (!$objMdIaAdmMetaOdsDTO) {
			throw new InfraException('A meta informada não existe ou não foi encontrada.');
		}

		return self::classificarMeta($idUsuario, $idProcedimento, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $staTipoUsuario);
	}

	private static function classificarMeta($idUsuario, $idProcedimento, $idMeta, $staTipoUsuario)
	{

		$verificacao = self::verificarExistenciaClassificacao($idProcedimento, $idMeta, $staTipoUsuario);

		if (!$verificacao['permitir']) {
			return [
				'status' => MdIaClassMetaOdsRN::$MSG_ERROR_RETORNO,
				'message' => $verificacao['retornoMsg']
			];
		}

		// Class meta ods novo registro
		$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
		$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaClassMetaOdsDTO->setNumIdUsuario($idUsuario);
		$objMdIaClassMetaOdsDTO->setStrSinSugestaoAceita('S');
		$objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaClassMetaOdsDTO->setStrRacional(null);
		$objMdIaClassMetaOdsDTO->setDblIdProcedimento($idProcedimento);
		$objMdIaClassMetaOdsDTO->setStrStaTipoUsuario($staTipoUsuario);
		(new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);

		$paramHistorico = [
			'IdProcedimento'        => $idProcedimento,
			'IdMeta'                => $idMeta,
			'IdUsuario'             => $idUsuario,
			'StaTipoUsuario'        => $staTipoUsuario,
			'Operacao'              => MdIaHistClassRN::$OPERACAO_INSERT,
			'IdMdIaHistClassSugest' => null
		];

		// Cadastrar o historico de "Sobrescrito por Usuário Externo"
		if (array_key_exists('tipoOperacao', $verificacao) && $verificacao['tipoOperacao'] == MdIaHistClassRN::$OPERACAO_SOBRESCRITA) {

			$objMdIaHistClassDTO = new MdIaHistClassDTO();
			$objMdIaHistClassDTO->setDblIdProcedimento($idProcedimento);
			$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
			$objMdIaHistClassDTO->setStrStaTipoUsuario(UsuarioRN::$TU_SISTEMA);
			$objMdIaHistClassDTO->setStrOperacao("I");
			$objMdIaHistClassDTO->retNumIdMdIaHistClass();
			$itemHistoricoSugeridoIA = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);

			$paramHistorico['Operacao'] = MdIaHistClassRN::$OPERACAO_SOBRESCRITA;
			$paramHistorico['IdMdIaHistClassSugest'] = $itemHistoricoSugeridoIA->getNumIdMdIaHistClass();
		}

		self::cadastrarHistorico($paramHistorico);

		self::removerNsaAplica($idProcedimento);

		$retorno['status'] = MdIaClassMetaOdsRN::$MSG_SUCESSO_RETORNO;
		$retorno['message'] = MdIaClassMetaOdsRN::$MSG_SUCESSO_RETORNO_WS;

		return $retorno;
	}

	private static function removerNsaAplica($idProcedimento)
	{
		$objMdIaOdsOnuNsaDTO = new MdIaOdsOnuNsaDTO();
		$objMdIaOdsOnuNsaRN = new MdIaOdsOnuNsaRN();
		$objMdIaOdsOnuNsaDTO->setDblIdProcedimento($idProcedimento);
		$objMdIaOdsOnuNsaDTO->retDblIdProcedimento();
		$registro = $objMdIaOdsOnuNsaRN->consultar($objMdIaOdsOnuNsaDTO);

		if ($registro) {
			$objMdIaOdsOnuNsaRN->excluir([$registro]);
		}
	}

	private static function cadastrarHistorico($param)
	{

		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setDblIdProcedimento($param['IdProcedimento']);
		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($param['IdMeta']);
		$objMdIaHistClassDTO->setStrOperacao($param['Operacao']);
		$objMdIaHistClassDTO->setNumIdUsuario($param['IdUsuario']);

		if (array_key_exists('IdMdIaHistClassSugest', $param)) {
			$objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($param['IdMdIaHistClassSugest']);
		}

		$objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaHistClassDTO->setStrSinSugestaoAceita(null);
		$objMdIaHistClassDTO->setStrRacional(null);
		$objMdIaHistClassDTO->setStrStaTipoUsuario($param['StaTipoUsuario']);
		(new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
	}

	private static function verificarExistenciaClassificacao($idProcedimento, $idMeta, $staTipoUsuario)
	{

		$verificacao = [];
		$verificacao['permitir'] = true;

		// Pesquisa na tela de classificacao apenas
		$objMdIaClassMetaOdsDTO = self::buscarMetaClassificada($idProcedimento, $idMeta);

		if ($objMdIaClassMetaOdsDTO) {

			// Verifica se é sugestão de UE sobre sugestão de IA para sobrescrever a sugestão da IA:
			if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == MdIaClassMetaOdsRN::$USUARIO_IA && $staTipoUsuario == MdIaClassMetaOdsRN::$USUARIO_EXTERNO) {
				$verificacao = [
					'permitir' => true,
					'tipoOperacao' => MdIaHistClassRN::$OPERACAO_SOBRESCRITA,
					''
				];
			} else {
				$verificacao = [
					'permitir' => false,
					'retornoMsg' => MdIaClassMetaOdsRN::$MSG_ERROR_JA_CADASTRADA
				];
			}
		}

		if (self::verificarSeJaFoiSugeridoPelaIa($idProcedimento, $idMeta) && $staTipoUsuario == MdIaClassMetaOdsRN::$USUARIO_IA) {
			$verificacao = [
				'permitir' => false,
				'retornoMsg' => MdIaClassMetaOdsRN::$MSG_ERROR_JA_SUGERIDA_IA
			];
		}

		return $verificacao;
	}

	private static function buscarMetaClassificada($idProcedimento, $idMeta)
	{

		$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
		$objMdIaClassMetaOdsDTO->setDblIdProcedimento($idProcedimento);
		$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
		$objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
		$objMdIaClassMetaOdsDTO->retStrSinSugestaoAceita();
		return (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
	}

	// verifica pelo historico se em algum momento a classificacao da meta já foi sugerido pela IA
	private static function verificarSeJaFoiSugeridoPelaIa($idProcedimento, $idMeta)
	{
		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setDblIdProcedimento($idProcedimento);
		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaHistClassDTO->setStrStaTipoUsuario(MdIaClassMetaOdsRN::$USUARIO_IA);
		$objMdIaHistClassDTO->retNumIdMdIaHistClass();
		$objMdIaHistClassDTO = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
		return $objMdIaHistClassDTO ? true : false;
	}

	public static function arrIdsObjetivosForteRelacao()
	{
		$arrIdsObjetivosForteRelacao = [];
		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->setStrSinForteRelacao("S");
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$arrObjMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->listar($objMdIaAdmMetaOdsDTO);

		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			if (!in_array($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds(), $arrIdsObjetivosForteRelacao)) {
				$arrIdsObjetivosForteRelacao[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds();
			}
		}

		return $arrIdsObjetivosForteRelacao;
	}

	public static function arrIdsMetasForteRelacao()
	{
		$arrIdsMetasForteRelacao = [];

		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->setStrSinForteRelacao("S");
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$arrObjMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->listar($objMdIaAdmMetaOdsDTO);

		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			$arrIdsMetasForteRelacao[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
		}

		return $arrIdsMetasForteRelacao;
	}

	public static function consultarObjetivoParaClassificacaoUsuExt($dados)
	{

		$metasPreSelecionadas = [];

		if (SessaoSEIExterna::getInstance()->isSetAtributo('METAS_SELECIONADAS')) {
			$metasPreSelecionadas = SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS');
		}

		$arrMetasSelecionadas = explode(",", $dados["MetasMarcadas"]);

		$objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
		$objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
		$objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$objMdIaAdmObjetivoOdsDTO->retStrNomeOds();
		$objMdIaAdmObjetivoOdsDTO->retStrDescricaoOds();
		$objMdIaAdmObjetivoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmObjetivoOdsRN = new MdIaAdmObjetivoOdsRN();
		$objMdIaAdmObjetivoOdsDTO = $objMdIaAdmObjetivoOdsRN->consultar($objMdIaAdmObjetivoOdsDTO);

		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrDescricaoMeta();
		$objMdIaAdmMetaOdsDTO->retStrSinForteRelacao();
		$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmMetaOdsDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
		$objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
		$arrObjMdIaAdmMetaOdsDTO = $objMdIaAdmMetaOdsRN->listar($objMdIaAdmMetaOdsDTO);

		$strCaptionTabela = "Metas";

		$descricaoObjetivo = "";

		$descricaoObjetivo .= "<div class='bg-light p-3 mb-2'>";
		$descricaoObjetivo .= "<div class='row'>";
		$descricaoObjetivo .= "<div class='col-2'>";
		$descricaoObjetivo .= "<img src='modulos/ia/imagens/Icones_Oficiais_ONU/" . $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() . "'/>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "<div class='col-10' style='margin-top: 25px'>";
		$descricaoObjetivo .= "<h4> Objetivo " . $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds() . " - " . $objMdIaAdmObjetivoOdsDTO->getStrNomeOds() . "</h4>";
		$descricaoObjetivo .= "<p>" . $objMdIaAdmObjetivoOdsDTO->getStrDescricaoOds() . "</p>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "</div>";
		$descricaoObjetivo .= "</div>";

		$temMetaForte = in_array('S', InfraArray::converterArrInfraDTO($arrObjMdIaAdmMetaOdsDTO, 'SinForteRelacao'));

		// ADICIONANDO SWITCH PARA AS METAS
		$descricaoObjetivo .= '<div class="row">';
		$descricaoObjetivo .= '<div class="col-12">';
		$descricaoObjetivo .= '<h6 class="d-flex align-items-center">';
		$descricaoObjetivo .= '<label class="switch mt-2">';
		$descricaoObjetivo .= '<input id="btn-checkbox" class="toggleMetasFortes" type="checkbox" ' . ($temMetaForte ? 'checked="checked"' : '') . ' onclick="atualizarListaMetas(this)">';
		$descricaoObjetivo .= '<span class="slider round"></span>';
		$descricaoObjetivo .= '</label>';
		$descricaoObjetivo .= '<strong>Exibir apenas as Metas desse Objetivo com forte relação temática com o(a) ' . SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno() . '</strong>';
		$descricaoObjetivo .= '</h6>';
		$descricaoObjetivo .= '</div>';
		$descricaoObjetivo .= '</div>';

		$table = "<div class='row'>";
		$table .= "<div class='col-12'>";
		$table .= "<table class='infraTable' id='tabela_ordenada'>";
		$table .= "<tr>";
		$table .= "<caption class='infraCaption'>" . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, count($arrObjMdIaAdmMetaOdsDTO)) . "</caption>";
		$table .= "<th class='infraTh' width='6%'></th>";
		$table .= "<th class='infraTh' width='6%'>Identificação</th>";
		$table .= "<th class='infraTh text-left' width='59%'>Descrição da Meta</th>";
		$table .= "</tr>";
		$i = 0;
		$table .= "<tbody>";

		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {

			$idMetaAtual = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
			$selecionado = (in_array($idMetaAtual, $arrMetasSelecionadas) || in_array($idMetaAtual, $metasPreSelecionadas)) ? true : false;

			$table .= "<tr class='item_meta " . ($objMdIaAdmMetaOdsDTO->getStrSinForteRelacao() != 'S' ? 'item_meta_fraca' : '') . "' style='display:" . ($temMetaForte ? '' : 'table-row') . "'>";
			$table .= '<td style="text-align: center">';
			$table .= PaginaSEI::getInstance()->getTrCheck($i, $idMetaAtual, $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $selecionado, 'Infra', 'onchange="salvarMetasSessao()"');
			$table .= '</td>';
			$table .= "<td style='text-align: center'>" . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "</td>";
			$table .= "<td>" . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta() . "</td>";
			$table .= "</tr>";

			$i++;
		}

		$table .= "</tbody>";
		$table .= "</table>";
		$table .= "</div>";
		$table .= "</div>";

		$html = $descricaoObjetivo . $table;
		return mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
	}

	public static function consultarObjetivoSelecionados($dados)
	{
		$arrMetasSelecionadas = explode(",", $dados['itensSelecionados']);

		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmMetaOds($arrMetasSelecionadas, InfraDTO::$OPER_IN);
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$arrObjMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->listar($objMdIaAdmMetaOdsDTO);

		$arrIdObjetivos = [];
		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			if (!in_array($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds(), $arrIdObjetivos)) {
				array_push($arrIdObjetivos, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds());
			}
		}

		return $arrIdObjetivos;
	}

	public static function salvarMetasSelecionadasSessao($dados)
	{

		$arrMetasSelecionadas = explode(",", $dados['itensSelecionados']);
		SessaoSEIExterna::getInstance()->setAtributo('METAS_SELECIONADAS', $arrMetasSelecionadas);
		return 'sucess';
	}

	public static function consultarMetasSelecionadasSessao()
	{

		$listaMetas = '';
		$metasPreSelecionadas = [];
		if (SessaoSEIExterna::getInstance()->isSetAtributo('METAS_SELECIONADAS')) {
			$metasPreSelecionadas = SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS');
		}

		if (!empty($metasPreSelecionadas)) {

			$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
			$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
			$objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
			$objMdIaAdmMetaOdsDTO->retStrDescricaoMeta();
			$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmMetaOds($metasPreSelecionadas, InfraDTO::$OPER_IN);
			$arrObjMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->listar($objMdIaAdmMetaOdsDTO);

			if (!empty($arrObjMdIaAdmMetaOdsDTO)) {
				foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {

					$textMeta = $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . ' - ' . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta();
					$strLimit = 100;
					$listaMetas .= '<h5 id="' . $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds() . '" class="' . (strlen($textMeta) > $strLimit ? 'metaItemLista' : '') . '" title="' . $textMeta . '" style="padding-bottom: 10px;">';
					$listaMetas .= '<img style="width:25px; height:20px; margin-right: 5px;" src="modulos/ia/imagens/sei_seta_direita.png">';
					if (strlen($textMeta) > $strLimit) {
						$listaMetas .= substr($textMeta, 0, $strLimit) . '<span class="points">...</span><span class="more" style="display:none;font-size: 1.25rem;">' . substr($textMeta, $strLimit) . '</span>';
					} else {
						$listaMetas .= $textMeta;
					}
					$listaMetas .= '</h5>';
				}
			}
		}

		return !empty($listaMetas) ? '<h6 class="alert alert-success mb-4">Sua demanda está contribuindo com os seguintes Objetivos de Desenvolvimento Sustentável da ONU:</h6>' . $listaMetas : '<h6 class="alert alert-warning">Sua demanda ainda não está contribuindo com os Objetivos de Desenvolvimento Sustentável da ONU.</h6>';
	}
}
