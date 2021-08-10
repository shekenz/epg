<x-index-layout>
	<x-slot name="title">
		{{ __('Contact') }}
	</x-slot>
	<form id="contact-form" action="{{ route('messages.forward') }}" method="post" enctype="multipart/form-data" autocomplete="off">
		@csrf
		@if ($errors->any())
			<h2 class="text-lg text-red-500">{{ __('An error occured. Please verify your form.')}}</h2>
		@endif
		<div class="input-wrapper">
			<input id="email" class="@if($errors->has('email')){{ 'error' }}@endif" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}">
			@if($errors->has('email'))
				@foreach ($errors->get('email') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>
		<div class="input-wrapper">
			<input id="subject" class="@if($errors->has('subject')){{ 'error' }}@endif" name="subject" type="text" value="{{ old('subject') }}" placeholder="{{ __('Subject') }}">
			@if($errors->has('subject'))
				@foreach ($errors->get('subject') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>
		<div class="input-wrapper">
			<textarea id="message" class="@if($errors->has('message')){{ 'error' }}@endif" name="message" placeholder="{{ __('Message') }}">{{ old('message') }}</textarea>
			@if($errors->has('message'))
				@foreach ($errors->get('message') as $error)
					
				@endforeach
			<p class="error-info my-1 text-red-500 text-sm italic">{{$error}}</p>
			@endif
		</div>
		<div class="text-right w-full">
			<button class="button">{{ __('Envoyer') }}</button>
		</div>
	</form>

</x-index-layout>