import { popUpPlus } from '../../shared/popup.mjs';

let quantityInput = document.getElementById('quantity');
let quantityHiddenInput = document.getElementById('quantity-hidden');
let preorderInput = document.getElementById('pre-order');
let originalQuantity = quantityInput.value;
let editForm = document.getElementById('edit-form');

let togglePreorder = checkBox => {
	if(checkBox.checked) {
		quantityInput.value = 0;
		quantityInput.setAttribute('disabled', true);
		quantityHiddenInput.removeAttribute('disabled');
	} else {
		quantityInput.value = originalQuantity;
		quantityInput.removeAttribute('disabled');
		quantityHiddenInput.setAttribute('disabled', true);
	}
}

// Init
togglePreorder(preorderInput);

// Events
if(editForm) {
	editForm.addEventListener('submit', e => {
		if(originalQuantity !== '0' && preorderInput.checked) {
			e.preventDefault();
			popUpPlus((wrapper, button) => {
				let title = document.createElement('h2');
				title.append(document.createTextNode('Warning'));
				wrapper.append(title);
				wrapper.append(document.createTextNode('Setting the book to pre-order will reset the stock quantity. Are you sure you want to proceed ?'));
				button.innerHTML = 'Proceed';
			}, () => {
				e.target.submit();
			});
		}
	});
}

quantityInput.addEventListener('input', e => {
	originalQuantity = e.target.value;
});

preorderInput.addEventListener('input', e => {
	togglePreorder(e.target);
});