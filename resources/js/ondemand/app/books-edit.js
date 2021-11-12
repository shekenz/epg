import { displayPanel } from '../../shared/helpers.mjs';
import Sortable from 'sortablejs';

const saveLoader = document.getElementById('save-loader');
const thumbs = document.getElementsByClassName('hover-thumb');

for(const thumb of thumbs) {
	const fullImg = thumb.nextElementSibling;
	fullImg.classList.toggle('hidden');
	const fullImgDim = fullImg.getBoundingClientRect();
	fullImg.classList.toggle('hidden');

	displayPanel(thumb, [(fullImgDim['width'] / -2), ((fullImgDim['height'] + 20) * -1)]);
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
