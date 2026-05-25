#!/usr/bin/env python3
import argparse, base64, json, sys
from pathlib import Path

try:
    import fitz  # PyMuPDF
except Exception as e:
    print(json.dumps({"ok": False, "error": "PYMUPDF_NOT_INSTALLED", "detail": str(e)}))
    sys.exit(2)

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--pdf", required=True)
    ap.add_argument("--pages", type=int, default=1)
    ap.add_argument("--max_px", type=int, default=1600)
    args = ap.parse_args()

    pdf_path = Path(args.pdf)
    if not pdf_path.exists():
        print(json.dumps({"ok": False, "error": "PDF_NOT_FOUND"}))
        return 1

    doc = fitz.open(str(pdf_path))
    out = []
    for i in range(min(args.pages, doc.page_count)):
        page = doc.load_page(i)
        rect = page.rect
        scale = 2.0
        if max(rect.width*scale, rect.height*scale) > args.max_px:
            scale = args.max_px / max(rect.width, rect.height)
        mat = fitz.Matrix(scale, scale)
        pix = page.get_pixmap(matrix=mat, alpha=False)
        png_bytes = pix.tobytes("png")
        out.append(base64.b64encode(png_bytes).decode("utf-8"))

    print(json.dumps({"ok": True, "images_b64_png": out}, ensure_ascii=False))
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
