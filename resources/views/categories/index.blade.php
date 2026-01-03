<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Create Category Form -->
                <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Create New Category') }}</h3>
                    <form action="{{ route('categories.store') }}" method="POST" class="flex flex-wrap gap-4">
                        @csrf
                        <div class="flex-1 min-w-[300px]">
                            <x-text-input name="name" class="w-full" placeholder="Category Name" required />
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <select name="parent_id" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">{{ __('No Parent (Main Category)') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}">-- {{ $child->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button>
                            {{ __('Create') }}
                        </x-primary-button>
                    </form>
                </div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($categories as $category)
                        <div class="group bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                                    </h4>
                                    <div class="flex items-center gap-4 mt-1">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->children->count() }} {{ __('Sub-categories') }}</p>
                                        <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->images->count() }} {{ __('Images') }}</p>
                                    </div>
                                </div>
                                <span class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                </span>
                            </div>
                            
                            @if($category->children->count() > 0)
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach($category->children->take(3) as $child)
                                        <a href="{{ route('categories.show', $child) }}" class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-md text-gray-600 dark:text-gray-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                    @if($category->children->count() > 3)
                                        <span class="text-xs text-gray-400">...</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('No categories found. Create your first one above!') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
