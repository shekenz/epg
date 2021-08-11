export function updateQuantityFor(id, relativeValue = 0) {
	// We have 2 sub-totals for each article : One from the article component, one from in the order summarize list
	let els = Array.from(document.getElementsByClassName(`quantity-for-id-${id}`));
	// Current quantity is parsed from article sub-total (Element 0)
	let currentQuantity = parseInt(els[0].firstChild.nodeValue);
	let newValue = currentQuantity + relativeValue;
	els.forEach(item => {
		// Sub-total from order list is always the child of a parenthesis-block
		// parenthesis-block must be hidden if quantity is less or equal to 1
		let parent = item.parentElement;
		if(parent.classList.contains('parenthesis-block') && newValue <= 1) {
			parent.classList.add('hidden');
		} else if (parent.classList.contains('hidden')) {
			parent.classList.remove('hidden');
		}
		// Update value to both sub-totals
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