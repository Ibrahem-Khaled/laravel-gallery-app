<x-app-layout>
    <style>
        .upload-zone {
            background-image: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05), transparent),
                              radial-gradient(circle at bottom left, rgba(168, 85, 247, 0.05), transparent);
        }
        .image-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .image-card:hover {
            transform: translateY(-8px);
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <nav class="flex text-xs text-gray-400 mb-2 uppercase tracking-widest font-bold">
                    <a href="{{ route('categories.index') }}" class="hover:text-indigo-500 transition-colors uppercase tracking-widest">Gallery</a>
                    <span class="mx-2">/</span>
                    @if($category->parent)
                        <a href="{{ route('categories.show', $category->parent) }}" class="hover:text-indigo-500 transition-colors">{{ $category->parent->name }}</a>
                        <span class="mx-2">/</span>
                    @endif
                    <span class="text-indigo-600 dark:text-indigo-400">{{ $category->name }}</span>
                </nav>
                <h2 class="font-black text-4xl text-gray-900 dark:text-white leading-tight">
                    {{ $category->name }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                    {{ $category->images->count() }} Images • {{ $category->children->count() }} Sub-categories
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                 <button onclick="document.getElementById('upload-area').scrollIntoView({behavior: 'smooth'})" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 dark:shadow-none transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    New Upload
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Sub-categories -->
            @if($category->children->count() > 0)
                <div class="mb-12">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                        Sub-categories
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child) }}" class="group px-6 py-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl hover:border-indigo-500 transition-all flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center group-hover:bg-indigo-500 transition-colors">
                                    <svg class="w-5 h-5 text-indigo-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                </div>
                                <div>
                                    <span class="block font-bold dark:text-white">{{ $child->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $child->images->count() }} images</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload Area & Images -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Left: Sidebar Admin -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Global Share Status -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Sharing Mode</h3>
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold {{ $category->is_public ? 'text-green-500' : 'text-gray-500' }}">
                                {{ $category->is_public ? '✅ PUBLIC GALLERY' : '🔒 PRIVATE MODE' }}
                            </span>
                            <form action="{{ route('categories.share', $category) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 {{ $category->is_public ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }} rounded-xl transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4" /></svg>
                                </button>
                            </form>
                        </div>
                        @if($category->is_public)
                            <button onclick="copyLink('{{ route('categories.public', $category->share_token) }}')" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                Share Full Category
                            </button>
                        @endif
                        <a href="{{ route('analytics.index') }}" class="w-full mt-4 py-3 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 rounded-xl font-bold border border-dashed border-gray-200 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-500 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            Global Analytics
                        </a>
                    </div>

                    <div id="upload-area" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm upload-zone">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Upload Center</h3>
                        <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                            
                            <div class="relative group">
                                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/50 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 dark:border-gray-700 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-3">
                                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4-4m4 4v12" /></svg>
                                        </div>
                                        <p class="text-xs font-bold text-gray-500 uppercase">Select Photos</p>
                                    </div>
                                    <input type="file" name="images[]" multiple class="hidden" required />
                                </label>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label value="Visibility Status" />
                                    <select name="is_public" class="w-full mt-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 rounded-xl font-medium">
                                        <option value="1">✨ Public Social Link</option>
                                        <option value="0">🔒 Private Folder</option>
                                    </select>
                                </div>
                                <x-primary-button class="w-full justify-center py-4 rounded-xl text-lg">
                                    Start Uploading
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Category Actions -->
                    <div class="bg-red-50/30 dark:bg-red-900/10 p-6 rounded-3xl border border-red-100 dark:border-red-900/20">
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-400 uppercase mb-4">Danger Management</h4>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Attention: All photos and sub-categories inside will be PERMANENTLY deleted. Continue?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 rounded-xl font-bold hover:bg-red-600 hover:text-white transition-all">
                                Delete Category
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Image Grid -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse($category->images as $image)
                            <div class="image-card bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                                <div class="aspect-square relative overflow-hidden group">
                                    <img src="{{ asset('storage/'.$image->path) }}" class="w-full h-full object-cover">
                                    <div class="absolute top-2 right-2 flex flex-col gap-1">
                                        @if($image->is_public)
                                            <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded-lg shadow-sm">PUBLIC</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-500 text-white text-[10px] font-bold rounded-lg shadow-sm">PRIVATE</span>
                                        @endif
                                        <div class="px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-[10px] text-white flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 7.523 3 10 3s8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd" /></svg>
                                            {{ $image->visitorLogs->count() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="p-3 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-50 dark:border-gray-700 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        @if($image->is_public)
                                            <button onclick="copyLink('{{ route('images.show', $image->share_token) }}')" class="py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold transition-all flex items-center justify-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                                Copy Link
                                            </button>
                                        @endif
                                        <a href="{{ route('images.logs', $image) }}" class="py-2 bg-white dark:bg-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl text-[10px] font-bold hover:bg-gray-100 transition-all flex items-center justify-center gap-1 text-gray-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                            View Logs
                                        </a>
                                    </div>
                                    <form action="{{ route('images.destroy', $image) }}" method="POST" onsubmit="return confirm('Delete image?')" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-[10px] font-bold transition-all flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Delete Photo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-32 text-center bg-gray-50 dark:bg-gray-900 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-800">
                                <div class="p-6 bg-white dark:bg-gray-800 inline-block rounded-3xl shadow-xl mb-6">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <h4 class="text-2xl font-black text-gray-900 dark:text-white">Gallery is Empty</h4>
                                <p class="text-gray-500 mt-2">Start uploading photos using the control center.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-10 right-10 transform translate-y-20 opacity-0 transition-all duration-500 ease-out z-50">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4">
            <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <span class="font-bold tracking-tight" id="toast-message">Success!</span>
        </div>
    </div>

    <script>
        function copyLink(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Link copied to clipboard!');
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toast-message');
            msg.innerText = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
    </script>
</x-app-layout>
