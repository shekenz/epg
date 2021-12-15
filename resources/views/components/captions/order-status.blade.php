@props(['label', 'status'])

<div {{ $attributes->class([
	'rounded-sm text-white text-center flex justify-between py-1 px-2 gap-x-2',
	'bg-red-500 dark:bg-red-600' => ($status == 'FAILED'),
	'bg-yellow-500' => ($status == 'CREATED'),
	'bg-blue-500 dark:bg-blue-600' => ($status == 'COMPLETED'),
	'bg-green-500 dark:bg-green-600' => ($status == 'SHIPPED'),
]) }}>
	@switch($status)
		@case('FAILED')
			<x-tabler-mood-sad class="text-red-300 inline" />
		@break
		@case('CREATED')
			<x-tabler-alert-triangle class="text-yellow-200 inline" />
		@break
		@case('COMPLETED')
			<x-tabler-brand-paypal class="text-blue-300 inline" />
		@break
		@case('SHIPPED')
			<x-tabler-truck-delivery class="text-green-300 inline" />
		@break
	@endswitch
	<div class="w-full text-center">{{ ___('paypal.status.'.$status) }}</div>
</div>