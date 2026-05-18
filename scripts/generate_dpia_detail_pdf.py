#!/usr/bin/env python3
"""
PERISAI — DPIA Detail PDF Generator (Portrait A4)
Input: JSON via stdin
{
  "meta": { "tahun": "2026", "generated_at": "..." },
  "dpia": { ... }
}
"""

import sys, json, os, io
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER, TA_JUSTIFY
from reportlab.platypus import (SimpleDocTemplate, Paragraph, Spacer,
                                 Table, TableStyle, HRFlowable, PageBreak)
try:
    from PIL import Image as PILImage
    HAS_PILLOW = True
except ImportError:
    HAS_PILLOW = False
from reportlab.lib.utils import ImageReader

BLACK = colors.black
WHITE = colors.white
GRAY  = colors.HexColor('#555555')
LGRAY = colors.HexColor('#cccccc')

PAGE_W, PAGE_H = A4
MARGIN    = 15 * mm
CONTENT_W = PAGE_W - MARGIN * 2

FONT      = 'Helvetica'
FONT_BOLD = 'Helvetica-Bold'
FS_FOOTER = 7
FS_TD     = 8
FS_TH     = 8
FS_BODY   = 9


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


def make_header(canvas_obj, doc, meta, logo1_src, logo2_src):
    canvas_obj.saveState()
    logo_h = 18*mm; logo_w = 18*mm; logo_gap = 3*mm
    logos_w = (logo_w+logo_gap)*2
    bar_h = 28*mm; bar_y = PAGE_H-bar_h; tx = MARGIN

    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, 13)
    canvas_obj.drawString(tx, PAGE_H-13*mm,
        'DATA PROTECTION IMPACT ASSESSMENT (DPIA)')
    canvas_obj.setFont(FONT_BOLD, 10)
    canvas_obj.drawString(tx, PAGE_H-20*mm,
        f"PEMERINTAH PROVINSI BALI  \u00b7  Tahun {meta.get('tahun','')}")

    logo1_x = PAGE_W-MARGIN-logos_w
    logo2_x = logo1_x+logo_w+logo_gap
    logo_y  = bar_y+(bar_h-logo_h)/2

    if isinstance(logo1_src, str) and not os.path.exists(logo1_src):
        canvas_obj.setStrokeColor(LGRAY); canvas_obj.setLineWidth(0.5)
        canvas_obj.rect(logo1_x, logo_y, logo_w, logo_h, fill=0, stroke=1)
        canvas_obj.setFillColor(GRAY); canvas_obj.setFont(FONT_BOLD, 5)
        canvas_obj.drawCentredString(logo1_x+logo_w/2, logo_y+logo_h/2, 'CSIRT')
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

    canvas_obj.setFillColor(GRAY); canvas_obj.setFont(FONT, FS_FOOTER)
    canvas_obj.drawString(MARGIN, 6*mm, f"Dicetak: {meta.get('generated_at','')}")
    canvas_obj.drawRightString(PAGE_W-MARGIN, 6*mm,
        f"PERISAI  \u00b7  DPIA  \u00b7  Hal {doc.page}")
    canvas_obj.restoreState()


def p(text, size=FS_BODY, bold=False, color=BLACK, align=TA_LEFT):
    return Paragraph(str(text) if text else '-',
        ParagraphStyle('p', fontName=FONT_BOLD if bold else FONT,
            fontSize=size, textColor=color,
            leading=size*1.4, alignment=align, wordWrap='CJK'))


def section_title(text):
    return Paragraph(text.upper(),
        ParagraphStyle('st', fontName=FONT_BOLD, fontSize=10,
            textColor=BLACK, leading=13, spaceBefore=4, spaceAfter=3))


def sub_title(text):
    return Paragraph(text,
        ParagraphStyle('sub', fontName=FONT_BOLD, fontSize=9,
            textColor=BLACK, leading=12, spaceBefore=4, spaceAfter=2))


def body(text):
    return Paragraph(str(text) if text else '-',
        ParagraphStyle('b', fontName=FONT, fontSize=FS_BODY,
            textColor=BLACK, leading=FS_BODY*1.5,
            alignment=TA_JUSTIFY, wordWrap='CJK'))


def bullet(text):
    return Paragraph(f"\u2022  {text}",
        ParagraphStyle('bl', fontName=FONT, fontSize=FS_BODY,
            textColor=BLACK, leading=FS_BODY*1.4, leftIndent=10))


def info_table(rows, col_ratio=(0.35, 0.65)):
    col_w = [CONTENT_W*col_ratio[0], CONTENT_W*col_ratio[1]]
    data = []
    for label, value in rows:
        data.append([
            Paragraph(label, ParagraphStyle('L', fontName=FONT_BOLD,
                fontSize=FS_TD, textColor=BLACK, leading=FS_TD*1.4)),
            Paragraph(str(value) if value else '-', ParagraphStyle('V',
                fontName=FONT, fontSize=FS_TD,
                textColor=BLACK, leading=FS_TD*1.4, wordWrap='CJK')),
        ])
    tbl = Table(data, colWidths=col_w)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0,0),(-1,-1), WHITE),
        ('GRID',          (0,0),(-1,-1), 0.75, BLACK),
        ('BOX',           (0,0),(-1,-1), 0.75, BLACK),
        ('TOPPADDING',    (0,0),(-1,-1), 4),
        ('BOTTOMPADDING', (0,0),(-1,-1), 4),
        ('LEFTPADDING',   (0,0),(-1,-1), 5),
        ('RIGHTPADDING',  (0,0),(-1,-1), 5),
        ('VALIGN',        (0,0),(-1,-1), 'TOP'),
    ]))
    return tbl


def threshold_table(triggers):
    hs = ParagraphStyle('th', fontName=FONT_BOLD, fontSize=FS_TH,
        textColor=BLACK, alignment=TA_CENTER, leading=10)
    col_w = [CONTENT_W*0.58, CONTENT_W*0.14, CONTENT_W*0.28]
    data = [[
        Paragraph('Trigger Wajib DPIA (Pasal 34 ayat 2 UU PDP)', hs),
        Paragraph('Terpenuhi?', hs),
        Paragraph('Keterangan', hs),
    ]]
    style_cmds = [
        ('GRID',          (0,0),(-1,-1), 0.75, BLACK),
        ('BOX',           (0,0),(-1,-1), 0.75, BLACK),
        ('BACKGROUND',    (0,0),(-1, 0), colors.HexColor('#E9ECEF')),
        ('VALIGN',        (0,0),(-1,-1), 'TOP'),
        ('TOPPADDING',    (0,0),(-1,-1), 3),
        ('BOTTOMPADDING', (0,0),(-1,-1), 3),
        ('LEFTPADDING',   (0,0),(-1,-1), 4),
        ('RIGHTPADDING',  (0,0),(-1,-1), 4),
    ]
    for i, t in enumerate(triggers, 1):
        terpenuhi = t.get('terpenuhi', False)
        data.append([
            p(t.get('trigger', '-'), size=FS_TD),
            Paragraph('YA' if terpenuhi else 'Tidak',
                ParagraphStyle('yn', fontName=FONT_BOLD, fontSize=FS_TD,
                    textColor=BLACK if terpenuhi else GRAY,
                    alignment=TA_CENTER, leading=10)),
            p(t.get('keterangan', '-'), size=FS_TD),
        ])
        if terpenuhi:
            style_cmds.append(('BACKGROUND', (1,i),(1,i),
                colors.HexColor('#D4EDDA')))
    tbl = Table(data, colWidths=col_w, repeatRows=1)
    tbl.setStyle(TableStyle(style_cmds))
    return tbl


def build_pdf(output_path, meta, dpia, logo1_path, logo2_path):
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = (load_image_no_bg(logo2_path) if logo2_path.endswith('.png')
                 else (logo2_path if os.path.exists(logo2_path) else logo2_path))

    doc = SimpleDocTemplate(output_path, pagesize=A4,
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=30*mm, bottomMargin=14*mm,
        title=f"DPIA {dpia.get('dpia_kode', '')}",
        author='PERISAI - Pemprov Bali')

    story = []
    LEVEL_COLOR = {
        'Tinggi': colors.HexColor('#F8D7DA'),
        'Sedang': colors.HexColor('#FFF3CD'),
        'Rendah': colors.HexColor('#D4EDDA'),
    }

    # ── Cover & Identitas
    story.append(Spacer(1, 4*mm))
    story.append(Paragraph(dpia.get('dpia_kode', ''),
        ParagraphStyle('kode', fontName=FONT_BOLD, fontSize=20,
            textColor=BLACK, leading=24, spaceAfter=3)))
    story.append(Paragraph(dpia.get('nama_aktivitas', ''),
        ParagraphStyle('nama', fontName=FONT_BOLD, fontSize=13,
            textColor=BLACK, leading=17, spaceAfter=6)))
    story.append(HRFlowable(width=CONTENT_W, thickness=1.5,
        color=BLACK, spaceAfter=6))

    story.append(info_table([
        ('OPD / Unit Kerja',      dpia.get('opd', '-')),
        ('Penanggung Jawab',       dpia.get('penanggung_jawab', '-')),
        ('Pejabat Pelindung Data', dpia.get('ppd', '-') or '-'),
        ('Referensi RoPA',         f"{dpia.get('ropa_kode', '-')}  —  {dpia.get('ropa_nama', '-')}"),
        ('Tanggal Penyusunan',     dpia.get('tanggal', '-')),
        ('Versi Dokumen',          dpia.get('versi', '1.0')),
        ('Klasifikasi Dokumen',    'INTERNAL — TLP:AMBER+STRICT'),
    ]))

    story.append(Spacer(1, 5*mm))

    # ══ A. THRESHOLD ══════════════════════════════
    story.append(section_title('A. Threshold Analysis — Alasan Wajib DPIA'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))
    story.append(body(
        'Berikut analisis trigger kewajiban DPIA berdasarkan Pasal 34 ayat 2 '
        'UU No.27/2022 tentang Pelindungan Data Pribadi:'
    ))
    story.append(Spacer(1, 3*mm))
    story.append(threshold_table(dpia.get('threshold', [])))
    story.append(Spacer(1, 4*mm))

    # ══ B. TIM & KONSULTASI ════════════════════════
    story.append(section_title('B. Tim yang Terlibat & Konsultasi Stakeholder'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))

    story.append(sub_title('B.1 Sumber Daya & Tim'))
    tim = dpia.get('tim_terlibat', [])
    if tim:
        for item in tim:
            story.append(bullet(item))
    else:
        story.append(body('-'))
    story.append(Spacer(1, 3*mm))

    story.append(sub_title('B.2 Konsultasi Pemangku Kepentingan'))
    story.append(body(dpia.get('konsultasi_stakeholder', '-')))

    story.append(PageBreak())

    # ══ C. ASESMEN RISIKO ══════════════════════════
    story.append(section_title('C. Asesmen Risiko'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))

    story.append(sub_title('C.1 Kriteria Penilaian Risiko'))
    story.append(body(dpia.get('kriteria_risiko', '-')))
    story.append(Spacer(1, 3*mm))

    # C.2 — tabel ancaman tunggal
    story.append(sub_title('C.2 Identifikasi Ancaman & Rencana Mitigasi'))
    risiko = dpia.get('risiko') or {}
    if risiko and risiko.get('ancaman'):
        level = risiko.get('level', 'Sedang')
        hs2 = ParagraphStyle('th2', fontName=FONT_BOLD, fontSize=FS_TD,
            textColor=BLACK, alignment=TA_CENTER, leading=10)
        col_w2 = [CONTENT_W*0.28, CONTENT_W*0.13, CONTENT_W*0.13, CONTENT_W*0.13, CONTENT_W*0.33]
        tbl_data = [
            [Paragraph('ANCAMAN', hs2), Paragraph('LIKELIHOOD', hs2),
             Paragraph('DAMPAK', hs2), Paragraph('LEVEL', hs2),
             Paragraph('RENCANA MITIGASI', hs2)],
            [p(risiko.get('ancaman', '-'), size=FS_TD),
             p(risiko.get('likelihood', '-'), size=FS_TD, align=TA_CENTER),
             p(risiko.get('dampak', '-'), size=FS_TD, align=TA_CENTER),
             Paragraph(f"<b>{level}</b>", ParagraphStyle('lv', fontName=FONT_BOLD,
                fontSize=FS_TD, alignment=TA_CENTER, leading=10,
                textColor=BLACK)),
             p(risiko.get('referensi_mitigasi', '-'), size=FS_TD)],
        ]
        tbl2 = Table(tbl_data, colWidths=col_w2)
        tbl2.setStyle(TableStyle([
            ('GRID',          (0,0),(-1,-1), 0.75, BLACK),
            ('BOX',           (0,0),(-1,-1), 0.75, BLACK),
            ('BACKGROUND',    (0,0),(-1, 0), colors.HexColor('#E9ECEF')),
            ('BACKGROUND',    (3,1),(3,  1), LEVEL_COLOR.get(level, WHITE)),
            ('VALIGN',        (0,0),(-1,-1), 'TOP'),
            ('TOPPADDING',    (0,0),(-1,-1), 3),
            ('BOTTOMPADDING', (0,0),(-1,-1), 3),
            ('LEFTPADDING',   (0,0),(-1,-1), 4),
            ('RIGHTPADDING',  (0,0),(-1,-1), 4),
        ]))
        story.append(tbl2)
    else:
        story.append(body('-'))
    story.append(Spacer(1, 3*mm))

    # C.3 — evaluasi residual 3 field
    story.append(sub_title('C.3 Evaluasi Risiko Residual'))
    story.append(info_table([
        ('Technical Security Controls',
            risiko.get('residual_technical', '-') if risiko else '-'),
        ('Privacy Governance Controls',
            risiko.get('residual_privacy', '-') if risiko else '-'),
        ('Organizational Governance Controls',
            risiko.get('residual_organizational', '-') if risiko else '-'),
    ]))

    story.append(PageBreak())

    # ══ D. KESIMPULAN ══════════════════════════════
    story.append(section_title('D. Kesimpulan & Keputusan'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))
    story.append(body(dpia.get('kesimpulan', '-')))
    story.append(Spacer(1, 8*mm))

    story.append(sub_title('Persetujuan & Pengesahan'))

    doc.build(story,
        onFirstPage=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src),
        onLaterPages=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Usage: python3 generate_dpia_detail_pdf.py <output_path>', file=sys.stderr)
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

    build_pdf(sys.argv[1], meta, payload.get('dpia', {}), logo1_path, logo2_path)
    print(f'DPIA PDF generated: {sys.argv[1]}', file=sys.stderr)
