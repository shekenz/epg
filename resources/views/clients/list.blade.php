<x-app-layout>

	<x-slot name="title">
			{{ ___('client list') }}
	</x-slot>

	<x-slot name="controls">
		<a class="button-shared" href="{{ route('clients.export') }}">{{ ___('CSV export') }}</a>
	</x-slot>

	<table>
		<thead class="font-bold">
			<tr>
				<td>{{ ___('last name') }}</td>
				<td>{{ ___('first name') }}</td>
				<td>{{ ___('email') }}</td>
				<td>{{ ___('country') }}</td>
		</thead>
		@foreach ($clients as $client)
			<tr>
				<td>{{ $client->lastname }}</td>
				<td>{{ $client->firstname }}</td>
				<td>{{ $client->email }}</td>
				<td>{{ $client->country_code }}</td>
			</tr>
		@endforeach
	</table>

</x-app-layout>