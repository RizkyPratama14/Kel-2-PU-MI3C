@extends('layouts.app')

@section('title', 'Dashboard Admin - Digicam Padang')

@section('content')
<div class="container mx-auto mt-10 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">🖥️ Dashboard Admin</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <p class="text-gray-700 mb-4">Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                    class="bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition">
                Logout Admin
            </button>
        </form>
    </div>
</div>
@endsection
