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
	public function consultarObjetivoProcedimento($dados)
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
		$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaAdmMetaOdsDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
		$objMdIaAdmMetaOdsRN = new MdIaAdmMetaOdsRN();
		$arrObjMdIaAdmMetaOdsDTO = $objMdIaAdmMetaOdsRN->listar($objMdIaAdmMetaOdsDTO);
		
		$objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
		$objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaClassificacaoOdsDTO->setNumIdProcedimento($dados["idProcedimento"]);
		$objMdIaClassificacaoOdsDTO->retNumIdMdIaClassificacaoOds();
		$objMdIaClassificacaoOdsDTO->retStrStaTipoUltimoUsuario();
		$objMdIaClassificacaoOdsDTO->setNumMaxRegistrosRetorno(1);
		$objMdIaClassificacaoOdsRN = new MdIaClassificacaoOdsRN();
		$objMdIaClassificacaoOdsDTO = $objMdIaClassificacaoOdsRN->consultar($objMdIaClassificacaoOdsDTO);
		
		
		
		$html = self::montarHTMLCabecalho($objMdIaAdmObjetivoOdsDTO);
		$html .= self::montarHTMLTopoTabela($dados);
		
		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			
			$sugestaoIa = '';
			$disabledRacional = "disabled";
			$itemMarcadoAvaliacao = "N";
			$txtRacional = '';
			$tr = "<tr>";
			$itemSugerido = '';
			
			if ($objMdIaClassificacaoOdsDTO) {
				$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
				$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
				$objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
				$objMdIaClassMetaOdsDTO->retStrStaTipoUltimoUsuario();
				$objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
				$objMdIaClassMetaOdsDTO->retNumIdMdIaAdmMetaOds();
				$objMdIaClassMetaOdsDTO->retStrRacional();
				$objMdIaClassMetaOdsDTO->setOrd('Cadastro', InfraDTO::$TIPO_ORDENACAO_DESC);
				$objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
				$objMdIaClassMetaOdsDTO = (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
			}
			
			//SE NÃO HOUVE CLASSIFICACAO NÃO É NECESSARIO VERIFICAR MAIS NADA
			if ($objMdIaClassificacaoOdsDTO && $objMdIaClassMetaOdsDTO) {
				
				$txtRacional = $objMdIaClassMetaOdsDTO->getStrRacional() ? $objMdIaClassMetaOdsDTO->getStrRacional() : '';
				
				//IDENTIFICAR SE A META JÁ FOI CLASSIFICADA DEVIDO TER SIDO ALTERADO POR UM USUARIO INTERNO
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == UsuarioRN::$TU_SIP) {
					$itemMarcadoAvaliacao = "S";
					$arrayItensMarcados[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
				}
				
				//IDENTIFICAR SE HOUVE SUGESTAO IA
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == UsuarioRN::$TU_SISTEMA) {
					$teveAlgumaSugestaoIa = "S";
					$sugestaoIa = "sugeridoIa";
					$disabledRacional = "";
					$itemSugerido = 'ia';
					$itensSugeridos[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
					$tr = "<tr class='table-info itemSugeridoIa'>";
				}
				
				// CASO SEJA SUGERIDO POR UM USUARIO EXTERNO E QUE AINDA NÃO FOI CLASSIFICADO POR UM USUARIO INTERNO
				if ($objMdIaClassMetaOdsDTO->getStrStaTipoUsuario() == UsuarioRN::$TU_EXTERNO) {
					$itemSugerido = 'usuario_externo';
					$tr = "<tr class='itemSugeridoUE' style='background-color: #ffbf94b5'>";
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
		return utf8_encode($html);
	}
	
	private function montarHTMLCabecalho($objMdIaAdmObjetivoOdsDTO)
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
		
		return $html;
	}
	
	private function montarHTMLTopoTabela($dados)
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
	
	private function montarTdCheckbox($i, $objMdIaAdmMetaOdsDTO, $itemMarcadoAvaliacao)
	{
		$html = '';
		$html .= "<td valign='top' style='vertical-align: middle;'>
                            " . PaginaSEI::getInstance()->getTrCheck($i, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $itemMarcadoAvaliacao) . "
                        </td>";
		return $html;
	}
	
	private function montarTdIdentificacao($itemSugerido, $i, $objMdIaAdmMetaOdsDTO)
	{
		$txtAjudaIa = 'A classificação desta Meta é apenas uma sugestão realizada pela Inteligência Artificial do SEI. Não é, ainda, uma classificação efetiva nessa Meta.

É obrigatório avaliar a sugestão do SEI IA, podendo Confirmar (polegar para cima) ou Não Confirmar (polegar para baixo) a sugestão.

Nos dois casos (Confirmar e Não Confirmar), deve preencher o Racional com os fundamentos da avaliação sobre a sugestão realizada pelo SEI IA.';
		
		$txtAjudaUE = 'A classificação desta Meta é apenas uma sugestão realizada por Usuário Externo. Não é, ainda, uma classificação efetiva nessa Meta.

É obrigatório avaliar, podendo Confirmar (polegar para cima) ou Não Confirmar (polegar para baixo) a sugestão.

Nos dois casos (Confirmar e Não Confirmar), deve preencher o Racional com os fundamentos da avaliação realizada sobre a sugestão.';
		switch ($itemSugerido) {
			case 'ia' :
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
			case 'usuario_externo' :
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
	
	private function montarTdDescricao($objMdIaAdmMetaOdsDTO)
	{
		$html = "";
		$html .= "<td>" . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta() . "</td>";
		
		return $html;
	}
	
	private function montarTdRacional($sugestaoIa, $i, $disabled, $txtRacional)
	{
		$html = "";
		if (self::avaliacaoEspecializada()) {
			$html .= "<td>
                    <textarea class='infraTextArea form-control " . $sugestaoIa . "'
                              name='txaRacional' id='txaRacional_" . $i . "'
                              rows='3'
                              cols='150'
                              onkeypress='return infraMascaraTexto(this, event, 500);'
                              maxlength='500' " . $disabled . ">" . $txtRacional . "</textarea>
                </td>";
		}
		return $html;
	}
	
	private function montarHTMLRodapeTabla($numRegistros, $dados, $teveAlgumaSugestaoIa, $arrayItensMarcados, $itensSugeridos)
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
	
	public function consultarHistObjetivo($dados)
	{
		$objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
		$objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["idObjetivo"]);
		$objMdIaClassificacaoOdsDTO->setNumIdProcedimento($dados["idProcedimento"]);
		$objMdIaClassificacaoOdsDTO->retNumIdMdIaClassificacaoOds();
		$objMdIaClassificacaoOdsRN = new MdIaClassificacaoOdsRN();
		$objMdIaClassificacaoOdsDTO = $objMdIaClassificacaoOdsRN->consultar($objMdIaClassificacaoOdsDTO);
		if (!empty($objMdIaClassificacaoOdsDTO)) {
			
			$objMdIaHistClassDTO = new MdIaHistClassDTO();
			$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
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
		}
		
		return $tabela;
		
	}
	
	public function consultarObjetivo($dados)
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
		$table .= "<th class='infraTh text-left' width='59%'>Descrição da Meta</th>";
		$table .= "<th class='infraTh center' width='30%'>Forte Relação Temática com o Órgão</th>";
		$table .= "</tr>";
		$i = 0;
		foreach ($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO) {
			$itemMarcado = $objMdIaAdmMetaOdsDTO->getStrSinForteRelacao() == 'S' ? true : false;
			if ($itemMarcado) {
				$arrayItensMarcados[] = $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds();
			}
			
			$table .= "<tr>";
			$table .= "<td class='text-center'>" . $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta() . "</td>";
			$table .= "<td>" . $objMdIaAdmMetaOdsDTO->getStrDescricaoMeta() . "</td>";
			$table .= "<td class='text-center'>";
			$table .= PaginaSEI::getInstance()->getTrCheck($i, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $itemMarcado);
			$table .= '</td>';
			$table .= "</tr>";
			$i++;
		}
		$table .= "</table>";
		$table .= "</div>";
		$table .= "<input type='hidden' id='hdnInfraItensSelecionados' name='hdnInfraItensSelecionados' value='" . implode(",", $arrayItensMarcados) . "'>";
		$table .= "<input type='hidden' id='hdnIdObjetivo' name='hdnIdObjetivo' value='" . $dados["idObjetivo"] . "'>";
		
		$historico = self::consultarHistObjetivo($dados);
		$html = $descricaoObjetivo . $historico . $table;
		return utf8_encode($html);
	}
	
	public function avaliacaoEspecializada()
	{
		$objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
		$objMdIaAdmOdsOnuDTO->retStrSinExibirAvaliacao();
		$objMdIaAdmOdsOnuDTO = (new MdIaAdmOdsOnuRN())->consultar($objMdIaAdmOdsOnuDTO);
		
		return $objMdIaAdmOdsOnuDTO->getStrSinExibirAvaliacao() == 'S' ? true : false;
	}
	
	private function getOperacao($operacao)
	{
		switch ($operacao) {
			case MdIaHistClassRN::$OPERACAO_INSERT :
				return MdIaHistClassRN:: $OPERACAO_INSERT_DESC;
			case MdIaHistClassRN::$OPERACAO_DELETE :
				return MdIaHistClassRN:: $OPERACAO_DELETE_DESC;
			case MdIaHistClassRN::$OPERACAO_CONFIRMACAO :
				return MdIaHistClassRN:: $OPERACAO_CONFIRMACAO_DESC;
			case MdIaHistClassRN::$OPERACAO_NÃO_CONFIRMACAO :
				return MdIaHistClassRN:: $OPERACAO_NÃO_CONFIRMACAO_DESC;
			case MdIaHistClassRN::$OPERACAO_SOBRESCRITA :
				return MdIaHistClassRN:: $OPERACAO_SOBRESCRITA_DESC;
		}
	}
	
	public function classificarOdsWS($idProcedimento, $meta, $idUsuario = null, $staTipoUsuario)
	{
		
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$idUsuario = $idUsuario ? $idUsuario : $objInfraParametro->getValor(MdIaClassificacaoOdsRN::$MODULO_IA_ID_USUARIO_SISTEMA, false);
		
		// Recupera a Meta para utilizar na Classificação
		$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
		$objMdIaAdmMetaOdsDTO->setStrIdentificacaoMeta($meta);
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
		$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmObjetivoOds();
		$objMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->consultar($objMdIaAdmMetaOdsDTO);
		
		if (!$objMdIaAdmMetaOdsDTO) {
			throw new InfraException('A meta informada não existe ou não foi encontrada.');
		}
		
		// Recupera a Classificação
		$objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
		$objMdIaClassificacaoOdsDTO->setNumIdProcedimento($idProcedimento);
		$objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds());
		$objMdIaClassificacaoOdsDTO->retNumIdProcedimento();
		$objMdIaClassificacaoOdsDTO->retNumIdMdIaClassificacaoOds();
		$objMdIaClassificacaoOdsDTO->retStrStaTipoUltimoUsuario();
		$objMdIaClassificacaoOdsDTO = (new MdIaClassificacaoOdsRN())->consultar($objMdIaClassificacaoOdsDTO);
		
		// Caso a Classificação JÁ EXISTA realiza a ATUALIZAÇÃO
		if ($objMdIaClassificacaoOdsDTO) {
			return self::alterarClassificacao($idUsuario, $objMdIaClassificacaoOdsDTO, $objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds(), $staTipoUsuario);
		}
		
		// Caso a Classificação NÃO EXISTA realiza o CADASTRO
		return self::cadastrarClassificacao($idUsuario, $idProcedimento, $objMdIaAdmMetaOdsDTO, $staTipoUsuario);
		
	}
	
	private function alterarClassificacao($idUsuario, $objMdIaClassificacaoOdsDTO, $idMeta, $staTipoUsuario)
	{
		
		$verificacao = self::verificarExistenciaClassificacao($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds(), $idMeta, $objMdIaClassificacaoOdsDTO, $staTipoUsuario);
		
		if (!$verificacao['permitir']) {
			return [
				'status' => MdIaClassificacaoOdsRN::$MSG_ERROR_RETORNO,
				'message' => $verificacao['retornoMsg']
			];
		}
		
		$objMdIaClassificacaoOdsRN = new MdIaClassificacaoOdsRN();
		$objMdIaClassificacaoOdsDTO->setStrStaTipoUltimoUsuario($staTipoUsuario);
		$objMdIaClassificacaoOdsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
		$objMdIaClassificacaoOdsRN->alterar($objMdIaClassificacaoOdsDTO);
		
		// Class meta ods novo registro
		$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
		$objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
		$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaClassMetaOdsDTO->setNumIdUsuario($idUsuario);
		$objMdIaClassMetaOdsDTO->setStrSinSugestaoAceita('S');
		$objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaClassMetaOdsDTO->setStrRacional(null);
		(new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);
		
		// Cadastrar o historico da classificação
//		$objMdIaHistClassDTO = new MdIaHistClassDTO();
//		$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
//		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
//		$objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
//		$objMdIaHistClassDTO->setNumIdUsuario($idUsuario);
//		$objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
//		$objMdIaHistClassDTO->setStrSinSugestaoAceita(null);
//		$objMdIaHistClassDTO->setStrRacional(null);
//		(new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
		
		// Cadastrar o historico de "Sobrescrito por Usuário Externo"
		if(array_key_exists('tipoOperacao', $verificacao) && $verificacao['tipoOperacao'] == MdIaHistClassRN::$OPERACAO_SOBRESCRITA){
			
			$objMdIaHistClassDTO = new MdIaHistClassDTO();
			$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
			$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
			$objMdIaHistClassDTO->setStrStaTipoUsuario(UsuarioRN::$TU_SISTEMA);
			$objMdIaHistClassDTO->setStrOperacao("I");
			$objMdIaHistClassDTO->retNumIdMdIaHistClass();
			$itemHistoricoSugeridoIA = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
			
			self::cadastrarHistorico([
				'IdClassificacao'       => $objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds(),
				'IdMeta'                => $idMeta,
				'Operacao'              => MdIaHistClassRN::$OPERACAO_SOBRESCRITA,
				'IdUsuario'             => $idUsuario,
				'IdMdIaHistClassSugest' => $itemHistoricoSugeridoIA->getNumIdMdIaHistClass()
			]);
			
		}else{
			
			// Cadastrar o historico da classificação
			self::cadastrarHistorico([
				'IdClassificacao'   => $objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds(),
				'IdMeta'            => $idMeta,
				'Operacao'          => MdIaHistClassRN::$OPERACAO_INSERT,
				'IdUsuario'         => $idUsuario
			]);
			
		}
		
		$retorno['status'] = MdIaClassificacaoOdsRN::$MSG_SUCESSO_RETORNO;
		$retorno['message'] = MdIaClassificacaoOdsRN::$MSG_SUCESSO_RETORNO_WS;
		
		return $retorno;
		
	}
	
	private function cadastrarHistorico($novoHistorico){
		
		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($novoHistorico['IdClassificacao']);
		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($novoHistorico['IdMeta']);
		$objMdIaHistClassDTO->setStrOperacao($novoHistorico['Operacao']);
		$objMdIaHistClassDTO->setNumIdUsuario($novoHistorico['IdUsuario']);
		
		if(array_key_exists('IdMdIaHistClassSugest', $novoHistorico)){
			$objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($novoHistorico['IdMdIaHistClassSugest']);
		}
		
		$objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaHistClassDTO->setStrSinSugestaoAceita(null);
		$objMdIaHistClassDTO->setStrRacional(null);
		(new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
		
	}
	
	private function cadastrarClassificacao($idUsuario, $idProtocolo, $objMdIaAdmMetaOdsDTO, $staTipoUsuario)
	{
		
		$objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
		$objMdIaClassificacaoOdsDTO->setNumIdProcedimento($idProtocolo);
		$objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmObjetivoOds());
		$objMdIaClassificacaoOdsDTO->setStrStaTipoUltimoUsuario($staTipoUsuario);
		$objMdIaClassificacaoOdsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
		$objMdIaClassificacaoOdsDTO = (new MdIaClassificacaoOdsRN())->cadastrar($objMdIaClassificacaoOdsDTO);
		
		$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
		$objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
		$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
		$objMdIaClassMetaOdsDTO->setNumIdUsuario($idUsuario);
		$objMdIaClassMetaOdsDTO->setNumIdUnidade(null);
		$objMdIaClassMetaOdsDTO->setStrSinSugestaoAceita('S');
		$objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaClassMetaOdsDTO->setStrRacional(null);
		$objMdIaClassMetaOdsDTO = (new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);
		
		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds());
		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds());
		$objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
		$objMdIaHistClassDTO->setNumIdUsuario($idUsuario);
		$objMdIaHistClassDTO->setNumIdUnidade(null);
		$objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
		$objMdIaHistClassDTO->setStrSinSugestaoAceita(null);
		$objMdIaHistClassDTO->setStrRacional(null);
		(new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
		
		$retorno = [
			'status' =>  MdIaClassificacaoOdsRN::$MSG_SUCESSO_RETORNO,
			'message' => MdIaClassificacaoOdsRN::$MSG_SUCESSO_RETORNO_WS
		];
		
		return $retorno;
		
	}
	
	private function verificarExistenciaClassificacao($idClassificacao, $idMeta, $objMdIaClassificacaoOdsDTO, $staTipoUsuario)
	{
		
		$verificacao = [];
		$verificacao['permitir'] = true;
		
		// Pesquisa na tela de classificacao apenas
		$existeCadastroMeta = self::verificarSeJaExisteCadastroDaMeta($idClassificacao, $idMeta);
		
		if (!empty($existeCadastroMeta)) {
			
			// Verifica se é sugestão de UE sobre sugestão de IA para sobrescrever a sugestão da IA:
			if($existeCadastroMeta->getStrStaTipoUsuario() == UsuarioRN::$TU_SISTEMA && $staTipoUsuario == MdIaClassificacaoOdsRN::$USUARIO_EXTERNO){
				
				$verificacao = [
					'permitir' => true,
					'tipoOperacao' => MdIaHistClassRN::$OPERACAO_SOBRESCRITA,
					''
				];
				
			}else{
				
				// Recupera dos dados do Processo:
				$objProcedimento = new ProcedimentoDTO();
				$objProcedimento->setDblIdProcedimento($objMdIaClassificacaoOdsDTO->getNumIdProcedimento());
				$objProcedimento->retStrProtocoloProcedimentoFormatado();
				$objProcedimento = (new ProcedimentoRN())->consultarRN0201($objProcedimento);
				
				$verificacao = [
					'permitir' => false,
					'retornoMsg' => str_replace('@numProcessoFormatado', $objProcedimento->getStrProtocoloProcedimentoFormatado(), MdIaClassificacaoOdsRN::$MSG_ERROR_JA_CADASTRADA)
				];
				
			}
			
		}
		
		if (self::verificarSeJaFoiSugeridoPelaIa($idClassificacao, $idMeta) && $staTipoUsuario == MdIaClassificacaoOdsRN::$USUARIO_IA) {
			$verificacao = [
				'permitir' => false,
				'retornoMsg' => MdIaClassificacaoOdsRN::$MSG_ERROR_JA_SUGERIDA_IA
			];
			
		}
		
		return $verificacao;
		
	}
	
	private function verificarSeJaExisteCadastroDaMeta($idClassificacao, $idMeta)
	{
		
		$objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
		$objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
		$objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
		$objMdIaClassMetaOdsDTO->retStrStaTipoUsuario();
		$objMdIaClassMetaOdsDTO->retStrStaTipoUltimoUsuario();
		$objMdIaClassMetaOdsDTO->retStrSinSugestaoAceita();
		return (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
		
	}
	
	// verifica pelo historico se em algum momento a classificacao da meta já foi sugerido pela IA
	private function verificarSeJaFoiSugeridoPelaIa($idClassificacao, $idMeta)
	{
		$objMdIaHistClassDTO = new MdIaHistClassDTO();
		$objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
		$objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($idMeta);
		$objMdIaHistClassDTO->setStrStaTipoUsuario(UsuarioRN::$TU_SISTEMA);
		$objMdIaHistClassDTO->retNumIdMdIaHistClass();
		$objMdIaHistClassDTO = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
		return $objMdIaHistClassDTO ? true : false;
	}
	
	public function arrIdsObjetivosForteRelacao()
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
	
	public function arrIdsMetasForteRelacao()
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
	
	public function consultarObjetivoParaClassificacaoUsuExt($dados)
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
		$descricaoObjetivo .= '<input id="btn-checkbox" class="toggleMetasFortes" type="checkbox" '.($temMetaForte ? 'checked="checked"' : '').' onclick="atualizarListaMetas(this)">';
		$descricaoObjetivo .= '<span class="slider round"></span>';
		$descricaoObjetivo .= '</label>';
		$descricaoObjetivo .= '<strong>Exibir apenas as Metas desse Objetivo com forte relação temática com o(a) '.SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno().'</strong>';
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
			
			$table .= "<tr class='item_meta ".($objMdIaAdmMetaOdsDTO->getStrSinForteRelacao() != 'S' ? 'item_meta_fraca' : '')."' style='display:".($temMetaForte ? '' : 'table-row')."'>";
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
		return utf8_encode($html);
	}
	
	public function consultarObjetivoSelecionados($dados)
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
	
	public function salvarMetasSelecionadasSessao($dados)
	{
		
		$arrMetasSelecionadas = explode(",", $dados['itensSelecionados']);
		SessaoSEIExterna::getInstance()->setAtributo('METAS_SELECIONADAS', $arrMetasSelecionadas);
		return 'sucess';
		
	}
	
	public function consultarMetasSelecionadasSessao()
	{
		
		$listaMetas = '';
		$metasPreSelecionadas = [];
		if(SessaoSEIExterna::getInstance()->isSetAtributo('METAS_SELECIONADAS')){
			$metasPreSelecionadas = SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS');
		}
		
		if(!empty($metasPreSelecionadas)){
			
			$objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
			$objMdIaAdmMetaOdsDTO->retNumIdMdIaAdmMetaOds();
			$objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
			$objMdIaAdmMetaOdsDTO->retStrDescricaoMeta();
			$objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmMetaOds($metasPreSelecionadas, InfraDTO::$OPER_IN);
			$arrObjMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->listar($objMdIaAdmMetaOdsDTO);
			
			if(!empty($arrObjMdIaAdmMetaOdsDTO)){
				foreach($arrObjMdIaAdmMetaOdsDTO as $objMdIaAdmMetaOdsDTO){
					
					$textMeta = $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta().' - '.$objMdIaAdmMetaOdsDTO->getStrDescricaoMeta();
					$strLimit = 100;
					$listaMetas .= '<h5 id="'.$objMdIaAdmMetaOdsDTO->getNumIdMdIaAdmMetaOds().'" class="'.(strlen($textMeta) > $strLimit ? 'metaItemLista' : '').'" title="'.$textMeta.'" style="padding-bottom: 10px;">';
					$listaMetas .= '<img style="width:25px; height:20px; margin-right: 5px;" src="modulos/ia/imagens/sei_seta_direita.png">';
					if(strlen($textMeta) > $strLimit){
						$listaMetas .= substr($textMeta, 0, $strLimit).'<span class="points">...</span><span class="more" style="display:none;font-size: 1.25rem;">'.substr($textMeta, $strLimit).'</span>';
					}else{
						$listaMetas .= $textMeta;
					}
					$listaMetas .= '</h5>';
					
				}
			}
		}
		
		return !empty($listaMetas) ? '<h6 class="alert alert-success mb-4">Sua demanda está contribuindo com os seguintes Objetivos de Desenvolvimento Sustentável da ONU:</h6>'.$listaMetas : '<h6 class="alert alert-warning">Sua demanda ainda não está contribuindo com os Objetivos de Desenvolvimento Sustentável da ONU.</h6>';
		
	}
	
}
