<x-app-layout>
	<x-slot name=title>
		{{ ___('user') }}
	</x-slot>

	<section class="titled">
		<h2>{{ ___('user') }}</h2>
		<div class="flex flex-col gap-8 sm:flex-row items-center p-8">
			<div class="flex-none m-auto sm:m-0">
				<img class="rounded-full shadow-md border border-gray-400 w-32 sm:w-48 my-2" src="{{ asset('img/default-thumbnail.jpg') }}" alt="Test thumbnail">
			</div>
			<div class="flex-grow mx-3 sm:mx-0">
				<span>{{ $user->username }}</span><br>
				<span>{{ $user->firstname }} {{ $user->lastname }}</span><br>
				<span>Role</span><br>
				<span>{{ $user->email }}</span><br>
				<span>{{ ___('registered since') }}: {{ $user->created_at }}</span>
				@if($user->username == Auth::user()->username)
					<nav class="buttons-wrapper">
						<a href="{{ route('users.edit', $user->id ) }}" class="button big">{{ ___('edit') }}</a>
					</nav>
				@endif
			</div>
		</div>
	</section>
	

</x-app-layout>
