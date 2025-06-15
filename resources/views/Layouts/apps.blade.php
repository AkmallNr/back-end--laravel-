<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Schedo - Atur Tugas, Jadwal, dan Fokusmu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-shadow { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .feature-icon { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded transform rotate-12 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">S</span>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-900 hover:text-gray-600 px-3 py-2 text-sm font-medium">Home</a>
                        <a href="{{ route('home') }}#features" class="text-gray-500 hover:text-gray-600 px-3 py-2 text-sm font-medium">Features</a>
                        <a href="{{ route('home') }}#download" class="text-gray-500 hover:text-gray-600 px-3 py-2 text-sm font-medium">Download</a>
                        <button class="bg-orange-400 hover:bg-orange-500 text-white px-6 py-2 rounded-full text-sm font-medium transition-colors">
                           <a href="{{ route('login') }}" class="bg-orange-400 hover:bg-orange-500 text-white px-6 py-2 rounded-full text-sm font-medium transition-colors">
                                Login
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>Schedo, January 2025 | Productivity 2025</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="{{"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
