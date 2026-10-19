<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Digicam Padang - Sewa kamera mudah, terjangkau, dan terpercaya di Padang. Pilih kamera favoritmu sekarang!">
    <title>@yield('title', 'Digicam Padang')</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('home') }}" class="logo">📷 Digicam Padang</a>
            <button class="hamburger" id="hamburger" aria-label="Buka menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <div class="nav-links" id="navLinks">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog*') ? 'active' : '' }}">Katalog</a>
                <a href="{{ route('price') }}" class="{{ request()->routeIs('price') ? 'active' : '' }}">Price List</a>
                <a href="{{ route('aturan') }}" class="{{ request()->routeIs('aturan') ? 'active' : '' }}">Aturan Sewa</a>
                <a href="{{ route('cek') }}" class="{{ request()->routeIs('cek') ? 'active' : '' }}">Cek Ketersediaan</a>
                <div class="nav-auth">
                    @auth
                        <div class="nav-user-menu">
                            <button class="nav-user-btn" id="userMenuBtn" aria-expanded="false">
                                <span class="nav-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="nav-username">{{ Auth::user()->name }}</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown" id="userDropdown">
                                <div class="nav-dropdown-header">
                                    <span class="nav-dropdown-name">{{ Auth::user()->name }}</span>
                                    <span class="nav-dropdown-email">{{ Auth::user()->email }}</span>
                                </div>
                                <div class="nav-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-dropdown-item nav-logout-btn">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 14H3a1 1 0 01-1-1V3a1 1 0 011-1h3M11 11l3-3-3-3M14 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn-outline {{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a>
                        <a href="{{ route('register') }}" class="nav-btn {{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="flash flash-success" id="flashMessage" role="alert">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill="currentColor"/>
            </svg>
            {{ session('success') }}
            <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="flash flash-error" id="flashMessage" role="alert">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-9H9v4h2V9zm0-4H9v2h2V5z" fill="currentColor"/>
            </svg>
            {{ session('error') }}
            <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @yield('content')

    <footer>
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <span class="footer-logo">📷 Digicam Padang</span>
                    <p>Sewa kamera mudah, terjangkau, dan terpercaya di Padang.</p>
                </div>
                <div class="footer-links">
                    <strong>Menu</strong>
                    <a href="{{ route('home') }}">Beranda</a>
                    <a href="{{ route('katalog') }}">Katalog</a>
                    <a href="{{ route('price') }}">Price List</a>
                    <a href="{{ route('aturan') }}">Aturan Sewa</a>
                    <a href="{{ route('cek') }}">Cek Ketersediaan</a>
                </div>
                <div class="footer-links">
                    <strong>Hubungi Kami</strong>
                    <a href="https://instagram.com/USERNAME_IG" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        Instagram
                    </a>
                    <a href="https://wa.me/62XXXXXXXXXXX" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <small>© {{ date('Y') }} Digicam Padang. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script>
        // Hamburger menu
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');
        if (hamburger && navLinks) {
            hamburger.addEventListener('click', () => {
                const isOpen = navLinks.classList.toggle('open');
                hamburger.classList.toggle('open', isOpen);
                hamburger.setAttribute('aria-expanded', isOpen);
            });
        }

        // User dropdown menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = userDropdown.classList.toggle('open');
                userMenuBtn.setAttribute('aria-expanded', isOpen);
            });
            document.addEventListener('click', () => {
                userDropdown.classList.remove('open');
                userMenuBtn.setAttribute('aria-expanded', 'false');
            });
        }

        // Auto-dismiss flash messages
        const flash = document.getElementById('flashMessage');
        if (flash) {
            setTimeout(() => {
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(() => flash.remove(), 400);
            }, 4000);
        }
    </script>
</body>
</html>