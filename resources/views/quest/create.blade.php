<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Create Quests') }}
                </div>
            </div>
            <div class="mt-4">
                <form id="create_quest" method="POST" action="{{ route('quest.store') }}">
                    @csrf
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Name') }}
                        </x-input-label>
                        <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" required />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Description') }}
                        </x-input-label>
                        <x-text-input id="description" class="block mt-2 w-full" type="text" name="description" required />
                    </div>
                    <x-input-error :messages="$errors->get('description')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Type') }}
                        </x-input-label>
                        <x-select id="type" name="type" class="mt-2" :options="$type" />
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Target Amount') }}
                        </x-input-label>
                        <x-text-input id="target" class="block mt-2 w-full" type="number" name="target" required />
                    </div>
                    <x-input-error :messages="$errors->get('target')" class="my-2" />
                    <div class="mb-4 hidden" id="multiple_percentage_amount">
                        <x-input-label>
                            {{ __('Target Mutiple For Percentage') }}
                        </x-input-label>
                        <x-text-input name="multiple_percentage_amount" class="block mt-2 w-full" type="number" />
                    </div>
                    <x-input-error :messages="$errors->get('multiple_percentage_amount')" class="my-2" />
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Reward') }}
                        </x-input-label>
                        <x-text-input id="reward" class="block mt-2 w-full" type="number" name="reward" required />
                    </div>
                    <x-input-error :messages="$errors->get('reward')" class="my-2" />
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#target_type').on('change', function() {
            var selectedType = $('#target_type').val();
            if (selectedType === 'percentage') {
                $('#multiple_percentage_amount').show();
            } else {
                $('#multiple_percentage_amount').hide();
            }
        });
    });
</script>