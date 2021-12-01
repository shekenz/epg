<x-guest-layout>
		<x-auth-card>

				<!-- Session Status -->
				<x-auth-session-status class="mb-4" :status="session('status')" />

				<!-- Validation Errors -->
				{{--<x-auth-validation-errors class="mb-4" :errors="$errors" />--}}

				<form method="POST" action="{{ route('login') }}">
						@csrf

						<!-- Email Address -->
			<!--
						<div>
								<x-label for="email" :value="__('Email')" />

								<x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus maxlength="256"/>
						</div>
			-->

			<!-- Username -->
			<!-- TODO autofocus on the field that has error -->
						<div>
								<x-input
									type="text"
									name="username"
									:label="___('username')"
									:value="old('username')"
									required
									maxlength="64"
								>@error('username'){{$message}}@enderror</x-input>
						</div>

						<!-- Password -->
						<div class="mt-4">
								<x-input
									type="password"
									name="password"
									:label="___('password')"
									required
									autocomplete="current-password" 
									minlength="8"
								>@error('password'){{$message}}@enderror</x-input>
						</div>

						<!-- Remember Me -->
						<div class="block mt-4">
								<label for="remember_me" class="inline-flex items-center">
										<input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
										<span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
								</label>
						</div>

						<div class="flex items-center justify-between mt-4">
							@if (Route::has('password.request'))
								<a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
									{{ __('Forgot your password?') }}
								</a>
							@endif

							@if( config('app.allow_register'))
								<a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">{{ __('Not yet registered?') }}</a>
							@endif

							<button class="button big">
								{{ ___('login') }}
							</button>
						</div>
					
				</form>
		</x-auth-card>
</x-guest-layout>
