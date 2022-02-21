<x-index-layout>

	<x-slot name="title">
		{{ ___('contact') }}
	</x-slot>

	<x-slot name="scripts">
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</x-slot>

	<form id="contact-form" action="{{ route('messages.forward') }}" method="post" enctype="multipart/form-data" autocomplete="off">
		@csrf
		@if ($errors->any())
			<h2 class="text-lg text-red-500">{{ __('An error occured. Please verify your form.')}}</h2>
		@endif
		<div class="input-wrapper">
			<input id="email" class="@if($errors->has('email')){{ 'error' }}@endif" name="email" type="email" value="{{ old('email') }}" placeholder="{{ ___('email') }}">
			@if($errors->has('email'))
				@foreach ($errors->get('email') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>
		<div class="input-wrapper">
			<input id="subject" class="@if($errors->has('subject')){{ 'error' }}@endif" name="subject" type="text" value="{{ old('subject') }}" placeholder="{{ ___('subject') }}">
			@if($errors->has('subject'))
				@foreach ($errors->get('subject') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>
		<div class="input-wrapper">
			<textarea id="message" class="@if($errors->has('message')){{ 'error' }}@endif" name="message" placeholder="{{ ___('message') }}">{{ old('message') }}</textarea>
			@if($errors->has('message'))
				@foreach ($errors->get('message') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>

		{{-- reCaptcha --}}
		<div class="input-wrapper text-right">
			<div class="g-recaptcha inline-block" data-sitekey="{{ config('app.recaptcha.site') }}"></div>
		
			@if($errors->has('g-recaptcha-response'))
				@foreach ($errors->get('g-recaptcha-response') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>

		<div class="text-right w-full">
			<button class="button">{{ ___('send') }}</button>
		</div>
	</form>

</x-index-layout>