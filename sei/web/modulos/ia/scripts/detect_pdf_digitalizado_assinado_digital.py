#!/usr/bin/env python3
# -*- coding: iso-8859-1 -*-
"""
PDF Analysis Script - Deteccao de digitalizacao e assinatura digital.

Uso:
  python3 detect_pdf_digitalizado_assinado_digital.py <arquivo.pdf> [opcoes]

Opcoes:
  --ocr          Executa deteccao de digitalizacao (precisa_ocr)
  --assinatura   Executa deteccao de assinatura digital (tem_assinatura_digital)

Sem opcoes: executa ambas as deteccoes (comportamento padrao).

Exemplos:
  python3 script.py doc.pdf --ocr
  python3 script.py doc.pdf --assinatura
  python3 script.py doc.pdf --ocr --assinatura
  python3 script.py doc.pdf
"""

import sys
import json
import os

try:
    import pymupdf as fitz
except ImportError:
    # Fallback if PyMuPDF not available
    result = {"precisa_ocr": True, "erro": "PyMuPDF not available", "cobertura": 0}
    print(json.dumps(result))
    sys.exit(0)


def calculate_image_coverage(pdf_path, threshold=50):
    """
    Analyze PDF pages to determine if they are scanned (low text) or digital (high text).
    
    Strategy: Count extractable text words per page.
    - Scanned PDFs: minimal text (0-50 words) - just OCR'd or images
    - Digital PDFs: substantial text (>50 words) - native content
    
    Threshold: 50 words
    - Below 50: page is likely scanned image
    - Above 50: page is likely digital content
    
    Args:
        pdf_path: Path to PDF file
        threshold: Minimum words for "digital" classification (default 50)
    
    Returns:
        dict: {
            "precisa_ocr": bool,  # True if mostly scanned, False if mostly digital
            "cobertura": float,   # Proportion of digital pages (0-1)
            "paginas_analisadas": int,  # Number of pages analyzed
            "paginas_digitais": int  # Number of pages classified as digital
        }
    """
    try:
        if not os.path.exists(pdf_path) or not os.path.isfile(pdf_path):
            return {
                "precisa_ocr": True,
                "erro": "Arquivo nao encontrado",
                "cobertura": 0
            }
        
        # Open PDF
        doc = fitz.open(pdf_path)
        
        if len(doc) == 0:
            return {
                "precisa_ocr": True,
                "erro": "PDF vazio",
                "cobertura": 0
            }
        
        pages_analyzed = 0
        pages_digital = 0
        
        # Analyze first 10 pages (optimization: don't analyze all pages)
        pages_to_check = min(10, len(doc))
        
        for page_num in range(pages_to_check):
            try:
                page = doc[page_num]
                pages_analyzed += 1
                
                # Extract text from page
                if hasattr(page, 'get_text'):
                    text = page.get_text()
                else:
                    text = page.getText()
                
                # Count words
                words = len(text.split())
                
                # Page is "digital" if has >= threshold words
                if words >= threshold:
                    pages_digital += 1
                    
            except Exception as e:
                # Skip pages with errors
                continue
        
        doc.close()
        
        if pages_analyzed == 0:
            return {"precisa_ocr": True, "cobertura": 0, "paginas_analisadas": 0, "paginas_digitais": 0}
        
        # Calculate coverage: proportion of digital pages
        coverage = pages_digital / pages_analyzed
        
        # If >= 50% of pages are digital, it's a digital document
        # If < 50% are digital, it's predominantly scanned (needs OCR)
        precisa_ocr = coverage < 0.5
        
        return {
            "precisa_ocr": precisa_ocr,
            "cobertura": round(coverage, 4),
            "paginas_analisadas": pages_analyzed,
            "paginas_digitais": pages_digital
        }
        
    except Exception as e:
        # Return safe default on any error
        return {
            "precisa_ocr": True,
            "erro": str(e),
            "cobertura": 0
        }


def has_digital_signature(doc):
    """
    Detect if PDF has digital signature structures.

    This is intentionally lightweight:
    1) signature flags (fast)
    2) signature widgets in pages (fast)
    3) bounded xref scan for /Sig markers (fallback)
    """
    try:
        # Fast path available in many PyMuPDF versions.
        if hasattr(doc, 'get_sigflags'):
            sig_flags = doc.get_sigflags()
            if sig_flags and int(sig_flags) > 0:
                return True
    except Exception:
        pass

    try:
        # Check signature widgets on pages.
        sig_widget_type = getattr(fitz, 'PDF_WIDGET_TYPE_SIGNATURE', None)
        pages_to_check = min(10, len(doc))

        for page_num in range(pages_to_check):
            page = doc[page_num]
            widgets = page.widgets() if hasattr(page, 'widgets') else None

            if not widgets:
                continue

            for widget in widgets:
                field_type = getattr(widget, 'field_type', None)
                field_type_string = str(getattr(widget, 'field_type_string', '')).lower()

                if sig_widget_type is not None and field_type == sig_widget_type:
                    return True

                if 'sig' in field_type_string or 'assin' in field_type_string:
                    return True
    except Exception:
        pass

    try:
        # Fallback: bounded scan to avoid high cost on very large PDFs.
        xref_len = doc.xref_length() if hasattr(doc, 'xref_length') else 0
        if xref_len > 0:
            limit = min(xref_len, 5000)
            for xref in range(1, limit):
                obj = doc.xref_object(xref, compressed=False)
                if not obj:
                    continue

                if '/FT/Sig' in obj or '/FT /Sig' in obj or '/Type/Sig' in obj or '/Type /Sig' in obj:
                    return True
    except Exception:
        pass

    return False


def main():
    """Main entry point."""
    args = sys.argv[1:]

    if not args:
        result = {"erro": "Arquivo nao fornecido"}
        print(json.dumps(result))
        sys.exit(1)

    pdf_path = args[0]
    flags = [a.lower() for a in args[1:]]

    # Sem flags: executa tudo (comportamento padrao para retrocompatibilidade).
    run_ocr = not flags or '--ocr' in flags
    run_assinatura = not flags or '--assinatura' in flags

    if not os.path.exists(pdf_path) or not os.path.isfile(pdf_path):
        result = {"erro": "Arquivo nao encontrado"}
        print(json.dumps(result))
        sys.exit(1)

    result = {}

    if run_ocr:
        ocr_result = calculate_image_coverage(pdf_path)
        result.update(ocr_result)

    if run_assinatura:
        try:
            doc = fitz.open(pdf_path)
            result['tem_assinatura_digital'] = has_digital_signature(doc)
            doc.close()
        except Exception:
            result['tem_assinatura_digital'] = False

    print(json.dumps(result))
    sys.exit(0)


if __name__ == "__main__":
    main()
