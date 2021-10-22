import { arrayByClass, coolDown } from '../../shared/helpers.mjs';
import { popUpPlus } from '../../shared/popup.mjs';
import { isThisHour } from 'date-fns';

const ordersForm = document.getElementById('orders-selection');
const orderRowsContainer = document.getElementById('order-rows');
const selectAllButton = document.getElementById('checkall');
const actions = arrayByClass('action');

const filterInput = document.getElementById('filter');
const filterDataInputIDs = ['filter-data-text', 'filter-data-book', 'filter-data-status', 'filter-data-coupon', 'filter-data-shipping']
const filterDataInputs = function() {
	let elements = new Object();
	filterDataInputIDs.forEach(id => {
		const propName = id.match(/[a-z]+$/g)[0];
		elements[propName] = document.getElementById(id);	
	});
	return elements;
}();

let filterDataInput = filterDataInputs.text;

const startDate = document.getElementById('start-date');
const endDate = document.getElementById('end-date');
const preorderInput = document.getElementById('preorder');
const loader = document.getElementById('loader');

// TODO a function that sets all of that automatically
const recycleBlueprint = document.getElementById('recycle-blueprint');
const trashBlueprint = document.getElementById('trash-blueprint');
const forkliftBlueprint = document.getElementById('forklift-blueprint');
const shippedBlueprint = document.getElementById('shipped-blueprint');
const archiveBlueprint = document.getElementById('archive-blueprint');
const noResult = document.getElementById('no-result');

// TODO Make it post data from a form and use it in archiving tool, and check route for method.
const createToolButton = (href, blueprint) => {
	const link = document.createElement('a');
	link.setAttribute('href', href);
	link.setAttribute('class', 'icon');
	const icon = blueprint.cloneNode(true);
	icon.classList.remove('hidden');
	link.append(icon);
	return link;
}

const request = () => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
	let method = filterInput.value;
	let data = filterDataInput.value;
	let from = startDate.value;
	let to = endDate.value;
	let hidden = (window.location.pathname.match(/\/hidden$/)) ? true : false;
	let preorder = preorderInput.checked;
	let url = `/api/orders/get/${method}/${from}/${to}/${hidden}/${preorder}/${data}`;
	fetch(url, {
		method: 'get',
		headers: {
			'accept': 'application/json',
		}
	})
	.then(r => {
		if(r.ok === true) {
			return r.json();
		} else {
			throw new Error('Cannot query server');
		}
	})
	.catch(error => {
		console.error(error);
	})
	.then(rJson => {
		if(rJson) {
			orderRowsContainer.innerHTML = '';
			if(rJson.length > 0) {
				noResult.classList.add('hidden');
				rJson.forEach((order) => {
					if(order.books.length > 0) {
						let orderCreationDate = new Date(order.created_at);

						// Parent row
						const row = document.createElement('tr');
						row.setAttribute('id', 'order-'+order.id);
						if(!order.read) {
							row.classList.add('unread');
						}

						// First cell with checkbox
						const firstCell = document.createElement('td');
						const firstCellInput = document.createElement('input');
						firstCellInput.setAttribute('class', 'checkbox');
						firstCellInput.setAttribute('type', 'checkbox');
						firstCellInput.setAttribute('value', order.id);
						firstCellInput.setAttribute('name', 'ids[]');
						firstCell.append(firstCellInput);
						row.append(firstCell);

						// Cells depending on their index
						const rowCells = [order.order_id, order.full_name, order.contact_email, order.pre_order, order.status, order.created_at_formated];
						rowCells.forEach((cellData, index) => {
							const cell = document.createElement('td');
							
							switch(index) {
								case 0: // order_id
									const orderLink = document.createElement('a');
									orderLink.setAttribute('class', 'default');
									orderLink.setAttribute('href', window.location.origin+'/dashboard/order/'+order.id);
									if(!cellData) {
										cellData = '[ ID commande manquante ]';
									}
									orderLink.append(cellData);
									cell.append(orderLink)
									break;
								case 3:
									cell.setAttribute('class', 'text-center');
									if(cellData === 1) {
										const forkliftIcon = forkliftBlueprint.cloneNode(true);
										forkliftIcon.classList.remove('hidden');
										cell.append(forkliftIcon);
									}
									break;
								case 4:
									const label = document.createElement('span');
									label.setAttribute('class', 'font-bold text-center inline-block w-full text-white px-2 py-1 rounded');
									let bgClass = '';
									switch(order.status) {
										case 'FAILED': bgClass = 'bg-red-500'; cellData = 'Échec'.toUpperCase(); break;
										case 'CREATED': bgClass = 'bg-yellow-500'; cellData = 'Créé'.toUpperCase(); break;
										case 'COMPLETED': bgClass = 'bg-blue-500'; cellData = 'Payé'.toUpperCase(); break;
										case 'SHIPPED': bgClass = 'bg-green-500'; cellData = 'Envoyé'.toUpperCase(); break;
									}
									label.classList.add(bgClass);
									label.append(cellData);
									cell.append(label);
									break;
								// case (5):
								// //case (6):
								// 	cell.append(formatDate(cellData));
								// 	break;
								default:
									cell.append(cellData);
									break;
							}
							row.append(cell);
						});

						// Last cell
						const toolsCell = document.createElement('td');
						switch(order.status) {
							case('FAILED') :
								toolsCell.append(createToolButton(window.location.origin+'/dashboard/order/cancel/'+order.id, trashBlueprint));
								break;
							case('CREATED') : 
								if(order.order_id && !isThisHour(orderCreationDate)) {
									toolsCell.append(createToolButton(window.location.origin+'/dashboard/order/recycle/'+order.order_id, recycleBlueprint));
								}
								break;
							case('COMPLETED') :
								const shippedLink = createToolButton('#', shippedBlueprint);
								shippedLink.classList.add('shipped');
								shippedLink.addEventListener('click', e => {
									e.preventDefault();
									popUpPlus((wrapper, button) => {

										button.firstChild.nodeValue = 'Ship';
															
										const title = document.createElement('h2');
										title.appendChild(document.createTextNode('Confirm shipping'));
								
										const shipTrackingForm = document.createElement('form');
										shipTrackingForm.setAttribute('method', 'POST');
										shipTrackingForm.setAttribute('action', window.location.origin+'/dashboard/order/shipped/'+order.order_id);
										shipTrackingForm.appendChild(document.getElementsByName('_token')[0].cloneNode());
										
										// Tracking URL
										const shipTrackingURL = document.createElement('input');
										shipTrackingURL.setAttribute('type', 'text');
										shipTrackingURL.setAttribute('name', 'tracking_url');
										shipTrackingURL.classList.add('input-shared');
										const shipTrackingURLLabel = document.createElement('label');
										shipTrackingURLLabel.classList.add('label-shared');
										shipTrackingURLLabel.classList.add('block');
										shipTrackingURLLabel.classList.add('text-lg');
										shipTrackingURLLabel.classList.add('mt-4');
										shipTrackingURLLabel.appendChild(document.createTextNode('Tracking URL :'))
								
										// Appending
										shipTrackingForm.appendChild(shipTrackingURLLabel);
										shipTrackingForm.appendChild(shipTrackingURL);
										
										wrapper.appendChild(title);
										wrapper.appendChild(shipTrackingForm);
										return shipTrackingForm;
									},
									returned => {
										document.getElementById('popup-loader').classList.toggle('hidden');
										
										//TODO prevent enter from submiting form 
										fetch(returned.action, {
											method: 'post',
											headers: {
												accept: 'application/json',
											},
											body: new FormData(returned)
										}).then(r => {
											if( r.status === 200 ) {
												return r.json();
											}
										}).then(rj => {
											document.getElementById('popup-loader').classList.toggle('hidden');
											document.getElementById('pop-up-wrapper').classList.add('hidden');
											document.getElementById('pop-inner-wrapper').innerHTML = '';
											const statusLabel = document.querySelector(`#order-${rj.id} td:nth-child(6) span`);
											statusLabel.innerHTML = 'Envoyé'.toUpperCase();
											statusLabel.classList.remove('bg-blue-500');
											statusLabel.classList.add('bg-green-500');
											document.querySelector(`#order-${rj.id} td:nth-child(8)`).removeChild(document.querySelector(`#order-${rj.id} td:nth-child(8) a.shipped`));
										});
									});
								});
								toolsCell.append(shippedLink);
								break;
							case('SHIPPED') :
								const archiveButtonForm = document.createElement('form');
								archiveButtonForm.setAttribute('action', window.location.origin+'/dashboard/order/archive/'+order.id);
								archiveButtonForm.setAttribute('method', 'POST');
								const tokenInput = document.createElement('input');
								tokenInput.setAttribute('type', 'hidden');
								tokenInput.setAttribute('name', '_token');
								const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');
								tokenInput.setAttribute('value', csrfToken);
								const submitButton = document.createElement('button');
								submitButton.classList.add('icon');

								submitButton.addEventListener('click', e => {
									e.preventDefault();
									if(confirm('Are you sure you want to archive this command ? Archived commands cannot be restored.')) {
										e.currentTarget.parentNode.submit();
									}
								});

								const archiveIcon = archiveBlueprint.cloneNode(true);
								archiveIcon.classList.remove('hidden');
								submitButton.append(archiveIcon);
								archiveButtonForm.append(tokenInput);
								archiveButtonForm.append(submitButton);
								toolsCell.append(archiveButtonForm);
								break;
						}
						toolsCell.setAttribute('class', 'text-right');
						row.append(toolsCell);

						// Append row
						orderRowsContainer.append(row);
					}
				});
			} else {
				noResult.classList.remove('hidden');
			}
		} else {
			throw new Error('Bad JSON response')
		}
	})
	.catch(error => {
		console.error(error);
	})
	.finally( () => {
		loader.classList.add('hidden');
	});
};

/**
 * Displays the corresponding filterDataInput depending on the selected filter
 * @param {string} inputName - The filter name
 * @param {function} callback - A callback function to be called after input display
 */
const switchInput = (inputName, callback = function() {}) => {
	// Add hidden class to all filterDataInputs
	Object.keys(filterDataInputs).forEach(input => {
		if(!filterDataInputs[input].classList.contains('hidden')) {
			filterDataInputs[input].classList.add('hidden');
		}
	});
	// Switch visibility on if inputName exists in filterDataInputs (and run callback)
	if(filterDataInputs[inputName]) {
		filterDataInputs[inputName].classList.remove('hidden');
		filterDataInput = filterDataInputs[inputName];
		callback(filterDataInputs[inputName]);
	} else {
		throw new Error(`${inputName} not found in data inputs object`);
	}
}

/**
 * Check input value to determine which filter is selected
 * @param {string} value - The input value or the filter name in our case
 */
const enableValueInput = value => {

	switch(value) {
		case('all'):
			switchInput('text', input => {
				input.disabled = true;
				input.setAttribute('disabled', true);
			});
			break;
		case('book'): 
			switchInput(value);
			break;
		case('status'): 
			switchInput(value);
			break;
		case('coupon'): 
			switchInput(value);
			break;
		case('shipping'): 
			switchInput(value);
			break;
		default:
			switchInput('text');
			if(filterDataInput.hasAttribute('disabled')) {
				filterDataInput.removeAttribute('disabled');
			}
			filterDataInput.focus();
			break;
	}
}

// Initialization
window.addEventListener('pageshow', () => {
	enableValueInput(filterInput.value);
	request();
});

// Form events
selectAllButton.addEventListener('click', e => {
	const checkboxes = arrayByClass('checkbox');
	checkboxes.forEach(checkbox => {
		checkbox.checked = e.target.checked;
	});
});

actions.forEach(action => {
	action.addEventListener('click', e => {
		if(e.target.id === 'pdf') {
			ordersForm.target = '_blank';
		} else if(ordersForm.hasAttribute('target')) {
			ordersForm.removeAttribute('target')
		}
		ordersForm.action = e.target.dataset.action;
		ordersForm.submit();
	});
});

// Filter events
filterInput.addEventListener('input', e => {
	enableValueInput(e.target.value);
	request();
});

filterDataInputs.text.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, request, 500));

startDate.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, request, 500));

endDate.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, request, 500));

filterDataInputs.book.addEventListener('input', request);
filterDataInputs.status.addEventListener('input', request);
filterDataInputs.coupon.addEventListener('input', request);
filterDataInputs.shipping.addEventListener('input', request);
preorderInput.addEventListener('input', request);
