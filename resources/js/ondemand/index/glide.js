import Glide from '@glidejs/glide';
import { arrayByClass } from '../../shared/helpers.mjs';

// Glides
let glides = new Array;

arrayByClass('glide').map((item,index) => {
	glides[index] = new Glide(item, {
		type: 'carousel',
		keyboard: false,
		animationDuration: 700,
		rewind: true,
		swipeThreshold: 50,
		gap: 1,
	}).mount();
});

glides.map((item, index) => {
	item.on('move.after', () => {
		document.getElementById('counter-'+index).firstChild.firstChild.nodeValue = item.index + 1;
	});
});