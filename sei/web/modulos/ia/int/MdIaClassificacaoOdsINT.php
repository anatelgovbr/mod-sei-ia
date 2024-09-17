<?
    /**
     * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
     *
     * 29/12/2023 - criado por sabino.colab
     *
     * Versão do Gerador de Código: 1.43.3
     */

    require_once dirname(__FILE__) . '/../../../SEI.php';

    class MdIaClassificacaoOdsINT extends InfraINT
    {
        function salvarClassificacaoOds($dados)
        {
	
	        $metasSelecionadasAnteriormente = explode(",", $dados["hdnHistoricoSelecionados"]);
            $metasSelecionadas = explode(",", $dados["hdnInfraItensSelecionados"]);
            $itensSugeridos = explode(",", $dados["hdnItensSugeridos"]);
            
            // Montando os arrays de trabalho:
	        $itensAdicionados	= array_diff($metasSelecionadas, array_merge($metasSelecionadasAnteriormente, $itensSugeridos));
	        $itensRemovidos 	= array_diff($metasSelecionadasAnteriormente, array_diff($metasSelecionadas, $itensSugeridos));
	        $sugestoesAceitas	= array_intersect($metasSelecionadas, $itensSugeridos);
	        $sugestoesRecusadas = array_diff($itensSugeridos, array_diff($metasSelecionadas, $metasSelecionadasAnteriormente));
	        
	        if(!empty(array_merge($itensAdicionados, $itensRemovidos, $sugestoesAceitas, $sugestoesRecusadas))){
             
            	$objMdIaClassificacaoOdsDTO = new MdIaClassificacaoOdsDTO();
                $objMdIaClassificacaoOdsDTO->setNumIdProcedimento($dados["hdnIdProcedimento"]);
                $objMdIaClassificacaoOdsDTO->setNumIdMdIaAdmObjetivoOds($dados["hdnIdObjetivo"]);
                $objMdIaClassificacaoOdsDTO->retNumIdMdIaClassificacaoOds();
                $classificacaoAnterior = (new MdIaClassificacaoOdsRN())->consultar($objMdIaClassificacaoOdsDTO);

                if(is_null($classificacaoAnterior)) {
                	
                    $objMdIaClassificacaoOdsDTO->setStrStaTipoUltimoUsuario(MdIaClassificacaoOdsRN::$USUARIO_PADRAO);
	                $objMdIaClassificacaoOdsDTO->setDthAlteracao(InfraData::getStrDataHoraAtual());
                    $objMdIaClassificacaoOdsDTO = (new MdIaClassificacaoOdsRN())->cadastrar($objMdIaClassificacaoOdsDTO);
                    $idClassificacao = $objMdIaClassificacaoOdsDTO->getNumIdMdIaClassificacaoOds();
                
                } else {
                	
                    $classificacaoAnterior->setStrStaTipoUltimoUsuario(MdIaClassificacaoOdsRN::$USUARIO_PADRAO);
                    $classificacaoAnterior->setDthAlteracao(InfraData::getStrDataHoraAtual());
	                (new MdIaClassificacaoOdsRN())->alterar($classificacaoAnterior);
	                $idClassificacao = $classificacaoAnterior->getNumIdMdIaClassificacaoOds();
                    
                }
                
                // Adicionando novas classificações que não foram sugeridas
                if(!empty($itensAdicionados)){
	
	                foreach($itensAdicionados as $itemAdicionado){
		
		                if(is_numeric($itemAdicionado)){
			
			                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
			                $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
			                $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($itemAdicionado);
			                $objMdIaClassMetaOdsDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
			                $objMdIaClassMetaOdsDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			                $objMdIaClassMetaOdsDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
			                $objMdIaClassMetaOdsDTO->setStrRacional($dados["racionais"]["txaRacional_".$itemAdicionado]);
			                (new MdIaClassMetaOdsRN())->cadastrar($objMdIaClassMetaOdsDTO);
			
			                $objMdIaHistClassDTO = new MdIaHistClassDTO();
			                $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
			                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($itemAdicionado);
			                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
			                $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
			                $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			                $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
			                $objMdIaHistClassDTO->setStrRacional($dados["racionais"]["txaRacional_".$itemAdicionado]);
			                (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
			
		                }
		
	                }
                	
                }
                
                // Aceitando sugestoes de IA ou UE
                if(!empty($sugestoesAceitas)){
	
	                foreach($sugestoesAceitas as $sugestaoAceita){
		
		                if(is_numeric($sugestaoAceita)){
			
			                $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
			                $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
			                $objMdIaClassMetaOdsDTO->retNumIdUsuario();
			                $objMdIaClassMetaOdsDTO->retNumIdUnidade();
			                $objMdIaClassMetaOdsDTO->retDthCadastro();
			                $objMdIaClassMetaOdsDTO->retStrSinSugestaoAceita();
			                $objMdIaClassMetaOdsDTO->retStrRacional();
			                $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
			                $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
			                $objMdIaClassMetaOdsDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
			                $objMdIaClassMetaOdsDTO->setNumMaxRegistrosRetorno(1);
			                $sugestaoAAceitar = (new MdIaClassMetaOdsRN())->consultar($objMdIaClassMetaOdsDTO);
			
			                if(!empty($sugestaoAAceitar)){
				
				                $sugestaoAAceitar->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
				                $sugestaoAAceitar->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				                $sugestaoAAceitar->setDthCadastro(InfraData::getStrDataHoraAtual());
				                $sugestaoAAceitar->setStrSinSugestaoAceita("S");
				                $sugestaoAAceitar->setStrRacional($dados["racionais"]["txaRacional_".$sugestaoAceita]);
				                (new MdIaClassMetaOdsRN())->alterar($sugestaoAAceitar);
				
				                $objMdIaHistClassDTO = new MdIaHistClassDTO();
				                $objMdIaHistClassDTO->retNumIdMdIaHistClass();
				                $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
				                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
				                $objMdIaHistClassDTO->setStrStaTipoUsuario([UsuarioRN::$TU_SISTEMA, UsuarioRN::$TU_EXTERNO], InfraDTO::$OPER_IN);
				                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
				                $objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
				                $objMdIaHistClassDTO->setNumMaxRegistrosRetorno(1);
				                $itemHistoricoSugerido = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
				
				                if(!empty($itemHistoricoSugerido)){
					
					                $objMdIaHistClassDTO = new MdIaHistClassDTO();
					                $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
					                $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoAceita);
					                $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_CONFIRMACAO);
					                $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
					                $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
					                $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
					                $objMdIaHistClassDTO->setStrRacional($dados["racionais"]["txaRacional_".$sugestaoAceita]);
					                $objMdIaHistClassDTO->setStrSinSugestaoAceita("S");
					                $objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($itemHistoricoSugerido->getNumIdMdIaHistClass());
					                (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
					
				                }
			                
			                }
			
		                }
		
	                }
                	
                }
		
		        // Removendo classificações anteriores
		        if(!empty($itensRemovidos)){
			
			        foreach($itensRemovidos as $itemRemovido){
				
				        if(is_numeric($itemRemovido)){
					
					        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
					        $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
					        $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($itemRemovido);
					        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
					        $itemASerRemovido = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);
					        
					        if(!empty($itemASerRemovido)){
						
						        (new MdIaClassMetaOdsRN())->excluir($itemASerRemovido);
						
						        $objMdIaHistClassDTO = new MdIaHistClassDTO();
						        $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
						        $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($itemRemovido);
						        $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_DELETE);
						        $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
						        $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						        $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
						        $objMdIaHistClassDTO->setStrRacional($dados["racionais"]["txaRacional_".$itemRemovido]);
						        (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
					        	
					        }
					
				        }
				
			        }
		        	
		        }
		
		        // Recusando sugestoes
		        if(!empty($sugestoesRecusadas)){
			
			        foreach($sugestoesRecusadas as $sugestaoRecusada){
				
				        if(is_numeric($sugestaoRecusada)){
					
					        $objMdIaClassMetaOdsDTO = new MdIaClassMetaOdsDTO();
					        $objMdIaClassMetaOdsDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
					        $objMdIaClassMetaOdsDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
					        $objMdIaClassMetaOdsDTO->retNumIdMdIaClassMetaOds();
					        $sugestaoARecusar = (new MdIaClassMetaOdsRN())->listar($objMdIaClassMetaOdsDTO);
					
					        if(!empty($sugestaoARecusar)) {
						
						        (new MdIaClassMetaOdsRN())->excluir($sugestaoARecusar);
						
						        $objMdIaHistClassDTO = new MdIaHistClassDTO();
						        $objMdIaHistClassDTO->retNumIdMdIaHistClass();
						        $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
						        $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
						        $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_INSERT);
						        $objMdIaHistClassDTO->setStrStaTipoUsuario([UsuarioRN::$TU_SISTEMA, UsuarioRN::$TU_EXTERNO], InfraDTO::$OPER_IN);
						        $objMdIaHistClassDTO->setOrdDthCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
						        $objMdIaHistClassDTO->setNumMaxRegistrosRetorno(1);
						        $itemHistoricoSugerido = (new MdIaHistClassRN())->consultar($objMdIaHistClassDTO);
						
						        if(!empty($itemHistoricoSugerido)){
							
							        $objMdIaHistClassDTO = new MdIaHistClassDTO();
							        $objMdIaHistClassDTO->setNumIdMdIaClassificacaoOds($idClassificacao);
							        $objMdIaHistClassDTO->setNumIdMdIaAdmMetaOds($sugestaoRecusada);
							        $objMdIaHistClassDTO->setStrOperacao(MdIaHistClassRN::$OPERACAO_NÃO_CONFIRMACAO);
							        $objMdIaHistClassDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
							        $objMdIaHistClassDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
							        $objMdIaHistClassDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
							        $objMdIaHistClassDTO->setStrSinSugestaoAceita("N");
							        $objMdIaHistClassDTO->setNumIdMdIaHistClassSugest($itemHistoricoSugerido->getNumIdMdIaHistClass());
							        $objMdIaHistClassDTO->setStrRacional($dados["racionais"]["txaRacional_".$sugestaoRecusada]);
							        (new MdIaHistClassRN())->cadastrar($objMdIaHistClassDTO);
							
						        }
						
					        }
					
				        }
				
			        }
		        	
		        }

                return json_encode(array("result" => "true", "reloadTo" => SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_recurso&arvore=1&id_procedimento='.$dados["hdnIdProcedimento"])));
                
            } else {
            	
                return json_encode(array("result" => "false", "mensagem" => utf8_encode("Nenhum item foi alterado desde a última classificação. Se não desejar realizar alterações clicar no botão Fechar.")));
            
            }

        }
        
    }
