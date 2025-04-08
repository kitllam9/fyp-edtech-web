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
                    {{ __('Create Badges') }}
                </div>
            </div>
            <div class="mt-4">
                <form id="create_badge" method="POST" action="{{ route('badge.store') }}">
                    @csrf
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Name') }}
                        </x-input-label>
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" required />
                    </div>
                    <x-input-error :messages="$errors->get('title')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Description') }}
                        </x-input-label>
                        <x-text-input id="description" class="block mt-2 w-full" type="text" name="description" required />
                    </div>
                    <x-input-error :messages="$errors->get('description')" class="my-2" />
                    <x-input-error :messages="$errors->get('type')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Target Amount') }}
                        </x-input-label>
                        <x-text-input id="target" class="block mt-2 w-full" type="number" name="target" required />
                    </div>
                    <x-input-error :messages="$errors->get('target')" class="my-2" />
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>