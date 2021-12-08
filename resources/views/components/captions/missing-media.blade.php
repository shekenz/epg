<div class="h-[50px] flex flex-col justify-center bg-red-400 text-white px-3 rounded-sm">
	<h4 class="flex items-center font-bold m-0 p-0 gap-x-1"><x-tabler-alert-triangle />{{ ___('no linked medium') }}</h4>
	@if($slot->isNotEmpty())
	<span class="text-sm italic">{{ $slot }}</span>
	@endif
</div>