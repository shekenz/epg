import { arrayByClass, coolDown } from '../../shared/helpers.mjs';
import { popUp } from '../../shared/popup.mjs';
import { updateQuantityFor, updateSubTotalFor } from '../../shared/update-article.mjs';
import { updateCartQuantity, setCartTotal } from '../../shared/update-cart.mjs';

// Global Values
let cartTotal = parseFloat(document.getElementById('cart-total').firstChild.nodeValue);
let shippingPrice = 0;
let shippingMethod = 0;
let couponValue = 0;
let couponPercentage = false;
let couponId = 0;

// Elements
let cart = document.getElementById('cart');
let shippingMethodInputs = (Array.from(document.getElementsByName('shipping-method')));
let articlesQuantityButtons = arrayByClass('qte-button');
let removeAllButtons = arrayByClass('remove-all-button');
let couponInput = document.getElementById('coupon-input');
let couponAlert = document.getElementById('coupon-alert');
let couponInfo = document.getElementById('coupon-info');
let couponLoader = document.getElementById('loader');

// -------------------------------------------------------------------------- Functions

// No connection error handler
let fetchErrorHandler = () => {
	popUp('Impossible to reach server. Please make sure you are connected to the internet.');
	console.error('Impossible to reach server. Please make sure you are connected to the internet.');
}

// ----------------------------------------------------------- Cart utils

// Update cart total
let updateCartTotal = (value = 0) => {
	cartTotal = Math.round((cartTotal + value)*100) / 100;

	// Update couponPrice
	let couponPrice = (couponPercentage) ? 
		(Math.round(couponValue * cartTotal) / -100)
		: (-1 * couponValue);

	// If coupon is higher than total, client pays only shippingPrice
	let cartTotalDisplay = ((cartTotal + couponPrice) < 0 ) ? shippingPrice : (cartTotal + shippingPrice + couponPrice);

	// Update cart total element
	let total = setCartTotal(cartTotalDisplay);

	// console.table({
	// 	'Cart': cartTotal,
	// 	'Shipping': shippingPrice,
	// 	'Coupon ID': couponId,
	// 	'Coupon Value': couponValue,
	// 	'Pourcentage': couponPercentage,
	// 	'Coupon Price': couponPrice,
	// 	'TOTAL': total
	// });
}

// Empty cart verification
let checkEmptyCart = () => {
	if(cartTotal <= 0) {
		document.getElementById('content').removeChild(document.getElementById('cart-wrapper'));
		document.getElementById('empty-cart-info').classList.toggle('hidden');
		//TODO trigger animation menu reset
		//window.location.href = window.location.origin;
	}
}

// ----------------------------------------------------------- Shipping method utils

// Set shipping global values
let setShipping = (input => {
	shippingMethod = input.value;
	shippingPrice = parseFloat(input.dataset.price);
	updateCartTotal();
});

// ----------------------------------------------------------- Coupon utils

// Coupon utils
let toggleCoupon = success => {
	if(success) {
		couponAlert.firstChild.nodeValue = 'This coupon is valid';
		couponAlert.classList.add('text-green-500');
		couponAlert.classList.remove('text-red-500');
		
		couponInfo.classList.remove('hidden');
	} else {
		couponAlert.firstChild.nodeValue = 'This coupon is not valid';
		couponAlert.classList.remove('text-green-500');
		couponAlert.classList.add('text-red-500');
		couponInfo.classList.add('hidden');
	}
};

let resetCoupon = () => {
	couponValue = 0;
	couponPercentage = false;
	couponId = 0;
	updateCartTotal();
};

// -------------------------------------------------------------------------- Events

// Increment/Decrement article buttons
articlesQuantityButtons.forEach(button => {
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

// Shipping methods
shippingMethodInputs.forEach(input => {
	if(input.hasAttribute('checked')) {
		setShipping(input);
	}
	input.addEventListener('focus', e => {
		setShipping(e.target);
	});
});

couponInput.addEventListener('input', coolDown(
	e => {
		e.target.value = e.target.value.toUpperCase();
		couponAlert.classList.add('hidden');
		couponLoader.classList.remove('hidden');
	}, 
	e => {
		if(e.target.value !== '') {
			window.fetch(`/api/coupon/get/${e.target.value}`, {
				method: 'post',
				headers: {
					'accept': 'application/json'
				}
			}).then(r => {
				if(r.ok) {
					return r.json();
				}
			}).then( jr => {
				couponAlert.classList.remove('hidden');
				couponLoader.classList.add('hidden');
				if(jr.id) {
					toggleCoupon(true);
					couponValue = Math.round(parseFloat(jr.value) * 100) / 100;
					couponId = jr.id;
					if(jr.type) {
						couponInfo.innerHTML = 'Coupon : -'+couponValue+'€';
					} else {
						couponPercentage = true;
						couponInfo.innerHTML = 'Coupon -'+couponValue+'%';
					}
					updateCartTotal();
				} else {
					toggleCoupon(false);
					resetCoupon();
				}
			});
		} else {
			couponLoader.classList.add('hidden');
			toggleCoupon(false);
			resetCoupon();
		}
	}, 
	500)
);

// Coupon Update
couponInput.addEventListener('input', e => {
	
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
							return fetch(`/api/order/create/${shippingMethod}/${couponId}`, {
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
						window.location.href = `${window.location.origin}/cart/success`;
					} else if(jsonResponse.error) {
						if(jsonResponse.error.name == 'INSTRUMENT_DECLINED') {
							// If payment refused
							return actions.restart();
						} else {
							console.error(jsonResponse.error);
						}
					} else {
						//--------------------------------------------------------- ERROR AT CAPTURING ORDER
						popUp('An internal error has occured while processing your order. Don\'t panic, your payment has been successfull and your cart has been saved. Our team has been notified and we will contact you as soon as possible on your payapal e-mail address to finalise your order. We are sorry for the inconvenience.', () => { window.location.href = window.location.origin });
					}
				});
			},

			onCancel: function (data) {
				return fetch(`/api/order/cancel/${data.orderID}`, {
					method: 'post',
					headers: {
						'accept': 'application/json'
					}
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