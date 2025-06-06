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
        {{ $attributes->merge(['class' => 'pl-4 pr-10 py-2 border rounded-lg w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500']) }}>
    
    <button 
        type="button"
        class="clear-button absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden w-4 h-4 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>