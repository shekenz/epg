// user-menu hide/unhide

userMenu = document.getElementById('user-menu');

hideButtons = new Array(document.getElementById('hide-button'), document.getElementById('unhide-button'));

for(const hideButton of hideButtons) {
	hideButton.addEventListener('click', (e) => {
		e.preventDefault();
		elements = document.getElementsByClassName('hideable');
		for(element of elements) {
			element.classList.toggle('hidden');
		}
		userMenu.classList.toggle('w-full');
	});
}