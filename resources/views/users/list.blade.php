<x-app-layout>
	<x-slot name="title">
		{{ ___('users') }}
	</x-slot>

	<x-slot name="controls">
		<a class="button-shared" href="{{ route('users.invitation') }}">{{ ___('invite') }}</a>
	</x-slot>

	@foreach($users as $user)
		<div class="flex flex-row items-center py-2 px-4  border-b border-gray-200">
			<div class="flex-none m-0">
				<img class="rounded-full border border-gray-400 shadow-md w-12 my-0 mr-4" src="{{ asset('img/default-thumbnail.jpg') }}" alt="Test thumbnail">
			</div>
			<div class="flex-grow mx-3 sm:mx-0 my-0">
				<a href="{{ route('users.display', $user->id )}}" class="text-current hover:underline text-xl">{{ $user->username }}</a><br>
				<span class="text-gray-500">{{ $user->email }}</span>
			</div>
			@if($user->username == Auth::user()->username)
			<div class="flex-none">
				<a href="{{ route('users.edit', $user->id ) }}" class="button-shared">{{ ___('edit') }}</a>
			</div>
			@endif
		</div>
	@endforeach
</x-app-layout>
