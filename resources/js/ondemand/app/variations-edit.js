import { popUpPlus } from '../../shared/popup.mjs';

let editForm = document.getElementById('edit-form');

editForm.addEventListener('submit', e => {
	if(preOrderCheckbox.checked !== preOrderInitValue) {
		e.preventDefault();
		popUpPlus((wrapper, button) => {
			let title = document.createElement('h2');
			title.append(document.createTextNode('Warning'));
			wrapper.append(title);
			wrapper.append(document.createTextNode('Stock quantity will be reset. Are you sure you want to proceed ?'));
			button.innerHTML = 'Proceed';
		}, () => {
			e.target.submit();
		});
	}
});