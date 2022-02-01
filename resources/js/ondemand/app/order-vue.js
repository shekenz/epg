import Vue from 'vue';
import debounce from 'lodash.debounce';
import VueI18n from 'vue-i18n';
import { isThursday } from 'date-fns';
Vue.use(VueI18n);

const locale = document.documentElement.lang.slice(0,2) || 'en';

// i18n data
const messages = {
  en: {
    methods:
		{
      all: '',
			order: 'Order',
			name: 'Name',
			email: 'E-mail address',
			status: 'Status',
			book: 'Book',
			coupon: 'Coupon',
			shipping: 'Shipping method',
    },
		status: {
			'FAILED': 'Failed',
			'CREATED': 'Pending',
			'COMPLETED': 'Paid',
			'SHIPPED': 'Shipped',
		},
  },
  fr: {
    methods:
		{
      all: '',
			order: 'Commande',
			name: 'Nom',
			email: 'Adresse e-mail',
			status: 'Statut',
			book: 'Livre',
			coupon: 'Coupon',
			shipping: 'Méthode d\'envoi',
    },
		status: {
			'FAILED': 'Echec',
			'CREATED': 'En cours',
			'COMPLETED': 'Payé',
			'SHIPPED': 'Envoyé',
		},
  }
}

// VueI18n instance
const i18n = new VueI18n({
  locale: locale,
  messages,
})

window.Events = new class {

	constructor() {
		this.vue = new Vue();
	}

	fire(event, data = null) {
		this.vue.$emit(event, data);
	}

	listen(event, callback) {
		this.vue.$on(event, callback);
	}

};

window.vue = new Vue(
	{
		i18n,
		el: '#orders',
		data: {
			csrfToken: document.getElementsByName('csrf-token')[0].getAttribute('content'),
			filters:
			{
				method: null,
				from: null,
				to: null,
				read: null,
				preorder: null,
				data: null
			},
			methods: [
				'all',
				'order',
				'name',
				'email',
				'status',
				'book',
				'coupon',
				'shipping',				
			],
			methodsTextDataType: ['all', 'order', 'name', 'email'],
			filtered: 0,
			orders: {},
			elements:
			{
				loader: document.getElementById('save-loader'),
			},
			currentOrder: null,
			selectAll: false,
			popup: false,
		},
		computed:
		{
			
		},
		methods:
		{
			log(msg)
			{
				console.log(msg);
			},
			
			route(id) {
				return window.location.protocol + '//' + window.location.host + '/dashboard/order/' + id;
			},

			debounceInput: debounce( e =>
				{
					vue.getOrders();
				}
			, 300),

			isRecyclable(order)
			{
				const elapsed = new Date() - new Date(order.created_at);
				// If order is older than 15 min, and is CREATED or FAILED
				return (elapsed >= 15*60*1000 && (order.status === 'CREATED' || order.status === 'FAILED'));
			},

			getOrders()
			{
				this.selectAll = false;
				this.checkAll(this.selectAll);
				fetch('/api/orders/filter',
					{
						method: 'POST',
						headers: {
							'Accept': 'application/json',
							'Content-Type': 'application/json'
						},
						body: JSON.stringify( this.filters )
					}
				).then(r =>
					{
						if(r.ok)
						{
							return r.json()
						}
					}
				).then(rJson =>
					{
						this.orders = rJson.data;
						this.filtered = this.orders.length;
					}
				);
			},

			getOrder(id, key)
			{
				fetch('/api/orders/'+id, 
					{
						method: 'GET',
						headers: {
							'Accept': 'application/json',
						}
					}
				).then(r =>
					{
						if(r.ok)
						{
							return r.json()
						}
					}
				).then(rJson =>
					{
						if(!this.orders[key].read) this.orders[key].read = true;
						this.currentOrder = rJson.data;

						// Calculating total price of all books
						this.currentOrder.order.total = this.currentOrder.order.books.reduce((total, book) => { 
							return Math.round((total + book.total_price) * 100) / 100;
						}, 0 );

						// Calculating coupon price depending on type
						const coupon = this.currentOrder.order.coupon;
						if(coupon)
						{
							if(coupon.fixed)
							{ // If coupon is a fixed value
								this.currentOrder.order.coupon_price = coupon.value * -1;
							} 
							else 
							{ // If coupon value is a percentage of total order
								this.currentOrder.order.coupon_price = Math.round(this.currentOrder.order.total * coupon.value) / -100;
							}
						} else {
							this.currentOrder.order.coupon_price = 0;
						}

						// Saving currentOrder as a state
						history.pushState({currentOrder: this.currentOrder}, null, window.location.origin+'/dashboard/order/'+id);
					}
				);
			},

			recycleOrder(order_id, index)
			{
				fetch('/dashboard/order/recycle/'+order_id, 
					{
						method: 'GET',
						headers: {
							'Accept': 'application/json',
						}
					}
				).then(r =>
					{
						if(r.ok)
						{
							return r.json()
						}
					}
				).then(rJson =>
					{
						this.$delete(this.orders, index);
					}
				);
			},

			returnToList()
			{
				this.currentOrder = null;
				history.pushState({currentOrder: null}, null, window.location.origin+'/dashboard/orders');
			},

			submit(url)
			{
				const selection = document.getElementById('selected-orders');
				selection.action = url
				let data = new FormData(document.getElementById('selected-orders'));
				selection.submit();
			},

			checkAll(check = true)
			{
				const elements = document.getElementsByName('ids[]');
				for(let el of elements)
				{
					el.checked = check;
				}
			},

			submitForm(formID, loaderID = '')
			{
				let loader = null;
				if(loaderID) {
					loader = document.getElementById(loaderID);
					loader.classList.remove('hidden');
				}
				const form = document.getElementById(formID);
				const url = form.action;
				const formData = new FormData(form);
				for(let entry of formData.entries()) {
					console.log(entry);
				}
				fetch(url, 
					{
						method: 'POST',
						headers: {
							//'X-CSRF-TOKEN': this.csrfToken,
							'Accept': 'application/json',
						},
						body: formData,
					}
				).then(r => {
					if(loader) loader.classList.add('hidden');
					this.popup = false;
					if(r.ok) this.currentOrder.order.status = 'SHIPPED';
				});
			}

		},
		mounted()
		{
			this.getOrders();
			history.pushState({currentOrder: null}, null);
		},
		created()
		{
			window.addEventListener('popstate', e => {
				window.vue.currentOrder = e.state.currentOrder;
				this.popup = false;
		 });
		}
	}
);
