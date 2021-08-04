
import { arrayByClass } from '../../shared/helpers.mjs';
import { updateCartQuantity } from '../../shared/update-cart.mjs';

const addToCartButtons = arrayByClass('add-to-cart-button');
const addedFlash = document.getElementById('added-flash');

let toggleAddedFlash = (open = false) => {
	if(open) {
		addedFlash.classList.remove('hidden');
		
		setTimeout(() => {
			addedFlash.classList.remove('h-0');
			addedFlash.classList.add('h-24');
		}, 50);
	} else {
		addedFlash.classList.remove('h-24');
		addedFlash.classList.add('h-0');
		setTimeout(() => {
			addedFlash.classList.add('hidden');
		}, 150);
	}
};

addToCartButtons.map(buttons => {
	buttons.addEventListener('click', e => {
		e.preventDefault();
		e.target.blur();
		fetch(e.target.href, {
			method: 'post',
			headers: {
				'accept': 'application/json'
			}
		}).then( () => {
			updateCartQuantity(1);
			toggleAddedFlash(true);
			setTimeout(() => {
				toggleAddedFlash();
			}, 4000);
		})
		.catch(error => {
			console.error(error);
		});
	});
});