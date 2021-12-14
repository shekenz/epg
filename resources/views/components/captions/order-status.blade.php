@props(['label', 'status'])

<div class="rounded-sm text-white text-center flex justify-between py-1 px-2 gap-x-2" :class="{
	'bg-red-500 dark:bg-red-600' : {{ $status }} == 'FAILED',
	'bg-yellow-500' : {{ $status }} == 'CREATED',
	'bg-blue-500 dark:bg-blue-600' : {{ $status }} == 'COMPLETED',
	'bg-green-500 dark:bg-green-600' : {{ $status }} == 'SHIPPED',
}">
<x-tabler-mood-sad class="text-red-300 inline" v-if="{{ $status }} == 'FAILED'"/>
<x-tabler-alert-triangle class="text-yellow-200 inline" v-if="{{ $status }} == 'CREATED'"/>
<x-tabler-brand-paypal class="text-blue-300 inline" v-if="{{ $status }} == 'COMPLETED'"/>
<x-tabler-truck-delivery class="text-green-300 inline" v-if="{{ $status }} == 'SHIPPED'"/>
<div class="w-full text-center">@php echo '{{ $t(\'status.\'+'.$status.') }}' @endphp</div></div>