@extends('layouts.app')

@section('title', 'Kebijakan Privasi — CSIRT Bali')

@section('content')
    <div class="privacy-page">

        {{-- Hero --}}
        <section class="privacy-hero">
            <div class="privacy-hero__badge">
                <span class="badge-dot"></span>
                Dokumen Resmi
            </div>
            <h1 class="privacy-hero__title">Kebijakan <span class="accent">Privasi</span></h1>
            <p class="privacy-hero__sub">CSIRT Bali — Computer Security Incident Response Team</p>
            <div class="privacy-hero__meta">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    Berlaku sejak: {{ $effectiveDate }}
                </span>
                <span class="divider">|</span>
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Diperbarui: {{ $lastUpdated }}
                </span>
            </div>
        </section>

        {{-- Table of Contents --}}
        <aside class="toc" id="toc">
            <div class="toc__header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6" />
                    <line x1="8" y1="12" x2="21" y2="12" />
                    <line x1="8" y1="18" x2="21" y2="18" />
                    <line x1="3" y1="6" x2="3.01" y2="6" />
                    <line x1="3" y1="12" x2="3.01" y2="12" />
                    <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
                Daftar Isi
            </div>
            <nav class="toc__nav">
                <a href="#pendahuluan" class="toc__link">1. Pendahuluan</a>
                <a href="#data-dikumpulkan" class="toc__link">2. Data yang Dikumpulkan</a>
                <a href="#penggunaan-data" class="toc__link">3. Penggunaan Data</a>
                <a href="#penyimpanan-keamanan" class="toc__link">4. Penyimpanan &amp; Keamanan</a>
                <a href="#berbagi-data" class="toc__link">5. Berbagi Data</a>
                <a href="#hak-pengguna" class="toc__link">6. Hak Pengguna</a>
                <a href="#cookies" class="toc__link">7. Cookies</a>
                <a href="#perubahan-kebijakan" class="toc__link">8. Perubahan Kebijakan</a>
                <a href="#kontak" class="toc__link">9. Hubungi Kami</a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="privacy-content">

            <section class="policy-section" id="pendahuluan">
                <div class="section-number">01</div>
                <h2 class="section-title">Pendahuluan</h2>
                <div class="section-body">
                    <p>CSIRT Bali (<em>Computer Security Incident Response Team</em> Provinsi Bali) berkomitmen untuk
                        melindungi privasi dan keamanan data pribadi setiap individu yang menggunakan layanan pelaporan
                        insiden keamanan siber kami.</p>
                    <p>Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi
                        informasi pribadi Anda sesuai dengan Undang-Undang Nomor 27 Tahun 2022 tentang Perlindungan Data
                        Pribadi (UU PDP) Republik Indonesia.</p>
                    <div class="policy-callout policy-callout--info">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <p>Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai
                            kebijakan ini. Jika Anda tidak menyetujui, mohon tidak menggunakan layanan kami.</p>
                    </div>
                </div>
            </section>

            <section class="policy-section" id="data-dikumpulkan">
                <div class="section-number">02</div>
                <h2 class="section-title">Data yang Dikumpulkan</h2>
                <div class="section-body">
                    <p>Kami mengumpulkan informasi yang Anda berikan secara langsung saat melaporkan insiden keamanan siber,
                        antara lain:</p>

                    <div class="data-grid">
                        <div class="data-card">
                            <div class="data-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <h3>Data Identitas</h3>
                            <ul>
                                <li>Nama lengkap</li>
                                <li>Nomor identitas (NIK/NIP)</li>
                                <li>Jabatan / instansi</li>
                            </ul>
                        </div>
                        <div class="data-card">
                            <div class="data-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <h3>Data Kontak</h3>
                            <ul>
                                <li>Alamat email</li>
                                <li>Nomor telepon</li>
                                <li>Alamat instansi</li>
                            </ul>
                        </div>
                        <div class="data-card">
                            <div class="data-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            </div>
                            <h3>Data Insiden</h3>
                            <ul>
                                <li>Deskripsi insiden</li>
                                <li>Timestamp kejadian</li>
                                <li>Sistem/aset terdampak</li>
                                <li>Bukti pendukung (log, screenshot)</li>
                            </ul>
                        </div>
                        <div class="data-card">
                            <div class="data-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                            </div>
                            <h3>Data Teknis</h3>
                            <ul>
                                <li>Alamat IP pengirim</li>
                                <li>User-agent browser</li>
                                <li>Log akses sistem</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="policy-section" id="penggunaan-data">
                <div class="section-number">03</div>
                <h2 class="section-title">Penggunaan Data</h2>
                <div class="section-body">
                    <p>Data yang Anda berikan digunakan <strong>semata-mata</strong> untuk keperluan penanganan insiden
                        keamanan siber dan tidak akan digunakan untuk tujuan komersial.</p>
                    <div class="usage-list">
                        @foreach ($usages as $usage)
                            <div class="usage-item">
                                <div class="usage-check">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <span>{{ $usage }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="policy-section" id="penyimpanan-keamanan">
                <div class="section-number">04</div>
                <h2 class="section-title">Penyimpanan &amp; Keamanan Data</h2>
                <div class="section-body">
                    <p>Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang sesuai standar untuk melindungi
                        data Anda dari akses tidak sah, perubahan, pengungkapan, atau penghancuran.</p>

                    <div class="security-measures">
                        <div class="measure">
                            <div class="measure__icon measure__icon--green">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <div>
                                <strong>Enkripsi Data</strong>
                                <p>Data sensitif dienkripsi menggunakan standar AES-256 saat penyimpanan dan TLS 1.3 saat
                                    transmisi.</p>
                            </div>
                        </div>
                        <div class="measure">
                            <div class="measure__icon measure__icon--blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div>
                                <strong>Kontrol Akses</strong>
                                <p>Akses data dibatasi hanya untuk personel CSIRT Bali yang berwenang dan membutuhkan data
                                    untuk tugas resmi.</p>
                            </div>
                        </div>
                        <div class="measure">
                            <div class="measure__icon measure__icon--orange">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                            </div>
                            <div>
                                <strong>Pemantauan Aktif</strong>
                                <p>Sistem pemantauan aktif 24/7 untuk mendeteksi dan merespons potensi ancaman keamanan
                                    data.</p>
                            </div>
                        </div>
                        <div class="measure">
                            <div class="measure__icon measure__icon--purple">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                            </div>
                            <div>
                                <strong>Retensi Data</strong>
                                <p>Data disimpan selama <strong>5 tahun</strong> sejak tanggal laporan, sesuai kebijakan
                                    retensi arsip pemerintah, kemudian dihapus secara aman.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="policy-section" id="berbagi-data">
                <div class="section-number">05</div>
                <h2 class="section-title">Berbagi Data dengan Pihak Ketiga</h2>
                <div class="section-body">
                    <p>CSIRT Bali <strong>tidak menjual, menyewakan, atau memperjualbelikan</strong> data pribadi Anda
                        kepada pihak ketiga. Data hanya dapat dibagikan dalam kondisi berikut:</p>
                    <div class="policy-callout policy-callout--warning">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <p>Pembagian data hanya dilakukan kepada instansi pemerintah terkait (BSSN, Kominfo, Kepolisian)
                            untuk keperluan penegakan hukum atau koordinasi penanganan insiden siber nasional, berdasarkan
                            dasar hukum yang sah.</p>
                    </div>
                </div>
            </section>

            <section class="policy-section" id="hak-pengguna">
                <div class="section-number">06</div>
                <h2 class="section-title">Hak Anda sebagai Subjek Data</h2>
                <div class="section-body">
                    <p>Sesuai UU PDP, Anda memiliki hak-hak berikut terkait data pribadi Anda:</p>
                    <div class="rights-grid">
                        <div class="right-card">
                            <span class="right-label">Akses</span>
                            <p>Meminta salinan data pribadi yang kami simpan tentang Anda.</p>
                        </div>
                        <div class="right-card">
                            <span class="right-label">Koreksi</span>
                            <p>Meminta perbaikan data yang tidak akurat atau tidak lengkap.</p>
                        </div>
                        <div class="right-card">
                            <span class="right-label">Penghapusan</span>
                            <p>Meminta penghapusan data dalam kondisi tertentu yang diatur undang-undang.</p>
                        </div>
                        <div class="right-card">
                            <span class="right-label">Keberatan</span>
                            <p>Mengajukan keberatan atas pemrosesan data Anda dalam situasi tertentu.</p>
                        </div>
                    </div>
                    <p class="mt-4">Untuk menggunakan hak-hak Anda, silakan hubungi kami melalui kontak yang tercantum di
                        bagian 9.</p>
                </div>
            </section>

            <section class="policy-section" id="cookies">
                <div class="section-number">07</div>
                <h2 class="section-title">Cookies &amp; Teknologi Pelacakan</h2>
                <div class="section-body">
                    <p>Portal CSIRT Bali menggunakan cookies yang <strong>diperlukan secara teknis</strong> untuk memastikan
                        fungsi sistem berjalan dengan baik. Kami tidak menggunakan cookies pihak ketiga untuk tujuan
                        periklanan atau analitik komersial.</p>
                    <table class="cookie-table">
                        <thead>
                            <tr>
                                <th>Nama Cookie</th>
                                <th>Tujuan</th>
                                <th>Masa Berlaku</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>XSRF-TOKEN</code></td>
                                <td>Perlindungan CSRF pada formulir</td>
                                <td>Sesi browser</td>
                            </tr>
                            <tr>
                                <td><code>csirt_session</code></td>
                                <td>Manajemen sesi pengguna</td>
                                <td>2 jam</td>
                            </tr>
                            <tr>
                                <td><code>remember_token</code></td>
                                <td>Autentikasi persisten (opsional)</td>
                                <td>30 hari</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="policy-section" id="perubahan-kebijakan">
                <div class="section-number">08</div>
                <h2 class="section-title">Perubahan Kebijakan</h2>
                <div class="section-body">
                    <p>Kami berhak memperbarui Kebijakan Privasi ini sewaktu-waktu. Perubahan signifikan akan diinformasikan
                        melalui notifikasi di portal kami atau melalui email ke alamat yang terdaftar, minimal <strong>14
                            hari</strong> sebelum perubahan berlaku.</p>
                    <p>Penggunaan layanan kami setelah tanggal efektif perubahan dianggap sebagai persetujuan Anda terhadap
                        kebijakan yang telah diperbarui.</p>
                </div>
            </section>

            <section class="policy-section" id="kontak">
                <div class="section-number">09</div>
                <h2 class="section-title">Hubungi Kami</h2>
                <div class="section-body">
                    <p>Jika Anda memiliki pertanyaan, kekhawatiran, atau ingin menggunakan hak-hak Anda terkait kebijakan
                        privasi ini, silakan hubungi <strong>Data Protection Officer (DPO)</strong> CSIRT Bali:</p>
                    <div class="contact-block">
                        <div class="contact-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                        </div>
                        <div class="contact-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z" />
                            </svg>
                            <span>{{ $contactPhone }}</span>
                        </div>
                        <div class="contact-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span>{{ $contactAddress }}</span>
                        </div>
                        <div class="contact-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span>Waktu respons: Hari kerja, pukul 08.00–16.00 WITA</span>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        {{-- Back to form --}}
        <div class="privacy-footer">
            <a href="{{ url()->previous() }}" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Kembali ke Formulir
            </a>
            <p class="privacy-footer__copy">© {{ date('Y') }} CSIRT Bali — Dinas Komunikasi, Informatika dan Statistik
                Provinsi Bali</p>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        :root {
            --csirt-navy: #0d1b2a;
            --csirt-blue: #1a56db;
            --csirt-cyan: #0ea5e9;
            --csirt-light: #f0f6ff;
            --csirt-border: #dbeafe;
            --csirt-text: #1e293b;
            --csirt-muted: #64748b;
            --csirt-green: #059669;
            --csirt-orange: #d97706;
            --csirt-purple: #7c3aed;
        }

        /* ── Layout ── */
        .privacy-page {
            max-width: 860px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--csirt-text);
        }

        /* ── Hero ── */
        .privacy-hero {
            text-align: center;
            padding: 3.5rem 1rem 2.5rem;
            border-bottom: 1px solid var(--csirt-border);
            margin-bottom: 2rem;
        }

        .privacy-hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--csirt-blue);
            background: var(--csirt-light);
            border: 1px solid var(--csirt-border);
            border-radius: 999px;
            padding: 0.3rem 1rem;
            margin-bottom: 1.25rem;
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--csirt-blue);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .privacy-hero__title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin: 0 0 0.5rem;
            color: var(--csirt-navy);
        }

        .privacy-hero__title .accent {
            color: var(--csirt-blue);
        }

        .privacy-hero__sub {
            font-size: 1rem;
            color: var(--csirt-muted);
            margin: 0 0 1.25rem;
        }

        .privacy-hero__meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: var(--csirt-muted);
            flex-wrap: wrap;
        }

        .privacy-hero__meta svg {
            vertical-align: middle;
            margin-right: 0.25rem;
        }

        .divider {
            opacity: .3;
        }

        /* ── Table of Contents ── */
        .toc {
            background: var(--csirt-light);
            border: 1px solid var(--csirt-border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2.5rem;
        }

        .toc__header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--csirt-blue);
            margin-bottom: 0.875rem;
        }

        .toc__nav {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.25rem 1.5rem;
        }

        .toc__link {
            font-size: 0.875rem;
            color: var(--csirt-text);
            text-decoration: none;
            padding: 0.25rem 0;
            border-bottom: 1px dotted transparent;
            transition: color .15s, border-color .15s;
        }

        .toc__link:hover {
            color: var(--csirt-blue);
            border-bottom-color: var(--csirt-blue);
        }

        /* ── Policy Section ── */
        .policy-section {
            position: relative;
            padding: 2rem 0 2rem 3.5rem;
            border-bottom: 1px solid var(--csirt-border);
        }

        .policy-section:last-of-type {
            border-bottom: none;
        }

        .section-number {
            position: absolute;
            left: 0;
            top: 2rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--csirt-blue);
            opacity: .5;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--csirt-navy);
            margin: 0 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--csirt-light);
        }

        .section-body p {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: #334155;
            margin: 0 0 0.875rem;
        }

        .section-body p:last-child {
            margin-bottom: 0;
        }

        .mt-4 {
            margin-top: 1rem !important;
        }

        /* ── Callout ── */
        .policy-callout {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .policy-callout p {
            margin: 0;
        }

        .policy-callout svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .policy-callout--info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .policy-callout--info svg {
            stroke: #2563eb;
        }

        .policy-callout--warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .policy-callout--warning svg {
            stroke: #d97706;
        }

        /* ── Data Grid ── */
        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .data-card {
            border: 1px solid var(--csirt-border);
            border-radius: 10px;
            padding: 1rem;
            background: #fff;
        }

        .data-card__icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--csirt-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.625rem;
            color: var(--csirt-blue);
        }

        .data-card h3 {
            font-size: 0.8125rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
            color: var(--csirt-navy);
        }

        .data-card ul {
            padding-left: 1rem;
            margin: 0;
            font-size: 0.8125rem;
            color: var(--csirt-muted);
            line-height: 1.7;
        }

        /* ── Usage List ── */
        .usage-list {
            margin-top: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .usage-item {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            font-size: 0.9rem;
            line-height: 1.5;
            color: #334155;
        }

        .usage-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--csirt-green);
        }

        /* ── Security Measures ── */
        .security-measures {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .measure {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1rem;
            border: 1px solid var(--csirt-border);
            border-radius: 10px;
            background: #fff;
        }

        .measure__icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .measure__icon--green {
            background: #dcfce7;
            color: #059669;
        }

        .measure__icon--blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .measure__icon--orange {
            background: #fef3c7;
            color: #b45309;
        }

        .measure__icon--purple {
            background: #ede9fe;
            color: #6d28d9;
        }

        .measure strong {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            color: var(--csirt-navy);
        }

        .measure p {
            font-size: 0.85rem;
            margin: 0;
            color: var(--csirt-muted);
            line-height: 1.5;
        }

        /* ── Rights Grid ── */
        .rights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .right-card {
            border: 1px solid var(--csirt-border);
            border-radius: 10px;
            padding: 1rem;
            background: #fff;
        }

        .right-label {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: var(--csirt-light);
            color: var(--csirt-blue);
            border-radius: 999px;
            padding: 0.2rem 0.6rem;
            margin-bottom: 0.5rem;
        }

        .right-card p {
            font-size: 0.8375rem;
            color: var(--csirt-muted);
            margin: 0;
            line-height: 1.6;
        }

        /* ── Cookie Table ── */
        .cookie-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-top: 1rem;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--csirt-border);
        }

        .cookie-table thead {
            background: var(--csirt-navy);
            color: #fff;
        }

        .cookie-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.04em;
        }

        .cookie-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--csirt-border);
            color: #334155;
        }

        .cookie-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cookie-table tbody tr:nth-child(even) {
            background: var(--csirt-light);
        }

        .cookie-table code {
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: monospace;
            color: var(--csirt-blue);
        }

        /* ── Contact Block ── */
        .contact-block {
            background: var(--csirt-light);
            border: 1px solid var(--csirt-border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            font-size: 0.9rem;
            color: var(--csirt-text);
        }

        .contact-item svg {
            flex-shrink: 0;
            margin-top: 2px;
            stroke: var(--csirt-blue);
        }

        .contact-item a {
            color: var(--csirt-blue);
            text-decoration: none;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        /* ── Footer ── */
        .privacy-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2rem 0 0;
            margin-top: 1rem;
            border-top: 1px solid var(--csirt-border);
            text-align: center;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--csirt-blue);
            background: var(--csirt-light);
            border: 1px solid var(--csirt-border);
            border-radius: 8px;
            padding: 0.625rem 1.25rem;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-back:hover {
            background: #dbeafe;
        }

        .privacy-footer__copy {
            font-size: 0.8rem;
            color: var(--csirt-muted);
            margin: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .policy-section {
                padding-left: 0;
            }

            .section-number {
                display: none;
            }

            .toc__nav {
                grid-template-columns: 1fr 1fr;
            }

            .data-grid,
            .rights-grid {
                grid-template-columns: 1fr 1fr;
            }

            .cookie-table {
                font-size: 0.78rem;
            }

            .cookie-table th,
            .cookie-table td {
                padding: 0.5rem 0.625rem;
            }
        }
    </style>
@endpush
