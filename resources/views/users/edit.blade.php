<x-app-layout>
        {{-- $user->username == Auth::user()->username --}}
            <x-slot name="title">
                {{ ___('edit your profile') }}
            </x-slot>

            <x-slot name="controls">
                
            </x-slot>

            <div class="flex flex-col gap-8 sm:flex-row items-start p-8">
                <div class="flex-none m-auto sm:m-0 text-center">
                    <img class="rounded-full shadow-md border border-gray-400 w-32 sm:w-48 my-2" src="{{ asset('img/default-thumbnail.jpg') }}" alt="Test thumbnail">
                    <a href="#" class="default text-sm">{{ ___('edit profile picture') }}</a>
                </div>
                <div class="flex-grow mx-3 sm:mx-0 my-2">
                    @if ($errors->any())
                        <div class="mb-4">
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

                    <form action="{{ route('users.update', $user->id) }}" method="post" class="lg:w-96">
                        @csrf
                        @method('PATCH')
                        <label for="username" class="label-shared">{{ ___('username') }}</label>
                        <input id="username" name="username" type="text" class="input-shared" value="{{ old('username') ?? $user->username }}" maxlength="64">
                        <label for="firstname" class="label-shared">{{ ___('first name') }}</label>
                        <input id="firstname" name="firstname" type="text" class="input-shared" value="{{ old('firstname') ?? $user->firstname }}" maxlength="64">
                        <label for="lastname" class="label-shared">{{ ___('last name') }}</label>
                        <input id="lastname" name="lastname" type="text" class="input-shared" value="{{ old('lastname') ?? $user->lastname }}" maxlength="64">
                        <label for="email" class="label-shared">{{ ___('email') }}</label>
                        <input id="email" name="email" type="text" class="input-shared" value="{{ old('email') ?? $user->email }}" maxlength="256">
                        <label for="password" class="label-shared">{{ ___('new password') }}</label>
                        <input id="password" name="password" type="password" class="input-shared" minlength="8">
                        <label for="password_confirmation" class="label-shared">{{ ___('confirm password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input-shared" minlength="8">
                        <label for="birthdate" class="label-shared">{{ ___('birthdate') }}</label>
                        <input id="birthdate" name="birthdate" type="date" class="input-shared" value="{{ old('birthdate') ?? $user->birthdate }}">
                        <input class="button-shared" type="submit">
                    </form>
                </div>
                <div class="justify-self-end bg-gray-200 p-4 rounded-lg">
                    <h3 class="text-lg border-b border-current">{{ ___('other actions') }}</h3>
                    <form action="{{ route('users.delete', $user->id) }}" method="post">
                        @csrf
                        <input class="button-shared mt-0" type="button" value="{{ ___('delete') }}" onClick="if(confirm('Are you sure to delete user {{ $user->username }} ?')){this.parentNode.submit()}">
                    </form>
                </div>
            </div>

</x-app-layout>
