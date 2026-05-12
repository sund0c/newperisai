#!/usr/bin/env python3
"""
PERISAI — IIV PDF Generator
============================
Script untuk generate laporan PDF Infrastruktur Informasi Vital (IIV).

Dipanggil dari Laravel controller via symfony/process:
    $process = new Process(['python3', $script, $tmpPdf], null, null, json_encode($payload), 60);

Payload JSON shape (dikirim via stdin):
{
    "meta": {
        "tahun"        : "2025",
        "opd"          : "Semua OPD",
        "filter_iiv"   : "Semua",
        "generated_at" : "Selasa, 12 Mei 2026 10:30",
        "generated_by" : "I Putu Admin",
        "total"        : 42,
        "kritis"       : 5,
        "terbatas"     : 20,
        "minor"        : 17,
        "belum"        : 0
    },
    "rows": [
        {
            "no"                    : 1,
            "kode_aset"             : "PL-0001",
            "nama_aset"             : "SIPD",
            "sub_klasifikasi"       : "Aplikasi Web",
            "klasifikasi"           : "Perangkat Lunak",
            "opd"                   : "Dinas Komunikasi dan Informatika",
            "dampak_operasional"    : "KRITIS",
            "dampak_data_informasi" : "TERBATAS",
            "dampak_finansial"      : "MINOR",
            "dampak_umum"           : "TERBATAS",
            "dampak_ketergantungan" : "KRITIS",
            "nilai_iiv"             : "KRITIS"
        }
    ]
}

Instalasi dependensi:
    pip3 install reportlab --break-system-packages

Output: PDF A4 landscape di path argv[1]
"""

import sys
import json
import os
from datetime import datetime

# ── Cek dependensi ────────────────────────────────────────────────────────────
try:
    from reportlab.lib.pagesizes import A4, landscape
    from reportlab.lib import colors
    from reportlab.lib.units import mm
    from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
    from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT
    from reportlab.platypus import (
        SimpleDocTemplate, Table, TableStyle, Paragraph,
        Spacer, HRFlowable, KeepTogether
    )
    from reportlab.pdfbase import pdfmetrics
    from reportlab.pdfbase.ttfonts import TTFont
except ImportError:
    sys.stderr.write("ERROR: reportlab tidak terinstal.\n")
    sys.stderr.write("Jalankan: pip3 install reportlab --break-system-packages\n")
    sys.exit(1)

# ── Konstanta warna PERISAI ───────────────────────────────────────────────────
NAVY        = colors.HexColor('#1e3a5f')
NAVY_LIGHT  = colors.HexColor('#2d5382')
SLATE       = colors.HexColor('#475569')
SLATE_LIGHT = colors.HexColor('#94a3b8')
WHITE       = colors.white
ROW_ALT     = colors.HexColor('#f8fafc')
ROW_NORMAL  = colors.white

# Warna nilai IIV
COL_KRITIS    = colors.HexColor('#fef2f2')
TXT_KRITIS    = colors.HexColor('#dc2626')
BORDER_KRITIS = colors.HexColor('#fca5a5')

COL_TERBATAS    = colors.HexColor('#fff7ed')
TXT_TERBATAS    = colors.HexColor('#ea580c')
BORDER_TERBATAS = colors.HexColor('#fdba74')

COL_MINOR    = colors.HexColor('#f0fdf4')
TXT_MINOR    = colors.HexColor('#16a34a')
BORDER_MINOR = colors.HexColor('#86efac')

COL_BELUM    = colors.HexColor('#f1f5f9')
TXT_BELUM    = colors.HexColor('#94a3b8')


# ── Helpers ───────────────────────────────────────────────────────────────────

def iiv_bg(val: str):
    """Return background color sesuai nilai IIV."""
    v = str(val).upper()
    if v == 'KRITIS':   return COL_KRITIS
    if v == 'TERBATAS': return COL_TERBATAS
    if v == 'MINOR':    return COL_MINOR
    return COL_BELUM

def iiv_txt(val: str):
    v = str(val).upper()
    if v == 'KRITIS':   return TXT_KRITIS
    if v == 'TERBATAS': return TXT_TERBATAS
    if v == 'MINOR':    return TXT_MINOR
    return TXT_BELUM

def iiv_short(val: str) -> str:
    v = str(val).upper()
    if v == 'KRITIS':   return 'K'
    if v == 'TERBATAS': return 'T'
    if v == 'MINOR':    return 'M'
    return '—'


def make_styles():
    base = getSampleStyleSheet()

    normal = ParagraphStyle(
        'NormalSmall',
        parent=base['Normal'],
        fontSize=7,
        leading=9,
        textColor=SLATE,
    )
    bold_small = ParagraphStyle(
        'BoldSmall',
        parent=normal,
        fontName='Helvetica-Bold',
        textColor=colors.HexColor('#1e293b'),
    )
    center_small = ParagraphStyle(
        'CenterSmall',
        parent=normal,
        alignment=TA_CENTER,
    )
    header_th = ParagraphStyle(
        'HeaderTH',
        parent=base['Normal'],
        fontSize=7,
        leading=9,
        textColor=WHITE,
        fontName='Helvetica-Bold',
        alignment=TA_CENTER,
    )
    kode_style = ParagraphStyle(
        'KodeStyle',
        parent=normal,
        fontName='Courier-Bold',
        fontSize=7,
        textColor=colors.HexColor('#4f46e5'),
    )
    return {
        'normal': normal,
        'bold': bold_small,
        'center': center_small,
        'th': header_th,
        'kode': kode_style,
    }


# ── Build PDF ─────────────────────────────────────────────────────────────────

def build_pdf(payload: dict, output_path: str):
    meta = payload.get('meta', {})
    rows = payload.get('rows', [])

    PAGE = landscape(A4)
    MARGIN = 15 * mm

    doc = SimpleDocTemplate(
        output_path,
        pagesize=PAGE,
        leftMargin=MARGIN,
        rightMargin=MARGIN,
        topMargin=MARGIN,
        bottomMargin=12 * mm,
        title=f"Laporan IIV PERISAI {meta.get('tahun','')}",
        author='PERISAI - Pemprov Bali',
        subject='Infrastruktur Informasi Vital',
    )

    styles = make_styles()
    story  = []
    W      = PAGE[0] - 2 * MARGIN  # lebar konten

    # ── 1. Header ────────────────────────────────────────────────────────────

    # Logo placeholder (kotak navy)
    logo_data = [[
        Paragraph('<b>P</b>', ParagraphStyle(
            'Logo', fontSize=22, textColor=WHITE, alignment=TA_CENTER,
            fontName='Helvetica-Bold',
        ))
    ]]
    logo_tbl = Table(logo_data, colWidths=[14*mm], rowHeights=[14*mm])
    logo_tbl.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), NAVY),
        ('VALIGN',     (0,0), (-1,-1), 'MIDDLE'),
        ('ALIGN',      (0,0), (-1,-1), 'CENTER'),
        ('ROUNDEDCORNERS', [3]),
    ]))

    title_para = Paragraph(
        '<font color="#1e3a5f"><b>PERISAI</b></font>',
        ParagraphStyle('Title', fontSize=16, fontName='Helvetica-Bold',
                       textColor=NAVY, leading=18)
    )
    sub1_para = Paragraph(
        '<b>Laporan Penilaian Infrastruktur Informasi Vital (IIV)</b>',
        ParagraphStyle('Sub1', fontSize=9.5, fontName='Helvetica-Bold',
                       textColor=colors.HexColor('#334155'), leading=12)
    )
    sub2_para = Paragraph(
        'Pemerintah Provinsi Bali — Sistem Manajemen Keamanan Informasi',
        ParagraphStyle('Sub2', fontSize=8, textColor=SLATE_LIGHT, leading=10)
    )

    header_tbl = Table(
        [[logo_tbl, [title_para, sub1_para, sub2_para]]],
        colWidths=[18*mm, W - 18*mm],
    )
    header_tbl.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('LEFTPADDING',  (1,0), (1,0), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
        ('BOTTOMPADDING',(0,0), (-1,-1), 0),
        ('TOPPADDING',   (0,0), (-1,-1), 0),
    ]))
    story.append(header_tbl)
    story.append(HRFlowable(width=W, thickness=2.5, color=NAVY,
                            spaceAfter=5, spaceBefore=6))

    # Meta bar
    meta_str = (
        f"<b>Tahun:</b> {meta.get('tahun','-')}   "
        f"<b>OPD:</b> {meta.get('opd','Semua OPD')}   "
        f"<b>Filter IIV:</b> {meta.get('filter_iiv','Semua')}   "
        f"<b>Dicetak:</b> {meta.get('generated_at','')}   "
        f"<b>Oleh:</b> {meta.get('generated_by','-')}"
    )
    story.append(Paragraph(meta_str, ParagraphStyle(
        'MetaBar', fontSize=7, textColor=SLATE, leading=10
    )))
    story.append(Spacer(1, 5*mm))

    # ── 2. Stat boxes ─────────────────────────────────────────────────────────

    stat_items = [
        ('Total Aset',    str(meta.get('total',0)),    NAVY,         WHITE),
        ('🔴 KRITIS',     str(meta.get('kritis',0)),   COL_KRITIS,   TXT_KRITIS),
        ('🟠 TERBATAS',   str(meta.get('terbatas',0)), COL_TERBATAS, TXT_TERBATAS),
        ('🟢 MINOR',      str(meta.get('minor',0)),    COL_MINOR,    TXT_MINOR),
        ('⬜ Belum Dinilai', str(meta.get('belum',0)), COL_BELUM,    TXT_BELUM),
    ]

    stat_cells = []
    for label, val, bg, fg in stat_items:
        cell = [
            Paragraph(label, ParagraphStyle(
                f'SL{label}', fontSize=6.5, textColor=fg if bg == NAVY else SLATE,
                alignment=TA_CENTER, fontName='Helvetica-Bold', leading=8
            )),
            Paragraph(f'<b>{val}</b>', ParagraphStyle(
                f'SV{label}', fontSize=20, textColor=fg,
                alignment=TA_CENTER, fontName='Helvetica-Bold', leading=24
            )),
        ]
        stat_cells.append(cell)

    stat_col_w = W / 5
    stat_tbl = Table([stat_cells], colWidths=[stat_col_w] * 5, rowHeights=[18*mm])
    stat_style = [
        ('VALIGN',        (0,0), (-1,-1), 'MIDDLE'),
        ('ALIGN',         (0,0), (-1,-1), 'CENTER'),
        ('LEFTPADDING',   (0,0), (-1,-1), 4),
        ('RIGHTPADDING',  (0,0), (-1,-1), 4),
        ('TOPPADDING',    (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('ROUNDEDCORNERS', [4]),
    ]
    for i, (_, _, bg, _) in enumerate(stat_items):
        stat_style.append(('BACKGROUND', (i,0), (i,0), bg))
        stat_style.append(('BOX', (i,0), (i,0),
                           0.5, BORDER_KRITIS if bg == COL_KRITIS else
                                BORDER_TERBATAS if bg == COL_TERBATAS else
                                BORDER_MINOR if bg == COL_MINOR else
                                colors.HexColor('#cbd5e1')))
    stat_tbl.setStyle(TableStyle(stat_style))
    story.append(stat_tbl)
    story.append(Spacer(1, 4*mm))

    # ── 3. Tabel data ─────────────────────────────────────────────────────────

    # Lebar kolom (total = W)
    # NO | KODE | NAMA ASET | OPD | KLAS | OPS | DATA | FIN | UMUM | KTRGT | IIV
    CW = [
        8*mm,   # no
        22*mm,  # kode
        55*mm,  # nama
        48*mm,  # opd
        28*mm,  # klasifikasi
        14*mm,  # ops
        14*mm,  # data
        14*mm,  # fin
        14*mm,  # umum
        16*mm,  # ketergt
        18*mm,  # nilai iiv
    ]
    # Sesuaikan sisa lebar ke nama aset & opd
    used = sum(CW)
    diff = W - used
    CW[2] += diff / 2
    CW[3] += diff / 2

    th = styles['th']
    headers = [
        Paragraph('No', th),
        Paragraph('Kode Aset', th),
        Paragraph('Nama Aset', th),
        Paragraph('OPD', th),
        Paragraph('Klasifikasi', th),
        Paragraph('Ops', th),
        Paragraph('Data', th),
        Paragraph('Fin', th),
        Paragraph('Umum', th),
        Paragraph('Ketergt.', th),
        Paragraph('Nilai IIV', th),
    ]

    tbl_data = [headers]

    for r in rows:
        val_iiv = str(r.get('nilai_iiv', '—')).upper()
        row_cells = [
            Paragraph(str(r.get('no', '')), styles['center']),
            Paragraph(str(r.get('kode_aset', '-')), styles['kode']),
            Paragraph(
                f"<b>{r.get('nama_aset','-')}</b>"
                + (f"<br/><font size='6' color='#94a3b8'>{r.get('sub_klasifikasi','')}</font>"
                   if r.get('sub_klasifikasi') else ''),
                styles['normal']
            ),
            Paragraph(str(r.get('opd', '-')), styles['normal']),
            Paragraph(str(r.get('klasifikasi', '-')), styles['center']),
        ]
        # 5 dimensi — tampilkan huruf K/T/M dengan warna
        for dim_key in ['dampak_operasional', 'dampak_data_informasi', 'dampak_finansial',
                        'dampak_umum', 'dampak_ketergantungan']:
            val_dim = str(r.get(dim_key, '—')).upper()
            short   = iiv_short(val_dim)
            color   = iiv_txt(val_dim).hexval() if hasattr(iiv_txt(val_dim), 'hexval') else '#000000'
            # Konversi color object ke hex string
            txt_color = iiv_txt(val_dim)
            hex_color = '#{:02X}{:02X}{:02X}'.format(
                int(txt_color.red * 255),
                int(txt_color.green * 255),
                int(txt_color.blue * 255),
            )
            row_cells.append(Paragraph(
                f'<font color="{hex_color}"><b>{short}</b></font>',
                styles['center']
            ))
        # Nilai IIV final
        row_cells.append(Paragraph(
            f'<b>{val_iiv if val_iiv != "—" else "Belum"}</b>',
            ParagraphStyle(
                f'IivCell{r.get("no",0)}',
                fontSize=7, fontName='Helvetica-Bold',
                alignment=TA_CENTER,
                textColor=iiv_txt(val_iiv),
            )
        ))
        tbl_data.append(row_cells)

    main_tbl = Table(tbl_data, colWidths=CW, repeatRows=1)

    tbl_style = [
        # Header row
        ('BACKGROUND',    (0,0), (-1,0), NAVY),
        ('TEXTCOLOR',     (0,0), (-1,0), WHITE),
        ('FONTNAME',      (0,0), (-1,0), 'Helvetica-Bold'),
        ('FONTSIZE',      (0,0), (-1,0), 7),
        ('ALIGN',         (0,0), (-1,0), 'CENTER'),
        ('VALIGN',        (0,0), (-1,0), 'MIDDLE'),
        ('ROWBACKGROUNDS',(0,1), (-1,-1), [ROW_NORMAL, ROW_ALT]),
        # Grid
        ('GRID',          (0,0), (-1,-1), 0.4, colors.HexColor('#e2e8f0')),
        ('LINEBELOW',     (0,0), (-1,0), 1,   NAVY_LIGHT),
        # Padding
        ('TOPPADDING',    (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING',   (0,0), (-1,-1), 4),
        ('RIGHTPADDING',  (0,0), (-1,-1), 4),
        # Vertikal align
        ('VALIGN',        (0,1), (-1,-1), 'MIDDLE'),
        # Kolom center: No, dimensi, IIV
        ('ALIGN', (0,1), (0,-1), 'CENTER'),
        ('ALIGN', (5,1), (-1,-1), 'CENTER'),
    ]

    # Warna background kolom nilai_iiv per baris
    for i, r in enumerate(rows, start=1):
        val = str(r.get('nilai_iiv', '')).upper()
        bg = iiv_bg(val)
        if bg != ROW_NORMAL:
            tbl_style.append(('BACKGROUND', (10, i), (10, i), bg))

    main_tbl.setStyle(TableStyle(tbl_style))
    story.append(main_tbl)

    # ── 4. Keterangan legend ──────────────────────────────────────────────────

    story.append(Spacer(1, 5*mm))
    legend_style = ParagraphStyle('Legend', fontSize=6.5, textColor=SLATE, leading=9)
    legend_text = (
        '<b>Keterangan Kolom:</b> '
        '<b>Ops</b>=Dampak Operasional | '
        '<b>Data</b>=Dampak Data/Informasi | '
        '<b>Fin</b>=Dampak Finansial | '
        '<b>Umum</b>=Dampak Umum/Sosial | '
        '<b>Ketergt.</b>=Dampak Ketergantungan'
        '<br/>'
        '<b>Nilai:</b> '
        '<font color="#dc2626"><b>K=KRITIS</b></font> (Gangguan skala nasional, pemulihan &gt;24 jam) | '
        '<font color="#ea580c"><b>T=TERBATAS</b></font> (Gangguan lingkup provinsi, &lt;24 jam) | '
        '<font color="#16a34a"><b>M=MINOR</b></font> (Gangguan sangat kecil / tidak berdampak)'
        '<br/>'
        '<b>Nilai IIV Final</b> = nilai tertinggi dari ke-5 dimensi. '
        'Satu dimensi KRITIS sudah cukup menjadikan aset tersebut <font color="#dc2626"><b>KRITIS</b></font>.'
    )
    story.append(Paragraph(legend_text, legend_style))

    # ── 5. Footer (via onFirstPage / onLaterPages) ────────────────────────────

    def add_footer(canvas, doc):
        canvas.saveState()
        canvas.setFont('Helvetica', 6.5)
        canvas.setFillColor(SLATE_LIGHT)

        footer_y = 8 * mm
        canvas.drawString(MARGIN, footer_y,
                          'PERISAI — Pemerintah Provinsi Bali — Rahasia / Terbatas')
        page_str = f'Halaman {doc.page}'
        canvas.drawRightString(PAGE[0] - MARGIN, footer_y, page_str)

        canvas.setStrokeColor(colors.HexColor('#e2e8f0'))
        canvas.setLineWidth(0.5)
        canvas.line(MARGIN, footer_y + 4*mm, PAGE[0] - MARGIN, footer_y + 4*mm)
        canvas.restoreState()

    doc.build(story, onFirstPage=add_footer, onLaterPages=add_footer)


# ── Entry point ───────────────────────────────────────────────────────────────

if __name__ == '__main__':
    if len(sys.argv) < 2:
        sys.stderr.write("Usage: echo '<json>' | python3 generate_iiv_pdf.py /tmp/output.pdf\n")
        sys.exit(1)

    output_path = sys.argv[1]

    try:
        raw = sys.stdin.read()
        if not raw.strip():
            sys.stderr.write("ERROR: Tidak ada data JSON dari stdin.\n")
            sys.exit(1)
        payload = json.loads(raw)
    except json.JSONDecodeError as e:
        sys.stderr.write(f"ERROR: JSON tidak valid — {e}\n")
        sys.exit(1)

    try:
        build_pdf(payload, output_path)
        sys.stdout.write(f"OK: {output_path}\n")
    except Exception as e:
        import traceback
        sys.stderr.write(f"ERROR: Gagal generate PDF — {e}\n")
        sys.stderr.write(traceback.format_exc())
        sys.exit(1)
