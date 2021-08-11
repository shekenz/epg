import { matchDimension, roundPrice } from './helpers.mjs';

export const updateCartQuantity = (relativeValue = 0) => {

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
		// Recalculate menu black-square dimensions
		matchDimension(document.getElementById('black-square'), el.parentElement.getBoundingClientRect(), 7, 1);
	} else {
		throw new TypeError('newValue is not a number');
	}
}

// Set a value to cartTotal
export const setCartTotal = value => {
	let total = roundPrice(value);
	document.getElementById('cart-total').firstChild.nodeValue = total;
	return total;
}