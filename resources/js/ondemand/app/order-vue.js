import Vue from 'vue';
import debounce from 'lodash.debounce';
import VueI18n from 'vue-i18n';
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
			orders: {},
			elements:
			{
				loader: document.getElementById('save-loader')
			},
			currentOrder: null,
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

			getOrders()
			{
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
					}
				);
			},

			getOrder(id)
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
						this.currentOrder = rJson.data;
						history.pushState({currentOrder: this.currentOrder}, null, window.location.origin+'/dashboard/order/'+id);
					}
				);
			},

			returnToList()
			{
				this.currentOrder = null
				history.pushState({currentOrder: null}, null, window.location.origin+'/dashboard/orders');
			},

			getCurrentOrder(prop) {

				if(this.currentOrder)
				{
					return eval('this.currentOrder.'+prop);
				} 
				else 
				{
					return null
				}
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
		 });
		}
	}
);
