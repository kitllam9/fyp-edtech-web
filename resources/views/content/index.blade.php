<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Learning Materials") }}
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <form action="{{ route('content.create') }}">
                    <x-primary-button>
                        {{ __('Create') }}
                    </x-primary-button>
                </form>
            </div>
            <div class="mt-4">
                <table id="content-table" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th>
                                <span class="flex items-center">
                                    Title
                                    <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                    </svg>
                                </span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($content as $c)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $c->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $c->type }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    if (document.getElementById("content-table") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#content-table", {
            searchable: false,
            perPageSelect: false,
            sortable: true,
        });
    }
</script>