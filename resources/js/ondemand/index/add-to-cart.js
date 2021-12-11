
import { arrayByClass } from '../../shared/helpers.mjs';
import { updateCartQuantity } from '../../shared/update-cart.mjs';
import { popFlash } from '../../shared/popup.mjs';

const addToCartButtons = arrayByClass('add-to-cart-button');
const translationHelper = document.getElementById('translation-helper');

// + Webview hack
const flashSuccessCooledDown = popFlash('<img src="/img/frog_logo_heart.svg" alt="Frog that loves you"><span class="mx-4">'+translationHelper.dataset.addedMessage+'</span><a class="button-lg" href="/cart"'+(webview ? ' target="_blank"' : '')+'>'+translationHelper.dataset.checkoutButton+'</a>');

const flashErrorCooledDown = message => {
	popFlash('<img src="/img/frog_logo_warning.svg" alt="Frog that warns you"><span class="mx-4">'+message+'</span>')();
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
		}).then( r => {
			if(r.status === 200) {
				updateCartQuantity(1);
				flashSuccessCooledDown();
			} else {
				flashErrorCooledDown(r.statusText);
			}
		})
		.catch(error => {
			console.error(error);
		});
	});
});