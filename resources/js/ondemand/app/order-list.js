import { arrayByClass, coolDown } from '../../shared/helpers.mjs';
import { isThisHour } from 'date-fns';

let ordersForm = document.getElementById('orders-selection');
let orderRowsContainer = document.getElementById('order-rows');
let selectAllButton = document.getElementById('checkall');
let actions = arrayByClass('action');
let filterInput = document.getElementById('filter');
let filterDataInputText = document.getElementById('filter-data-text');
let filterDataInputStatus = document.getElementById('filter-data-status');
let filterDataInputCoupons = document.getElementById('filter-data-coupons');
let filterDataInputShipping = document.getElementById('filter-data-shipping');
let filterDataInput = filterDataInputText;
let startDate = document.getElementById('start-date');
let endDate = document.getElementById('end-date');
let preorderInput = document.getElementById('preorder');
let loader = document.getElementById('loader');
let recycleBlueprint = document.getElementById('recycle-blueprint');
let trashBlueprint = document.getElementById('trash-blueprint');
let forkliftBlueprint = document.getElementById('forklift-blueprint');
let noResult = document.getElementById('no-result');

let coolDownFire = e => {
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
	//console.log(url);
	fetch(url, {
		method: 'post',
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
						let row = document.createElement('tr');
						if(!order.read) {
							row.classList.add('unread');
						}

						// First cell with checkbox
						let firstCell = document.createElement('td');
						let firstCellInput = document.createElement('input');
						firstCellInput.setAttribute('class', 'checkbox');
						firstCellInput.setAttribute('type', 'checkbox');
						firstCellInput.setAttribute('value', order.id);
						firstCellInput.setAttribute('name', 'ids[]');
						firstCell.append(firstCellInput);
						row.append(firstCell);

						// Cells depending on their index
						let rowCells = [order.order_id, order.full_name, order.email_address, order.pre_order, order.status, order.created_at_formated];
						rowCells.forEach((cellData, index) => {
							let cell = document.createElement('td');
							
							switch(index) {
								case 0: // order_id
									let orderLink = document.createElement('a');
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
										let forkliftIcon = forkliftBlueprint.cloneNode(true);
										forkliftIcon.classList.remove('hidden');
										cell.append(forkliftIcon);
									}
									break;
								case 4:
									let label = document.createElement('span');
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
						let toolsCell = document.createElement('td');
						if(order.status === 'FAILED') {
							let trashLink = document.createElement('a');
							trashLink.setAttribute('href', window.location.origin+'/dashboard/order/cancel/'+order.id);
							trashLink.setAttribute('class', 'icon');
							let trashIcon = trashBlueprint.cloneNode(true);
							trashIcon.classList.remove('hidden');
							trashLink.append(trashIcon);
							toolsCell.append(trashLink);
						} else if(order.status === 'CREATED' && order.order_id && !isThisHour(orderCreationDate)) {
							let recycleLink = document.createElement('a');
							recycleLink.setAttribute('href', window.location.origin+'/dashboard/order/recycle/'+order.order_id);
							recycleLink.setAttribute('class', 'icon');
							let recycleIcon = recycleBlueprint.cloneNode(true);
							recycleIcon.classList.remove('hidden');
							recycleLink.append(recycleIcon);
							toolsCell.append(recycleLink);
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

let enableValueInput = value => {
	switch(value) {
		case('all'):
			filterDataInputText.classList.remove('hidden');
			filterDataInputStatus.classList.add('hidden');
			filterDataInputCoupons.classList.add('hidden');
			filterDataInputShipping.classList.add('hidden');
			filterDataInput = filterDataInputText; 
			filterDataInputText.disabled = true;
			filterDataInputText.setAttribute('disabled', true);
			break;
		case('status'): 
			filterDataInputText.classList.add('hidden');
			filterDataInputStatus.classList.remove('hidden');
			filterDataInputCoupons.classList.add('hidden');
			filterDataInputShipping.classList.add('hidden');
			filterDataInput = filterDataInputStatus;
			break;
		case('coupon'): 
			filterDataInputText.classList.add('hidden');
			filterDataInputStatus.classList.add('hidden');
			filterDataInputCoupons.classList.remove('hidden');
			filterDataInputShipping.classList.add('hidden');
			filterDataInput = filterDataInputCoupons;
			break;
		case('shipping'): 
			filterDataInputText.classList.add('hidden');
			filterDataInputStatus.classList.add('hidden');
			filterDataInputCoupons.classList.add('hidden');
			filterDataInputShipping.classList.remove('hidden');
			filterDataInput = filterDataInputShipping;
			break;
		default:
			filterDataInputText.classList.remove('hidden');
			filterDataInputStatus.classList.add('hidden');
			filterDataInputCoupons.classList.add('hidden');
			filterDataInputShipping.classList.add('hidden');
			filterDataInput = filterDataInputText; 
			if(value !== 'all') {
				if(filterDataInput.hasAttribute('disabled')) {
					filterDataInput.removeAttribute('disabled');
				}
				filterDataInput.focus();
			}
			break;
	}
}

window.addEventListener('pageshow', e => {
	enableValueInput(filterInput.value);
});

selectAllButton.addEventListener('click', e => {
	let checkboxes = arrayByClass('checkbox');
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

filterInput.addEventListener('input', e => {
	enableValueInput(e.target.value);
	coolDownFire(e);
});


filterDataInputText.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, coolDownFire, 500));
startDate.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, coolDownFire, 500));
endDate.addEventListener('input', coolDown(() => {
	if(loader.classList.contains('hidden')) {
		loader.classList.remove('hidden');
	}
}, coolDownFire, 500));
filterDataInputStatus.addEventListener('input', coolDownFire);
filterDataInputCoupons.addEventListener('input', coolDownFire);
filterDataInputShipping.addEventListener('input', coolDownFire);
preorderInput.addEventListener('input', coolDownFire);
