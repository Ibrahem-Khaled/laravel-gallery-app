<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $image->name ?? 'Gallery Image' }}</title>
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $image->name ?? 'Shared Image' }}">
    <meta property="og:description" content="{{ $image->description ?? 'Check out this image in my gallery.' }}">
    <meta property="og:image" content="{{ asset('storage/'.$image->path) }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="{{ $image->name ?? 'Shared Image' }}">
    <meta property="twitter:description" content="{{ $image->description ?? 'Check out this image in my gallery.' }}">
    <meta property="twitter:image" content="{{ asset('storage/'.$image->path) }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
        }
        .image-container img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .image-container:hover img {
            transform: scale(1.02);
        }
        .btn-glow {
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="p-6">
    <div class="max-w-4xl w-full glass p-8 md:p-12 text-center animate-in fade-in zoom-in duration-700">
        <div class="mb-8">
            <h1 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent mb-2">
                {{ $image->name ?? 'Untitled Image' }}
            </h1>
            <p class="text-gray-400 text-lg">{{ $image->description ?? 'Shared via Gallery App' }}</p>
        </div>

        <div class="image-container inline-block mb-10 cursor-zoom-in group relative">
            <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->name }}" class="transition-all duration-500">
            <!-- Secondary Download (Initially faint or hidden) -->
            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ asset('storage/'.$image->path) }}" download class="p-3 bg-white/20 backdrop-blur-md hover:bg-white/40 text-white rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </a>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
            <button onclick="zoomToggle()" id="zoom-btn" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all btn-glow shadow-xl flex items-center gap-3 text-lg">
                <span id="zoom-text">Enlarge Photo</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
            </button>
            <a href="{{ asset('storage/'.$image->path) }}" download class="px-8 py-4 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-2xl font-bold transition-all border border-white/10 flex items-center gap-2">
                Download Original
            </a>
        </div>

        <div class="mt-12 pt-8 border-t border-white/10">
            <p class="text-gray-500 text-sm font-bold uppercase tracking-widest">Share this gallery with others • {{ $image->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <script>
        let isZoomed = false;
        const container = document.querySelector('.image-container');
        const img = container.querySelector('img');
        const zoomText = document.querySelector('#zoom-text');

        function zoomToggle() {
            if (!isZoomed) {
                img.style.transform = 'scale(1.25)';
                img.style.maxHeight = '95vh';
                container.style.cursor = 'zoom-out';
                zoomText.innerText = 'Zoom Out';
            } else {
                img.style.transform = 'scale(1)';
                img.style.maxHeight = '80vh';
                container.style.cursor = 'zoom-in';
                zoomText.innerText = 'Enlarge Photo';
            }
            isZoomed = !isZoomed;
        }

        container.addEventListener('click', zoomToggle);
    </script>
</body>
</html>
