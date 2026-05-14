#!/usr/bin/env python3
"""
PERISAI — Kategorisasi Sistem Elektronik (SE) PDF Generator
Meniru struktur generate_iiv_pdf.py
Columns: NO | KODE ASET | NAMA ASET | SUB KLASIFIKASI | OPD | TOTAL NILAI | KATEGORI SE
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

FS_H2      = 18
FS_H3      = 13
FS_DESC    = 7.5
FS_DESC_LH = FS_DESC * 1.2
FS_SUMMARY = 9
FS_TH      = 8
FS_TD      = 8
FS_TD_SM   = 6.5
FS_FOOTER  = 7

FONT      = 'Helvetica'
FONT_BOLD = 'Helvetica-Bold'

SE_LABELS = {
    'STRATEGIS': 'STRATEGIS',
    'TINGGI':    'TINGGI',
    'RENDAH':    'RENDAH',
}


def load_image_no_bg(path):
    if not HAS_PILLOW or not os.path.exists(path):
        return path
    img = PILImage.open(path).convert('RGBA')
    data = img.getdata()
    new_data = []
    for r, g, b, a in data:
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

    # H2
    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, FS_H2)
    versi = meta.get('versi_se', '-')
    title_str = f"KATEGORISASI SE:: Tahun {meta.get('tahun', '')}"
    canvas_obj.drawString(tx, PAGE_H - 14 * mm, title_str)

    # H3
    canvas_obj.setFont(FONT_BOLD, FS_H3)
    opd = meta.get('opd', '')
    if opd and opd != 'Semua OPD':
        pemilik = opd
    else:
        pemilik = 'PEMERINTAH PROVINSI BALI (Semua OPD)'
    canvas_obj.drawString(tx, PAGE_H - 20 * mm, f"Pemilik Aset: {pemilik}")

    # Description
    desc = (
        'Kategorisasi Sistem Elektronik (SE) dilakukan berdasarkan 10 indikator penilaian '
        'dengan pilihan jawaban A (skor 5), B (skor 2), atau C (skor 1). '
        'Total skor di atas 35 dikategorikan STRATEGIS, total skor 16 s/d 34 dikategorikan TINGGI, '
        'di bawah itu dikategorikan RENDAH. Kategori STRATEGIS wajib memenuhi standard ISO 27001. TINGGI wajib memenuhi standard BSSN/SMKI Pemprov Bali.'
        'Penilaian ini hanya berlaku untuk aset klasifikasi Perangkat Lunak.'
        'Pemilik Aset bertanggung jawab atas keakuratan penilaian ini.'
    )
    canvas_obj.setFont(FONT, FS_DESC)
    canvas_obj.setFillColor(GRAY)

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

    line_y    = PAGE_H - 25 * mm
    line_step = FS_DESC_LH * 0.3528
    for ln in lines:
        if line_y < bar_y + 2 * mm:
            break
        canvas_obj.drawString(tx, line_y, ln)
        line_y -= line_step * mm

    # Logos
    logo1_x = PAGE_W - MARGIN - logos_total
    logo2_x = logo1_x + logo_w + logo_gap
    logo_y  = bar_y + (bar_h - logo_h) / 2

    if isinstance(logo1_src, str) and not os.path.exists(logo1_src):
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

    # Footer
    canvas_obj.setFillColor(GRAY)
    canvas_obj.setFont(FONT, FS_FOOTER)
    canvas_obj.drawString(MARGIN, 6 * mm, f"Dicetak: {meta.get('generated_at', '')}")
    canvas_obj.drawRightString(PAGE_W - MARGIN, 6 * mm,
                               f"PERISAI  \u00b7  Hal {doc.page}")

    canvas_obj.restoreState()


def build_filter_summary(meta):
    parts = (
        f"<b>Kategori SE:</b> {meta.get('kategori_se', 'Semua')}  |  "
        f"<b>Versi SE:</b> {meta.get('versi_se', '-')}  |  "
        f"<b>Total Aset:</b> {meta.get('total', 0)}  |  "
        f"<b>Strategis:</b> {meta.get('strategis', 0)}  |  "
        f"<b>Tinggi:</b> {meta.get('tinggi', 0)}  |  "
        f"<b>Rendah:</b> {meta.get('rendah', 0)}  |  "
        f"<b>Belum Dinilai:</b> {meta.get('belum', 0)}"
    )
    return Paragraph(parts, ParagraphStyle('fb',
        fontName=FONT, fontSize=FS_SUMMARY,
        textColor=BLACK, leading=FS_SUMMARY * 1.2))


def build_pdf(output_path, meta, rows, logo1_path, logo2_path):
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = load_image_no_bg(logo2_path) if logo2_path.endswith('.png') else (
        logo2_path if os.path.exists(logo2_path) else logo2_path
    )

    doc = SimpleDocTemplate(output_path, pagesize=landscape(A4),
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=40 * mm, bottomMargin=13 * mm,
        title=f"PERISAI SE {meta.get('tahun', '')}",
        author='PERISAI - Pemprov Bali')

    col_widths = [
        10 * mm,   # NO
        22 * mm,   # KODE ASET
        70 * mm,   # NAMA ASET
        50 * mm,   # KLAS / SUB KLAS
        70 * mm,   # OPD
        20 * mm,   # TOTAL NILAI
        30 * mm,   # KATEGORI SE
    ]

    hs = ParagraphStyle('th', fontName=FONT_BOLD, fontSize=FS_TH,
                        textColor=BLACK, alignment=TA_CENTER,
                        leading=FS_TH * 1.2, wordWrap='CJK')

    table_data = [[
        Paragraph('NO', hs),
        Paragraph('KODE ASET', hs),
        Paragraph('NAMA ASET', hs),
        Paragraph('SUB KLAS / KLAS', hs),
        Paragraph('OPD', hs),
        Paragraph('SKOR', hs),
        Paragraph('KATEGORI SE', hs),
    ]]

    for row in rows:
        total_nilai = row.get('total_nilai')
        kategori    = row.get('kategori_se')

        total_str = f"{total_nilai}" if total_nilai is not None else '-'
        kat_str   = SE_LABELS.get(kategori, 'Belum dinilai') if kategori else 'Belum dinilai'

        table_data.append([
            cell(str(row['no']), align=TA_CENTER),
            cell(row.get('kode_aset', ''), bold=True),
            nama_cell(row.get('nama_aset', ''), row.get('keterangan', '')),
            klas_cell(row.get('klasifikasi', '-'), row.get('sub_klasifikasi', '-')),
            cell(row.get('opd', '-')),
            cell(total_str, align=TA_CENTER, bold=total_nilai is not None),
            cell(kat_str, bold=bool(kategori), align=TA_CENTER),
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
        print('Usage: python3 generate_se_pdf.py <output_path>', file=sys.stderr)
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