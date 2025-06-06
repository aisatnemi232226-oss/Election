@props([
    'id' => 'search-box',
    'placeholder' => 'Search...',
    'containerClass' => 'relative inline-block w-full md:w-auto'
])

<div class="{{ $containerClass }}">
    <input 
        type="text" 
        id="{{ $id }}"
        placeholder="{{ $placeholder }}" 
        {{ $attributes->merge(['class' => 'pl-4 pr-10 py-2 border rounded-lg w-full']) }}>
    
    <button 
        type="button"
        class="clear-button absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>