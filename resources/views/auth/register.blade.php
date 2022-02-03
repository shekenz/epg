<x-guest-layout>
    <x-auth-card>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

			<!-- Transmitiong invitation token for its deletion after user is registered -->
			@if( isset($invitetoken) )
			<input type="hidden" name="invitetoken" value="{{ $invitetoken }}">
            @endif

            <!-- Last name -->
            <div class="mt-4">
                <x-input :label="___('last name')" name="lastname" class="block mt-1 w-full" type="text" maxlength="64" :value="old('lastname')" required autofocus />
            </div>

            <!-- First name -->
            <div class="mt-4">
                <x-input :label="___('first name')" name="firstname" class="block mt-1 w-full" type="text" maxlength="64" :value="old('firstname')" required autofocus />
            </div>

            <!-- Username -->
            <div class="mt-4">
                <x-input :label="___('username')" name="username" class="block mt-1 w-full" type="text" maxlength="64" :value="old('username')" required />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input :label="___('email')" name="email" class="block mt-1 w-full" type="email" maxlength="256" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input :label="___('password')" name="password" class="block mt-1 w-full"
                                type="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input :label="___('confirm password')" name="password_confirmation"
                                type="password"
																required />
            </div>
            
            <!-- Birth date -->
            <div class="mt-4">
                <x-input :label="___('birthdate')" name="birthdate" class="block mt-1 w-full" type="date" name="birthdate" :value="old('birthdate')" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-post :href="route('register')" :label="___('register')" />
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
