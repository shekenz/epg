import Sortable from 'sortablejs';

// Dropzones declaration
const mediaLink = document.getElementById('media-link');
const mediaLibrary = document.getElementById('media-library');
const SWAP_THRESHOLD = 1;

let toggleVisibility = e => {
	if(!e.item.style.visibility) {
		e.item.style.visibility = 'hidden';
	} else {
		e.item.style.visibility = null;
	}
}

let disablePlaceholder = e => {
	let libPlaceholder = document.getElementById('media-library-placeholder');
	let linkPlaceholder = document.getElementById('media-link-placeholder');
	if(libPlaceholder) {
		mediaLibrary.removeChild(libPlaceholder);
	}
	if(linkPlaceholder) {
		mediaLink.removeChild(linkPlaceholder);
	}
}

let toggleInput = e => {
	let firstElement = e.item.firstElementChild;
	if(firstElement.tagName.toUpperCase() === 'INPUT') {
	 	e.item.removeChild(firstElement);
	} else {
		let input = document.createElement('input');
		let thumbnail = e.item.firstElementChild;
		input.setAttribute('name', 'media[]');
		input.setAttribute('type', 'hidden');
		input.setAttribute('value', thumbnail.dataset.id);
	 	e.item.insertBefore(input, thumbnail);
	}
}

const sharedOptions = {
	group: 'shared',
	swapThreshold: SWAP_THRESHOLD,
	animation: 150,
	filter: '.drop-placeholder',
	onChange: disablePlaceholder,
	onStart: toggleVisibility,
	onEnd: toggleVisibility,
	onAdd: toggleInput,
}

const mediaLibrarySortable = Sortable.create(mediaLibrary, {...sharedOptions, sort: false});
const mediaLinkSortable = Sortable.create(mediaLink, {...sharedOptions, sort: true});

