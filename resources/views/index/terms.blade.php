<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ config('app.name') }} - {{ ___('terms & conditions') }}</title>
	<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Bold.woff') }}" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Italic.woff') }}" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Medium.woff') }}" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-MediumItalic.woff') }}" as="font" type="font/woff" crossorigin>
	<link rel="preload" href="{{ asset('fonts/MonumentGroteskTrial-Regular.woff') }}" as="font" type="font/woff" crossorigin>
	<link rel="stylesheet" href="{{ asset('css/terms.css') }}">
</head>
<body>
	<main>
		<img src="{{ asset('img/frog_logo.svg') }}" class="m-auto w-32 mb-10">
		{{ $terms }}
	</main>
</body>
</html>