<x-app-layout>

	<x-slot name="title">
		{{ $shippingMethod->label }}
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

	<form method="POST" action="{{ route('shippingMethods.update', $shippingMethod->id) }}" class="mb-2 w-full flex gap-8">
		@csrf
		@method('patch')
		<div class="flex-shrink-0">
			<label>{{ ___('name') }} : </label><input type="text" maxlength="127" name="label" class="input-shared" value="{{ old('label') ?? $shippingMethod->label }}"/><br>
			<label>{{ ___('base price') }} : </label><input type="number" step="0.01" min="0" name="price" class="input-shared" value="{{ old('price') ?? $shippingMethod->price }}"/><br>
			<label>{{ ___('maximum weight') }} (g) : </label><input type="number" min="0" name="max_weight" class="input-shared" value="{{ old('max_weight') ?? $shippingMethod->max_weight }}"/>
			<label>{{ ___('rule') }} : </label><select name="rule" class="input-shared">
				<option></option>
				<option value="national" @if($shippingMethod->rule == 'national') selected="selected" @endif>{{ ___('national') }}</option>
				<option value="international" @if($shippingMethod->rule == 'international') selected="selected" @endif>{{ ___('international') }}</option>
			</select>
		</div>
		<textarea placeholder="{{ ___('description') }}" class="w-full rounded-lg border border-gray-300" name="info">{{ old('info') ?? $shippingMethod->info }}</textarea>
		<input type="submit" value="{{ ___('save') }}" class="button-shared self-center" />
	</form>

</x-layout>