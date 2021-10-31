import Glide from '@glidejs/glide';
import { arrayByClass } from '../../shared/helpers.mjs';
import Html from '@glidejs/glide/src/components/html';

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

		// Init
		const glideIndex = e.target.dataset.glideIndex; // Get the glide index
		const track = document.getElementById('glide-' + glideIndex).firstElementChild.firstElementChild; // Get the track wrapper
		const loader = document.getElementById('variations-loader-' + glideIndex);
		loader.classList.remove('hidden');

		// Images arrays
		const imagesURLs = JSON.parse(e.target.value);
		const imagesLoaded = [];

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

		}

		// Preloading all images
		imagesURLs.forEach((url, index) => {
			const imageRessource = new Image();
			imageRessource.src = window.location.origin+'/'+url;
			imageRessource.addEventListener('load', () => {
				imagesLoaded[index] = imageRessource;
				console.log(`Loaded image ${url}`);
				if(imagesLoaded.length === imagesURLs.length) {
					allLoaded();
				}
			});
		});

	});

}