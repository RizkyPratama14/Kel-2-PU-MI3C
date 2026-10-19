@extends('layouts.app')
@section('title', 'Price List')

@section('content')
<section class="section">
    <div class="container">
        <h2>Price List</h2>
        <p class="sub">Harga sewa per hari. Konfirmasi skema harga per jam dengan klien.</p>
        <table>
            <thead><tr><th>Kamera</th><th>Merek</th><th>Harga / Hari</th></tr></thead>
            <tbody>
            @foreach($cameras as $c)
                <tr>
                    <td>{{ $c->nama }}</td>
                    <td>{{ $c->merek }}</td>
                    <td>Rp {{ number_format($c->harga_per_hari, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection