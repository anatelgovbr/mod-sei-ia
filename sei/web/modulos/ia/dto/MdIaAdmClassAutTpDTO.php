<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 * 21/05/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.45.1
 **/


require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmClassAutTpDTO extends InfraDTO
{
  public function getStrNomeTabela()
  {
    return 'md_ia_adm_class_aut_tp';
  }

  public function montar()
  {
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmClassAutTp', 'id_md_ia_adm_class_aut_tp');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmMetaOds', 'id_md_ia_adm_meta_ods');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento', 'id_tipo_procedimento');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
          'NomeTipoProcedimento',
          'nome',
          'tipo_procedimento');

    $this->configurarPK('IdMdIaAdmClassAutTp', InfraDTO::$TIPO_PK_NATIVA);
    $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento', 'id_tipo_procedimento');

  }
}
