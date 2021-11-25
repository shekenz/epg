import Sortable from 'sortablejs';
import { popUpPlus } from '../../shared/popup.mjs';

const saveLoader = document.getElementById('save-loader');
const thumbs = document.getElementsByClassName('hover-thumb');

for(const thumb of thumbs) {
	thumb.addEventListener('click', e => {
		popUpPlus(el => {
			let img = document.createElement('img');
			img.setAttribute('src', thumb.dataset.fullSrc);
			el.append(img);
		})
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
