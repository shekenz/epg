import { arrayByClass, matchDimension } from '../shared/helpers.mjs';

window.addEventListener('load', () => {

	const blackSquare = document.createElement('div');
	blackSquare.setAttribute('id', 'black-square');
	document.body.insertBefore(blackSquare, document.getElementById('menu'));

	// Initial transform
	// Adding delay to make sure browsers exited their first render loop.
	// Safari will apply a weird bounding box otherwise.
	setTimeout(() => {
		matchDimension(blackSquare, arrayByClass('active')[0].getBoundingClientRect(), 7, 1);
	}, 20);
	
	// Adding menu click event
	const menuItems = arrayByClass('menu-item');
	menuItems.map(item => {
		item.addEventListener('click', e => {
			e.preventDefault();
			console.log(e.currentTarget);
			blackSquare.classList.add('animated');
			const itemDimension = e.currentTarget.getBoundingClientRect();
			const href = e.currentTarget.href;
			matchDimension(blackSquare, itemDimension, 7, 1);
			setTimeout(() => { 
				console.log(e.currentTarget);
				window.location = href;
			}, 300);
		});
	});

	// Resize event
	window.addEventListener('resize', () => {
		applyTransform(arrayByClass('active')[0].getBoundingClientRect(), 7, 1);
	});
});

