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
                    {{ __('Create Learning Materials') }}
                </div>
            </div>
            <div class="mt-4">
                <form>
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Title') }}
                        </x-input-label>
                        <x-text-input id="title" class="block mt-2 w-full" type="text" name="title" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Type') }}
                        </x-input-label>
                        <x-select id="type" name="type" class="mt-2" :options="$types" />
                    </div>
                    <div class="mb-4">
                        <x-text-editor />
                    </div>
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>