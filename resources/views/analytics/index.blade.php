<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-3xl text-gray-900 dark:text-white leading-tight">
                {{ __('Global Analytics Center') }}
            </h2>
            <div class="flex gap-2">
                <form action="{{ route('analytics.index') }}" method="GET" class="flex gap-2">
                    <select name="category_id" onchange="this.form.submit()" class="border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl text-sm font-bold">
                        <option value="">All Categories</option>
                        @foreach(Auth::user()->categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Analytics Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm shadow-indigo-50">
                    <span class="text-xs text-gray-400 uppercase tracking-widest font-black">Total Activity</span>
                    <h3 class="text-4xl font-black text-indigo-600 mt-2">{{ $logs->total() }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Visit sessions tracked</p>
                </div>
                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-200 text-white">
                    <span class="text-xs text-white/60 uppercase tracking-widest font-black">Active Regions</span>
                    <h3 class="text-4xl font-black mt-2">{{ $logs->unique('country')->count() }}</h3>
                    <p class="text-xs text-white/80 mt-1">Different countries</p>
                </div>
                <div class="bg-purple-600 p-8 rounded-3xl shadow-xl shadow-purple-200 text-white">
                    <span class="text-xs text-white/60 uppercase tracking-widest font-black">Camera Captures</span>
                    <h3 class="text-4xl font-black mt-2">{{ $logs->whereNotNull('camera_image_path')->count() + $logs->whereNotNull('camera_image_base64')->count() }}</h3>
                    <p class="text-xs text-white/80 mt-1">Photos captured</p>
                </div>
                <div class="bg-green-600 p-8 rounded-3xl shadow-xl shadow-green-200 text-white">
                    <span class="text-xs text-white/60 uppercase tracking-widest font-black">Location Data</span>
                    <h3 class="text-4xl font-black mt-2">{{ $logs->whereNotNull('latitude')->count() }}</h3>
                    <p class="text-xs text-white/80 mt-1">Precise locations</p>
                </div>
            </div>

            <!-- Detailed Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm shadow-indigo-50 sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-50 dark:border-gray-700">
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest">Visitor / IP</th>
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest">Camera</th>
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest">Destination</th>
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest">Location Info</th>
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest">Device Details</th>
                                <th class="py-6 px-4 font-black text-gray-400 text-[10px] uppercase tracking-widest text-right">Activity Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($logs as $log)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                    <td class="py-6 px-4">
                                        <span class="text-sm font-black text-indigo-600 block">{{ $log->ip_address }}</span>
                                        <span class="text-[10px] text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full mt-1 inline-block">ID: #V{{ $log->id }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($log->camera_image_path || $log->camera_image_base64)
                                            <div class="relative group">
                                                <img src="{{ $log->camera_image_path ? asset('storage/'.$log->camera_image_path) : 'data:image/jpeg;base64,' . $log->camera_image_base64 }}" 
                                                     alt="Visitor capture" 
                                                     class="w-16 h-16 object-cover rounded-lg cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                                                     onclick="openImageModal(this.src)">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-lg transition-colors"></div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($log->category_id)
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-tighter">CATEGORY</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->category->name }}</span>
                                            </div>
                                        @elseif($log->gallery_image_id)
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-tighter">SINGLE IMAGE</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->image->name ?? 'Image' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($log->latitude && $log->longitude)
                                            <div class="space-y-1">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->country }}</span>
                                                    <span class="text-xs text-gray-400">{{ $log->city }}</span>
                                                </div>
                                                @if($log->address)
                                                    <div class="text-[10px] text-gray-500 truncate max-w-[200px]" title="{{ $log->address }}">
                                                        📍 {{ Str::limit($log->address, 40) }}
                                                    </div>
                                                @endif
                                                <a href="https://www.google.com/maps?q={{ $log->latitude }},{{ $log->longitude }}" 
                                                   target="_blank" 
                                                   class="text-[10px] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                                                    🗺️ Map
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->country }}</span>
                                                <span class="text-xs text-gray-400">{{ $log->city }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="max-w-[180px] truncate text-[11px] text-gray-500 font-medium" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </div>
                                        <div class="text-[10px] text-indigo-400 mt-1 font-bold truncate">Ref: {{ $log->referrer ?? 'Direct' }}</div>
                                    </td>
                                    <td class="py-6 px-4 text-right">
                                        <span class="text-xs font-black text-gray-900 dark:text-white">{{ $log->created_at->format('H:i A') }}</span>
                                        <span class="text-[10px] text-gray-400 block font-bold">{{ $log->created_at->format('d M, Y') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-20 text-center text-gray-400 font-bold uppercase tracking-widest">No tracking data available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-8 bg-gray-50 dark:bg-gray-900/50">
                    {{ $logs->links() }}
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
