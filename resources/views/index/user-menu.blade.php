<div id="user-menu" class="fixed flex w-full top-0">
	<a id="hide-button" class="hideable" title="Hide this menu" href="#"><x-tabler-eye-off class="pb-1 inline w-5 h-5" /></a>
	<a id="unhide-button" class="hideable hidden" title="Un-hide this menu" href="#"><x-tabler-eye class="pb-1 inline w-5 h-5" /></a>
	<div id="user-menu-left" class="hideable">
		<a href="{{ url('/dashboard') }}"><x-tabler-gauge class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('dashboard') }}</a>
		<a href="{{ url('/dashboard/books') }}"><x-tabler-book class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('books') }}</a>
		<a href="{{ url('/dashboard/media') }}"><x-tabler-photo class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('media') }}</a>
		<a href="{{ url('/dashboard/orders') }}"><x-tabler-receipt class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('orders') }}<span id="orderUnread" class="notification @if(empty(cache('newOrders'))) hidden @endif">{{ cache('newOrders')}}</span></a>
		<a href="{{ url('/dashboard/settings') }}"><x-tabler-settings class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('settings') }}</a>
	</div>
	<div class="flex-grow hideable"></div>
	<div id="user-menu-right" class="hideable">
		<a class="flex-none base-con-link" href="{{ route('users.display', Auth::user()->id)}}"><x-tabler-user class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ Auth::user()->username }}</a>
		<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit()"><x-tabler-logout class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ ___('logout') }}</a><form id="logout-form" class="hidden" action="{{ url('/logout') }}" method="POST">{{ csrf_field() }} </form>
		@if( !setting('app.published') )
		<span class="bg-red-600 text-white rounded-xl ml-2 pb-px pr-3">
			<x-tabler-alert-triangle class="pb-1 ml-2 mr-1 inline w-5 h-5" />{{ __('Website is not published') }}
		</span>
		@endif
	</div>
</div>