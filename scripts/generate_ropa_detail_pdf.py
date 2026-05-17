#!/usr/bin/env python3
"""
PERISAI — RoPA Detail PDF Generator (Portrait A4)
Sections: Informasi Umum | Data Pribadi | Penyimpanan & Pemrosesan |
          Pengamanan | Penerima Data | Hak & Risiko
"""

import sys, json, os, io
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER
from reportlab.platypus import (SimpleDocTemplate, Paragraph, Spacer, PageBreak,
                                 Table, TableStyle, HRFlowable)
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

DASAR_LABELS = {
    'consent':                  'Persetujuan eksplisit subjek data (Consent)',
    'contractual':              'Pemenuhan kewajiban perjanjian (Contractual)',
    'legal_obligation':         'Kewajiban hukum pengendali (Legal Obligation)',
    'vital_interests':          'Perlindungan kepentingan vital subjek (Vital Interests)',
    'public_interests':         'Kepentingan umum / pelayanan publik (Public Interests)',
    'legitimate_interests':     'Kepentingan sah lainnya (Legitimate Interests)',
    'keseimbangan_kepentingan': 'Keseimbangan kepentingan Pengendali dan hak Subjek Data Pribadi',
}

HAK_LABELS = {
    5:  'Hak mendapatkan informasi pemrosesan (Pasal 5)',
    6:  'Hak memutakhirkan data pribadinya (Pasal 6)',
    7:  'Hak akses dan mendapatkan salinan (Pasal 7)',
    8:  'Hak mengakhiri pemrosesan / menghapus (Pasal 8)',
    9:  'Hak menarik persetujuan (Pasal 9)',
    10: 'Hak keberatan atas pemrosesan otomatis (Pasal 10)',
    11: 'Hak menunda atau membatasi pemrosesan (Pasal 11)',
    12: 'Hak atas gugatan ganti rugi (Pasal 12)',
    13: 'Hak interoperabilitas (Pasal 13)',
}


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

    logo_h   = 18 * mm
    logo_w   = 18 * mm
    logo_gap = 3  * mm
    logos_w  = (logo_w + logo_gap) * 2
    bar_h    = 28 * mm
    bar_y    = PAGE_H - bar_h
    tx       = MARGIN

    # Judul — hitam tebal
    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, 13)
    canvas_obj.drawString(tx, PAGE_H - 13*mm,
        'RECORD OF PROCESSING ACTIVITIES (RoPA)')

    # Sub judul — Pemerintah Provinsi Bali + Tahun
    canvas_obj.setFont(FONT_BOLD, 10)
    canvas_obj.setFillColor(BLACK)
    canvas_obj.drawString(tx, PAGE_H - 20*mm,
        f"PEMERINTAH PROVINSI BALI  \u00b7  Tahun {meta.get('tahun','')}")

    # Logos kanan
    logo1_x = PAGE_W - MARGIN - logos_w
    logo2_x = logo1_x + logo_w + logo_gap
    logo_y  = bar_y + (bar_h - logo_h) / 2

    if isinstance(logo1_src, str) and not os.path.exists(logo1_src):
        canvas_obj.setStrokeColor(LGRAY)
        canvas_obj.setLineWidth(0.5)
        canvas_obj.rect(logo1_x, logo_y, logo_w, logo_h, fill=0, stroke=1)
        canvas_obj.setFillColor(GRAY)
        canvas_obj.setFont(FONT_BOLD, 5)
        canvas_obj.drawCentredString(logo1_x + logo_w/2, logo_y + logo_h/2, 'CSIRT')
    else:
        canvas_obj.drawImage(logo1_src, logo1_x, logo_y,
            width=logo_w, height=logo_h, preserveAspectRatio=True, anchor='c')

    if isinstance(logo2_src, str) and not os.path.exists(logo2_src):
        badge_h = logo_h/2 - 1
        cx2 = logo2_x + logo_w/2
        canvas_obj.setFillColor(colors.HexColor('#dddddd'))
        canvas_obj.setStrokeColor(BLACK)
        canvas_obj.setLineWidth(0.8)
        canvas_obj.rect(logo2_x, logo_y+badge_h+2, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(BLACK)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y+badge_h+badge_h/2+1, 'TLP:AMBER')
        canvas_obj.setFillColor(BLACK)
        canvas_obj.rect(logo2_x, logo_y, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(WHITE)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y+badge_h/2-2, '+STRICT')
    else:
        canvas_obj.drawImage(logo2_src, logo2_x, logo_y,
            width=logo_w, height=logo_h, preserveAspectRatio=True, anchor='c')

    # Footer
    canvas_obj.setFillColor(GRAY)
    canvas_obj.setFont(FONT, FS_FOOTER)
    canvas_obj.drawString(MARGIN, 6*mm, f"Dicetak: {meta.get('generated_at','')}")
    canvas_obj.drawRightString(PAGE_W - MARGIN, 6*mm,
        f"PERISAI  \u00b7  Hal {doc.page}")

    canvas_obj.restoreState()


def p(text, size=FS_TD, bold=False, color=BLACK, align=TA_LEFT):
    return Paragraph(str(text) if text else '-',
        ParagraphStyle('p',
            fontName=FONT_BOLD if bold else FONT,
            fontSize=size, textColor=color,
            leading=size * 1.35, alignment=align, wordWrap='CJK'))


def section_title(text):
    return Paragraph(text.upper(),
        ParagraphStyle('st',
            fontName=FONT_BOLD, fontSize=10,
            textColor=BLACK, leading=13,
            spaceBefore=6, spaceAfter=2))


def info_table(rows):
    """2-column label-value table — solid black border, uniform thickness."""
    col_w = [CONTENT_W * 0.33, CONTENT_W * 0.67]
    data  = []
    for label, value in rows:
        val_str = str(value) if (value is not None and value != '') else '-'
        data.append([
            Paragraph(label, ParagraphStyle('L',
                fontName=FONT_BOLD, fontSize=FS_TD,
                textColor=BLACK, leading=FS_TD * 1.35)),
            Paragraph(val_str, ParagraphStyle('V',
                fontName=FONT, fontSize=FS_TD,
                textColor=BLACK, leading=FS_TD * 1.35, wordWrap='CJK')),
        ])
    tbl = Table(data, colWidths=col_w)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0, 0), (-1, -1), WHITE),
        ('GRID',          (0, 0), (-1, -1), 0.75, BLACK),
        ('BOX',           (0, 0), (-1, -1), 0.75, BLACK),
        ('TOPPADDING',    (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING',   (0, 0), (-1, -1), 5),
        ('RIGHTPADDING',  (0, 0), (-1, -1), 5),
        ('VALIGN',        (0, 0), (-1, -1), 'TOP'),
    ]))
    return tbl


def bullet_list(items, size=FS_TD):
    if not items:
        return [p('-')]
    return [
        Paragraph(f"\u2022  {item}",
            ParagraphStyle('bl',
                fontName=FONT, fontSize=size,
                textColor=BLACK, leading=size * 1.4, leftIndent=8))
        for item in items
    ]


def recipients_table(recipients):
    if not recipients:
        return [p('Tidak ada penerima data yang terdaftar.', color=GRAY)]

    hs = ParagraphStyle('th',
        fontName=FONT_BOLD, fontSize=FS_TH,
        textColor=BLACK, alignment=TA_CENTER, leading=FS_TH * 1.3)

    col_w = [
        CONTENT_W * 0.22,  # profil
        CONTENT_W * 0.12,  # tipe
        CONTENT_W * 0.14,  # peran
        CONTENT_W * 0.26,  # tujuan
        CONTENT_W * 0.26,  # mekanisme
    ]

    data = [[
        Paragraph('PENERIMA', hs),
        Paragraph('TIPE', hs),
        Paragraph('PERAN', hs),
        Paragraph('TUJUAN PENGIRIMAN', hs),
        Paragraph('MEKANISME', hs),
    ]]

    PERAN_LABELS = {
        'pengendali':         'Pengendali',
        'pengendali_bersama': 'Pengendali Bersama',
        'prosesor':           'Prosesor',
    }

    for r in recipients:
        data.append([
            p(r.get('profil_penerima', '-')),
            p(r.get('tipe', '-').capitalize(), align=TA_CENTER),
            p(PERAN_LABELS.get(r.get('peran', ''), '-') if r.get('tipe') == 'eksternal' else '\u2014', align=TA_CENTER),
            p(r.get('tujuan_pengiriman', '-')),
            p(r.get('mekanisme_pengiriman', '-')),
        ])

    tbl = Table(data, colWidths=col_w, repeatRows=1)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0, 0), (-1,  0), WHITE),
        ('FONTNAME',      (0, 0), (-1,  0), FONT_BOLD),
        ('ALIGN',         (0, 0), (-1,  0), 'CENTER'),
        ('VALIGN',        (0, 0), (-1, -1), 'TOP'),
        ('BACKGROUND',    (0, 1), (-1, -1), WHITE),
        ('GRID',          (0, 0), (-1, -1), 0.75, BLACK),
        ('BOX',           (0, 0), (-1, -1), 0.75, BLACK),
        ('TOPPADDING',    (0, 0), (-1, -1), 3),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 3),
        ('LEFTPADDING',   (0, 0), (-1, -1), 4),
        ('RIGHTPADDING',  (0, 0), (-1, -1), 4),
    ]))
    return [tbl]


def build_pdf(output_path, meta, activity, logo1_path, logo2_path):
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = (load_image_no_bg(logo2_path) if logo2_path.endswith('.png')
                 else (logo2_path if os.path.exists(logo2_path) else logo2_path))

    doc = SimpleDocTemplate(output_path, pagesize=A4,
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=30 * mm, bottomMargin=14 * mm,
        title=f"RoPA {activity.get('kode', '')}",
        author='PERISAI - Pemprov Bali')

    story = []

    # ── Kode Aktivitas
    story.append(Spacer(1, 4*mm))
    story.append(Paragraph(activity.get('kode', ''),
        ParagraphStyle('kode',
            fontName=FONT_BOLD, fontSize=20,
            textColor=BLACK, leading=24, spaceAfter=3)))

    # ── Nama Aktivitas
    story.append(Paragraph(activity.get('nama_aktivitas', ''),
        ParagraphStyle('nama',
            fontName=FONT_BOLD, fontSize=13,
            textColor=BLACK, leading=17, spaceAfter=6)))

    # ── Garis pemisah
    story.append(HRFlowable(width=CONTENT_W, thickness=1.5,
        color=BLACK, spaceAfter=8))

    # ── I. Informasi Umum
    story.append(section_title('I. Informasi Umum'))
    story.append(info_table([
        ('OPD / Unit Kerja',  activity.get('opd', '-')),
        ('Penanggung Jawab',  activity.get('penanggung_jawab', '-')),
        ('Deskripsi & Tujuan', activity.get('deskripsi_tujuan', '-')),
        ('Proses Sebelumnya', activity.get('proses_sebelumnya', '-')),
        ('Proses Setelahnya', activity.get('proses_setelahnya', '-')),
        ('Catatan Tambahan',  activity.get('catatan', '-')),
    ]))

    assets = activity.get('assets', [])
    if assets:
        story.append(Spacer(1, 2*mm))
        story.append(p('Sistem / Aplikasi yang Digunakan:', bold=True, size=FS_TD))
        story += bullet_list([
            f"{a.get('nama', '-')}  [{a.get('peran_aset', 'primer').capitalize()}]"
            for a in assets
        ])

    story.append(Spacer(1, 4*mm))

    # ── II. Data Pribadi
    story.append(section_title('II. Data Pribadi'))
    story.append(info_table([
        ('Subjek Data Pribadi', activity.get('subjek_data', '-')),
        ('Sumber Pemerolehan',  activity.get('sumber_pemerolehan', '-')),
    ]))

    data_umum     = [d['jenis_data'] for d in activity.get('personal_data_types', []) if not d.get('is_spesifik')]
    data_spesifik = [d['jenis_data'] for d in activity.get('personal_data_types', []) if d.get('is_spesifik')]

    story.append(Spacer(1, 2*mm))
    story.append(p('Data Pribadi Umum:', bold=True, size=FS_TD))
    story += bullet_list(data_umum) if data_umum else [p('\u2014')]

    story.append(Spacer(1, 2*mm))
    story.append(p('Data Pribadi Spesifik:', bold=True, size=FS_TD))
    story += bullet_list(data_spesifik) if data_spesifik else [p('\u2014')]

    story.append(Spacer(1, 4*mm))

    # ── III. Penyimpanan & Pemrosesan
    story.append(section_title('III. Penyimpanan & Pemrosesan'))

    metode = []
    if activity.get('metode_elektronik'):     metode.append('Elektronik')
    if activity.get('metode_non_elektronik'): metode.append('Non-Elektronik')

    dasar_list = [
        DASAR_LABELS.get(d['dasar_pemrosesan'], d['dasar_pemrosesan'])
        for d in activity.get('legal_bases', [])
    ]

    story.append(info_table([
        ('Penyimpanan Data',   activity.get('penyimpanan_data', '-')),
        ('Metode Pemrosesan',  ', '.join(metode) if metode else '-'),
        ('Dasar Pemrosesan',   '\n'.join(dasar_list) if dasar_list else '-'),
        ('Referensi Hukum',    activity.get('referensi_dasar_hukum', '-')),
        ('Masa Retensi',       activity.get('masa_retensi', '-')),
    ]))

    # ── IV. Pengamanan — selalu mulai di halaman baru
    story.append(PageBreak())
    story.append(section_title('IV. Pengamanan Data'))
    story.append(info_table([
        ('Langkah Teknis',     activity.get('langkah_teknis', '-')),
        ('Langkah Organisasi', activity.get('langkah_organisasi', '-')),
    ]))

    story.append(Spacer(1, 4*mm))

    # ── V. Penerima Data
    story.append(section_title('V. Penerima Data'))
    story += recipients_table(activity.get('recipients', []))

    story.append(Spacer(1, 4*mm))

    # ── VI. Hak Subjek & Asesmen Risiko
    story.append(section_title('VI. Hak Subjek Data & Asesmen Risiko'))

    INDIKATOR_LABELS = {
        'keputusan_otomatis': 'Pengambilan keputusan otomatis berdampak hukum signifikan',
        'data_spesifik':      'Pemrosesan data pribadi spesifik',
        'skala_besar':        'Pemrosesan skala besar (> 1.000 subjek)',
        'evaluasi_penskoran': 'Evaluasi, penskoran, atau pemantauan sistematis',
        'pencocokan_data':    'Pencocokan atau penggabungan kelompok data',
        'teknologi_baru':     'Penggunaan teknologi baru dalam pemrosesan',
        'membatasi_hak':      'Pemrosesan yang membatasi hak subjek data',
    }

    hak_list = [
        HAK_LABELS.get(int(h['pasal']), str(h['pasal']))
        for h in activity.get('subject_rights', [])
    ]

    indikator_list = [
        INDIKATOR_LABELS.get(i['indikator'], i['indikator'])
        for i in activity.get('risk_indicators', [])
    ]

    dpia_required = len(indikator_list) > 0

    rows_risiko = [
        ('Hak Subjek yang Berlaku', '\n'.join(hak_list) if hak_list else '-'),
    ]
    if indikator_list:
        rows_risiko.append(('Indikator Risiko Tinggi (DPIA Diperlukan)', '\n'.join(indikator_list)))
    else:
        rows_risiko.append(('DPIA Diperlukan', 'Tidak — tidak ada indikator risiko tinggi yang terpenuhi'))
    rows_risiko.append(('Narasi Asesmen Risiko', activity.get('narasi_risiko', '-')))

    story.append(info_table(rows_risiko))

    doc.build(story,
        onFirstPage=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src),
        onLaterPages=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Usage: python3 generate_ropa_detail_pdf.py <output_path>', file=sys.stderr)
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

    build_pdf(sys.argv[1], meta, payload.get('activity', {}), logo1_path, logo2_path)
    print(f'PDF generated: {sys.argv[1]}', file=sys.stderr)