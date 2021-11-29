<x-app-layout>
	<x-slot name="title">
			{{ ___('archived orders') }}
	</x-slot>

	<x-slot name="leftControls">
		<a href="{{ route('orders') }}" class="mini-button"><x-tabler-chevron-left /></a>
	</x-slot>
	
	<form id="orders-selection" method="POST">
		@csrf
		<table id="orders-table" class="app-table">
			<thead>
				<td>{{ ___('order') }}</td>
				<td>{{ ___('client') }}</td>
				<td>{{ ___('client email') }}</td>
				<td>{{ ___('created at') }}</td>
				<td>{{ ___('archived at') }}</td>
				<td>{{ ___('version') }}</td>
			</thead>
			<tbody id="order-rows">
				@foreach($archivedOrders as $archiveOrder)
				<tr>
					<td><a class="underline text-gray-500 hover:text-inherit" href="{{ route('archive.display', $archiveOrder->id) }}">{{ $archiveOrder->order_id }}</a></td>
					<td>{{ $archiveOrder->full_name }}</td>
					<td>{{ $archiveOrder->email_address }}</td>
					<td>{{ $archiveOrder->created_at }}</td>
					<td>{{ $archiveOrder->archived_at }}</td>
					<td>{{ $archiveOrder->version }}</td>
				</tr>
				@endforeach
				{{--
				<tr>
					<td colspan="8"><img class="m-auto my-8" src="{{ asset('img/loader2.gif') }}" alt="loader animation"></td>
				</tr>
				--}}
			</tbody>
		</table>
	</form>

	<div id="no-result" class="hidden text-center text-xl text-gray-400 my-8">
		{{ __('No result found') }}
	</div>

	<x-tabler-recycle class="hidden" id="recycle-blueprint"/>
	<x-tabler-trash class="hidden" id="trash-blueprint"/>
	{{-- Used to be a forkilft icon, hence the id (used in order-list.js) --}}
	<x-tabler-clock class="hidden" id="forklift-blueprint"/>
	<x-tabler-truck-delivery class="hidden" id="shipped-blueprint"/>
	<x-tabler-archive class="hidden" id="archive-blueprint"/>

</x-app-layout>