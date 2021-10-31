import { displayPanel } from '../../shared/helpers.mjs';

const thumbs = document.getElementsByClassName('hover-thumb');

for(const thumb of thumbs) {
	const fullImg = thumb.nextElementSibling;
	fullImg.classList.toggle('hidden');
	const fullImgDim = fullImg.getBoundingClientRect();
	fullImg.classList.toggle('hidden');

	displayPanel(thumb, [(fullImgDim['width'] / -2), ((fullImgDim['height'] + 20) * -1)]);
}
