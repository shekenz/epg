<nav id="left-bar" class="side-bar">
	<x-sidebar-item icon="dashboard">{{ ___('dashboard') }}</x-sidebar-item>
	<x-sidebar-item route="books" icon="book">{{ ___('books') }}</x-sidebar-item>
	<x-sidebar-item route="media" icon="photo">{{ ___('media') }}</x-sidebar-item>
	<x-sidebar-item route="orders" icon="receipt">{{ ___('orders') }}@if(!empty(cache('newOrders')))<x-captions.new-orders>{{ cache('newOrders')}}</x-captions.new-orders>@endif</x-sidebar-item>
	<x-sidebar-item route="clients" icon="mood-smile">{{ ___('clients') }}</x-sidebar-item>
	<x-sidebar-item route="users" icon="users">{{ ___('users') }}</x-sidebar-item>
	<x-sidebar-item route="settings" icon="settings">{{ ___('settings') }}</x-sidebar-item>
	<div class="flex-grow"></div>
	<span class="text-gray-400 dark:text-gray-500 self-center m-2 text-sm">Created by <a class="underline" href="https://github.com/shekenz">Shekenz</a></span>
</nav>