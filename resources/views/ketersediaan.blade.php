@extends('layouts.app')
@section('title', 'Pilih Jadwal Sewa - Digicam Padang')

@section('content')
<section class="section" style="padding-top: 24px;">
    <div class="container">
        @guest
        {{-- Form terkunci jika belum login --}}
        <div class="auth-card" style="max-width: 580px; margin: 40px auto; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 12px;">🔒</div>
            <h3 style="font-size: 1.4rem; color: var(--color-text-heading); margin-bottom: 12px;">
                Form Jadwal Terkunci
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 28px;">
                Form pemilihan tanggal dan jam sewa hanya terbuka untuk penyewa yang telah terdaftar dan masuk ke akun. Silakan masuk atau buat akun baru terlebih dahulu.
            </p>
            <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('login') }}" class="btn" style="min-width: 160px;">
                    🔑 Masuk ke Akun
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline" style="min-width: 160px;">
                    📝 Daftar Akun Baru
                </a>
            </div>
            <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--color-border); font-size: 0.85rem; color: var(--color-text-muted);">
                💡 Anda tetap dapat melihat <a href="{{ route('katalog') }}" style="color: var(--color-accent);">Katalog Kamera</a> dan <a href="{{ route('price') }}" style="color: var(--color-accent);">Price List</a> sebelum login.
            </div>
        </div>
        @else
        {{-- Tampilan Cek Ketersediaan Persis Sesuai Mockup --}}
        <div style="max-width: 630px; margin: 0 auto;">
            {{-- Top Meta Info --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding: 0 4px; font-size: 0.88rem;">
                <div>
                    <span style="color: var(--color-text-muted);">Penyewa:</span>
                    <strong style="color: var(--color-text-heading); margin-left: 6px;">{{ Auth::user()->name }}</strong>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--color-text-muted);">
                    <span>⏰ Waktu Sekarang: <strong id="realtimeClock" style="color: var(--color-text-heading); font-family: monospace; font-size: 0.9rem;">--:--:-- WIB</strong></span>
                    <span class="badge-online">Online</span>
                </div>
            </div>

            @if(session('error'))
            <div style="background: rgba(231, 76, 60, 0.12); border: 1px solid rgba(231, 76, 60, 0.3); border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 18px; color: var(--color-error); font-size: 0.88rem;">
                {{ session('error') }}
            </div>
            @endif

            <form class="form-box" style="max-width: 100%; background: #FFFFFF; border-radius: 20px; box-shadow: 0 8px 30px rgba(255, 127, 162, 0.12); padding: 28px 26px; border: 1px solid rgba(255, 127, 162, 0.2);" method="POST" action="{{ route('cek.validate') }}" id="scheduleForm">
                @csrf

                {{-- 1. Pilih Kamera --}}
                <div style="margin-bottom: 16px;">
                    <label for="camera_id" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; display: block;">Pilih Kamera</label>
                    <select id="camera_id" name="camera_id" required style="width: 100%; border-radius: 12px; padding: 12px 14px; border: 1px solid rgba(255, 127, 162, 0.35); background: #FFFDFE;">
                        <option value="">-- Pilih kamera --</option>
                        @foreach($cameras as $c)
                            <option value="{{ $c->id }}"
                                data-name="{{ $c->nama }}"
                                data-harga="{{ $c->harga_per_hari }}"
                                @selected(old('camera_id', $selectedCameraId ?? '') == $c->id)>
                                {{ $c->nama }} ({{ $c->merek }}) - Rp {{ number_format($c->harga_per_hari, 0, ',', '.') }}/hari
                            </option>
                        @endforeach
                    </select>
                    @error('camera_id')
                        <div class="auth-error" style="color: var(--color-error); font-size: 0.82rem; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2. Tanggal & Jam Mulai Sewa --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label for="tanggal_mulai" style="font-weight: 600; font-size: 0.86rem; margin-bottom: 6px; display: block;">Tanggal Mulai Sewa</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                               style="width: 100%; border-radius: 12px; padding: 11px 14px; border: 1px solid rgba(255, 127, 162, 0.35); background: #FFFDFE;">
                        @error('tanggal_mulai')
                            <div class="auth-error" style="color: var(--color-error); font-size: 0.82rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label for="jam_mulai" style="font-weight: 600; font-size: 0.86rem; margin: 0;">Jam Mulai Sewa</label>
                            <button type="button" id="btnSetJamSekarang" style="background: none; border: none; color: #FF7FA2; font-size: 0.78rem; font-weight: 600; cursor: pointer; padding: 0;">⚡ Set Jam Sekarang</button>
                        </div>
                        <input type="time" id="jam_mulai" name="jam_mulai"
                               value="{{ old('jam_mulai', now('Asia/Jakarta')->format('H:i')) }}" required
                               style="width: 100%; border-radius: 12px; padding: 11px 14px; border: 1px solid rgba(255, 127, 162, 0.35); background: #FFFDFE;">
                        @error('jam_mulai')
                            <div class="auth-error" style="color: var(--color-error); font-size: 0.82rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 3. Tanggal Mulai Sewa (Tgl/Bln/Thn) --}}
                <div style="margin-bottom: 16px;">
                    <label for="tanggal_mulai_formatted" style="font-weight: 600; font-size: 0.86rem; margin-bottom: 6px; display: block;">Tanggal Mulai Sewa (Tgl/Bln/Thn)</label>
                    <div style="position: relative;">
                        <input type="text" id="tanggal_mulai_formatted" readonly
                               style="width: 100%; border-radius: 12px; padding: 11px 40px 11px 14px; border: 1px solid rgba(255, 127, 162, 0.3); background: #FFFDFE; cursor: default; font-weight: 500;">
                        <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 1.1rem;">🗓️</span>
                    </div>
                    <div id="tanggal_mulai_text" style="color: #FF7FA2; font-size: 0.82rem; margin-top: 5px; font-weight: 500;">
                        📅 --
                    </div>
                </div>

                {{-- 4. Pilihan Cepat Durasi (Sesuai Pricelist Kamera) --}}
                <div class="durasi-pill-container">
                    <div class="durasi-pill-title">
                        💡 Pilihan Cepat Durasi (Sesuai Pricelist Kamera):
                    </div>
                    <div class="durasi-pill-grid" id="durasiPillGrid">
                        <button type="button" class="durasi-pill" data-hours="3">+3 Jam</button>
                        <button type="button" class="durasi-pill" data-hours="6">+6 Jam</button>
                        <button type="button" class="durasi-pill" data-hours="8">+8 Jam</button>
                        <button type="button" class="durasi-pill" data-hours="12">+12 Jam</button>
                        <button type="button" class="durasi-pill active" data-hours="24">+24 Jam (1 Hari)</button>
                        <button type="button" class="durasi-pill" data-hours="48">+48 Jam (2 Hari)</button>
                        <button type="button" class="durasi-pill" data-hours="72">+72 Jam (3 Hari)</button>
                    </div>
                </div>

                {{-- 5. Tanggal & Jam Pengembalian --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; margin-top: 14px;">
                    <div>
                        <label for="tanggal_kembali" style="font-weight: 600; font-size: 0.86rem; margin-bottom: 6px; display: block;">Tanggal Pengembalian</label>
                        <input type="date" id="tanggal_kembali" name="tanggal_kembali"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_kembali', date('Y-m-d', strtotime('+1 day'))) }}" required
                               style="width: 100%; border-radius: 12px; padding: 11px 14px; border: 1px solid rgba(255, 127, 162, 0.35); background: #FFFDFE;">
                        @error('tanggal_kembali')
                            <div class="auth-error" style="color: var(--color-error); font-size: 0.82rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="jam_kembali" style="font-weight: 600; font-size: 0.86rem; margin-bottom: 6px; display: block;">Jam Pengembalian</label>
                        <input type="time" id="jam_kembali" name="jam_kembali"
                               value="{{ old('jam_kembali', now('Asia/Jakarta')->format('H:i')) }}" required
                               style="width: 100%; border-radius: 12px; padding: 11px 14px; border: 1px solid rgba(255, 127, 162, 0.35); background: #FFFDFE;">
                        @error('jam_kembali')
                            <div class="auth-error" style="color: var(--color-error); font-size: 0.82rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 6. Tanggal Pengembalian (Tgl/Bln/Thn) --}}
                <div style="margin-bottom: 18px;">
                    <label for="tanggal_kembali_formatted" style="font-weight: 600; font-size: 0.86rem; margin-bottom: 6px; display: block;">Tanggal Pengembalian (Tgl/Bln/Thn)</label>
                    <div style="position: relative;">
                        <input type="text" id="tanggal_kembali_formatted" readonly
                               style="width: 100%; border-radius: 12px; padding: 11px 40px 11px 14px; border: 1px solid rgba(255, 127, 162, 0.3); background: #FFFDFE; cursor: default; font-weight: 500;">
                        <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 1.1rem;">🗓️</span>
                    </div>
                    <div id="tanggal_kembali_text" style="color: #FF7FA2; font-size: 0.82rem; margin-top: 5px; font-weight: 500;">
                        📅 --
                    </div>
                </div>

                {{-- Feedback Validasi Real-time --}}
                <div id="validationFeedback" style="margin-top: 14px; padding: 12px 16px; border-radius: var(--radius-md); display: none; font-size: 0.86rem;"></div>

                {{-- 7. Submit Button --}}
                <button type="submit" class="btn" id="submitBtn" style="margin-top: 18px; width: 100%; border-radius: 25px; padding: 14px; font-weight: 700; font-size: 0.95rem; background: #FF7FA2; color: #FFFFFF; box-shadow: 0 4px 14px rgba(255, 127, 162, 0.35);">
                    ✔ Validasi & Tentukan Rencana Sewa
                </button>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clockEl         = document.getElementById('realtimeClock');
            const btnSetNow       = document.getElementById('btnSetJamSekarang');
            const tglMulai        = document.getElementById('tanggal_mulai');
            const jamMulai        = document.getElementById('jam_mulai');
            const tglKembali      = document.getElementById('tanggal_kembali');
            const jamKembali      = document.getElementById('jam_kembali');
            const tglMulaiFmt     = document.getElementById('tanggal_mulai_formatted');
            const tglMulaiTxt     = document.getElementById('tanggal_mulai_text');
            const tglKembaliFmt   = document.getElementById('tanggal_kembali_formatted');
            const tglKembaliTxt   = document.getElementById('tanggal_kembali_text');
            const pills           = document.querySelectorAll('.durasi-pill');
            const feedback        = document.getElementById('validationFeedback');
            const submitBtn       = document.getElementById('submitBtn');
            const cameraSelect    = document.getElementById('camera_id');

            const bulanIndo = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            // 1. Live Ticking Clock (WIB)
            function tickClock() {
                const now = new Date();
                // Format HH:MM:SS
                const hrs = String(now.getHours()).padStart(2, '0');
                const mins = String(now.getMinutes()).padStart(2, '0');
                const secs = String(now.getSeconds()).padStart(2, '0');
                if (clockEl) {
                    clockEl.textContent = `${hrs}:${mins}:${secs} WIB`;
                }
            }
            setInterval(tickClock, 1000);
            tickClock();

            // 2. Format tanggal ke format DD/MM/YYYY dan label teks bahasa Indonesia
            function updateFormattedDates() {
                if (tglMulai && tglMulai.value) {
                    const parts = tglMulai.value.split('-');
                    if (parts.length === 3) {
                        const y = parts[0], m = parseInt(parts[1], 10), d = parseInt(parts[2], 10);
                        if (tglMulaiFmt) tglMulaiFmt.value = `${String(d).padStart(2, '0')}/${String(m).padStart(2, '0')}/${y}`;
                        if (tglMulaiTxt) tglMulaiTxt.textContent = `📅 ${d} ${bulanIndo[m - 1]} ${y}`;
                    }
                }
                if (tglKembali && tglKembali.value) {
                    const parts = tglKembali.value.split('-');
                    if (parts.length === 3) {
                        const y = parts[0], m = parseInt(parts[1], 10), d = parseInt(parts[2], 10);
                        if (tglKembaliFmt) tglKembaliFmt.value = `${String(d).padStart(2, '0')}/${String(m).padStart(2, '0')}/${y}`;
                        if (tglKembaliTxt) tglKembaliTxt.textContent = `📅 ${d} ${bulanIndo[m - 1]} ${y}`;
                    }
                }
            }

            // 3. Durasi yang valid per kamera berdasarkan Price List resmi @digicampadang_
            const cameraDurations = {
                1: [3, 6, 8, 12, 24],          // Canon EOS M10: 3j, 6j, 8j, 12j, 24j
                2: [6, 12, 24, 48, 72],        // Sony Cybershot W320: 6j, 12j, 24j, 48j, 72j (3 Hari)
                3: [6, 12, 24],                // Kodak Pixpro FZ55: 6j, 12j, 24j
            };

            function updateAvailableDurations() {
                const camId = parseInt(cameraSelect.value, 10);
                // Jika kamera belum dipilih, semua durasi dapat diklik
                const validHours = cameraDurations[camId] || [3, 6, 8, 12, 24, 48, 72];

                let hasActiveValid = false;

                pills.forEach(pill => {
                    const hours = parseInt(pill.getAttribute('data-hours'), 10);
                    if (validHours.includes(hours)) {
                        pill.disabled = false;
                        pill.classList.remove('disabled');
                        if (pill.classList.contains('active')) {
                            hasActiveValid = true;
                        }
                    } else {
                        pill.disabled = true;
                        pill.classList.add('disabled');
                        pill.classList.remove('active');
                    }
                });

                // Jika durasi yang sedang aktif masuk ke status disabled untuk kamera ini,
                // alihkan otomatis ke durasi valid yang sesuai (prioritas 24 jam atau opsi pertama)
                if (!hasActiveValid) {
                    const fallbackHours = validHours.includes(24) ? 24 : validHours[0];
                    const targetPill = document.querySelector(`.durasi-pill[data-hours="${fallbackHours}"]`);
                    if (targetPill) {
                        targetPill.classList.add('active');
                        applyDuration(fallbackHours);
                    }
                }
            }

            cameraSelect.addEventListener('change', function() {
                updateAvailableDurations();
                validateSchedule();
            });

            // 4. Hitung tanggal & jam kembali berdasarkan durasi jam yang dipilih
            function applyDuration(hours) {
                if (!tglMulai.value || !jamMulai.value) {
                    const now = new Date();
                    tglMulai.value = now.toISOString().split('T')[0];
                    jamMulai.value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                }

                const start = new Date(`${tglMulai.value}T${jamMulai.value}`);
                if (isNaN(start.getTime())) return;

                const end = new Date(start.getTime() + hours * 60 * 60 * 1000);
                const ey = end.getFullYear();
                const em = String(end.getMonth() + 1).padStart(2, '0');
                const ed = String(end.getDate()).padStart(2, '0');
                tglKembali.value = `${ey}-${em}-${ed}`;

                const eHrs = String(end.getHours()).padStart(2, '0');
                const eMins = String(end.getMinutes()).padStart(2, '0');
                jamKembali.value = `${eHrs}:${eMins}`;

                updateFormattedDates();
                validateSchedule();
            }

            // 5. Klik tombol pill durasi
            pills.forEach(pill => {
                pill.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.disabled || this.classList.contains('disabled')) return;
                    pills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    const hours = parseInt(this.getAttribute('data-hours'), 10);
                    applyDuration(hours);
                });
            });

            // 6. Tombol "⚡ Set Jam Sekarang"
            if (btnSetNow) {
                btnSetNow.addEventListener('click', function(e) {
                    e.preventDefault();
                    const now = new Date();
                    const y = now.getFullYear();
                    const m = String(now.getMonth() + 1).padStart(2, '0');
                    const d = String(now.getDate()).padStart(2, '0');
                    tglMulai.value = `${y}-${m}-${d}`;
                    jamMulai.value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

                    // Cari pill yang aktif dan hitung ulang
                    const activePill = document.querySelector('.durasi-pill.active');
                    const hours = activePill ? parseInt(activePill.getAttribute('data-hours'), 10) : 24;
                    applyDuration(hours);
                });
            }

            // 7. Validasi real-time
            function validateSchedule() {
                if (!tglMulai.value || !jamMulai.value || !tglKembali.value || !jamKembali.value) {
                    if (feedback) feedback.style.display = 'none';
                    return;
                }

                const start = new Date(`${tglMulai.value}T${jamMulai.value}`);
                const end = new Date(`${tglKembali.value}T${jamKembali.value}`);

                if (end <= start) {
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.background = 'rgba(231, 76, 60, 0.12)';
                        feedback.style.border = '1px solid rgba(231, 76, 60, 0.3)';
                        feedback.style.color = 'var(--color-error)';
                        feedback.innerHTML = '⚠️ <strong>Validasi Pengembalian:</strong> Tanggal dan jam pengembalian harus setelah waktu mulai sewa!';
                    }
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.6';
                    }
                    return;
                }

                const diffHours = Math.round((end - start) / (1000 * 60 * 60));
                if (feedback) {
                    feedback.style.display = 'none';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            }

            // Event listener input manual tanggal & jam
            [tglMulai, jamMulai, tglKembali, jamKembali].forEach(el => {
                if (!el) return;
                el.addEventListener('change', () => {
                    updateFormattedDates();
                    validateSchedule();
                });
                el.addEventListener('input', () => {
                    updateFormattedDates();
                    validateSchedule();
                });
            });

            // Inisialisasi awal
            updateFormattedDates();
            updateAvailableDurations();
            validateSchedule();
        });
        </script>
        @endguest
    </div>
</section>
@endsection
