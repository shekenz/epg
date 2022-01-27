import { popUpPlus } from '../../shared/popup.mjs';

const editForm = document.getElementById('edit-form');

editForm.addEventListener('submit', e => {
	if(preOrderCheckbox.checked !== preOrderInitValue) {
		e.preventDefault();
		popUpPlus((wrapper, title, button) => {
			title.innerHTML = 'Warning';
			wrapper.append(document.createTextNode('Stock quantity will be reset. Are you sure you want to proceed ?'));
			button.innerHTML = 'Proceed';
		}, () => {
			e.target.submit();
		});
	}
});