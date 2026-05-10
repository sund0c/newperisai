#!/usr/bin/env python3
"""
PERISAI — Asset PDF Generator
Font: Helvetica (sans-serif equivalent in ReportLab)
Columns: NO | KODE ASET | NAMA ASET | SUB KLASIFIKASI | OPD | STATUS
"""

import sys
import json
import os
import io

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4, landscape
from reportlab.lib.units import mm
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.lib.utils import ImageReader

try:
    from PIL import Image as PILImage
    HAS_PILLOW = True
except ImportError:
    HAS_PILLOW = False

BLACK  = colors.black
WHITE  = colors.white
GRAY   = colors.HexColor('#555555')
LGRAY  = colors.HexColor('#aaaaaa')

PAGE_W, PAGE_H = landscape(A4)
MARGIN = 12 * mm

# ── Typography scale (ReportLab pt ≈ CSS px * 0.75)
# H2  → 18pt  (~24px)
# H3  → 13pt  (~17px)
# desc → desc_size with leading 1.2
# table header → 8pt bold
# table cell   → 8pt

FS_H2       = 18     # "ASET INFORMASI ..."
FS_H3       = 13     # "Pemilik Aset: ..."
FS_DESC     = 7.5    # "Pemilik Aset bertanggung..." — 0.9em of ~8.5 base
FS_DESC_LH  = FS_DESC * 1.2   # line-height: 1.2
FS_SUMMARY  = 9
FS_TH       = 8
FS_TD       = 8
FS_TD_SM    = 6.5
FS_FOOTER   = 7

FONT        = 'Helvetica'
FONT_BOLD   = 'Helvetica-Bold'


def load_image_no_bg(path):
    """Load PNG stripping near-white/gray border pixels via Pillow."""
    if not HAS_PILLOW or not os.path.exists(path):
        return path  # fallback: return path, let ReportLab handle it
    img = PILImage.open(path).convert('RGBA')
    data = img.getdata()
    new_data = []
    for r, g, b, a in data:
        # treat near-white or near-gray as transparent
        if r > 210 and g > 210 and b > 210:
            new_data.append((255, 255, 255, 0))
        else:
            new_data.append((r, g, b, a))
    img.putdata(new_data)
    buf = io.BytesIO()
    img.save(buf, format='PNG')
    buf.seek(0)
    return ImageReader(buf)


def cell(text, size=FS_TD, bold=False, align=TA_LEFT, color=BLACK):
    return Paragraph(str(text) if text else '-',
        ParagraphStyle('c',
            fontName=FONT_BOLD if bold else FONT,
            fontSize=size, textColor=color,
            leading=size * 1.2, alignment=align, wordWrap='CJK'))


def nama_cell(nama, keterangan):
    ket = (keterangan or '')
    content = (
        f"<b>{nama}</b><br/>"
        f"<font size='{FS_TD_SM}' color='#555555'>{ket}</font>"
    ) if ket else f"<b>{nama}</b>"
    return Paragraph(content,
        ParagraphStyle('nm', fontName=FONT, fontSize=FS_TD,
                       leading=FS_TD * 1.3, textColor=BLACK, wordWrap='CJK'))


def klas_cell(klasifikasi, sub_klas):
    return Paragraph(
        f"{sub_klas}<br/>"
        f"<font size='{FS_TD_SM}' color='#555555'>{klasifikasi}</font>",
        ParagraphStyle('kl', fontName=FONT, fontSize=FS_TD,
                       leading=FS_TD * 1.1, textColor=BLACK, wordWrap='CJK'))


def make_header(canvas_obj, doc, meta, logo1_src, logo2_src):
    canvas_obj.saveState()

    logo_h      = 18 * mm
    logo_w      = 18 * mm
    logo_gap    = 3  * mm
    logos_total = (logo_w + logo_gap) * 2
    bar_h       = 42 * mm
    bar_y       = PAGE_H - bar_h
    tx          = MARGIN
    text_max_w  = PAGE_W - MARGIN * 2 - logos_total - 10 * mm

    # ── H2: ASET INFORMASI ... :: Tahun XXXX
    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, FS_H2)
    klas = meta.get('klasifikasi', 'Semua Klasifikasi')
    if klas and klas.lower() != 'semua klasifikasi':
        title_str = f"ASET INFORMASI {klas.upper()} :: Tahun {meta.get('tahun', '')}"
    else:
        title_str = f"ASET INFORMASI Tahun {meta.get('tahun', '')}"
    canvas_obj.drawString(tx, PAGE_H - 14 * mm, title_str)

    # ── H3: Pemilik Aset: ...
    canvas_obj.setFont(FONT_BOLD, FS_H3)
    pemilik = meta.get('pemilik_aset', 'PEMERINTAH PROVINSI BALI')
    canvas_obj.drawString(tx, PAGE_H - 20 * mm, f"Pemilik Aset: {pemilik}")

    # ── Description: font-size 0.9em, line-height 1.2
    # Full text as required
    desc = (
        'Pemilik Aset bertanggung jawab terhadap proses bisnis/layanannya, pengelolaan aset informasi, '
        'pengukuran nilai aset, klasifikasi aset, kategorisasi Sistem Elektronik, penilaian vitalitas, '
        'penilaian kepatuhan, pemetaan risiko, analisis risiko, serta penyusunan dan implementasi mitigasi risiko. '
        'Kepala OPD/UPTD sebagai Pemilik Risiko bertanggung jawab untuk menyetujui rencana mitigasi risiko, '
        'menetapkan tingkat risiko yang dapat diterima (acceptable risk), menyetujui residual risk, '
        'serta memastikan serta memastikan dukungan sumber daya yang diperlukan.'
    )
    canvas_obj.setFont(FONT, FS_DESC)
    canvas_obj.setFillColor(GRAY)

    # Word-wrap using actual string width measurement
    words   = desc.split()
    lines   = []
    current = ''
    for w in words:
        test = (current + ' ' + w).strip()
        if canvas_obj.stringWidth(test, FONT, FS_DESC) <= text_max_w:
            current = test
        else:
            if current:
                lines.append(current)
            current = w
    if current:
        lines.append(current)

    line_y = PAGE_H - 25 * mm
    line_step = FS_DESC_LH * 0.3528  # pt to mm (1pt = 0.3528mm)
    for ln in lines:
        if line_y < bar_y + 2 * mm:
            break
        canvas_obj.drawString(tx, line_y, ln)
        line_y -= line_step * mm

    # ── Logos: right side, vertically centred
    logo1_x = PAGE_W - MARGIN - logos_total
    logo2_x = logo1_x + logo_w + logo_gap
    logo_y  = bar_y + (bar_h - logo_h) / 2

    # Draw logo 1 (logobaliprovcsirt)
    if isinstance(logo1_src, str) and not os.path.exists(logo1_src):
        # placeholder
        canvas_obj.setFillColor(WHITE)
        canvas_obj.setStrokeColor(LGRAY)
        canvas_obj.setLineWidth(0.5)
        canvas_obj.rect(logo1_x, logo_y, logo_w, logo_h, fill=1, stroke=1)
        canvas_obj.setFillColor(GRAY)
        canvas_obj.setFont(FONT_BOLD, 5)
        cx = logo1_x + logo_w / 2
        canvas_obj.drawCentredString(cx, logo_y + logo_h / 2 + 2, 'BALIPROV')
        canvas_obj.drawCentredString(cx, logo_y + logo_h / 2 - 4, 'CSIRT')
    else:
        canvas_obj.drawImage(logo1_src, logo1_x, logo_y,
                             width=logo_w, height=logo_h,
                             preserveAspectRatio=True, anchor='c')

    # Draw logo 2 (tlp_teaser_amber_strict)
    if isinstance(logo2_src, str) and not os.path.exists(logo2_src):
        badge_h = logo_h / 2 - 1
        cx2 = logo2_x + logo_w / 2
        canvas_obj.setFillColor(colors.HexColor('#dddddd'))
        canvas_obj.setStrokeColor(BLACK)
        canvas_obj.setLineWidth(0.8)
        canvas_obj.rect(logo2_x, logo_y + badge_h + 2, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(BLACK)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y + badge_h + badge_h / 2 + 1, 'TLP:AMBER')
        canvas_obj.setFillColor(BLACK)
        canvas_obj.setStrokeColor(BLACK)
        canvas_obj.rect(logo2_x, logo_y, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(WHITE)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y + badge_h / 2 - 2, '+STRICT')
    else:
        canvas_obj.drawImage(logo2_src, logo2_x, logo_y,
                             width=logo_w, height=logo_h,
                             preserveAspectRatio=True, anchor='c')

    # ── Footer
    canvas_obj.setFillColor(GRAY)
    canvas_obj.setFont(FONT, FS_FOOTER)
    canvas_obj.drawString(MARGIN, 6 * mm, f"Dicetak: {meta.get('generated_at', '')}")
    canvas_obj.drawRightString(PAGE_W - MARGIN, 6 * mm,
                               f"PERISAI  \u00b7  Hal {doc.page}")

    canvas_obj.restoreState()


def build_filter_summary(meta):
    parts = (
        f"<b>Klasifikasi:</b> {meta.get('klasifikasi', 'Semua Klasifikasi')}  |  "
        f"<b>Status:</b> {meta.get('status_label', 'Semua')}  |  "
        f"<b>Total Aset:</b> {meta.get('total', 0)}"
    )
    return Paragraph(parts, ParagraphStyle('fb',
        fontName=FONT, fontSize=FS_SUMMARY,
        textColor=BLACK, leading=FS_SUMMARY * 1.2))


def build_pdf(output_path, meta, rows, logo1_path, logo2_path):
    # Pre-process logos to strip background
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = load_image_no_bg(logo2_path) if logo2_path.endswith('.png') else (
        logo2_path if os.path.exists(logo2_path) else logo2_path
    )

    doc = SimpleDocTemplate(output_path, pagesize=landscape(A4),
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=40 * mm, bottomMargin=13 * mm,
        title=f"PERISAI Aset Informasi {meta.get('tahun', '')}",
        author='PERISAI - Pemprov Bali')

    col_widths = [
        10 * mm,   # NO
        22 * mm,   # KODE ASET
        88 * mm,   # NAMA ASET
        48 * mm,   # SUB KLASIFIKASI
        80 * mm,   # OPD
        20 * mm,   # STATUS
    ]

    hs = ParagraphStyle('th', fontName=FONT_BOLD, fontSize=FS_TH,
                        textColor=BLACK, alignment=TA_CENTER,
                        leading=FS_TH * 1.2, wordWrap='CJK')

    table_data = [[
        Paragraph('NO', hs),
        Paragraph('KODE ASET', hs),
        Paragraph('NAMA ASET', hs),
        Paragraph('SUB\nKLASIFIKASI', hs),
        Paragraph('OPD', hs),
        Paragraph('STATUS', hs),
    ]]

    for row in rows:
        table_data.append([
            cell(str(row['no']), align=TA_CENTER),
            cell(row['kode_aset'], bold=True),
            nama_cell(row['nama_aset'], row.get('keterangan', '')),
            klas_cell(row['klasifikasi'], row['sub_klasifikasi']),
            cell(row['opd']),
            cell(row['status'], align=TA_CENTER),
        ])

    tbl = Table(table_data, colWidths=col_widths, repeatRows=1)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0, 0), (-1, 0),  WHITE),
        ('FONTNAME',      (0, 0), (-1, 0),  FONT_BOLD),
        ('FONTSIZE',      (0, 0), (-1, 0),  FS_TH),
        ('ALIGN',         (0, 0), (-1, 0),  'CENTER'),
        ('VALIGN',        (0, 0), (-1, 0),  'MIDDLE'),
        ('BACKGROUND',    (0, 1), (-1, -1), WHITE),
        ('FONTNAME',      (0, 1), (-1, -1), FONT),
        ('FONTSIZE',      (0, 1), (-1, -1), FS_TD),
        ('VALIGN',        (0, 1), (-1, -1), 'TOP'),
        ('GRID',          (0, 0), (-1, -1), 0.75, BLACK),
        ('BOX',           (0, 0), (-1, -1), 1.0,  BLACK),
        ('TOPPADDING',    (0, 0), (-1, -1), 2),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 2),
        ('LEFTPADDING',   (0, 0), (-1, -1), 4),
        ('RIGHTPADDING',  (0, 0), (-1, -1), 4),
    ]))

    story = [
        build_filter_summary(meta),
        Spacer(1, 1 * mm),
        tbl,
    ]

    if not rows:
        story.append(Spacer(1, 8 * mm))
        story.append(Paragraph(
            '<i>Tidak ada data aset yang sesuai dengan filter yang dipilih.</i>',
            ParagraphStyle('e', fontName=FONT, fontSize=9,
                           textColor=GRAY, alignment=TA_CENTER)))

    doc.build(story,
        onFirstPage=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src),
        onLaterPages=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Usage: python3 generate_asset_pdf.py <output_path>', file=sys.stderr)
        sys.exit(1)
    try:
        payload = json.loads(sys.stdin.read())
    except json.JSONDecodeError as e:
        print(f'JSON parse error: {e}', file=sys.stderr)
        sys.exit(1)

    script_dir = os.path.dirname(os.path.abspath(__file__))
    public_dir = os.path.join(script_dir, '..', 'public', 'images')

    meta       = payload.get('meta', {})
    logo1_path = meta.get('logo1_path') or os.path.join(public_dir, 'logobaliprovcsirt.png')
    logo2_path = meta.get('logo2_path') or os.path.join(public_dir, 'tlp', 'tlp_teaser_amber_strict.jpg')

    build_pdf(sys.argv[1], meta, payload.get('rows', []), logo1_path, logo2_path)
    print(f'PDF generated: {sys.argv[1]}', file=sys.stderr)