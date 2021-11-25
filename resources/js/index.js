// Index
require('./index/menu');
// require('./index/theme');
require('./shared/flash');
//require('./shared/new-orders');

//let orderUnread = document.getElementById('orderUnread');
let orderUnread = false // Desactivated the commands notification because of performance issues.

if(orderUnread) {
	window.fetch('/api/orders/unread/count', {
		method: 'post',
		headers: {
			'accept': 'application/json',
		},
	}).then(response => {
		return response.json();
	}).then(jsonResponse => {
		if(jsonResponse.count && jsonResponse.count > 0) {
			orderUnread.classList.remove('hidden');
			orderUnread.appendChild(document.createTextNode(jsonResponse.count));
		}
	});
}