<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Badges') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Badges") }}
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <form action="{{ route('badge.create') }}">
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
                                    {{ __('Title') }}
                                    <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                    </svg>
                                </span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Target') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($badges as $b)
                        <tr>
                            <td class="px-6 py-4 max-w-24 whitespace-nowrap">{{ $b->name }}</td>
                            <td class="px-6 py-4 max-w-24 whitespace-nowrap">{{ $b->target }}</td>
                            <td class="px-6 py-4 max-w-24 whitespace-nowrap">
                                <form method="POST" class="inline" action="{{ route('badge.delete', $b) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>
                                        <i class="material-icons">&#xe872;</i>
                                    </x-danger-button>
                                </form>
                                <form method="GET" class="inline" action="{{ route('badge.edit', $b) }}">
                                    <x-success-button class="ml-1">
                                        <i class="material-icons">&#xe3c9;</i>
                                    </x-success-button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
<style>
    .datatable-wrapper .datatable-table thead th,
    .datatable-wrapper .datatable-table tbody th,
    .datatable-wrapper .datatable-table tbody td {
        max-width: 8rem !important;
        overflow: hidden;
        white-space: normal;
        word-wrap: break-word;
    }
</style>
<script>
    if (document.getElementById("content-table") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#content-table", {
            searchable: false,
            perPageSelect: false,
            sortable: true,
        });
    }
</script>