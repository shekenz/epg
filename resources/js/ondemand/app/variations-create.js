
// Globals (we need them in variations-edit.js)
window.preOrderCheckbox = document.getElementById('pre-order');
window.preOrderInitValue = preOrderCheckbox.checked;

// Elements
let stockInput = document.getElementById('stock');
let stockInitValue = stockInput.value;
let stockHiddenInput = document.getElementById('stock-hidden');

// preOrder logic

let togglePreOrder = test => {
	stockInput.disabled = test;
	stockHiddenInput.disabled = !test;
}

console.log(preOrderInitValue);

togglePreOrder(preOrderInitValue);

preOrderCheckbox.addEventListener('input', e => {
	togglePreOrder(e.target.checked);
	if(stockInitValue < 0) {
		if(e.target.checked) {
			stockInput.value = stockHiddenInput.value = stockInitValue;
		} else {
			stockInput.value = stockHiddenInput.value = 0;
		}
	} else {
		if(e.target.checked) {
			stockInput.value = stockHiddenInput.value = 0;
		} else {
			stockInput.value = stockHiddenInput.value = stockInitValue;
		}
	}
});

stockInput.addEventListener('input', e => {
	stockHiddenInput.value = e.target.value
});