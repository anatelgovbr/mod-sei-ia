<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 30/12/2025 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.3
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaInteracaoArqAvulsoINT extends InfraINT
{

    private static function normalizarNomeArquivoParaExibicao($strNomeArquivo)
    {
        if ($strNomeArquivo === null) {
            return '';
        }

        if (mb_check_encoding($strNomeArquivo, 'UTF-8')) {
            return $strNomeArquivo;
        }

        return InfraString::toUTF8($strNomeArquivo);
    }

    public function recuperarDadosUploadTopico($IdMdIaInteracaoChat)
    {
        $objMdIaInteracaoArqAvulso = new MdIaInteracaoArqAvulsoDTO();
        $objMdIaInteracaoArqAvulso->setNumIdMdIaInteracaoChat($IdMdIaInteracaoChat);
        $objMdIaInteracaoArqAvulso->retNumIdMdIaInteracaoArqAvulso();
        $objMdIaInteracaoArqAvulso->retStrNomeOriginal();

        $arrArquivos = InfraArray::converterArrInfraDTO((new MdIaInteracaoArqAvulsoRN)->listar($objMdIaInteracaoArqAvulso), 'NomeOriginal');

        foreach ($arrArquivos as $indice => $nomeArquivo) {
            $arrArquivos[$indice] = self::normalizarNomeArquivoParaExibicao($nomeArquivo);
        }

        return $arrArquivos;
    }

    public static function obterMapaIconesPorExtensao()
    {
        $mapa = [
            // Word
            'doc'  => Icone::DOCUMENTO_WORD,
            'docx' => Icone::DOCUMENTO_WORD,

            // Imagens
            'jpg'  => Icone::DOCUMENTO_IMAGEM,
            'jpeg' => Icone::DOCUMENTO_IMAGEM,
            'png'  => Icone::DOCUMENTO_IMAGEM,
            'gif'  => Icone::DOCUMENTO_IMAGEM,
            'bmp'  => Icone::DOCUMENTO_IMAGEM,

            // PowerPoint
            'ppt'  => Icone::DOCUMENTO_POWERPOINT,
            'pps'  => Icone::DOCUMENTO_POWERPOINT,

            // Excel
            'xls'  => Icone::DOCUMENTO_EXCEL,
            'xlsx' => Icone::DOCUMENTO_EXCEL,

            // Texto
            'txt'  => Icone::DOCUMENTO_TXT,

            // PDF
            'pdf'  => Icone::DOCUMENTO_PDF,

            // Aplicáveis
            'exe'  => Icone::DOCUMENTO_APLICATIVO,
            'com'  => Icone::DOCUMENTO_APLICATIVO,

            // Compactados
            'zip'  => Icone::DOCUMENTO_ZIP,
            'rar'  => Icone::DOCUMENTO_RAR,

            // OpenDocument
            'ods'  => Icone::DOCUMENTO_ODS,
            'odt'  => Icone::DOCUMENTO_ODT,
            'odp'  => Icone::DOCUMENTO_ODP,
            'odg'  => Icone::DOCUMENTO_ODG,

            // HTML
            'html' => Icone::DOCUMENTO_HTML,
            'htm'  => Icone::DOCUMENTO_HTML,

            // Vídeo
            'avi'  => Icone::DOCUMENTO_VIDEO,
            'swf'  => Icone::DOCUMENTO_VIDEO,
            'wmv'  => Icone::DOCUMENTO_VIDEO,
            'mp4'  => Icone::DOCUMENTO_VIDEO,
            'webm' => Icone::DOCUMENTO_VIDEO,

            // Áudio
            'mp3'  => Icone::DOCUMENTO_AUDIO,
            'wma'  => Icone::DOCUMENTO_AUDIO,
            'ogg'  => Icone::DOCUMENTO_AUDIO,
            'm4a'  => Icone::DOCUMENTO_AUDIO,
            'flac' => Icone::DOCUMENTO_AUDIO,
            'aac'  => Icone::DOCUMENTO_AUDIO,
            'opus' => Icone::DOCUMENTO_AUDIO,
        ];

        return [
            'mapa'   => $mapa,
            'default' => Icone::DOCUMENTO_NAO_IDENTIFICADO
        ];
    }
}
