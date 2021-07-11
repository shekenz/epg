import { arrayByClass } from '../../shared/helpers.mjs';
import { popUp } from '../../shared/popup.mjs';
import { updateQuantityFor, updateSubTotalFor } from '../../shared/update-article.mjs';
import { updateCartQuantity, updateCartTotal } from '../../shared/update-cart.mjs';

// Shipping method init
let shippingPrice = 0;
let shippingMethod = 0;

// Elements
let cart = document.getElementById('cart');
let shippingMethodInputs = (Array.from(document.getElementsByName('shipping-method')));
let incrementButtons = arrayByClass('qte-button');
let removeAllButtons = arrayByClass('remove-all-button');

// No connection error handler
let fetchErrorHandler = () => {
	popUp('Impossible to reach server. Please make sure you are connected to the internet.');
	console.error('Impossible to reach server. Please make sure you are connected to the internet.');
}

// Empty cart verification
let checkEmptyCart = () => {
	if(parseFloat(document.getElementById('cart-total').firstChild.nodeValue) - shippingPrice === 0) {
		document.getElementById('content').removeChild(document.getElementById('cart-wrapper'));
		document.getElementById('empty-cart-info').classList.toggle('hidden');
		//TODO trigger animation menu reset
		//window.location.href = window.location.origin;
	}
}

// Initiate current shippingMethod ID
shippingMethodInputs.forEach(input => {
	if(input.hasAttribute('checked')) {
		shippingMethod = input.value;
		shippingPrice = parseFloat(input.dataset.price);
		updateCartTotal(shippingPrice);
	}
});

// Increment/Decrement article buttons //TODO rename
incrementButtons.forEach(button => {
	button.addEventListener('click', e => {
		e.preventDefault();
		// Returns any numbers at the end of string
		let id = /[0-9]+$/.exec(e.target.href)[0];
		fetch(e.target.href, {
			method: 'post',
			headers: {
				'accept': 'application/json'
			}
		})
		.then(response => {
			if(response.status === 200) {
				return response.json();
			}
		})
		.catch(() => {
			fetchErrorHandler;
		})
		.then(jsonResponse => {
			let book = jsonResponse.book;
			// Book modifier is 1 when adding book and -1 when removing book
			updateCartQuantity(book.modifier);
			updateSubTotalFor(book.id, book.price * book.modifier);
			// updateQuantityFor returns false when article quantity reaches 0
			if(!updateQuantityFor(book.id, book.modifier)) {
				document.getElementById('cart').removeChild(document.getElementById(`article-${book.id}`));
			};
			// updateCartTotal
			updateCartTotal(book.price * book.modifier);
			checkEmptyCart();
		})
		.catch(() => {});
	})
});

// Remove all units of an article
removeAllButtons.forEach(button => {
	button.addEventListener('click', e => {
		e.preventDefault();
		// Returns any numbers at the end of string
		let bookID = /[0-9]+$/.exec(e.target.href)[0];
		fetch(e.target.href, {
			method: 'post',
			headers: {
				'accept': 'application/json'
			}
		})
		.then( response => {
			if(response.status === 200) {
				return response.json();
			}
		}).catch(() => {
			fetchErrorHandler;
		})
		.then( jsonResponse => {
			if(jsonResponse.bookID === bookID) {
				cart.removeChild(document.getElementById('article-'+jsonResponse.bookID));
				updateCartQuantity(parseInt(jsonResponse.removedUnits) * -1);
				// updateCartTotal
				updateCartTotal(parseFloat(jsonResponse.removedAmount) * -1)
				checkEmptyCart();
			} else {
				console.error('BookID mismatch');
			}
		})
		.catch(() => {});
	});
});

// Update price and shippingMethod ID on change
shippingMethodInputs.forEach(input => {
	input.addEventListener('focus', e => {
		shippingMethod = e.target.value;
		let newShippingPrice = parseFloat(e.target.dataset.price);
		updateCartTotal( (shippingPrice * -1));
		updateCartTotal(newShippingPrice);
		shippingPrice = newShippingPrice;
	});
});

arrayByClass('shipping-method').map(input => {
	input.addEventListener('change', e => {
		let totalEl = document.getElementById('total');
		shippingPrice = parseFloat(e.target.value);
		let totalNoShipping = parseFloat(totalEl.dataset.totalNoShipping);
		totalEl.firstChild.nodeValue = shippingPrice + totalNoShipping;
		document.getElementById('shipping-price').firstChild.nodeValue = shippingPrice;
	});
});

// Paypal
let checkCartButton = document.getElementById('checkCartButton');
if(checkCartButton) {
	checkCartButton.addEventListener('click', e => {
		e.preventDefault();
		fetch(`/api/cart/check`, {
			method: 'post',
			headers: {
				'accept': 'application/json'
			}
		}).then(response => {
			return response.json();
		}).catch(() => {
			fetchErrorHandler;
		}).then(jsonResponse => {
			console.log(jsonResponse);
		});
	});

	if('paypal' in window) {
		paypal.Buttons({
			createOrder: () => {
				return fetch(`/api/cart/check`, {
					method: 'post',
					headers: {
						'accept': 'application/json'
					}
				}).then( // Check cart fetch response
					cartCheckResponse => {
						return cartCheckResponse.json();
					}, fetchErrorHandler
				).then( // Check cart fetch JSON response
					cartCheckResponseJSON => {
						if(cartCheckResponseJSON.updated) {
							popUp('Some articles from you cart are not available anymore. Your cart will now be reloaded. Please check your order again before payment.', () => { window.location.reload() });
						} else {
							return fetch(`/api/order/create/${shippingMethod}`, {
								method: 'post',
								headers: {
								'accept': 'application/json'
								}
							}).then( // Create fetch response
								createResponse => {
									return createResponse.json();
								}, fetchErrorHandler
							).then( // Create fetch response JSON
								createResponseJSON => {
									if(createResponseJSON.id && !createResponseJSON.error) {
										return createResponseJSON.id;
									} else if(createResponseJSON.error) {
										// We have error details
										console.error(createResponseJSON.error);
									} else {
										//--------------------------------------------------------- ERROR AT CREATING ORDER
										popUp('An internal error has occured while creating your order. Our team has been warned and we will work on it as soon as possible. Please try to purchase your goods later. We are sorry for the inconvenience.');
									}
								}
							);
						}
					}
				);
			},
			
			onShippingChange: (data, actions) => {
				return fetch(`/api/order/check-country/${data.shipping_address.country_code}`, {
					method: 'post',
					headers: {
						'accept': 'application/json'
					}
				}).then(
					response => {
						return response.json();
					}, fetchErrorHandler
				).then(jsonResponse => {
						return (jsonResponse.country) ? actions.resolve() : actions.reject();
				});
			},

			onApprove: function(data, actions) {
				return fetch(`/api/order/capture/${data.orderID}`, {
					method: 'post',
					headers: {
						'accept': 'application/json'
					},
				}).then(
					response => {
						return response.json();
					}, fetchErrorHandler
				).then(jsonResponse => {
					if(jsonResponse.id && !jsonResponse.error) {
						window.location.href = `${window.location.origin}/order/success/${jsonResponse.id}`;
					} else if(jsonResponse.error) {
						if(jsonResponse.error.name == 'INSTRUMENT_DECLINED') {
							// If payment refused
							return actions.restart();
						} else {
							console.error(jsonResponse.error);
						}
					} else {
						//--------------------------------------------------------- ERROR AT CAPTURING ORDER
						popUp('An internal error has occured while processing your order. Don\'t panic, your payment has been successfull and your cart has been saved. Our team has been notified and we will contact you as soon as possible on your payapal e-mail address to finalise your order. We are sorry for the inconvenience.');
					}
				});
			},

			onCancel: function (data) {
				return fetch(`/api/order/cancel/${data.orderID}`, {
					method: 'post'
				}).then(
					response => {
						return response.json();
					}, fetchErrorHandler
				).then(jsonResponse => {
					if(jsonResponse.delete == data.orderID) {
						window.location.replace(`${window.location.origin}/cart`);
					}
				});
			},

			onError: (error) => {
				console.error(error);
			},

			style: {
				color:  'black',
				//label: 'pay',
				height: 40,
			}
		}).render('#paypal-checkout-button');
	}
}