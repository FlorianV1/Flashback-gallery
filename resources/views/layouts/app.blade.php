<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Flashback Gallery' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400i,400&family=dm-sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen" style="background-color: #FAF7F2; color: #2C1810; font-family: 'DM Sans', sans-serif;">

    {{-- Accent stripe --}}
    <div class="h-0.5 w-full" style="background: linear-gradient(90deg, #C84B00 0%, #E85D04 40%, #FF8C38 60%, #E85D04 80%, #C84B00 100%);"></div>

    {{-- Navigation --}}
    <header style="border-bottom: 1px solid #DDD5C5; background: rgba(250,247,242,0.93); backdrop-filter: blur(12px);" class="sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('gallery') }}" class="flex items-center gap-2.5 group">
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <rect x="2" y="6" width="28" height="20" rx="2" fill="#E85D04" opacity="0.12"/>
                        <rect x="2" y="6" width="28" height="20" rx="2" stroke="#E85D04" stroke-width="1.5"/>
                        <circle cx="16" cy="16" r="5" stroke="#E85D04" stroke-width="1.5"/>
                        <circle cx="16" cy="16" r="2" fill="#E85D04"/>
                        <rect x="22" y="9" width="4" height="3" rx="0.5" fill="#E85D04" opacity="0.6"/>
                        <rect x="6" y="9" width="2" height="2" rx="0.3" fill="#E85D04" opacity="0.4"/>
                    </svg>
                    <div class="leading-none">
                        <span style="font-family: 'DM Serif Display', serif; font-size: 1.3rem; color: #2C1810; letter-spacing: -0.01em;">Flashback</span><span style="font-family: 'DM Serif Display', serif; font-size: 1.3rem; color: #E85D04; letter-spacing: -0.01em;"> Gallery</span>
                    </div>
                </a>
                <nav class="flex items-center gap-1 text-sm">
                    @auth
                        <a href="/admin"
                           class="px-3 py-1.5 rounded-sm text-sm transition-all"
                           style="color: #8B7355;"
                           onmouseenter="this.style.color='#2C1810'; this.style.background='rgba(44,24,16,0.06)'"
                           onmouseleave="this.style.color='#8B7355'; this.style.background='transparent'">
                            {{ auth()->user()->isAdmin() ? 'Dashboard' : 'My Albums' }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-sm text-sm transition-all"
                                    style="color: #8B7355;"
                                    onmouseenter="this.style.color='#2C1810'; this.style.background='rgba(44,24,16,0.06)'"
                                    onmouseleave="this.style.color='#8B7355'; this.style.background='transparent'">
                                Sign out
                            </button>
                        </form>
                    @else
                        <a href="/admin/login"
                           class="px-3 py-1.5 rounded-sm text-sm transition-all"
                           style="color: #8B7355;"
                           onmouseenter="this.style.color='#2C1810'; this.style.background='rgba(44,24,16,0.06)'"
                           onmouseleave="this.style.color='#8B7355'; this.style.background='transparent'">
                            Sign in
                        </a>
                        <a href="/admin/register"
                           class="px-3 py-1.5 rounded-sm text-sm font-medium transition-all"
                           style="background: #E85D04; color: #ffffff; border-radius: 4px;"
                           onmouseenter="this.style.background='#C84B00'"
                           onmouseleave="this.style.background='#E85D04'">
                            Sign up
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-20 py-10" style="border-top: 1px solid #DDD5C5;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2 opacity-60">
                    <svg class="w-4 h-4" viewBox="0 0 32 32" fill="none">
                        <rect x="2" y="6" width="28" height="20" rx="2" stroke="#8B7355" stroke-width="1.5"/>
                        <circle cx="16" cy="16" r="5" stroke="#8B7355" stroke-width="1.5"/>
                        <circle cx="16" cy="16" r="2" fill="#8B7355"/>
                    </svg>
                    <span style="font-family: 'DM Serif Display', serif; color: #8B7355; font-size: 0.875rem;">Flashback Gallery</span>
                </div>
                <p class="text-xs" style="color: #A89880;">
                    Florian's cameraatje &nbsp;&middot;&nbsp; {{ date('Y') }}
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
