#!/usr/bin/env python3
"""
Convert a PDF to PNG images (first N pages).
Usage: pdf_to_images.py input.pdf output_dir [max_pages]
Requires: PyMuPDF (fitz)
"""
import sys, os

def main():
    if len(sys.argv) < 3:
        print("Usage: pdf_to_images.py input.pdf output_dir [max_pages]", file=sys.stderr)
        return 2
    pdf_path = sys.argv[1]
    out_dir = sys.argv[2]
    max_pages = int(sys.argv[3]) if len(sys.argv) >= 4 else 2

    os.makedirs(out_dir, exist_ok=True)

    try:
        import fitz  # PyMuPDF
    except Exception as e:
        print("PyMuPDF (fitz) not installed:", e, file=sys.stderr)
        return 3

    doc = fitz.open(pdf_path)
    n = min(len(doc), max_pages)
    for i in range(n):
        page = doc.load_page(i)
        # Render at a higher resolution for better OCR/vision
        mat = fitz.Matrix(2, 2)
        pix = page.get_pixmap(matrix=mat, alpha=False)
        out_path = os.path.join(out_dir, f"page_{i+1}.png")
        pix.save(out_path)
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
