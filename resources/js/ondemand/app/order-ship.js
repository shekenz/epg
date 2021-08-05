import { popUpPlus } from '../../shared/popup.mjs';
import { arrayByClass } from '../../shared/helpers.mjs';

arrayByClass('new-tab').forEach(item => {
	item.addEventListener('click', e => {
		e.preventDefault();
		window.open(e.target.href);
	});
});

document.getElementById('ship-form').addEventListener('submit', e => {
	e.preventDefault();
	popUpPlus((wrapper, button) => {

		button.firstChild.nodeValue = 'Ship';

		let title = document.createElement('h2');
		title.appendChild(document.createTextNode('Confirm shipping'));

		let shipTrackingForm = document.createElement('form');
		shipTrackingForm.setAttribute('method', 'POST');
		shipTrackingForm.setAttribute('action', e.target.action);
		shipTrackingForm.appendChild(document.getElementsByName('_token')[0].cloneNode());
		
		// Tracking URL
		let shipTrackingURL = document.createElement('input');
		shipTrackingURL.setAttribute('type', 'text');
		shipTrackingURL.setAttribute('name', 'tracking_url');
		shipTrackingURL.classList.add('input-shared');
		let shipTrackingURLLabel = document.createElement('label');
		shipTrackingURLLabel.classList.add('label-shared');
		shipTrackingURLLabel.classList.add('block');
		shipTrackingURLLabel.classList.add('text-lg');
		shipTrackingURLLabel.classList.add('mt-4');
		shipTrackingURLLabel.appendChild(document.createTextNode('Tracking URL :'))

		// Appending
		shipTrackingForm.appendChild(shipTrackingURLLabel);
		shipTrackingForm.appendChild(shipTrackingURL);
		
		wrapper.appendChild(title);
		wrapper.appendChild(shipTrackingForm);
		return shipTrackingForm;
	},
	returned => {
		document.getElementById('popup-loader').classList.toggle('hidden');
		returned.submit();
	});
});