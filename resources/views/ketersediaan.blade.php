@extends('layouts.app')
@section('title', 'Pilih Jadwal Sewa - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <h2>Pemilihan Jadwal & Rencana Sewa</h2>
        <p class="sub">Tentukan unit kamera favoritmu serta tanggal dan jam mulai hingga pengembalian sewa.</p>

        @guest
        {{-- US-1008: Form booking hanya terbuka setelah login --}}
        <div class="auth-card" style="max-width: 580px; margin: 0 auto; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 12px;">🔒</div>
            <h3 style="font-size: 1.4rem; color: var(--color-text-heading); margin-bottom: 12px;">
                Form Jadwal Terkunci
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 28px;">
                Sesuai ketentuan <strong>Sprint 1</strong>, form pemilihan tanggal dan jam sewa hanya terbuka untuk penyewa yang telah terdaftar dan masuk ke akun. Silakan masuk atau buat akun baru terlebih dahulu.
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
        {{-- US-1003 & US-1008: Form terbuka untuk pengguna terautentikasi --}}
        <div style="max-width: 620px; margin: 0 auto;">
            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-muted);">Penyewa:</span>
                    <strong style="color: var(--color-text-heading); margin-left: 6px;">{{ Auth::user()->name }}</strong>
                </div>
                <span class="badge tersedia" style="position: static; font-size: 0.72rem;">Akun Terverifikasi</span>
            </div>

            @if(session('rencana_sewa'))
            <div style="background: var(--gradient-card); border: 1px solid var(--color-success); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 28px; box-shadow: var(--shadow-md);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                    <span style="font-size: 1.5rem;">🎉</span>
                    <h3 style="margin: 0; color: var(--color-success); font-size: 1.15rem;">Rencana Sewa Tervalidasi</h3>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 0.9rem; margin-bottom: 18px;">
                    <div>
                        <span style="color: var(--color-text-muted); display: block; font-size: 0.8rem;">Kamera</span>
                        <strong style="color: var(--color-text-heading);">{{ session('rencana_sewa.camera.nama') }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--color-text-muted); display: block; font-size: 0.8rem;">Total Durasi</span>
                        <strong style="color: var(--color-gold);">{{ session('rencana_sewa.durasi_jam') }} Jam</strong>
                    </div>
                    <div>
                        <span style="color: var(--color-text-muted); display: block; font-size: 0.8rem;">Mulai Sewa</span>
                        <span>{{ session('rencana_sewa.mulai') }}</span>
                    </div>
                    <div>
                        <span style="color: var(--color-text-muted); display: block; font-size: 0.8rem;">Pengembalian</span>
                        <span>{{ session('rencana_sewa.kembali') }}</span>
                    </div>
                    <div style="grid-column: 1 / -1; padding-top: 10px; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--color-text-muted);">Estimasi Biaya:</span>
                        <strong style="font-size: 1.2rem; color: var(--color-gold);">Rp {{ number_format(session('rencana_sewa.estimasi_harga'), 0, ',', '.') }}</strong>
                    </div>
                </div>

                @php
                    $waText = "Halo Digicam Padang, saya (" . Auth::user()->name . ") ingin mengonfirmasi rencana sewa kamera " . session('rencana_sewa.camera.nama') . " dari " . session('rencana_sewa.mulai') . " sampai " . session('rencana_sewa.kembali') . " (durasi " . session('rencana_sewa.durasi_jam') . " jam, estimasi Rp " . number_format(session('rencana_sewa.estimasi_harga'), 0, ',', '.') . "). Mohon info ketersediaan unit dan penguncian jadwal.";
                @endphp
                <a href="https://wa.me/6283181125461?text={{ urlencode($waText) }}" target="_blank" rel="noopener" class="btn btn-wa" style="width: 100%;">
                    💬 Lanjut Hubungi Admin WhatsApp
                </a>
            </div>
            @endif

            <form class="form-box" style="max-width: 100%;" method="POST" action="{{ route('cek.validate') }}" id="scheduleForm">
                @csrf

                <div>
                    <label for="camera_id">Pilih Kamera</label>
                    <select id="camera_id" name="camera_id" required>
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
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tanggal & Jam Mulai Sewa --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px;">
                    <div>
                        <label for="tanggal_mulai" style="margin-top: 0;">Tanggal Mulai Sewa</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        @error('tanggal_mulai')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="jam_mulai" style="margin-top: 0;">Jam Mulai Sewa</label>
                        <input type="time" id="jam_mulai" name="jam_mulai"
                               value="{{ old('jam_mulai', '10:00') }}" required>
                        @error('jam_mulai')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal & Jam Pengembalian (US-1003) --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 16px;">
                    <div>
                        <label for="tanggal_kembali" style="margin-top: 0;">Tanggal Pengembalian</label>
                        <input type="date" id="tanggal_kembali" name="tanggal_kembali"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_kembali', date('Y-m-d', strtotime('+1 day'))) }}" required>
                        @error('tanggal_kembali')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="jam_kembali" style="margin-top: 0;">Jam Pengembalian</label>
                        <input type="time" id="jam_kembali" name="jam_kembali"
                               value="{{ old('jam_kembali', '10:00') }}" required>
                        @error('jam_kembali')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Feedback Validasi Real-time --}}
                <div id="validationFeedback" style="margin-top: 20px; padding: 14px 18px; border-radius: var(--radius-md); display: none; font-size: 0.88rem;"></div>

                <button type="submit" class="btn" id="submitBtn" style="margin-top: 24px; width: 100%;">
                    ✅ Validasi & Tentukan Rencana Sewa
                </button>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tglMulai = document.getElementById('tanggal_mulai');
            const jamMulai = document.getElementById('jam_mulai');
            const tglKembali = document.getElementById('tanggal_kembali');
            const jamKembali = document.getElementById('jam_kembali');
            const feedback = document.getElementById('validationFeedback');
            const submitBtn = document.getElementById('submitBtn');

            function validateScheduleClient() {
                if (!tglMulai.value || !jamMulai.value || !tglKembali.value || !jamKembali.value) {
                    feedback.style.display = 'none';
                    return;
                }

                const start = new Date(`${tglMulai.value}T${jamMulai.value}`);
                const end = new Date(`${tglKembali.value}T${jamKembali.value}`);
                const now = new Date();

                // Validasi tanggal kembali tidak boleh sebelum tanggal mulai (US-1003)
                if (end <= start) {
                    feedback.style.display = 'block';
                    feedback.style.background = 'rgba(231, 76, 60, 0.12)';
                    feedback.style.border = '1px solid rgba(231, 76, 60, 0.3)';
                    feedback.style.color = 'var(--color-error)';
                    feedback.innerHTML = '⚠️ <strong>Validasi Pengembalian:</strong> Tanggal dan jam pengembalian harus setelah waktu mulai sewa!';
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    return;
                }

                // Hitung durasi jam
                const diffMs = end - start;
                const hours = Math.round(diffMs / (1000 * 60 * 60));

                feedback.style.display = 'block';
                feedback.style.background = 'rgba(46, 204, 113, 0.12)';
                feedback.style.border = '1px solid rgba(46, 204, 113, 0.3)';
                feedback.style.color = 'var(--color-success)';
                feedback.innerHTML = `✅ <strong>Jadwal Valid:</strong> Durasi sewa terhitung <strong>${hours} Jam</strong> (${(hours / 24).toFixed(1)} hari). Silakan klik validasi di bawah.`;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }

            [tglMulai, jamMulai, tglKembali, jamKembali].forEach(el => {
                el.addEventListener('change', validateScheduleClient);
                el.addEventListener('input', validateScheduleClient);
            });

            validateScheduleClient();
        });
        </script>
        @endguest
    </div>
</section>
@endsection