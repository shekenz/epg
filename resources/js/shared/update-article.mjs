export function updateQuantityFor(id, relativeValue = 0) {
	let els = Array.from(document.getElementsByClassName(`quantity-for-id-${id}`));
	let currentQuantity = parseInt(els[0].firstChild.nodeValue);
	let newValue = currentQuantity + relativeValue;
	els.forEach(item => {
		let parent = item.parentElement;
		if(parent.classList.contains('parenthesis-block') && newValue <= 1) {
			parent.classList.add('hidden');
		} else if (parent.classList.contains('hidden')) {
			parent.classList.remove('hidden');
		}
		item.firstChild.nodeValue = newValue;
	});
	return (newValue > 0);
}

export function updateSubTotalFor(id, relativeValue = 0) {
	let els = Array.from(document.getElementsByClassName(`subtotal-for-id-${id}`));
	let currentSubTotal = parseFloat(els[0].firstChild.nodeValue);
	let newValue = currentSubTotal + relativeValue;
	els.forEach(item => {
		item.firstChild.nodeValue = Math.round(newValue * 100) / 100;
	});
}