import Sortable from 'sortablejs';

const reorderRequest = e => {

	const books = e.target.children;
	let order = {};

	for(let i = 0; i < books.length; i++) {
		order[parseInt(books[i].dataset.id)] = i;
	}

	const form = new FormData()
	form.set('order', JSON.stringify(order));

	window.fetch('/api/books/reorder', {
		method: 'post',
		headers: {
			'accept': 'application/json'
		},
		body: form
	});

}

const options = {
	handle: '.cursor-grab',
	animation: 150,
	onSort: reorderRequest,
}

Sortable.create(document.getElementById('books-sortable'), options);