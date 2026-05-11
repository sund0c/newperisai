#!/usr/bin/env python3
"""
PERISAI — Asset Detail PDF Generator (Portrait A4)
"""
import sys, json, os, io
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
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

PAGE_W, PAGE_H = A4
MARGIN = 15 * mm

FONT      = 'Helvetica'
FONT_BOLD = 'Helvetica-Bold'

MONTHS_ID = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

DATE_FIELDS = {'tgl_lisensi_berakhir', 'tgl_kontrak_berakhir'}

FIELD_LABELS = {
    'pl': {
        'url':                  'URL APLIKASI',
        'versi':                'VERSI',
        'lisensi':              'LISENSI',
        'tgl_lisensi_berakhir': 'TGL. LISENSI BERAKHIR',
        'vendor':               'VENDOR / PENGEMBANG',
        'lead_developer':       'LEAD DEVELOPER',
        'platform':             'PLATFORM',
        'lokasi_hosting':       'LOKASI HOSTING',
        'nama_server_lainnya':  'LOKASI SERVER LAINNYA',
        'nama_server':          'NAMA SERVER',
    },
    'pk': {
        'merk':            'MERK',
        'model':           'MODEL',
        'serial_number':   'SERIAL NUMBER',
        'tahun_perolehan': 'TAHUN PEROLEHAN',
        'kondisi':         'KONDISI',
        'lokasi_fisik':    'LOKASI FISIK',
        'ip_address':      'IP ADDRESS',
        'spesifikasi':     'SPESIFIKASI',
    },
    'sp': {
        'merk':            'MERK',
        'model':           'MODEL',
        'serial_number':   'SERIAL NUMBER',
        'kapasitas':       'KAPASITAS',
        'tahun_perolehan': 'TAHUN PEROLEHAN',
        'kondisi':         'KONDISI',
        'lokasi_fisik':    'LOKASI FISIK',
    },
    'sk': {
        'jabatan':              'JABATAN',
        'unit_kerja':           'UNIT KERJA',
        'no_hp':                'NO. HP',
        'email':                'EMAIL',
        'tipe':                 'TIPE',
        'akses_sistem':         'AKSES SISTEM',
        'tgl_kontrak_berakhir': 'TGL. KONTRAK BERAKHIR',
    },
    'di': {
        'bentuk':           'BENTUK INFORMASI',
        'lokasi_fisik':     'LOKASI FISIK',
        'lokasi_elektronik':'LOKASI ELEKTRONIK',
        'format':           'FORMAT',
        'klasifikasi_data': 'KLASIFIKASI DATA',
        'retensi':          'RETENSI (TAHUN)',
        'enkripsi':         'ENKRIPSI',
        'metode_enkripsi':  'METODE ENKRIPSI',
    },
}


def format_date(val):
    """Convert '2026-12-31' atau '2026-12-31T00:00:00Z' → '31 Desember 2026'."""
    try:
        # Strip ISO time part jika ada: '2026-12-31T00:00:00.000000Z' → '2026-12-31'
        date_part = str(val).split('T')[0].strip()
        parts = date_part.split('-')
        if len(parts) == 3:
            y, m, d = int(parts[0]), int(parts[1]), int(parts[2])
            return f"{d:02d} {MONTHS_ID[m]} {y}"
    except Exception:
        pass
    return val


def load_image_no_bg(path):
    if not HAS_PILLOW or not os.path.exists(path):
        return path
    img = PILImage.open(path).convert('RGBA')
    new_data = [(255, 255, 255, 0) if r > 210 and g > 210 and b > 210 else (r, g, b, a)
                for r, g, b, a in img.getdata()]
    img.putdata(new_data)
    buf = io.BytesIO()
    img.save(buf, format='PNG')
    buf.seek(0)
    return ImageReader(buf)


def make_header(canvas_obj, doc, meta, logo1_src, logo2_src):
    canvas_obj.saveState()

    logo_h   = 20 * mm
    logo_w   = 20 * mm
    logo_gap = 3  * mm
    logos_w  = (logo_w + logo_gap) * 2
    bar_h    = 30 * mm
    bar_y    = PAGE_H - bar_h
    tx       = MARGIN

    # ── Judul
    canvas_obj.setFillColor(BLACK)
    canvas_obj.setFont(FONT_BOLD, 14)
    canvas_obj.drawString(tx, PAGE_H - 20 * mm,
        f"INFORMASI ASET Tahun {meta.get('tahun', '')}")

    # ── Logos kanan
    logo1_x = PAGE_W - MARGIN - logos_w
    logo2_x = logo1_x + logo_w + logo_gap
    logo_y  = bar_y + (bar_h - logo_h) / 2

    if isinstance(logo1_src, str) and not os.path.exists(logo1_src):
        canvas_obj.setStrokeColor(colors.HexColor('#cccccc'))
        canvas_obj.setLineWidth(0.5)
        canvas_obj.rect(logo1_x, logo_y, logo_w, logo_h, fill=0, stroke=1)
        canvas_obj.setFillColor(GRAY)
        canvas_obj.setFont(FONT_BOLD, 5)
        canvas_obj.drawCentredString(logo1_x + logo_w / 2, logo_y + logo_h / 2, 'CSIRT')
    else:
        canvas_obj.drawImage(logo1_src, logo1_x, logo_y,
                             width=logo_w, height=logo_h,
                             preserveAspectRatio=True, anchor='c')

    if isinstance(logo2_src, str) and not os.path.exists(logo2_src):
        badge_h = logo_h / 2 - 1
        cx2     = logo2_x + logo_w / 2
        canvas_obj.setFillColor(colors.HexColor('#dddddd'))
        canvas_obj.rect(logo2_x, logo_y + badge_h + 2, logo_w, badge_h, fill=1, stroke=0)
        canvas_obj.setFillColor(BLACK)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y + badge_h + badge_h / 2 + 1, 'TLP:AMBER')
        canvas_obj.setFillColor(BLACK)
        canvas_obj.rect(logo2_x, logo_y, logo_w, badge_h, fill=1, stroke=0)
        canvas_obj.setFillColor(WHITE)
        canvas_obj.setFont(FONT_BOLD, 6)
        canvas_obj.drawCentredString(cx2, logo_y + badge_h / 2 - 2, '+STRICT')
    else:
        canvas_obj.drawImage(logo2_src, logo2_x, logo_y,
                             width=logo_w, height=logo_h,
                             preserveAspectRatio=True, anchor='c')

    # ── Footer
    canvas_obj.setFillColor(GRAY)
    canvas_obj.setFont(FONT, 6.5)
    canvas_obj.drawString(MARGIN, 7 * mm, f"Dicetak: {meta.get('generated_at', '')}")
    canvas_obj.drawRightString(PAGE_W - MARGIN, 7 * mm, f"PERISAI  \u00b7  Hal {doc.page}")

    canvas_obj.restoreState()


def info_table(rows):
    col_w = [(PAGE_W - MARGIN * 2) * 0.35, (PAGE_W - MARGIN * 2) * 0.65]
    data  = []
    for label, value in rows:
        data.append([
            Paragraph(label, ParagraphStyle('L',
                fontName=FONT, fontSize=8,
                textColor=BLACK, leading=11)),
            Paragraph(str(value) if value else '-', ParagraphStyle('V',
                fontName=FONT, fontSize=8,
                textColor=BLACK, leading=11, wordWrap='CJK')),
        ])
    tbl = Table(data, colWidths=col_w)
    tbl.setStyle(TableStyle([
        ('BACKGROUND',    (0, 0), (-1, -1), WHITE),
        ('GRID',          (0, 0), (-1, -1), 0.75, BLACK),
        ('BOX',           (0, 0), (-1, -1), 0.75, BLACK),
        ('TOPPADDING',    (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING',   (0, 0), (-1, -1), 6),
        ('RIGHTPADDING',  (0, 0), (-1, -1), 6),
        ('VALIGN',        (0, 0), (-1, -1), 'TOP'),
    ]))
    return tbl


def section_title(text):
    return Paragraph(text.upper(),
        ParagraphStyle('ST', fontName=FONT_BOLD, fontSize=12,
                       textColor=BLACK, leading=12, spaceAfter=4))


def build_pdf(output_path, meta, basic_info, detail_data, kodeklas, logo1_path, logo2_path):
    logo1_src = load_image_no_bg(logo1_path)
    logo2_src = (load_image_no_bg(logo2_path) if logo2_path.endswith('.png')
                 else (logo2_path if os.path.exists(logo2_path) else logo2_path))

    doc = SimpleDocTemplate(output_path, pagesize=A4,
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=32 * mm, bottomMargin=15 * mm,
        title=f"Detail Aset {basic_info.get('kode_aset', '')}",
        author='PERISAI - Pemprov Bali')

    story = []

    # ── Kode Aset — besar, bold, no background
    story.append(Paragraph(
        basic_info.get('kode_aset', ''),
        ParagraphStyle('KA', fontName=FONT_BOLD, fontSize=20,
                       textColor=BLACK, leading=20, spaceAfter=4)))
    story.append(Spacer(1, 5 * mm))

    # ── I. Informasi Dasar
    story.append(section_title('I. Informasi Dasar Aset'))
    story.append(Spacer(1, 1 * mm))
    story.append(info_table([
        ('NAMA ASET',       basic_info.get('nama_aset', '-')),
        ('OPD',             basic_info.get('opd', '-')),
        ('KLASIFIKASI',     basic_info.get('klasifikasi', '-')),
        ('SUB KLASIFIKASI', basic_info.get('sub_klasifikasi', '-')),
        ('KETERANGAN',      basic_info.get('keterangan', '-')),
    ]))
    story.append(Spacer(1, 5 * mm))

    # ── II. Kelengkapan Data
    labels      = FIELD_LABELS.get(kodeklas, {})
    detail_rows = []
    for key, label in labels.items():
        val = detail_data.get(key)
        if val is None or val == '':
            val = '-'
        elif key in DATE_FIELDS and val != '-':
            val = format_date(val)
        detail_rows.append((label, val))

    if detail_rows:
        story.append(section_title('II. Kelengkapan Data'))
        story.append(Spacer(1, 1 * mm))
        story.append(info_table(detail_rows))

    doc.build(story,
        onFirstPage=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src),
        onLaterPages=lambda c, d: make_header(c, d, meta, logo1_src, logo2_src))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Usage: python3 generate_asset_detail_pdf.py <output_path>', file=sys.stderr)
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

    build_pdf(sys.argv[1], meta,
              payload.get('basic_info', {}),
              payload.get('detail_data', {}),
              payload.get('kodeklas', ''),
              logo1_path, logo2_path)
    print(f'PDF generated: {sys.argv[1]}', file=sys.stderr)