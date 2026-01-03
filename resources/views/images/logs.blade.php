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
                                    <td class="py-4 px-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $log->country }}</span>
                                            <span class="text-xs text-gray-500">{{ $log->city }}</span>
                                        </div>
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
                                    <td colspan="5" class="py-12 text-center text-gray-500">No logs found yet.</td>
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
</x-app-layout>
