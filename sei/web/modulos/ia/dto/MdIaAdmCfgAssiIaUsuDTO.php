<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 27/03/2024 - criado por sabino.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '../../../../SEI.php';

class MdIaAdmCfgAssiIaUsuDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_ia_adm_cfg_assi_ia_usu';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmCfgAssiIaUsu', 'id_md_ia_adm_cfg_assi_ia_usu');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdIaAdmConfigAssistIA', 'id_md_ia_adm_conf_assist_ia');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUsuario', 'sigla', 'usuario');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeUsuario', 'nome', 'usuario');

        $this->configurarPK('IdMdIaAdmCfgAssiIaUsu', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');
        $this->configurarFK('IdMdIaAdmConfigAssistIA', 'md_ia_adm_config_assist_ia', 'id_md_ia_adm_conf_assist_ia');
    }
}
