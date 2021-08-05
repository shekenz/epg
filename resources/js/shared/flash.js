import { popFlash } from './popup.mjs';

// Flash fade out
let flash = document.getElementById('flash');

if(flash) {
	let logo;
	switch(flash.dataset.type) {
		case 'success' :
			logo = '<img src="/img/frog_logo_heart.svg" alt="Frog that loves you">';
			break;
		case 'warning' :
			logo = '<img src="/img/frog_logo_warning.svg" alt="Frog that warns you">';
			break;
		case 'error' :
			logo = '<img src="/img/frog_logo_error.svg" alt="Frog that says no">';
			break;
		default :
			logo = '<img src="/img/frog_logo_books.svg" alt="Frog standing on a pile of books">';
			break;
	}
	popFlash(logo+'<span class="mx-4">'+flash.firstChild.nodeValue+'</span>')()
}