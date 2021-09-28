<x-app-layout>
	<x-slot name=title>
		{{ ___('user') }}
	</x-slot>

	<div class="flex flex-col gap-8 sm:flex-row items-center p-8">
		<div class="flex-none m-auto sm:m-0">
			<img class="rounded-full shadow-md border border-gray-400 w-32 sm:w-48 my-2" src="{{ asset('img/default-thumbnail.jpg') }}" alt="Test thumbnail">
		</div>
		<div class="flex-grow mx-3 sm:mx-0">
			<span class="text-xl">{{ $user->username }}</span><br>
			<span class="text-gray-500">{{ $user->firstname }} {{ $user->lastname }}</span><br>
			<span class="text-gray-500">Role</span><br>
			<span class="text-gray-500">{{ $user->email }}</span><br>
			<span class="text-gray-500">{{ ___('registered since') }}: {{ $user->created_at }}</span>
		</div>
		@if($user->username == Auth::user()->username)
		<div class="flex-none self-start">
			<a href="{{ route('users.edit', $user->id ) }}" class="button-shared">{{ ___('edit') }}</a>
		</div>
		@endif
	</div>

</x-app-layout>
