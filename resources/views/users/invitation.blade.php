<x-app-layout>
	<x-slot name="title">
		{{ ___('invite new user') }}
	</x-slot>

	@if ($errors->any())
        <div class="mb-4" :errors="$errors">
            <div class="font-medium text-red-600">
                {{ __('Whoops! Something went wrong.') }}
            </div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
	<form action="{{ route('users.invite') }}" method="POST" class="flex flex-row gap-x-4 mb-4 items-center">
		@csrf
		<label for="email" class="label-shared whitespace-nowrap">{{ ___('email') }} : </label>
		<input class="input-shared" id="email" name="email" type="text" value="{{ old('email')}}" maxlength="256">
		<input class="button-shared" type="submit">
	</form>

</x-app-layout>