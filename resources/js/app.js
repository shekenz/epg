// Laravel
//require('./bootstrap');
require('alpinejs');

// App
require('./shared/flash');
require('./app/publication');

// Add extra class to pop-up-wrapper blur effect is not supported (Firefox)
if(navigator.appVersion.indexOf('AppleWebKit') === -1) {
	const popipWrapper = document.getElementById('pop-up-wrapper')
	popipWrapper.classList.add('bg-white');
	popipWrapper.classList.add('bg-opacity-90');
}