#!/usr/bin/env python3
"""
PERISAI — RoPA List PDF Generator (Landscape A4)
Columns: NO | KODE | NAMA AKTIVITAS | OPD | PENANGGUNG JAWAB | DASAR PEMROSESAN | DPIA
"""

import sys, json, os, io
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4, landscape
from reportlab.lib.units import mm
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
try:
    from PIL import Image as PILImage
    HAS_PILLOW = True
except ImportError:
    HAS_PILLOW = False
from reportlab.lib.utils import ImageReader

BLACK = colors.black
WHITE = colors.white
GRAY  = colors.HexColor('#555555')
LGRAY = colors.HexColor('#aaaaaa')

PAGE_W, PAGE_H = landscape(A4)
MARGIN = 15 * mm

FONT      = 'Helvetica'
FONT_BOLD = 'Helvetica-Bold'

FS_H2      = 16
FS_H3      = 12
FS_DESC    = 7.5
FS_DESC_LH = FS_DESC * 1.2
FS_SUMMARY = 9
FS_TH      = 7.5
FS_TD      = 7.5
FS_TD_SM   = 6
FS_FOOTER  = 7


def load_image_no_bg(path):
    if not HAS_PILLOW or not os.path.exists(path):
        return path
    img = PILImage.open(path).convert('RGBA')
    new_data = [(255,255,255,0) if r>210 and g>210 and b>210 else (r,g,b,a)
                for r,g,b,a in img.getdata()]
    img.putdata(new_data)
    buf = io.BytesIO()
    img.save(buf, format='PNG')
    buf.seek(0)
    return ImageReader(buf)


def cell(text, size=FS_TD, bold=False, align=TA_LEFT, color=BLACK):
    return Paragraph(str(text) if text else '-',
        ParagraphStyle('c', fontName=FONT_BOLD if bold else FONT,
            fontSize=size, textColor=color,
            leading=size*1.3, alignment=align, wordWrap='CJK'))


def dasar_cell(items):
    DASAR_SHORT = {
        'consent':                  'Consent',
        'contractual':              'Contractual',
        'legal_obligation':         'Legal Obligation',
        'vital_interests':          'Vital Interests',
        'public_interests':         'Public Interests',
        'legitimate_interests':     'Legit. Interests',
        'keseimbangan_kepentingan': 'Keseimbangan',
    }
    if not items:
        return cell('-', align=TA_CENTER)
    labels = [DASAR_SHORT.get(i, i) for i in items]
    return Paragraph('<br/>'.join(labels),
        ParagraphStyle('d', fontName=FONT, fontSize=FS_TD_SM,
            textColor=BLACK, leading=FS_TD_SM*1.3, wordWrap='CJK'))


def dpia_cell(required):
    if required:
        return Paragraph('<b>Diperlukan</b>',
            ParagraphStyle('dp', fontName=FONT_BOLD, fontSize=FS_TD,
                textColor=colors.HexColor('#C53030'),
                leading=FS_TD*1.3, alignment=TA_CENTER))
    return Paragraph('Tidak',
        ParagraphStyle('dp', fontName=FONT, fontSize=FS_TD,
            textColor=colors.HexColor('#276749'),
            leading=FS_TD*1.3, alignment=TA_CENTER))


def make_header(canvas_obj, doc, meta, logo1_src, logo2_src):
    canvas_obj.saveState()

    logo_h      = 18 * mm
    logo_w      = 18 * mm
    logo_gap    = 3  * mm
    logos_total = (logo_w + logo_gap) * 2
    bar_h       = 38 * mm
    bar_y       = PAGE_H - bar_h
    tx          = MARGIN
    text_max_w  = PAGE_W - MARGIN*2 - logos_total - 10*mm

    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, FS_H2)
    canvas_obj.drawString(tx, PAGE_H - 13*mm,
        f"RECORD OF PROCESSING ACTIVITIES (RoPA) :: Tahun {meta.get('tahun','')}")

    canvas_obj.setFont(FONT_BOLD, FS_H3)
    opd_val = meta.get('opd', '')
    if opd_val and opd_val.lower() not in ('semua opd', ''):
        subtitle = f"PEMERINTAH PROVINSI BALI  \u00b7  {opd_val}"
    else:
        subtitle = "PEMERINTAH PROVINSI BALI"
    canvas_obj.drawString(tx, PAGE_H - 19*mm, subtitle)

    desc = (
        'Dokumen ini merupakan catatan aktivitas pemrosesan data pribadi yang disusun '
        'sesuai dengan kewajiban Pengendali Data Pribadi berdasarkan Undang-Undang No. 27 Tahun 2022 '
        'tentang Pelindungan Data Pribadi (UU PDP). Dokumen bersifat rahasia dan hanya untuk '
        'keperluan internal serta audit kepatuhan pelindungan data pribadi Pemprov Bali.'
    )
    canvas_obj.setFont(FONT, FS_DESC)
    canvas_obj.setFillColor(GRAY)
    words = desc.split()
    lines, current = [], ''
    for w in words:
        test = (current + ' ' + w).strip()
        if canvas_obj.stringWidth(test, FONT, FS_DESC) <= text_max_w:
            current = test
        else:
            if current: lines.append(current)
            current = w
    if current: lines.append(current)

    line_y    = PAGE_H - 24*mm
    line_step = FS_DESC_LH * 0.3528
    for ln in lines:
        if line_y < bar_y + 2*mm: break
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
        canvas_obj.drawCentredString(logo1_x+logo_w/2, logo_y+logo_h/2, 'BALIPROV')
    else:
        canvas_obj.drawImage(logo1_src, logo1_x, logo_y,
            width=logo_w, height=logo_h, preserveAspectRatio=True, anchor='c')

    if isinstance(logo2_src, str) and not os.path.exists(logo2_src):
        badge_h = logo_h/2-1; cx2 = logo2_x+logo_w/2
        canvas_obj.setFillColor(colors.HexColor('#dddddd'))
        canvas_obj.setStrokeColor(BLACK); canvas_obj.setLineWidth(0.8)
        canvas_obj.rect(logo2_x, logo_y+badge_h+2, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(BLACK); canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y+badge_h+badge_h/2+1, 'TLP:AMBER')
        canvas_obj.setFillColor(BLACK)
        canvas_obj.rect(logo2_x, logo_y, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(WHITE); canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y+badge_h/2-2, '+STRICT')
    else:
        canvas_obj.drawImage(logo2_src, logo2_x, logo_y,
            width=logo_w, height=logo_h, preserveAspectRatio=True, anchor='c')

    # Footer
    canvas_obj.setFillColor(GRAY)
    canvas_obj.setFont(FONT, FS_FOOTER)
    canvas_obj.drawString(MARGIN, 6*mm, f"Dicetak: {meta.get('generated_at','')}")
    canvas_obj.drawRightString(PAGE_W-MARGIN, 6*mm, f"PERISAI  \u00b7  Hal {doc.page}")
    canvas_obj.restoreState()


def build_filter_summary(meta):
    parts = (
        f"<b>OPD:</b> {meta.get('opd', 'Semua OPD')}  |  "
        f"<b>Total Aktivitas:</b> {meta.get('total', 0)}"
    )
    return Paragraph(parts, ParagraphStyle('fb', fontName=FONT,
        fontSize=FS_SUMMARY, textColor=BLACK, leading=FS_SUMMARY*1.2))


def build_pdf(output_path, meta, rows, logo1_path, logo2_path):
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = (load_image_no_bg(logo2_path) if logo2_path.endswith('.png')
                 else (logo2_path if os.path.exists(logo2_path) else logo2_path))

    doc = SimpleDocTemplate(output_path, pagesize=landscape(A4),
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=36*mm, bottomMargin=13*mm,
        title=f"PERISAI RoPA {meta.get('tahun','')}",
        author='PERISAI - Pemprov Bali')

    col_widths = [
        10*mm,   # NO
        25*mm,   # KODE
        102*mm,  # NAMA AKTIVITAS
        55*mm,   # OPD
        55*mm,   # PENANGGUNG JAWAB
        20*mm,   # DPIA
    ]  # total = 267mm = 297mm - 2*15mm

    hs = ParagraphStyle('th', fontName=FONT_BOLD, fontSize=FS_TH,
        textColor=BLACK, alignment=TA_CENTER,
        leading=FS_TH*1.2, wordWrap='CJK')

    table_data = [[
        Paragraph('NO', hs),
        Paragraph('KODE', hs),
        Paragraph('NAMA AKTIVITAS', hs),
        Paragraph('OPD', hs),
        Paragraph('PENANGGUNG\nJAWAB', hs),
        Paragraph('DPIA', hs),
    ]]

    for row in rows:
        table_data.append([
            cell(str(row['no']), align=TA_CENTER),
            cell(row['kode'], bold=True),
            cell(row['nama_aktivitas']),
            cell(row['opd']),
            cell(row['penanggung_jawab']),
            dpia_cell(row.get('dpia_required', False)),
        ])

    tbl = Table(table_data, colWidths=col_widths, repeatRows=1)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,0),  WHITE),
        ('FONTNAME',      (0,0), (-1,0),  FONT_BOLD),
        ('ALIGN',         (0,0), (-1,0),  'CENTER'),
        ('VALIGN',        (0,0), (-1,-1), 'TOP'),
        ('BACKGROUND',    (0,1), (-1,-1), WHITE),
        ('GRID',          (0,0), (-1,-1), 0.75, BLACK),
        ('BOX',           (0,0), (-1,-1), 0.75, BLACK),
        ('TOPPADDING',    (0,0), (-1,-1), 2),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2),
        ('LEFTPADDING',   (0,0), (-1,-1), 3),
        ('RIGHTPADDING',  (0,0), (-1,-1), 3),
    ]))

    story = [build_filter_summary(meta), Spacer(1, 1*mm), tbl]

    if not rows:
        story += [Spacer(1, 8*mm), Paragraph(
            '<i>Tidak ada aktivitas RoPA yang sesuai dengan filter yang dipilih.</i>',
            ParagraphStyle('e', fontName=FONT, fontSize=9,
                textColor=GRAY, alignment=TA_CENTER))]

    doc.build(story,
        onFirstPage=lambda c,d: make_header(c,d,meta,logo1_src,logo2_src),
        onLaterPages=lambda c,d: make_header(c,d,meta,logo1_src,logo2_src))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Usage: python3 generate_ropa_list_pdf.py <output_path>', file=sys.stderr)
        sys.exit(1)
    try:
        payload = json.loads(sys.stdin.read())
    except json.JSONDecodeError as e:
        print(f'JSON error: {e}', file=sys.stderr)
        sys.exit(1)

    script_dir = os.path.dirname(os.path.abspath(__file__))
    public_dir = os.path.join(script_dir, '..', 'public', 'images')
    meta       = payload.get('meta', {})
    logo1_path = meta.get('logo1_path') or os.path.join(public_dir, 'logobaliprovcsirt.png')
    logo2_path = meta.get('logo2_path') or os.path.join(public_dir, 'tlp', 'tlp_teaser_amber_strict.jpg')

    build_pdf(sys.argv[1], meta, payload.get('rows', []), logo1_path, logo2_path)
    print(f'PDF generated: {sys.argv[1]}', file=sys.stderr)