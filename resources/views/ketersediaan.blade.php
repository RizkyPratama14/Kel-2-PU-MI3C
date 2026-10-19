@extends('layouts.app')
@section('title', 'Cek Ketersediaan')

@section('content')
<section class="section">
    <div class="container">
        <h2>Cek Ketersediaan</h2>
        <p class="sub">Pilih kamera, tanggal, dan jam sewa.</p>

        <form class="form-box" onsubmit="event.preventDefault(); alert('Fitur pengecekan akan dibuat di Sprint 2.');">
            <label for="camera">Kamera</label>
            <select id="camera" required>
                <option value="">-- Pilih kamera --</option>
                @foreach($cameras as $c)
                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                @endforeach
            </select>

            <label for="tanggal">Tanggal Sewa</label>
            <input type="date" id="tanggal" required>

            <label for="jam">Jam Mulai</label>
            <input type="time" id="jam" required>

            <label for="durasi">Durasi (jam)</label>
            <input type="number" id="durasi" min="1" value="24" required>

            <button class="btn" style="margin-top:20px">Cek Ketersediaan</button>
        </form>
    </div>
</section>
@endsection