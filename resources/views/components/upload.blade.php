@props(['disabled', 'label', 'name' => 'files'])

{{-- Check input.label.php for more info --}}
<x-label>@if($attributes->has('disabled')) <x-slot name="disabled"></x-slot> @endif {{ $label }} : </x-label>

<label for="upload" class="
	border 
	border-gray-300
	bg-transparent
	shadow-tight-window
	dark:shadow-none
	dark:border-gray-600
	dark:bg-gray-600
	p-4
	block
">
	<div class="
		h-64
		border-[4px]
		border-gray-300
		dark:border-gray-500
		text-gray-300 
		dark:text-gray-500
		border-dashed
		cursor-pointer
		flex
		flex-col
		justify-center
		items-center
	">
		<x-icon-drop class="w-24 h-24 mb-4 hidden" />
		<span class="text-3xl mb-8">{{ __('app.upload.info') }}</span>
		<span class="justify-self-end">({{ $slot }})</span>
	</div>
</label>
<input
	id="upload"
	type="file"
	name="{{ $name.'[]' }}"
	accept=".jpg,.jpeg,.png,.gif"
	multiple 
	class="
		w-[0.1px]
		h-[0.1px]
		opacity-0
		overflow-hidden
		absolute
		z-[-1]
	"
	onchange="
		let s = '';
		if(this.files.length > 1) s = 's';
		this.previousElementSibling.firstElementChild.children[1].firstChild.nodeValue = `${this.files.length} file${s} selected`;
	"
/>