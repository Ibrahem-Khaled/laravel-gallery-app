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
                    <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl text-white shadow-xl">
                        <h4 class="font-bold mb-1">Modern Gallery App</h4>
                        <p class="text-sm text-white/80">Every shareable link you send is now optimized for WhatsApp/Facebook previews automatically!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
