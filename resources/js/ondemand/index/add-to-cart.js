
import { arrayByClass } from '../../shared/helpers.mjs';
import { updateCartQuantity } from '../../shared/update-cart.mjs';
const axios = require('axios');

const addToCartButtons = arrayByClass('add-to-cart-button');

addToCartButtons.map(item => {
	item.addEventListener('click', e => {
		e.preventDefault();
		e.target.blur();
		axios.post(e.target.href).then( response => {
			updateCartQuantity(1);
		})
		.catch(error => {
			console.error(error);
		});
	});
});