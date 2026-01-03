<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Visitor Logs for') }}: {{ $image->name ?? 'Untitled Image' }}
            </h2>
            <a href="{{ route('categories.show', $image->category_id) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-gray-900 dark:text-white">
                &larr; Back to Gallery
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 uppercase tracking-wider font-bold">Total Views</span>
                    <h3 class="text-3xl font-bold text-indigo-600 mt-1">{{ $logs->total() }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 uppercase tracking-wider font-bold">Unique IPs</span>
                    <h3 class="text-3xl font-bold text-purple-600 mt-1">{{ $image->visitorLogs()->distinct('ip_address')->count() }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <img src="{{ asset('storage/'.$image->path) }}" class="h-16 w-full object-cover rounded-xl shadow-sm">
                </div>
            </div>

            <!-- Detailed Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">IP Address</th>
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">Camera</th>
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">Location</th>
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">Device / Browser</th>
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">Referrer</th>
                                <th class="py-4 px-4 font-bold text-gray-500 text-xs uppercase">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                                    <td class="py-4 px-4 text-sm font-mono text-indigo-500">{{ $log->ip_address }}</td>
                                    <td class="py-4 px-4">
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
                                    <td class="py-4 px-4 text-sm">
                                        @if($log->latitude && $log->longitude)
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $log->country }}</span>
                                                    <span class="text-xs text-gray-500">{{ $log->city }}</span>
                                                </div>
                                                @if($log->address)
                                                    <div class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $log->address }}">
                                                        📍 {{ $log->address }}
                                                    </div>
                                                @endif
                                                <a href="https://www.google.com/maps?q={{ $log->latitude }},{{ $log->longitude }}" 
                                                   target="_blank" 
                                                   class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                                                    🗺️ View on Map
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $log->country }}</span>
                                                <span class="text-xs text-gray-500">{{ $log->city }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-sm">
                                        <div class="max-w-[250px] truncate text-gray-600 dark:text-gray-400" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm">
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-500">
                                            {{ $log->referrer ?? 'Direct' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-500">
                                        {{ $log->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">No logs found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-6 border-t border-gray-100 dark:border-gray-700">
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
