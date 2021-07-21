import { arrayByClass } from '../shared/helpers.mjs';

window.addEventListener('load', () => {

	let blackSquare = document.createElement('div');
	blackSquare.setAttribute('id', 'black-square');
	document.body.insertBefore(blackSquare, document.getElementById('menu-wrapper').nextElementSibling);

	let applyTransform = (boundingRect, xOffset = 0, yOffset = 0) => {
		blackSquare.style.left = `${boundingRect.left - xOffset}px`;
		blackSquare.style.top = `${boundingRect.top - yOffset}px`;
		blackSquare.style.width = `${boundingRect.width + (xOffset*2) }px`;
		blackSquare.style.height = `${boundingRect.height + 2 + (yOffset*2) }px`;
	}

	// Initial transform
	// Adding delay to make sure browsers exited their first render loop.
	// Safari will apply a weird bounding box otherwise.
	setTimeout(() => {
		applyTransform(arrayByClass('active')[0].getBoundingClientRect(), 7, 1);
	}, 20);
	
	// Adding menu click event
	let menuItems = arrayByClass('menu-item');
	menuItems.map(item => {
		item.addEventListener('click', e => {
			e.preventDefault();
			blackSquare.classList.add('animated');
			let itemDimension = e.target.getBoundingClientRect();
			applyTransform(itemDimension, 7, 1);
			setTimeout(() => { window.location = e.target.href }, 300);
		});
	});

	// Adding menu hover event (for blending menu items underlines)
	let menuItemsUnder = arrayByClass('menu-item-under');
	menuItems.map((item, index) => {
		item.addEventListener('mouseenter', () => {
			menuItemsUnder[index].classList.toggle('hover');
		});
		item.addEventListener('mouseleave', () => {
			menuItemsUnder[index].classList.toggle('hover');
		});
	});

	// Resize event
	window.addEventListener('resize', () => {
		applyTransform(arrayByClass('active')[0].getBoundingClientRect(), 7, 1);
	});
});

