<x-app-layout>

	<x-slot name="title">
			{{ ___('client list') }}
	</x-slot>

	<x-slot name="controls">
		<a class="button-shared" href="{{ route('clients.export') }}">{{ ___('CSV export') }}</a>
	</x-slot>

	<x-section class="full" :title="___('client list')">
		<x-buttons>
			<x-button :href="route('clients.export')" :label="___('CSV export')" class="big" />
		</x-buttons>
		<table class="big">
			<thead>
				<tr>
					<td>{{ ___('last name') }}</td>
					<td>{{ ___('first name') }}</td>
					<td>{{ ___('email') }}</td>
					<td>{{ ___('country') }}</td>
				</tr>
			</thead>
			<tbody>
			@foreach ($clients as $client)
				<tr>
					<td>{{ $client->lastname }}</td>
					<td>{{ $client->firstname }}</td>
					<td>{{ $client->email }}</td>
					<td>{{ $client->country_code }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</x-section>

</x-app-layout>