@props(['full'])

<svg width="128px" height="128px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
	@isset($full)		
		<circle cx="50" cy="50" r="32" fill="currentColor" opacity="1">
			<animate attributeType="XML" attributeName="opacity" values="0.8;0;0.8" dur="2s" repeatCount="indefinite"/>
		</circle>
	@endisset
	<circle transform="rotate(-90, 50, 50)" cx="50" cy="50" r="42" stroke-width="8" stroke="currentColor" fill="transparent" stroke-dasharray="300" stroke-dashoffset="0">
		<animate attributeType="XML" attributeName="stroke-dashoffset" from="0" to="600" dur="2s" repeatCount="indefinite"/>
		<animateTransform attributeType="XML" attributeName="transform" type="rotate" from="270 50 50" to="-90 50 50" dur="4s" repeatCount="indefinite" />
	</circle>
</svg>