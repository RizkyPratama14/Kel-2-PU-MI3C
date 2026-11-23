@extends('layouts.app')
@section('title', 'Form Booking - Digicam Padang')

@section('content')
<section class="section" style="padding-top: 24px;">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb-nav">
            <a href="{{ route('home') }}">Beranda</a>
            <span>›</span>
            <a href="{{ route('cek') }}">Cek Ketersediaan</a>
            <span>›</span>
            <span>Form Pemesanan</span>
        </nav>

        {{-- Progress Step Indicator --}}
        <div class="booking-progress-bar">
            <div class="progress-step done">
                <span class="step-number">✓</span>
                <span>Pilih Jadwal</span>
            </div>
            <div class="step-divider done"></div>
            <div class="progress-step active">
                <span class="step-number">2</span>
                <span>Lengkapi Booking</span>
            </div>
            <div class="step-divider"></div>
            <div class="progress-step">
                <span class="step-number">3</span>
                <span>Konfirmasi WhatsApp</span>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 28px;">
            <h2 style="margin-bottom: 6px;">Pemesanan Kamera Digital</h2>
            <p class="sub" style="margin-bottom: 0;">Lengkapi data di bawah untuk menyelesaikan booking sewa kamera Anda.</p>
        </div>

        <div class="booking-layout">

            {{-- ========= RINGKASAN PESANAN (KIRI) ========= --}}
            @php
                $camId = (int)($rencana['camera']->id ?? 1);
                $fotoKamera = match($camId) {
                    1 => 'canon-eos-m10.jpg',
                    2 => 'sony-cybershot-w320.jpg',
                    3 => 'kodak-pixpro-fz55.jpg',
                    default => 'canon-eos-m10.jpg',
                };
            @endphp
            <div class="booking-summary-card">
                <img src="{{ asset('images/' . $fotoKamera) }}"
                     alt="{{ $rencana['camera']->nama }}"
                     class="summary-camera-img"
                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">

                <div class="booking-summary-header" style="padding-bottom: 12px; margin-bottom: 14px;">
                    <div>
                        <div class="booking-summary-title">{{ $rencana['camera']->nama }}</div>
                        <div class="booking-summary-brand" style="color: var(--color-accent); font-weight: 600;">{{ $rencana['camera']->merek }} Series</div>
                    </div>
                </div>

                {{-- Tag Kelengkapan Sewa --}}
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--color-text-muted); margin-bottom: 6px;">📦 Kelengkapan Termasuk:</div>
                <div class="package-inclusions">
                    <span class="package-tag">🔋 Baterai & Charger</span>
                    <span class="package-tag">💾 Memory SD Card</span>
                    <span class="package-tag">💼 Pouch Pelindung</span>
                    <span class="package-tag">🔌 Card Reader OTG</span>
                </div>

                {{-- Visual Timeline Sewa --}}
                <div class="timeline-box">
                    <div class="timeline-item">
                        <div class="timeline-dot start"></div>
                        <div>
                            <div class="timeline-title">Mulai Pengambilan</div>
                            <div class="timeline-val">{{ $rencana['mulai'] }} WIB</div>
                        </div>
                    </div>
                    <div class="timeline-line"></div>
                    <div class="timeline-item">
                        <div class="timeline-dot end"></div>
                        <div>
                            <div class="timeline-title">Jadwal Pengembalian</div>
                            <div class="timeline-val">{{ $rencana['kembali'] }} WIB</div>
                        </div>
                    </div>
                </div>

                {{-- Detail Biaya & DP --}}
                <div class="booking-summary-rows">
                    <div class="booking-summary-row">
                        <span>⏱️ Total Durasi</span>
                        <strong style="color: var(--color-gold); font-size: 1rem;">{{ $rencana['durasi_jam'] }} Jam</strong>
                    </div>
                    <div class="booking-summary-row">
                        <span>🏷️ Tarif Total</span>
                        <strong>Rp {{ number_format($rencana['estimasi_harga'], 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="booking-summary-price">
                    <div>
                        <span style="display: block; font-size: 0.76rem; text-transform: uppercase; font-weight: 700; color: var(--color-accent);">Wajib Bayar DP (50%)</span>
                        <strong style="font-size: 1.35rem; color: var(--color-accent-hover);">Rp {{ number_format($rencana['estimasi_harga'] / 2, 0, ',', '.') }}</strong>
                    </div>
                    <span style="font-size: 0.78rem; background: white; padding: 4px 10px; border-radius: var(--radius-full); border: 1px solid rgba(255,127,162,0.3); color: var(--color-text);">Kunci Slot</span>
                </div>

                <div class="booking-dp-note" style="margin-top: 10px;">
                    💳 Sisa pelunasan <strong>Rp {{ number_format($rencana['estimasi_harga'] / 2, 0, ',', '.') }}</strong> dibayarkan saat serah terima kamera.
                </div>
            </div>

            {{-- ========= FORM BOOKING (KANAN) ========= --}}
            <div class="booking-form-card">

                {{-- Status ketersediaan (US-1004) --}}
                @if(isset($tersedia))
                    @if($tersedia)
                        <div class="availability-badge available" style="display: flex; align-items: center; gap: 8px;">
                            <span>✅</span>
                            <span><strong>Unit Tersedia!</strong> Kamera <strong>{{ $rencana['camera']->nama }}</strong> masih tersedia untuk jadwal sewa ini.</span>
                        </div>
                    @else
                        <div class="availability-badge unavailable" style="display: flex; align-items: flex-start; gap: 8px;">
                            <span>🔴</span>
                            <span>
                                <strong>Kamera {{ $rencana['camera']->nama }} Sudah Dipesan!</strong>
                                <br>
                                Unit ini sudah terisi pada waktu tersebut. Namun Anda tetap dapat menyewa unit kamera lain untuk jam yang sama.
                                <br>
                                <a href="{{ route('cek') }}" style="text-decoration: underline; font-weight: 700;">← Klik di sini untuk memilih kamera lain</a>
                            </span>
                        </div>
                    @endif
                @endif

                <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                    @csrf

                    {{-- Data tersembunyi dari sesi jadwal --}}
                    <input type="hidden" name="camera_id"       value="{{ $camId }}">
                    <input type="hidden" name="mulai_at"        value="{{ $rencana['mulai_at'] }}">
                    <input type="hidden" name="selesai_at"      value="{{ $rencana['selesai_at'] }}">
                    <input type="hidden" name="durasi_jam"      value="{{ $rencana['durasi_jam'] }}">
                    <input type="hidden" name="estimasi_harga"  value="{{ $rencana['estimasi_harga'] }}">

                    {{-- Data Penyewa (Format Identik Halaman Register) --}}
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" disabled class="input-disabled">
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" disabled class="input-disabled">
                    </div>

                    <div class="form-group">
                        <label for="no_whatsapp">Nomor WhatsApp <span class="required-mark">*</span></label>
                        <input type="text" id="no_whatsapp" name="no_whatsapp"
                               value="{{ old('no_whatsapp', Auth::user()->no_whatsapp ?? '') }}"
                               placeholder="Contoh: 08123456789" required maxlength="20">
                        <div class="field-hint">Digunakan untuk konfirmasi booking via WhatsApp.</div>
                        @error('no_whatsapp')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Pengambilan (Format Identik Halaman Register) --}}
                    <div class="form-group">
                        <label for="lokasi_pickup">Alamat / Lokasi Pengambilan Kamera <span class="required-mark">*</span></label>
                        <input type="text" id="lokasi_pickup" name="lokasi_pickup"
                               value="{{ old('lokasi_pickup') }}"
                               placeholder="Contoh: Kampus Politeknik Negeri Padang, Gedung A" required>
                        <div class="field-hint">Isi dengan alamat atau titik temu pengambilan kamera.</div>
                        @error('lokasi_pickup')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Tambahan <span class="optional-mark">(opsional)</span></label>
                        <textarea id="catatan" name="catatan" rows="3"
                                  placeholder="Misal: permintaan khusus atau aksesoris tambahan">{{ old('catatan') }}</textarea>
                    </div>

                    {{-- Pilihan Metode Pembayaran DP dengan Lambang Asli --}}
                    <div class="form-group" style="margin-top: 26px;">
                        <label style="margin-bottom: 6px;">Pilih Metode Pembayaran DP 50% <span class="required-mark">*</span></label>
                        <div class="field-hint" style="margin-top: -2px; margin-bottom: 14px;">Pilih metode transfer pembayaran uang muka yang Anda inginkan:</div>

                        <div class="payment-methods">

                            {{-- 1. Transfer Bank (BCA, Mandiri, BRI, BNI) --}}
                            <label class="payment-option {{ old('metode_pembayaran', 'bank_transfer') === 'bank_transfer' ? 'selected' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="bank_transfer"
                                       {{ old('metode_pembayaran', 'bank_transfer') === 'bank_transfer' ? 'checked' : '' }} required>
                                <div class="payment-logo-badge bank">
                                    <svg width="48" height="28" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="60" rx="6" fill="#F8F9FA"/>
                                        <path d="M50 8L16 24V28H84V24L50 8Z" fill="#003D79"/>
                                        <rect x="22" y="32" width="9" height="16" fill="#005E9E"/>
                                        <rect x="39" y="32" width="9" height="16" fill="#00529C"/>
                                        <rect x="56" y="32" width="9" height="16" fill="#005E9E"/>
                                        <rect x="73" y="32" width="9" height="16" fill="#F15A24"/>
                                        <rect x="14" y="50" width="72" height="4" fill="#003D79"/>
                                    </svg>
                                </div>
                                <span class="payment-info">
                                    <span class="payment-name">Transfer Bank</span>
                                    <div class="bank-tags">
                                        <span class="bank-tag" style="color: #005E9E;">BCA</span>
                                        <span class="bank-tag" style="color: #003D79;">Mandiri</span>
                                        <span class="bank-tag" style="color: #00529C;">BRI</span>
                                        <span class="bank-tag" style="color: #F15A24;">BNI</span>
                                    </div>
                                </span>
                            </label>

                            {{-- 2. DANA --}}
                            <label class="payment-option {{ old('metode_pembayaran') === 'dana' ? 'selected' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="dana"
                                       {{ old('metode_pembayaran') === 'dana' ? 'checked' : '' }} required>
                                <div class="payment-logo-badge dana">
                                    <svg width="52" height="32" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="88" height="54" rx="6" fill="#118EEA"/>
                                        <text x="44" y="34" font-family="'Poppins', sans-serif" font-size="20" font-weight="900" fill="white" text-anchor="middle" letter-spacing="1">DANA</text>
                                    </svg>
                                </div>
                                <span class="payment-info">
                                    <span class="payment-name">DANA</span>
                                    <span class="payment-desc">Dompet Digital DANA</span>
                                </span>
                            </label>

                            {{-- 3. OVO --}}
                            <label class="payment-option {{ old('metode_pembayaran') === 'ovo' ? 'selected' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="ovo"
                                       {{ old('metode_pembayaran') === 'ovo' ? 'checked' : '' }} required>
                                <div class="payment-logo-badge ovo">
                                    <svg width="52" height="32" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="88" height="54" rx="6" fill="#4C3494"/>
                                        <circle cx="20" cy="27" r="9" stroke="white" stroke-width="4" fill="none"/>
                                        <circle cx="20" cy="27" r="4" fill="white"/>
                                        <text x="56" y="35" font-family="'Poppins', sans-serif" font-size="20" font-weight="900" fill="white" text-anchor="middle" letter-spacing="1">OVO</text>
                                    </svg>
                                </div>
                                <span class="payment-info">
                                    <span class="payment-name">OVO</span>
                                    <span class="payment-desc">Dompet Digital OVO</span>
                                </span>
                            </label>

                            {{-- 4. GoPay --}}
                            <label class="payment-option {{ old('metode_pembayaran') === 'gopay' ? 'selected' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="gopay"
                                       {{ old('metode_pembayaran') === 'gopay' ? 'checked' : '' }} required>
                                <div class="payment-logo-badge gopay">
                                    <svg width="52" height="32" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="88" height="54" rx="6" fill="#00AED6"/>
                                        <circle cx="20" cy="27" r="9" fill="white"/>
                                        <circle cx="20" cy="27" r="5" fill="#00AED6"/>
                                        <circle cx="20" cy="27" r="2.5" fill="white"/>
                                        <text x="54" y="34" font-family="'Poppins', sans-serif" font-size="18" font-weight="900" fill="white" text-anchor="middle">gopay</text>
                                    </svg>
                                </div>
                                <span class="payment-info">
                                    <span class="payment-name">GoPay</span>
                                    <span class="payment-desc">Saldo GoPay / Gojek</span>
                                </span>
                            </label>

                        </div>
                        @error('metode_pembayaran')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Persetujuan Aturan --}}
                    <div style="margin-top: 24px; padding: 16px; background: rgba(255, 127, 162, 0.05); border: 1px dashed rgba(255, 127, 162, 0.4); border-radius: var(--radius-md);">
                        <label class="agree-row" style="margin-top: 0; padding: 0; background: transparent; border: none;">
                            <input type="checkbox" name="setuju_aturan" required {{ old('setuju_aturan') ? 'checked' : '' }}>
                            <span>
                                Saya bersedia menitipkan <strong>KTP / KTM Asli</strong> sebagai jaminan sewa dan menyetujui seluruh <a href="{{ route('aturan') }}" target="_blank">Aturan Sewa Digicam Padang</a>.
                            </span>
                        </label>
                        @error('setuju_aturan')
                            <div class="auth-error" style="margin-top: 8px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" class="btn" style="width: 100%; margin-top: 24px; font-size: 1.05rem; padding: 16px; font-weight: 700; box-shadow: 0 6px 20px rgba(255,127,162,0.3);"
                            @if(isset($tersedia) && !$tersedia) disabled style="opacity: 0.6; cursor: not-allowed;" @endif>
                        ✨ Kirim Pemesanan & Kunci Jadwal
                    </button>

                    <div class="booking-trust-badge">
                        <span>🔒 Data Terenkripsi</span>
                        <span>⚡ Konfirmasi Cepat WhatsApp</span>
                        <span>📷 Unit Terawat & Siap Pakai</span>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<script>
// Highlight pilihan metode pembayaran
document.querySelectorAll('.payment-option input[type=radio]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
        radio.closest('.payment-option').classList.add('selected');
    });
});
</script>
@endsection
