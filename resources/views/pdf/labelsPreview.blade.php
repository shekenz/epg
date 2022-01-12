<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Labels preview</title>
	<link rel="stylesheet" href="{{ asset('css/labels.css') }}">
	<script type="text/javascript" src="{{ asset('js/labels.js') }}" defer></script>
</head>
<body class="bg-gray-300">
	<nav id="menu" class="fixed h-20 w-full border-b border-gray-400 bg-white top-0 flex justify-between items-center">
		<a class="fixed h-20 flex items-center hover:underline text-xl ml-3" href="{{ route('orders') }}"><x-tabler-arrow-left class="inline-block mr-1" />{{ __('Back') }}</a>
		<form id="mainForm" class="m-auto w-1/2 flex justify-between items-center" method="POST" action="{{ route('orders.labels') }}">
			@csrf
			<div>
				<label for="extra">{{ __('Insert') }} </label><input class="input-inline" id="extra" type="number" min="0" max="11" value="0"> {{ __('blank labels') }}
			</div>
			<div>
				<input id="submit-packaging-list" type="button" class="button-shared cursor-pointer" value="{{ __('Packaging list') }}">
				<input id="submit-labels" type="button" class="button-shared ml-2 cursor-pointer" value="{{ __('Labels') }}">
			</div>
			@foreach ($orders as $order)
				<input type="hidden" name="ids[]" value="{{ $order->id }}">
			@endforeach
		</form>
	</nav>
	@php $chunks = $orders->chunk(12) @endphp
	<main id="mainWrapper" class="mt-28">
		@foreach($chunks as $orders)
		<div class="page border border-black my-12 mx-auto bg-white shadow-lg grid grid-cols-2 grid-rows-6">
			@foreach($orders as $order)
				<div class="label border-b border-r border-dotted flex items-center">
					<div class="ml-20 text-[1.25rem] leading-6">
						<span class="font-bold">{{ $order->full_name }}</span><br>
						{{ $order->address_line_1 }}<br>
						@isset($order->address_line_2)
						{{ $order->address_line_2 }}<br>
						@endif
						{{ $order->postal_code.', '.$order->admin_area_2 }}<br>
						@isset($order->admin_area_1)
						{{ $order->admin_area_1 }}<br>
						@endif
						{{ strtoupper(config('countries.'.$order->country_code)) }}<br>
					</div>
				</div>
			@endforeach
		</div>
		@endforeach
	</main>
</body>
</html>