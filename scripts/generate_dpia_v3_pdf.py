#!/usr/bin/env python3
"""
PERISAI — DPIA PDF Generator v3 (Portrait A4)
- Referensi RoPA masuk tabel identitas
- Matriks risiko diperkecil jadi ringkasan teks
- Label mitigasi → Rencana Mitigasi
- Ringkasan Publik dihapus
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
        canvas_obj.setFillColor(GRAY); canvas_obj.setFont(FONT_BOLD,5)
        canvas_obj.drawCentredString(logo1_x+logo_w/2, logo_y+logo_h/2,'CSIRT')
    else:
        canvas_obj.drawImage(logo1_src, logo1_x, logo_y,
            width=logo_w, height=logo_h, preserveAspectRatio=True, anchor='c')

    if isinstance(logo2_src, str) and not os.path.exists(logo2_src):
        badge_h = logo_h/2-1; cx2 = logo2_x+logo_w/2
        canvas_obj.setFillColor(colors.HexColor('#dddddd'))
        canvas_obj.setStrokeColor(BLACK); canvas_obj.setLineWidth(0.8)
        canvas_obj.rect(logo2_x, logo_y+badge_h+2, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(BLACK); canvas_obj.setFont(FONT_BOLD,6)
        canvas_obj.drawCentredString(cx2, logo_y+badge_h+badge_h/2+1, 'TLP:AMBER')
        canvas_obj.setFillColor(BLACK)
        canvas_obj.rect(logo2_x, logo_y, logo_w, badge_h, fill=1, stroke=1)
        canvas_obj.setFillColor(WHITE); canvas_obj.setFont(FONT_BOLD,6)
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


def info_table(rows, col_ratio=(0.33, 0.67)):
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
    col_w = [CONTENT_W*0.60, CONTENT_W*0.15, CONTENT_W*0.25]
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


def risk_table(risks):
    """Tabel ancaman dengan kolom Rencana Mitigasi."""
    hs = ParagraphStyle('th', fontName=FONT_BOLD, fontSize=7,
        textColor=BLACK, alignment=TA_CENTER, leading=9)
    cs = ParagraphStyle('td', fontName=FONT, fontSize=7,
        textColor=BLACK, leading=10, wordWrap='CJK')
    cc = ParagraphStyle('tc', fontName=FONT_BOLD, fontSize=7,
        textColor=BLACK, alignment=TA_CENTER, leading=9)

    col_w = [CONTENT_W*0.05, CONTENT_W*0.26, CONTENT_W*0.10,
             CONTENT_W*0.10, CONTENT_W*0.10, CONTENT_W*0.39]

    LEVEL_COLOR = {
        'Tinggi': colors.HexColor('#F8D7DA'),
        'Sedang': colors.HexColor('#FFF3CD'),
        'Rendah': colors.HexColor('#D4EDDA'),
    }

    data = [[
        Paragraph('No', hs),
        Paragraph('Ancaman / Sumber Risiko', hs),
        Paragraph('Likelihood', hs),
        Paragraph('Dampak', hs),
        Paragraph('Level', hs),
        Paragraph('Rencana Mitigasi', hs),
    ]]
    style_cmds = [
        ('GRID',          (0,0),(-1,-1), 0.75, BLACK),
        ('BOX',           (0,0),(-1,-1), 0.75, BLACK),
        ('BACKGROUND',    (0,0),(-1, 0), colors.HexColor('#E9ECEF')),
        ('VALIGN',        (0,0),(-1,-1), 'TOP'),
        ('TOPPADDING',    (0,0),(-1,-1), 3),
        ('BOTTOMPADDING', (0,0),(-1,-1), 3),
        ('LEFTPADDING',   (0,0),(-1,-1), 3),
        ('RIGHTPADDING',  (0,0),(-1,-1), 3),
    ]
    for i, r in enumerate(risks, 1):
        level = r.get('level', 'Sedang')
        data.append([
            Paragraph(str(i), cc),
            Paragraph(r.get('ancaman', '-'), cs),
            Paragraph(r.get('likelihood', '-'), cc),
            Paragraph(r.get('dampak', '-'), cc),
            Paragraph(level, cc),
            Paragraph(r.get('mitigasi', '-'), cs),
        ])
        style_cmds.append(('BACKGROUND', (4,i),(4,i),
            LEVEL_COLOR.get(level, WHITE)))
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
        title=f"DPIA {dpia.get('dpia_kode','')}",
        author='PERISAI - Pemprov Bali')

    story = []

    # ── Cover identitas — Referensi RoPA masuk tabel
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
        ('Pejabat Pelindung Data', dpia.get('ppd', '-')),
        ('Referensi RoPA',         dpia.get('ropa_kode', '-') + '  —  ' + dpia.get('ropa_nama', '-')),
        ('Tanggal Penyusunan',     dpia.get('tanggal', '-')),
        ('Versi Dokumen',          dpia.get('versi', '1.0')),
        ('Klasifikasi Dokumen',    'INTERNAL — TLP:AMBER+STRICT'),
    ]))

    story.append(Spacer(1, 5*mm))

    # ══════════════════════════════════════════════
    # A. THRESHOLD ANALYSIS
    # ══════════════════════════════════════════════
    story.append(section_title('A. Threshold Analysis — Alasan Wajib DPIA'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))
    story.append(body(
        'Berikut analisis trigger kewajiban DPIA berdasarkan Pasal 34 ayat 2 '
        'UU No.27/2022 tentang Pelindungan Data Pribadi:'
    ))
    story.append(Spacer(1, 3*mm))
    story.append(threshold_table(dpia.get('threshold', [])))
    story.append(Spacer(1, 4*mm))

    # ══════════════════════════════════════════════
    # B. TIM & KONSULTASI
    # ══════════════════════════════════════════════
    story.append(section_title('B. Tim yang Terlibat & Konsultasi Stakeholder'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))

    story.append(sub_title('B.1 Sumber Daya & Tim'))
    for item in dpia.get('tim_terlibat', []):
        story.append(bullet(item))
    story.append(Spacer(1, 3*mm))

    story.append(sub_title('B.2 Konsultasi Pemangku Kepentingan'))
    story.append(body(dpia.get('konsultasi_stakeholder', '-')))

    story.append(PageBreak())

    # ══════════════════════════════════════════════
    # C. ASESMEN RISIKO
    # ══════════════════════════════════════════════
    story.append(section_title('C. Asesmen Risiko'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))

    story.append(sub_title('C.1 Kriteria Penilaian Risiko'))
    story.append(body(dpia.get('kriteria_risiko', '-')))
    story.append(Spacer(1, 3*mm))

    story.append(sub_title('C.2 Identifikasi Ancaman & Rencana Mitigasi'))
    story.append(risk_table(dpia.get('risiko', [])))
    story.append(Spacer(1, 3*mm))

    story.append(sub_title('C.3 Evaluasi Risiko Residual'))
    story.append(body(dpia.get('evaluasi_residual', '-')))

    story.append(PageBreak())

    # ══════════════════════════════════════════════
    # D. KESIMPULAN & KEPUTUSAN
    # ══════════════════════════════════════════════
    story.append(section_title('D. Kesimpulan & Keputusan'))
    story.append(HRFlowable(width=CONTENT_W, thickness=0.75, color=BLACK, spaceAfter=4))
    story.append(body(dpia.get('kesimpulan', '-')))
    story.append(Spacer(1, 8*mm))

    # ── Tanda tangan
    story.append(sub_title('Persetujuan & Pengesahan'))
    sign_col = CONTENT_W / 3
    sign_data = [[
        Paragraph('Disusun oleh', ParagraphStyle('sh', fontName=FONT_BOLD,
            fontSize=FS_TD, alignment=TA_CENTER, leading=10)),
        Paragraph('Diperiksa oleh', ParagraphStyle('sh', fontName=FONT_BOLD,
            fontSize=FS_TD, alignment=TA_CENTER, leading=10)),
        Paragraph('Disetujui oleh', ParagraphStyle('sh', fontName=FONT_BOLD,
            fontSize=FS_TD, alignment=TA_CENTER, leading=10)),
    ],[
        Paragraph('\n\n\n\n________________\nAnalis CSIRT',
            ParagraphStyle('ss', fontName=FONT, fontSize=FS_TD,
                alignment=TA_CENTER, leading=10)),
        Paragraph('\n\n\n\n________________\nKa. Bid. Keamanan Informasi',
            ParagraphStyle('ss', fontName=FONT, fontSize=FS_TD,
                alignment=TA_CENTER, leading=10)),
        Paragraph('\n\n\n\n________________\nPejabat Pelindung Data Pribadi',
            ParagraphStyle('ss', fontName=FONT, fontSize=FS_TD,
                alignment=TA_CENTER, leading=10)),
    ]]
    sign_tbl = Table(sign_data, colWidths=[sign_col]*3)
    sign_tbl.setStyle(TableStyle([
        ('GRID',          (0,0),(-1,-1), 0.75, BLACK),
        ('BOX',           (0,0),(-1,-1), 0.75, BLACK),
        ('BACKGROUND',    (0,0),(-1, 0), colors.HexColor('#E9ECEF')),
        ('VALIGN',        (0,0),(-1,-1), 'MIDDLE'),
        ('ALIGN',         (0,0),(-1,-1), 'CENTER'),
        ('TOPPADDING',    (0,0),(-1,-1), 4),
        ('BOTTOMPADDING', (0,0),(-1,-1), 4),
    ]))
    story.append(sign_tbl)

    doc.build(story,
        onFirstPage=lambda c,d: make_header(c,d,meta,logo1_src,logo2_src),
        onLaterPages=lambda c,d: make_header(c,d,meta,logo1_src,logo2_src))


# ── Data DPIA ─────────────────────────────────────────────────
DPIA_DATA = {
    "dpia_kode":      "DPIA-0001",
    "ropa_kode":      "RoPA-0001",
    "ropa_nama":      "Penerimaan dan Pengelolaan Laporan Kerentanan & Insiden Keamanan Siber (CSIRT)",
    "nama_aktivitas": "Penerimaan dan Pengelolaan Laporan Kerentanan & Insiden Keamanan Siber (CSIRT)",
    "opd":            "Dinas Komunikasi, Informatika, dan Statistik Prov. Bali",
    "penanggung_jawab": "Kepala Bidang Persandian dan Keamanan Informasi",
    "ppd":            "Kepala Bidang Keamanan Informasi Diskominfos",
    "tanggal":        "17 Mei 2026",
    "versi":          "1.0",

    "threshold": [
        {
            "trigger":    "Pengambilan keputusan otomatis yang berdampak hukum signifikan",
            "terpenuhi":  False,
            "keterangan": "Tidak ada keputusan otomatis — semua keputusan melibatkan analis manusia",
        },
        {
            "trigger":    "Pemrosesan data pribadi spesifik",
            "terpenuhi":  False,
            "keterangan": "Hanya data kontak biasa (nama, email, telepon) — tidak ada data spesifik",
        },
        {
            "trigger":    "Pemrosesan skala besar (> 1.000 subjek)",
            "terpenuhi":  False,
            "keterangan": "Skala terbatas — laporan CSIRT per tahun relatif kecil",
        },
        {
            "trigger":    "Evaluasi / penskoran / pemantauan sistematis",
            "terpenuhi":  False,
            "keterangan": "Tidak ada pemantauan sistematis terhadap subjek data",
        },
        {
            "trigger":    "Pencocokan atau penggabungan dataset besar",
            "terpenuhi":  False,
            "keterangan": "Data pelapor tidak dicocokkan dengan dataset lain",
        },
        {
            "trigger":    "Penggunaan teknologi baru dalam pemrosesan",
            "terpenuhi":  True,
            "keterangan": "Portal aduan berbasis web dengan sistem ticketing otomatis — teknologi baru bagi Pemprov Bali",
        },
        {
            "trigger":    "Pemrosesan yang berpotensi membatasi hak subjek data",
            "terpenuhi":  True,
            "keterangan": "Identitas pelapor/peneliti keamanan berpotensi terbuka — membahayakan keselamatan pelapor",
        },
    ],

    "tim_terlibat": [
        "Analis CSIRT Diskominfos — penyusun DPIA dan pengelola laporan harian",
        "Kabid Persandian & Keamanan Informasi — reviewer dan approver",
        "Pejabat Pelindung Data Pribadi (PPD) — pengesah dokumen DPIA",
        "OPD pemilik aset terdampak — konsultasi teknis mitigasi",
        "Tim Hukum / Biro Hukum Pemprov Bali — konsultasi aspek regulasi",
    ],
    "konsultasi_stakeholder": (
        "DPIA ini disusun dengan melibatkan konsultasi internal bersama Kabid Persandian, "
        "Tim Hukum Pemprov Bali, dan representatif OPD terkait. Konsultasi eksternal "
        "dilakukan dengan BSSN selaku koordinator CSIRT nasional. Subjek data (pelapor) "
        "dikonsultasikan secara tidak langsung melalui kebijakan Responsible Disclosure "
        "yang dipublikasikan di portal CSIRT."
    ),

    "kriteria_risiko": (
        "Likelihood — Rendah: probabilitas ancaman < 30%; Sedang: 30–70%; Tinggi: > 70%. "
        "Dampak — Rendah: ketidaknyamanan minor; Sedang: kerugian terukur; "
        "Tinggi: bahaya fisik / finansial signifikan / diskriminasi. "
        "Level risiko = kombinasi Likelihood x Dampak."
    ),
    "risiko": [
        {
            "ancaman":    "Keterbukaan identitas pelapor/peneliti ke publik atau pihak tidak berwenang",
            "likelihood": "Sedang",
            "dampak":     "Tinggi",
            "level":      "Tinggi",
            "mitigasi":   "Pseudonimisasi identitas, opsi anonim wajib tersedia, RBAC ketat pada sistem ticketing, NDA seluruh analis, enkripsi komunikasi detail kerentanan",
        },
        {
            "ancaman":    "Akses tidak sah ke sistem ticketing oleh pihak internal",
            "likelihood": "Rendah",
            "dampak":     "Sedang",
            "level":      "Rendah",
            "mitigasi":   "RBAC dengan prinsip least privilege, audit log komprehensif, review akses berkala, MFA pada sistem",
        },
        {
            "ancaman":    "Kebocoran detail teknis kerentanan sebelum dimitigasi (premature disclosure)",
            "likelihood": "Sedang",
            "dampak":     "Tinggi",
            "level":      "Tinggi",
            "mitigasi":   "Kebijakan Responsible Disclosure 90 hari, seluruh komunikasi teknis melalui saluran terenkripsi, NDA OPD penerima disposisi",
        },
        {
            "ancaman":    "Penyalahgunaan data kontak pelapor di luar tujuan penanganan laporan",
            "likelihood": "Rendah",
            "dampak":     "Sedang",
            "level":      "Rendah",
            "mitigasi":   "Purpose limitation ketat dalam SOP, audit log akses data pelapor, sanksi disiplin bagi pelanggaran",
        },
        {
            "ancaman":    "Serangan siber pada infrastruktur portal aduan CSIRT",
            "likelihood": "Sedang",
            "dampak":     "Sedang",
            "level":      "Sedang",
            "mitigasi":   "Penetration testing berkala, WAF, backup terenkripsi harian, incident response plan tersedia",
        },
        {
            "ancaman":    "Kehilangan data laporan akibat kegagalan sistem atau bencana",
            "likelihood": "Rendah",
            "dampak":     "Sedang",
            "level":      "Rendah",
            "mitigasi":   "Backup harian ke server cadangan di lokasi berbeda, disaster recovery plan, RTO < 4 jam",
        },
    ],
    "evaluasi_residual": (
        "Setelah seluruh rencana mitigasi diterapkan, risiko residual berada pada level SEDANG "
        "untuk dua ancaman utama — keterbukaan identitas pelapor dan premature disclosure — "
        "karena faktor human error tidak dapat sepenuhnya dieliminasi. Risiko ini diterima "
        "(acceptable) dengan syarat: monitoring dan audit log berjalan kontinyu, pelatihan "
        "rutin minimal 1x/tahun dilaksanakan, dan DPIA ini ditinjau ulang apabila terdapat "
        "perubahan signifikan pada sistem, regulasi, atau cakupan pemrosesan. "
        "Seluruh risiko lain berada pada level RENDAH dan dapat diterima tanpa syarat tambahan."
    ),

    "kesimpulan": (
        "Aktivitas Penerimaan dan Pengelolaan Laporan Kerentanan & Insiden Keamanan Siber "
        "CSIRT Pemprov Bali dinyatakan LAYAK DILANJUTKAN dengan penerapan seluruh rencana "
        "mitigasi yang tercantum dalam dokumen ini.\n\n"
        "Keputusan:\n"
        "1. Aktivitas dapat berjalan dengan kontrol yang direncanakan.\n"
        "2. Opsi pelaporan anonim WAJIB dipertahankan sebagai fitur utama portal.\n"
        "3. Kebijakan Responsible Disclosure WAJIB dipublikasikan secara terbuka di portal CSIRT.\n"
        "4. DPIA ini wajib ditinjau ulang apabila terdapat perubahan signifikan pada sistem, "
        "regulasi, atau cakupan pemrosesan data pribadi.\n"
        "5. Audit kepatuhan DPIA dilaksanakan setiap 12 bulan."
    ),
}

META = {
    "tahun":        "2026",
    "generated_at": "Minggu, 17 Mei 2026 WITA",
}


if __name__ == '__main__':
    output = sys.argv[1] if len(sys.argv) > 1 else '/tmp/dpia_v3.pdf'
    script_dir = os.path.dirname(os.path.abspath(__file__))
    public_dir = os.path.join(script_dir, '..', 'public', 'images')
    logo1_path = os.path.join(public_dir, 'logobaliprovcsirt.png')
    logo2_path = os.path.join(public_dir, 'tlp', 'tlp_teaser_amber_strict.jpg')
    build_pdf(output, META, DPIA_DATA, logo1_path, logo2_path)
    print(f'DPIA PDF generated: {output}', file=sys.stderr)
