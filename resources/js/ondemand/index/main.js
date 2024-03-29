import { arrayByClass } from '../../shared/helpers.mjs';
import { updateCartQuantity } from '../../shared/update-cart.mjs';
import { popFlash } from '../../shared/popup.mjs';
import Glide from '@glidejs/glide';
import Html from '@glidejs/glide/src/components/html';

const addToCartButtons = arrayByClass('add-to-cart-button');
const translationHelper = document.getElementById('translation-helper');

const flashSuccessCooledDown = popFlash('<img src="/img/frog_logo_heart.svg" alt="Frog that loves you"><span class="mx-4">'+translationHelper.dataset.addedMessage+'</span><a class="button-lg" href="/cart">'+translationHelper.dataset.checkoutButton+'</a>');

const flashErrorCooledDown = message => {
	popFlash('<img src="/img/frog_logo_warning.svg" alt="Frog that warns you"><span class="mx-4">'+message+'</span>')();
};

addToCartButtons.map(buttons => {
	buttons.addEventListener('click', e => {
		e.preventDefault();
		e.currentTarget.blur();
		fetch(e.currentTarget.href, {
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

// Glide ---------------------------------------------------------------------

// Update fix from driskell
// https://github.com/glidejs/glide/pull/457
function HtmlFix(Glide, Components, Events) {
    const HtmlFix = Html(Glide, Components, Events);
    Events.on('update', () => {
        HtmlFix.mount();
    });
    return HtmlFix;
}

// Glides
let glides = new Array;
let glidesElements = arrayByClass('glide');

glidesElements.forEach((element, index) => {
	glides[index] = new Glide(element, {
		type: 'carousel',
		keyboard: false,
		animationDuration: 700,
		rewind: true,
		swipeThreshold: 50,
		gap: 1,
	}).mount({Html: HtmlFix});
});

glides.forEach((glide, index) => {
	glide.on('move.after', () => {
		document.getElementById('counter-' + index).firstChild.firstChild.nodeValue = glide.index + 1;
	});
});

// Variations
const variationInput = document.getElementsByClassName('variations-select');

for(let input of variationInput) {

	// When selecting a new variation
	input.addEventListener('input', e => {

		// Elements
		const glideIndex = e.target.dataset.glideIndex; // Get the glide index
		const track = document.getElementById('glide-' + glideIndex).firstElementChild.firstElementChild; // Get the track wrapper
		const loader = document.getElementById('variations-loader-' + glideIndex);
		const price = document.getElementById('price-' + glideIndex);
		const addToCart = document.getElementById('add-to-cart-' + glideIndex);
		const counter = document.getElementById('counter-' + glideIndex);

		// Images arrays
		const variationData = JSON.parse(e.target.value);
		const imagesLoaded = [];

		// Init
		loader.classList.remove('hidden');

		// Updating price
		price.firstChild.nodeValue = variationData.price;

		// Updating add to cart button
		if(variationData.stock > 0) { // Normaly if we have a positive stock, preorder should be off
			addToCart.classList.remove('out');
			addToCart.firstElementChild.firstChild.nodeValue = addToCart.firstElementChild.dataset.labelAdd
		} else if(variationData.preorder) { // preorder is 0 or 1
			addToCart.classList.remove('out');
			addToCart.firstElementChild.firstChild.nodeValue = addToCart.firstElementChild.dataset.labelPre
		} else { // if stock <= 0 and preorder is false
			if(!addToCart.classList.contains('out')) {
				addToCart.classList.add('out');
			}
			addToCart.firstElementChild.firstChild.nodeValue = addToCart.firstElementChild.dataset.labelOut
		}
		// We replace the link's href no matter what, since it's always the same and it's deactivated by 'out' class
		addToCart.href = addToCart.href.replace(/\/[0-9]+$/, '/'+variationData.id);

		// Updating info
		const extra = document.getElementById('extra-info-' + glideIndex);
		if(variationData.extra !== null) {
			extra.classList.remove('hidden');
			extra.firstElementChild.nextElementSibling.innerHTML = variationData.extra;
		} else {
			extra.classList.add('hidden');
		}

		const allLoaded = () => {

			// Cleaning slides
			const slides = Array.from(track.children);
			slides.forEach((slide, index) => {
				if(!slide.classList.contains('glide__slide--clone')) {
					track.removeChild(slide);
				}
			});

			// Inserting new slides
			imagesLoaded.forEach(image => {
				const newLi = document.createElement('li');
				newLi.setAttribute('class', 'glide__slide text-center');
				image.setAttribute('class', 'm-auto w-full');
				newLi.append(image);
				track.append(newLi);
			});

			// Updating glide
			glides[glideIndex].update();
			glides[glideIndex].go('=0');

			loader.classList.add('hidden');

			// Updating counter
			counter.lastElementChild.firstChild.nodeValue = variationData.media.length;

		}

		// Preloading all images
		variationData.media.forEach((url, index) => {
			const imageRessource = new Image();
			imageRessource.src = window.location.origin+'/'+url;
			imageRessource.addEventListener('load', () => {
				imagesLoaded[index] = imageRessource;
				//console.log(`Loaded image ${url}`);
				if(imagesLoaded.length === variationData.media.length) {
					allLoaded();
				}
			});
		});

	});

}