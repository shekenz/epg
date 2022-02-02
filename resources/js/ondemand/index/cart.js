import { roundPrice, arrayByClass, coolDown } from '../../shared/helpers.mjs';
import { popUp, popFlash } from '../../shared/popup.mjs';
import { updateQuantityFor, updateSubTotalFor } from '../../shared/update-article.mjs';
import { updateCartQuantity, setCartTotal } from '../../shared/update-cart.mjs';

// Sentry
import * as Sentry from "@sentry/browser";
import { Integrations } from "@sentry/tracing";
import Sortable from 'sortablejs';

Sentry.init({
  dsn: "https://07f45debc8da4555b7bde057f0f2f391@o1036209.ingest.sentry.io/6003767",
  integrations: [new Integrations.BrowserTracing()],

  // Set tracesSampleRate to 1.0 to capture 100%
  // of transactions for performance monitoring.
  // We recommend adjusting this value in production
  tracesSampleRate: 1.0,
});

const debug = false;

// If cart is not empty
if(document.getElementById('cart-wrapper')) {

	// Global Values
	let cartTotal = parseFloat(document.getElementById('cart-total').dataset.rawTotal);
	let cartSubTotal = parseFloat(document.getElementById('cart-sub-total').firstChild.nodeValue);
	let shippingPrice = 0;
	let shippingMethod = 0;
	let couponValue = 0;
	let couponId = 0;
	let couponIsFixed = true;
	let couponPrice = 0;
	let weightTotal = parseInt(document.getElementById('cart-total-weight').dataset.totalWeight);
	let total = 0;

	// Elements
	const cart = document.getElementById('cart');

	const articlesQuantityButtons = arrayByClass('qte-button');
	const removeAllButtons = arrayByClass('remove-all-button');

	const shippingAddressInputs = arrayByClass('shipping-address-input');
	const countryInput = document.getElementById('country-input');

	const shippingMethodInputs = (Array.from(document.getElementsByName('shipping-method')));
	const shippingMethodInputsNational = document.getElementsByClassName('national');
	const shippingMethodInputsInternational = document.getElementsByClassName('international');

	const shippingInfo = document.getElementById('shipping-info');

	const couponInput = document.getElementById('coupon-input');
	const couponAlert = document.getElementById('coupon-alert');
	const couponInfo = document.getElementById('coupon-info');
	const couponLoader = document.getElementById('loader');


	// -------------------------------------------------------------------------- Functions

	// Dump all price values
	const dumpData = () => {
		console.table({
			'Cart': cartTotal,
			'Coupon ID': couponId,
			'Coupon Value': couponValue,
			'Coupon Price': couponPrice,
			'Shipping': shippingPrice+' ('+shippingMethod+')',
			'Total Weight': weightTotal,
			'TOTAL': total,
		});
	}

	// No connection error handler
	const fetchErrorHandler = () => {
		popUp('Impossible to reach server. Please make sure you are connected to the internet.');
		console.error('Impossible to reach server. Please make sure you are connected to the internet.');
	}

	// Form error handler
	const inputError = (inputName, message = '') => {
		const input = document.getElementsByName(inputName)[0];
		if(input === undefined) {
			throw new Error(`C\'ant find element with name "${inputName}"`);
		} else {
			input.classList.add('error');
			if(input.nextElementSibling.tagName.toUpperCase() === 'SPAN' && message !== '') {
				input.nextElementSibling.innerHTML = message;
			}
		}
	}

	const inputErrorReset = input => {
		if(input.classList.contains('error')) {
			input.classList.remove('error');
			if(input.nextElementSibling.tagName.toUpperCase() === 'SPAN') {
				input.nextElementSibling.innerHTML = '';
			}
		}
	}

	// ----------------------------------------------------------- Cart logic

	// TODO Important !! Check for max weight on live update !!

	// Update cart (global) subtotal
	const updateCartSubTotal = (value = 0) => {
		cartSubTotal = roundPrice(cartSubTotal + value)
		document.getElementById('cart-sub-total').firstChild.nodeValue = cartSubTotal;
	}

	// Update weight total
	const updateWeightTotal = (value = 0) => {
		weightTotal += value;
	}

	// Update cart total
	const updateCartTotal = (value = 0) => {
		cartTotal = roundPrice(cartTotal + value);

		// If coupon is higher than total, client pays only shippingPrice
		const cartTotalDisplay = ((cartTotal + couponPrice) < 0 ) ? shippingPrice : (cartTotal + shippingPrice + couponPrice);

		// Update cart total element
		total = setCartTotal(cartTotalDisplay);

		if(debug) {
			dumpData();
		}
	}

	// Empty cart verification
	const checkEmptyCart = () => {
		if(cartTotal <= 0) {
			document.getElementById('content').removeChild(document.getElementById('cart-wrapper'));
			document.getElementById('empty-cart-info').classList.toggle('hidden');
			//TODO trigger animation menu reset
			//window.location.href = window.location.origin;
		}
	}

	// ----------------------------------------------------------- Shipping method logic

	// Look up in price range for the shipping price corresponding to the order's total weight
	const findStopPrice = (price, pricesData) => {
		price = parseFloat(price);
		for(const priceStop of JSON.parse(pricesData)) {
			if( weightTotal >= priceStop.weight ) {
				price = priceStop.price;
			}
		};
		return price;
	}

	// Higlights price of the selected shipping method input
	const updateShippingFormInputs = () => {
		if(debug) { console.log('updateShippingFormInputs'); }
		shippingMethodInputs.forEach(input => {
			if(input.checked) {
				input.parentNode.nextElementSibling.classList.add('highlight');
				if(input.parentNode.parentNode.lastElementChild.firstChild !== null) {
					shippingInfo.innerHTML = input.parentNode.parentNode.lastElementChild.firstChild.nodeValue;
				} else {
					shippingInfo.innerHTML = '';
				}
			} else {
				input.parentNode.nextElementSibling.classList.remove('highlight');
			}
		});
	}

	// Updating prices in each input of the shipping methods form
	const updateShippingMethodsPrice = () => {
		if(debug) { console.log('updateShippingMethodsPrice'); }
		shippingMethodInputs.forEach(input => {
			const newShippingPrice = findStopPrice(input.dataset.defaultPrice, input.dataset.prices);
			input.parentNode.nextElementSibling.innerHTML = `${newShippingPrice}&nbsp;€`;
		});
	}

	// Check for country and display shipping method accordingly (national or international)
	const updateShippingForm = input => {
		if(debug) { console.log('updateShippingForm'); }

		if(input.value === 'FR') {

			let isAlreadyChecked = false;
			for(let natWrapper of shippingMethodInputsNational) {
				natWrapper.classList.remove('hidden');
				let shippingMethodInput = natWrapper.firstElementChild.firstElementChild;
				if(shippingMethodInput.dataset.defaultPrice !== "0" && !isAlreadyChecked) {
					shippingMethodInput.checked = isAlreadyChecked = true;
				}
			}

			for(let intWrapper of shippingMethodInputsInternational) {
				intWrapper.classList.add('hidden');
			}
		
		} else {

			for(let natWrapper of shippingMethodInputsNational) {
				natWrapper.classList.add('hidden');
			}

			for(let intWrapper of shippingMethodInputsInternational) {
				intWrapper.classList.remove('hidden');
			}

			// In case it's international shipping, we don't have to check for free hand delivery
			// we just select the first input
			shippingMethodInputsInternational[0].firstElementChild.firstElementChild.checked = true;
		}

		updateShippingPrice();
		updateShippingFormInputs();

	}

	// Set shipping global values
	const updateShippingPrice = () => {
		if(debug) { console.log('updateShippingPrice'); }
		shippingMethodInputs.forEach(input => {
			if(input.checked) {
				shippingMethod = input.value;
				shippingPrice = findStopPrice(input.dataset.defaultPrice, input.dataset.prices);
			}
		});
	}

	// Common updates to be run after cart is updated
	const runCommonUpdates = book => {
		// Update cart quantity
		updateCartQuantity(book.modifier);
		// Update global sub-total
		updateCartSubTotal(book.price * book.modifier);
		// Update total weight
		updateWeightTotal(book.weight * book.modifier);
		// Update coupon price
		updateCoupon();
		// Update shipping prices inputs in shipping form
		updateShippingMethodsPrice();
		// Update global shipping price
		updateShippingPrice();
		// Update cart total with coupon
		updateCartTotal(book.price * book.modifier);
		// Check if cart is empty
		checkEmptyCart();
	}

	// ----------------------------------------------------------- Coupons logic
	const updateCoupon = () => {
		couponPrice = couponIsFixed ? 
			(-1 * couponValue)
			: (Math.round(couponValue * cartSubTotal) / -100);
		couponInfo.innerHTML = `<span>Coupon (-${couponValue}${couponIsFixed ? '&nbsp;€' : '%'})</span><span>${couponPrice}&nbsp;€</span>`;
	}

	const resetCoupon = () => {
		couponValue = 0;
		couponId = 0;
		couponPrice = 0;
		updateCartTotal();
	};

	// -------------------------------------------------------------------------- Init
	updateShippingForm(countryInput);
	updateCartTotal();

	// -------------------------------------------------------------------------- Events

	// Increment/Decrement article buttons
	articlesQuantityButtons.forEach(button => {
		button.addEventListener('click', e => {
			e.preventDefault();
			// Returns any numbers at the end of string
			const id = /[0-9]+$/.exec(e.currentTarget.href)[0];
			fetch(e.currentTarget.href, {
				method: 'post',
				headers: {
					accept: 'application/json'
				}
			})
			.then(response => {
				if(response.status === 200) {
					return response.json();
				} else {
					popFlash('<img class="h-28 inline-block" src="/img/frog_logo_warning.svg" alt="Frog that warns you"><span class="mx-4">'+response.statusText+'</span>')();
				}
			})
			.catch(() => {
				fetchErrorHandler;
			})
			.then(jsonResponse => {
				const book = jsonResponse.book;
				// Update article sub-total first to prevent error when article quantity reach 0
				// Book modifier is 1 when adding book and -1 when removing book
				updateSubTotalFor(book.id, book.price * book.modifier);
				// Update article quantity
				// updateQuantityFor returns false when article quantity reaches 0
				// and also updates order summarize list
				if(!updateQuantityFor(book.id, book.modifier)) {
					document.getElementById('cart').removeChild(document.getElementById(`article-${book.id}`));
					document.getElementById('summarize-list').removeChild(document.getElementById(`summarize-book-${book.id}`))
				};
				runCommonUpdates(book);
			})
			.catch(() => {});
		})
	});

	// Remove all units of an article
	removeAllButtons.forEach(button => {
		button.addEventListener('click', e => {
			e.preventDefault();
			// Returns any numbers at the end of string
			const bookID = /[0-9]+$/.exec(e.currentTarget.href)[0];
			fetch(e.currentTarget.href, {
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
				if(jsonResponse.book.id.toString() === bookID) {
					// Note : In this situation, jsonResponse.book.modifier is the negative quantity of deleted books (Ex: -3 for 3 deleted books)
					// Removing article from cart and from summarize list
					cart.removeChild(document.getElementById('article-'+jsonResponse.book.id));
					document.getElementById('summarize-list').removeChild(document.getElementById('summarize-book-'+jsonResponse.book.id));
					runCommonUpdates(jsonResponse.book);
				} else {
					console.error('BookID mismatch');
				}
			})
			.catch(() => {});
		});
	});

	// Check shipping address input for errors
	shippingAddressInputs.forEach(input => {
		input.addEventListener('focus', e => {
			inputErrorReset(e.currentTarget);
		});
	});

	// Check country and display shipping methods accordingly
	countryInput.addEventListener('input', e => {
		updateShippingForm(e.currentTarget);
		updateCartTotal();
	});

	// Select a shipping method
	shippingMethodInputs.forEach(input => {
		input.addEventListener('input', e => {
			updateShippingPrice();
			updateShippingFormInputs();
			updateCartTotal();
		});
	});

	// Coupon Update
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
					couponLoader.classList.add('hidden');		
					if(jr.id) { // if coupon is valid
						couponAlert.classList.add('hidden');
						couponInfo.classList.remove('hidden');
						couponValue = parseFloat(jr.value);
						couponId = jr.id;
						couponIsFixed = (jr.type);
						updateCoupon();
						updateCartTotal();
					} else { // if coupon is invalid
						couponAlert.classList.remove('hidden');
						couponInfo.classList.add('hidden');
						resetCoupon();
					}
				});
			} else {
				couponLoader.classList.add('hidden');
				couponAlert.classList.add('hidden');
				couponInfo.classList.add('hidden');
				resetCoupon();
			}
		},
		500)
	);

	// Paypal
	const checkCartButton = document.getElementById('paypal-checkout-button');
	if(checkCartButton) {
		if('paypal' in window) {
			paypal.Buttons({
				onClick: (data, actions) => {
					let anyErrors = false;
					shippingAddressInputs.forEach(input => {
						if(input.hasAttribute('name') && input.value === '' && input.hasAttribute('required')) {
							inputError(input.name);
							anyErrors = true
						}
					});
					if(anyErrors) {
						return actions.reject();
					}
				},

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
							let needUpdating = false;
							for(const bookInCart of Object.values(cartCheckResponseJSON)) {
								//console.log(bookInCart);
								if(!(document.getElementsByClassName(`quantity-for-id-${bookInCart.id}`)[0].firstChild.nodeValue === bookInCart.cartQuantity.toString())) {
									needUpdating = true;
								}
							}
							if(needUpdating) {
								popUp('Some articles from you cart are not available anymore. Your cart will now be reloaded. Please check your order again before payment.', () => { window.location.reload() });
							} else {
								const shippingAddress = new FormData(document.getElementById('shipping-address-form'));
								// TODO Refractor chaining and catching errors when we'll have a better understanding of promises
								// Chain needs to be broken and stop Paypal error ID expected
								return fetch(`/api/order/create/${shippingMethod}/${couponId}`, {
									method: 'post',
									headers: {
										'accept': 'application/json'
									},
									body: shippingAddress,
								}).then( // Create fetch response
									createResponse => {
										return createResponse.json();
									}, fetchErrorHandler
								).then( // Create fetch response JSON
									createResponseJSON => {
										if(createResponseJSON.id && !createResponseJSON.error) {
											return createResponseJSON.id;
										} else if(createResponseJSON.errors && createResponseJSON.message === 'The given data was invalid.') {
											// Specific Laravel generated error
											console.error('Laravel Error');
											for (const [inputName, errors] of Object.entries(createResponseJSON.errors)) {
												const message = Object.values(errors).join('<br>');
												inputError(inputName, message);
											}
										} else {
											//--------------------------------------------------------- ERROR AT CREATING ORDER
											fetch('/api/report/', {
												method: 'POST',
												headers: {
													'Content-Type': 'application/json'
												},
												body: JSON.stringify(createResponseJSON)
											});
											console.error(createResponseJSON);
											popUp('An internal error has occured while creating your order. Our team has been warned and we will work on it as soon as possible. Please try to purchase your goods later. We are sorry for the inconvenience.');
										}
									}
								).catch(
									error => { console.warn(error); }
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
							if(jsonResponse.error.name === 'INSTRUMENT_DECLINED') {
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
						if(jsonResponse.delete === data.orderID) {
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

}