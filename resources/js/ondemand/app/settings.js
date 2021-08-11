import { popUpPlus } from '../../shared/popup.mjs';
import { arrayByClass, randomString } from '../../shared/helpers.mjs';
import { formatISO } from 'date-fns';

document.getElementById('shipping-allowed-countries').addEventListener('input', e => {
	if(e.target.value.search(/^([A-z]{2},+)*([A-z]{2},*)?$/g) < 0) {
		e.target.classList.add('error');
	} else {
		e.target.classList.remove('error');
	}
});

arrayByClass('delete-coupon').forEach(button => {
	button.addEventListener('click', e => {
		let coupon = e.currentTarget.parentNode.firstElementChild.firstElementChild.firstChild.nodeValue;
		if(!confirm(`Are you sure you want to delete coupon ${coupon} ?`)) { e.preventDefault() };
	}, false);
});

arrayByClass('delete-shipping-method').forEach(button => {
	button.addEventListener('click', e => {
		let shippingMethod = e.currentTarget.parentNode.parentNode.firstElementChild.firstChild.nodeValue;
		if(!confirm(`Are you sure you want to delete shipping method ${shippingMethod} ?`)) { e.preventDefault() };
	}, false);
});

document.getElementById('add-coupon').addEventListener('click', e => {
	popUpPlus((wrapper, button) => {
		e.preventDefault();

		// ----------------------- Init
		
		button.firstChild.nodeValue = 'Add';
		button.setAttribute('disabled', true);

		let br = document.createElement('br');

		let title = document.createElement('h2');
		title.append('Add new coupon');

		let couponForm = document.createElement('form');
		couponForm.setAttribute('method', 'POST');
		couponForm.setAttribute('action', e.target.href);

		// ----------------------- Wrappers

		let dateOutterWrapper = document.createElement('div');
		dateOutterWrapper.classList.add('flex');
		dateOutterWrapper.classList.add('gap-x-2');
		let couponOutterWrapper = dateOutterWrapper.cloneNode();
		couponOutterWrapper.classList.add('items-start');

		dateOutterWrapper.classList.add('mt-3');
		let startsInnerWrapper = document.createElement('div');
		startsInnerWrapper.classList.add('w-full');
		let expiresInnerWrapper = startsInnerWrapper.cloneNode();
		let quantityInnerWrapper = startsInnerWrapper.cloneNode();

		let labelWrapper = document.createElement('div');

		// ----------------------- Inputs & labels

		let labelInput = document.createElement('input');
		labelInput.setAttribute('name', 'label');
		labelInput.setAttribute('type', 'text');
		labelInput.setAttribute('placeholder', 'LABEL');
		labelInput.setAttribute('maxlength', '8');
		labelInput.setAttribute('autocomplete', 'off');
		labelInput.classList.add('coupon-input');
		labelInput.classList.add('flex-grow');

		labelInput.addEventListener('input', e => {
			e.target.value = e.target.value.toUpperCase();
		});

		let labelGeneration = document.createElement('a');
		labelGeneration.setAttribute('href', '#');
		labelGeneration.append('Generate random label');
		labelGeneration.classList.add('base-link');
		labelGeneration.classList.add('text-sm');
		labelGeneration.classList.add('italic');

		labelGeneration.addEventListener('click', e => {
			e.preventDefault();
			labelInput.value = randomString();
		});

		let valueInput = document.createElement('input');
		valueInput.setAttribute('name', 'value');
		valueInput.setAttribute('type', 'number');
		valueInput.setAttribute('placeholder', 'Value');
		valueInput.classList.add('coupon-input');
		valueInput.classList.add('flex-grow');

		let typeInput = document.createElement('select');
		typeInput.setAttribute('name', 'type');
		typeInput.classList.add('coupon-input');

		let typeOptionPercentage = document.createElement('option');
		typeOptionPercentage.append('%');
		typeOptionPercentage.setAttribute('value', 0);
		let typeOptionAmount = document.createElement('option');
		typeOptionAmount.append('€');
		typeOptionAmount.setAttribute('value', 1);

		let startsInput = document.createElement('input');
		startsInput.setAttribute('name', 'starts_at');
		startsInput.setAttribute('type', 'date');
		startsInput.setAttribute('id', 'starts-at');
		startsInput.setAttribute('value', formatISO(new Date(), {representation: 'date'}));
		startsInput.classList.add('input-shared');
		let startsInputLabel = document.createElement('label');
		startsInputLabel.append('Valid from :');
		startsInputLabel.setAttribute('for', 'starts-at');
		startsInputLabel.classList.add('label-shared');
		startsInputLabel.classList.add('lg:text-lg');

		let expiresInput = document.createElement('input');
		expiresInput.setAttribute('name', 'expires_at');
		expiresInput.setAttribute('type', 'date');
		expiresInput.setAttribute('id', 'expires-at');
		expiresInput.classList.add('input-shared');
		let expiresInputLabel = document.createElement('label');
		expiresInputLabel.append('To :');
		expiresInputLabel.setAttribute('for', 'expires-at');
		expiresInputLabel.classList.add('label-shared');
		expiresInputLabel.classList.add('lg:text-lg');
		let expiresInfo = document.createElement('span');
		expiresInfo.append('(Leave default for infinity)');
		expiresInfo.classList.add('text-sm');
		expiresInfo.classList.add('text-gray-500');
		expiresInfo.classList.add('italic');

		let quantityInput = document.createElement('input');
		quantityInput.setAttribute('name', 'quantity');
		quantityInput.setAttribute('type', 'number');
		quantityInput.setAttribute('min', 0);
		quantityInput.setAttribute('value', 0);
		quantityInput.classList.add('input-shared');
		let quantityInputLabel = document.createElement('label');
		quantityInputLabel.append('Quantity :');
		quantityInputLabel.classList.add('label-shared');
		quantityInputLabel.classList.add('lg:text-lg');
		let quantityInfo = document.createElement('span');
		quantityInfo.append('(Leave 0 for no limit)');
		quantityInfo.classList.add('text-sm');
		quantityInfo.classList.add('text-gray-500');
		quantityInfo.classList.add('italic');


		// ----------------------- Functions

		// Validate inputs
		let validate = () => {
			if(labelInput.value !== '' && valueInput.value !== '') {
				button.removeAttribute('disabled');
			} else {
				button.setAttribute('disabled', true);
			}
		}

		// ----------------------- Events
		[labelInput, valueInput].forEach(input => {
			input.addEventListener('input', validate);
		});

		// ----------------------- Appends

		typeInput.append(typeOptionPercentage);
		typeInput.append(typeOptionAmount);
		labelWrapper.append(labelInput);
		labelWrapper.append(br.cloneNode());
		labelWrapper.append(labelGeneration);
		couponOutterWrapper.append(labelWrapper);
		couponOutterWrapper.append(valueInput);
		couponOutterWrapper.append(typeInput);
		couponForm.append(couponOutterWrapper);

		startsInnerWrapper.append(startsInputLabel);
		startsInnerWrapper.append(br.cloneNode());
		startsInnerWrapper.append(startsInput);
		expiresInnerWrapper.append(expiresInputLabel);
		expiresInnerWrapper.append(br.cloneNode());
		expiresInnerWrapper.append(expiresInput);
		expiresInnerWrapper.append(expiresInfo);
		quantityInnerWrapper.append(quantityInputLabel);
		quantityInnerWrapper.append(br.cloneNode());
		quantityInnerWrapper.append(quantityInput);
		quantityInnerWrapper.append(quantityInfo);

		dateOutterWrapper.append(quantityInnerWrapper);
		dateOutterWrapper.append(startsInnerWrapper);
		dateOutterWrapper.append(expiresInnerWrapper);

		couponForm.append(dateOutterWrapper);
		couponForm.append(document.getElementsByName('_token')[0].cloneNode());
		
		wrapper.append(title);
		wrapper.append(couponForm);

		return couponForm;

	}, returned => {

		document.getElementById('popup-loader').classList.toggle('hidden');
		returned.submit();

	});
});

arrayByClass('shipping-range-wrapper').forEach( wrapper => {
	wrapper.addEventListener('mouseenter', e => {
		e.currentTarget.firstElementChild.classList.remove('hidden');
	});

	wrapper.addEventListener('mouseleave', e => {
		e.currentTarget.firstElementChild.classList.add('hidden');
	});

	wrapper.addEventListener('mousemove', e => {
		const cursor = e.currentTarget.firstElementChild;
		const xOffset = e.currentTarget.getBoundingClientRect().left;
		cursor.style.left =  (e.clientX - xOffset) + 'px';
	});
});