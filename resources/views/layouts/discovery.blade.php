<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>R E C I C L E D ☆ — @yield('title', 'Tu Música')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CDN + Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        accent: {
                            DEFAULT: '#8B5CF6',
                            dark: '#7C3AED',
                            light: '#A78BFA',
                        },
                        surface: {
                            DEFAULT: '#0F0F0F',
                            card: '#1A1A2E',
                            hover: '#252542',
                            glass: 'rgba(255,255,255,0.05)',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * { scrollbar-width: thin; scrollbar-color: #A78BFA #F3E8FF; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F3E8FF; }
        ::-webkit-scrollbar-thumb { background: #A78BFA; border-radius: 3px; }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.4); }
            50% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.8); }
        }
        .pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

        .glass {
            background: rgba(255,255,255,0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .glass-strong {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.7);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-[#F4EBFF] font-sans text-gray-900 antialiased min-h-screen relative">
    {{-- Decorative sharp background overlay as per mockup --}}
    <div class="fixed inset-0 pointer-events-none z-0 opacity-40 mix-blend-multiply" style="background-image: url('data:image/svg+xml,%3Csvg width=\'100%25\' height=\'100%25\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cdefs%3E%3Cpattern id=\'p\' width=\'100\' height=\'100\' patternUnits=\'userSpaceOnUse\'%3E%3Cpath d=\'M0 100l50-50 50 50v-100l-50 50-50-50z\' fill=\'none\' stroke=\'%23d8b4e2\' stroke-width=\'0.5\' opacity=\'0.5\'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=\'100%25\' height=\'100%25\' fill=\'url(%23p)\'/%3E%3C/svg%3E'); background-size: cover;"></div>
    
    <div class="relative z-10 w-full h-full">
        @yield('content')
    </div>
</body>
</html>
