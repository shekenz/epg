const switches = document.getElementsByClassName('switch');
const darkModeSwitch = document.getElementById('darkmode');

// Dark theme switch
if(document.documentElement.classList.contains('dark')) darkModeSwitch.classList.remove('off');

darkModeSwitch.addEventListener('click', (e) => {
	e.preventDefault();
	if (!document.documentElement.classList.contains('dark')) {
		document.documentElement.classList.add('dark');
		localStorage.theme = 'dark';
	} else {
		document.documentElement.classList.remove('dark');
		localStorage.theme = 'light';
	}
});