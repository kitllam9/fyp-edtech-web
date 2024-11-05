@props([
'options' => []
])

<select {{ $attributes->merge(['class' => 'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500']) }}>
    @foreach($options as $value => $label)
    <option value="{{ $value }}" isSelected($value)>{{ $label }}</option>
    @endforeach
</select>