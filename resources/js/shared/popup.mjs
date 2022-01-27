/** @module popUp */

export function popUp(message, callback = function() {}) {
	document.getElementById('pop-up-message').innerHTML = message;
	document.getElementById('pop-up-wrapper').classList.toggle('hidden');
	const closeHandler = () => {
		document.getElementById('pop-up-wrapper').classList.toggle('hidden');
		callback();
		document.getElementById('pop-up-close').removeEventListener('click', closeHandler);
	}
	document.getElementById('pop-up-close').addEventListener('click', closeHandler);
}

/**
 * Callback to customize the popup's content.
 * 
 * @callback run 
 * @param {HTMLElement} el Popup's main wrapper where to append content.
 * @param {HTMLElement} button Popup's accept button for customization.
 */

/**
 * Callback called on accept.
 * 
 * @callback buttonCallback
 * @param returned 
 */

/**
 * Open up a new popup with generated content.
 * 
 * @param {run} Callback Fills up the popup's main wrapper.
 * @param {buttonCallback} Callback Called on accept button click.
 */
export function popUpPlus(run = (el, title, button) => {}, buttonCallback = (returned) => {}) {
	const button = document.getElementById('pop-up-button');
	const innerWrapper = document.getElementById('pop-content');
	const title = document.getElementById('pop-up-title');

	const returned = run(innerWrapper, title, button);

	document.getElementById('pop-up-wrapper').classList.toggle('hidden');

	const closeRoutine = () => {
		document.getElementById('pop-up-wrapper').classList.toggle('hidden');
		innerWrapper.innerHTML = '';
	}

	const buttonHandler = e => {
		if(!e.target.hasAttribute('disabled')) {
			new Promise(() => {
				buttonCallback(returned);
			}).then(() => {
				closeRoutine();
			});
			document.getElementById('pop-up-button').removeEventListener('click', buttonHandler);
		}
	}
	button.addEventListener('click', buttonHandler);

	const closeHandler = () => {
		closeRoutine();
		document.getElementById('pop-up-close').removeEventListener('click', closeHandler);
	}
	document.getElementById('pop-up-close').addEventListener('click', closeHandler);
}

export const popFlash = (html, timeout = 5000) => {

	// Outter-globals (Scopped to outter function)
	let timeoutId = false;
	const wrapper = document.getElementById('dyna-flash');

	if(wrapper) {
		// Closure
		return () => {
			// Init
			wrapper.innerHTML = html;
			wrapper.classList.remove('hidden');
			setTimeout(() => {
				wrapper.classList.remove('h-0');
				wrapper.classList.add('h-32');
			}, 10);

			// Clear time out if muliple fire
			if(timeoutId) { clearTimeout(timeoutId); }

			// Fire after cooldown
			timeoutId = setTimeout(() => {	
				wrapper.classList.add('h-0');
				wrapper.classList.remove('h-32');
				setTimeout(() => {
					wrapper.classList.add('hidden');
				}, 500);
			}, timeout);
		}
	} else {
		throw new Error('Flash main wrapper element not found');
	}
}