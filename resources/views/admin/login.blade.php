@extends('layouts.app')

@section('title', 'Login Admin - Digicam Padang')

@section('content')
<div class="container mx-auto max-w-md mt-10 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center mb-6">🔐 Login Admin</h2>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border rounded-lg px-3 py-2 @error('email') border-red-500 @enderror"
                       placeholder="admin@digicampadang.com" required autofocus>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password"
                       class="w-full border rounded-lg px-3 py-2 @error('password') border-red-500 @enderror"
                       placeholder="Masukkan password" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                Masuk sebagai Admin
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">← Kembali ke halaman utama</a>
        </p>
    </div>
</div>
@endsection
