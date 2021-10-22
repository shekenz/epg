<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center mr-8">
                    <a href="{{ route('index') }}">
                        <!-- <x-application-logo class="block h-10 w-auto fill-current text-gray-600" /> -->
                        {{ config('app.name') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-tabler-gauge class="mr-1"/>{{ ___('dashboard') }}
                    </x-nav-link>
										<x-nav-link :href="route('books')" :active="request()->routeIs('books')">
                        <x-tabler-book class="mr-1"/>{{ ___('books') }}
                    </x-nav-link>
                    <x-nav-link :href="route('media')" :active="request()->routeIs('media')">
                        <x-tabler-photo class="mr-1"/>{{ ___('media') }}
                    </x-nav-link>
                    <x-nav-link :href="route('orders')" :active="request()->routeIs('orders')">
                        <x-tabler-receipt class="mr-1"/>{{ ___('orders') }}<span id="orderUnread" class="notification hidden"></span>
                    </x-nav-link>
                    <x-nav-link :href="route('clients')" :active="request()->routeIs('clients')">
                        <x-tabler-mood-smile class="mr-1"/>{{ ___('clients') }}
                    </x-nav-link>
                    <x-nav-link :href="route('users')" :active="request()->routeIs('users')">
                        <x-tabler-users class="mr-1"/>{{ ___('users') }}
                    </x-nav-link>
										<x-nav-link :href="route('settings')" :active="request()->routeIs('settings')">
                        <x-tabler-settings class="mr-1"/>{{ ___('settings') }}
                    </x-nav-link>
										<x-nav-link :href="route('users.display', Auth::user()->id)" :active="request()->routeIs('users.display')">
                        <x-tabler-user class="mr-1"/>{{ Auth::user()->username }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Items -->
            <div class="hidden space-x-8 sm:flex sm:items-center sm:ml-6">
				<form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a class="text-sm font-medium text-gray-500 hover:text-gray-700" href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <x-tabler-logout class="mr-1 inline"/>{{ ___('logout') }}
										</a>
                </form>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="w-full sm:w64 bg-white fixed sm:relative hidden sm:hidden">

        <!-- Responsive Settings Options -->
        <div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-tabler-gauge class="mr-1 inline"/>{{ ___('dashboard') }}
            </x-responsive-nav-link>
        </div>

		<div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('books')" :active="request()->routeIs('books')">
                <x-tabler-book class="mr-1 inline"/>{{ ___('books') }}
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('media')" :active="request()->routeIs('media')">
                <x-tabler-photo class="mr-1 inline"/>{{ ___('media') }}
            </x-responsive-nav-link>
        </div>
		
        <div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('orders')" :active="request()->routeIs('orders')">
                <x-tabler-receipt class="mr-1 inline"/>{{ ___('orders') }}
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('clients')" :active="request()->routeIs('clients')">
                <x-tabler-mood-smile class="mr-1 inline"/>{{ ___('clients') }}
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('users')" :active="request()->routeIs('users')">
                <x-tabler-users class="mr-1 inline"/>{{ ___('users') }}
            </x-responsive-nav-link>
        </div>

		<div class="border-t border-gray-200">
            <x-responsive-nav-link :href="route('settings')" :active="request()->routeIs('settings')">
                <x-tabler-settings class="mr-1 inline"/>{{ ___('settings') }}
            </x-responsive-settings>
        </div>

        <div class="border-t border-gray-200">
           <x-responsive-nav-link :href="route('users.display', Auth::user()->id)" :active="request()->routeIs('user.display')">
			<x-tabler-user class="mr-1 inline"/>{{ Auth::user()->username }}
            </x-responsive-nav-link> 
        </div>

        <div class="border-t border-gray-200">
            {{-- We dont't want the email to appear on mobile--}}
            {{-- <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>

                <div class="ml-3">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div> --}}
            <div class="border-b border-gray-200">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <x-tabler-logout class="mr-1 inline"/>{{ ___('logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
