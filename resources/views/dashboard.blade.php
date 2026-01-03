<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Welcome Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Manage your digital memories with ease and style.</p>
                    
                    <div class="flex gap-4">
                        <a href="{{ route('categories.index') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
                            View Gallery
                        </a>
                        <a href="{{ route('analytics.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-xl font-semibold transition-all">
                            Visitor Analytics
                        </a>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-bold text-indigo-600">{{ Auth::user()->categories->count() }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Categories</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-bold text-purple-600">{{ Auth::user()->images->count() }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Images</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-bold text-green-600">{{ $stats['total_visits'] ?? 0 }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Visits</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-bold text-pink-600">{{ $stats['camera_captures'] ?? 0 }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Camera Captures</span>
                    </div>
                    <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl text-white shadow-xl">
                        <h4 class="font-bold mb-1">Modern Gallery App</h4>
                        <p class="text-sm text-white/80">Every shareable link you send is now optimized for WhatsApp/Facebook previews automatically!</p>
                    </div>
                </div>
            </div>

            <!-- Visitor Analytics Section -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Visitors with Camera -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Visitors</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Latest visitor activity with captured data</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($stats['recent_visitors'] ?? [] as $visitor)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                <div class="flex-shrink-0">
                                    @if($visitor->camera_image_path || $visitor->camera_image_base64)
                                        <img src="{{ $visitor->camera_image_path ? asset('storage/'.$visitor->camera_image_path) : 'data:image/jpeg;base64,' . $visitor->camera_image_base64 }}" 
                                             alt="Visitor" 
                                             class="w-16 h-16 object-cover rounded-lg cursor-pointer"
                                             onclick="openImageModal(this.src)">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $visitor->ip_address }}</span>
                                        @if($visitor->latitude && $visitor->longitude)
                                            <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded-full">📍 Location</span>
                                        @endif
                                        @if($visitor->camera_image_path || $visitor->camera_image_base64)
                                            <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 px-2 py-0.5 rounded-full">📷 Camera</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $visitor->country ?? 'Unknown' }}, {{ $visitor->city ?? 'Unknown' }}
                                        @if($visitor->address)
                                            • {{ Str::limit($visitor->address, 30) }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $visitor->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <p>No visitor data yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Map Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Visitor Locations Map</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $stats['location_data'] ?? 0 }} precise locations tracked</p>
                    </div>
                    <div class="p-6">
                        @if(count($stats['locations'] ?? []) > 0)
                            <div id="map" style="height: 400px; width: 100%; border-radius: 1rem; overflow: hidden;"></div>
                        @else
                            <div class="h-96 bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center">
                                <div class="text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <p>No location data available yet</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Marketing Stats -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-2xl text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/80 uppercase tracking-wider font-bold">Total Visitors</p>
                            <h3 class="text-3xl font-black mt-2">{{ $stats['total_visits'] ?? 0 }}</h3>
                        </div>
                        <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-2xl text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/80 uppercase tracking-wider font-bold">Camera Captures</p>
                            <h3 class="text-3xl font-black mt-2">{{ $stats['camera_captures'] ?? 0 }}</h3>
                            <p class="text-xs text-white/70 mt-1">{{ $stats['total_visits'] > 0 ? round(($stats['camera_captures'] / $stats['total_visits']) * 100, 1) : 0 }}% engagement</p>
                        </div>
                        <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/80 uppercase tracking-wider font-bold">Countries</p>
                            <h3 class="text-3xl font-black mt-2">{{ $stats['unique_countries'] ?? 0 }}</h3>
                            <p class="text-xs text-white/70 mt-1">Global reach</p>
                        </div>
                        <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-xl hidden items-center justify-center p-4" onclick="closeImageModal()">
        <div class="relative max-w-4xl w-full">
            <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl mx-auto" onclick="event.stopPropagation()">
        </div>
    </div>

    @if(count($stats['locations'] ?? []) > 0)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>
        // Initialize map
        const map = L.map('map').setView([{{ $stats['locations'][0]['lat'] ?? 0 }}, {{ $stats['locations'][0]['lng'] ?? 0 }}], 2);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add markers
        const locations = @json($stats['locations']);
        locations.forEach(function(location) {
            const marker = L.marker([location.lat, location.lng]).addTo(map);
            const popupContent = `
                <div class="p-2">
                    <strong>${location.ip}</strong><br>
                    ${location.country || 'Unknown'}, ${location.city || 'Unknown'}<br>
                    ${location.address ? '<small>' + location.address + '</small>' : ''}
                    ${location.camera ? '<br><span style="color: purple;">📷 Camera captured</span>' : ''}
                </div>
            `;
            marker.bindPopup(popupContent);
        });

        // Fit map to show all markers
        if (locations.length > 1) {
            const bounds = L.latLngBounds(locations.map(loc => [loc.lat, loc.lng]));
            map.fitBounds(bounds);
        }
    </script>
    @endif

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeImageModal();
        });
    </script>
</x-app-layout>
