import Sortable from 'sortablejs';

const saveLoader = document.getElementById('save-loader');
const thumbs = document.getElementsByClassName('hover-thumb');
const imgPopup = document.getElementById('img-popup-wrapper');
const title = document.getElementById('img-popup-title');
const closeImgPopupButton = document.getElementById('close-img-popup');
const nextImgButton = document.getElementById('next-img-popup');
const previousImgButton = document.getElementById('previous-img-popup');
const imgPopupContent = document.getElementById('img-popup-content');
let imgCollection;
let currentIndex;

// imgPopup Events handlers

const moveImgSlide = (inverseDirection = false) => {
	const direction = (inverseDirection) ? -1 : 1;
	currentIndex = ((currentIndex + direction) % imgCollection.length);
	if(currentIndex < 0) {
		currentIndex = imgCollection.length - 1;
	}
	title.innerHTML = imgCollection[currentIndex].dataset.title;
	imgPopupContent.src = imgCollection[currentIndex].dataset.fullSrc;
}

const closePopup = e => {
	e.preventDefault();
	imgPopupContent.src = '/img/loader2.gif';
	imgPopup.classList.add('hidden');
}

const nextImg = () => { moveImgSlide(); };
const previousImg = () => { moveImgSlide(true); };

// imgPopup Events

closeImgPopupButton.addEventListener('click', closePopup);
nextImgButton.addEventListener('click', nextImg);
previousImgButton.addEventListener('click', previousImg);

window.addEventListener('keydown', e => {
	switch(e.key) {
		case 'x' : closePopup(e); break;
		case 'ArrowLeft' : previousImg(); break;
		case 'ArrowRight' : nextImg(); break;
	}
});

for(const thumb of thumbs) {
	thumb.addEventListener('click', e => {
		imgCollection = e.currentTarget.parentElement.children;
		currentIndex = parseInt(e.currentTarget.dataset.index);
		imgPopupContent.src = e.currentTarget.dataset.fullSrc;
		title.innerHTML = e.currentTarget.dataset.title;
		imgPopup.classList.remove('hidden');
	});
}

// Reorder variations

const sortWrapper = document.getElementById('variation-table-body')

const reorderRequest = e => {

	const variations = e.target.children;
	let order = {};

	for(let i = 0; i < variations.length; i++) {
		order[parseInt(variations[i].dataset.id)] = i;
	}

	const form = new FormData()
	form.set('order', JSON.stringify(order));

	saveLoader.classList.remove('hidden');
	window.fetch(`/api/variations/${sortWrapper.dataset.bookInfoId}/reorder`, {
		method: 'post',
		headers: {
			'accept': 'application/json'
		},
		body: form
	}).then(() => {
		saveLoader.classList.add('hidden');
	});

}

const options = {
	handle: '.cursor-grab',
	animation: 150,
	onSort: reorderRequest,
}

Sortable.create(sortWrapper, options);
