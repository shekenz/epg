import { matchDimension } from './helpers.mjs';

export function updateCartQuantity(relativeValue = 0) {

	let el = document.getElementById('cart-menu-count');
	if (el.firstChild) {
		var currentQuantity = parseInt(el.firstChild.nodeValue.match(/\d+/));
	} else {
		var currentQuantity = 0;
	}

	let newValue = currentQuantity + relativeValue;
	if(!isNaN(newValue)) {
		if(newValue > 0) {
			el.innerHTML = ` (${newValue})`;
		} else {
			el.innerHTML = '';
		}
		matchDimension(document.getElementById('black-square'), el.parentElement.getBoundingClientRect(), 7, 1);
	} else {
		throw new TypeError('newValue is not a number');
	}
}

export function setCartTotal(value) {
	let total = Math.round(value * 100) / 100;
	document.getElementById('cart-total').firstChild.nodeValue = total;
	return total;
}