<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-gray-800">
    <flux:header sticky container class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('b2b-home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar scrollable class="-mb-px max-lg:hidden">
            <flux:navbar.item :href="route('b2b-home')" :current="request()->routeIs('b2b-home')" wire:navigate>
                {{ __('Home') }}
            </flux:navbar.item>
            <flux:navbar.item :href="route('shop.index')" :current="request()->routeIs('shop.index')" wire:navigate>
                {{ __('Shop') }}
            </flux:navbar.item>
            <flux:navbar.item :href="route('contact')" :current="request()->routeIs('contact')" wire:navigate>
                {{ __('Contact') }}
            </flux:navbar.item>
            <flux:navbar.item :href="route('wholesale.index')" :current="request()->routeIs('wholesale.index')"
                wire:navigate>
                {{ __('Wholesale') }}
            </flux:navbar.item>
            @auth

                <flux:navbar.item :href="route('history')" :current="request()->routeIs('history')" wire:navigate>
                    {{ __('History') }}
                </flux:navbar.item>
                @if (in_array(Auth::user()->role, ['admin', 'sales-admin', 'accounting']))
                    <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navbar.item>
                @endif
            @endauth
        </flux:navbar>

        <flux:spacer />

        @guest
            <flux:navbar>
                <flux:button size="sm" variant="outline" icon="arrow-right-end-on-rectangle"
                    href="{{ route('register') }}">Register
                </flux:button>
                <flux:button size="sm" variant="primary" icon="arrow-right-end-on-rectangle"
                    href="{{ route('login') }}">Login
                </flux:button>
            </flux:navbar>
        @endguest
        @auth
            <flux:navbar>
                <flux:tooltip :content="__('Cart')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="shopping-cart" href="{{ route('cart') }}"
                        :current="request()->routeIs('cart')" :label="__('Cart')" />
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.Business-info')" icon="briefcase-business" wire:navigate>
                            {{ __('Business Info') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                            data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-gray-200 bg-gray-50 pt-2 dark:border-gray-700 dark:bg-gray-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                <flux:navlist.item :href="route('b2b-home')" :current="request()->routeIs('b2b-home')" wire:navigate>
                    {{ __('Home') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('shop.index')" :current="request()->routeIs('shop.index')"
                    wire:navigate>
                    {{ __('Shop') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('contact')" :current="request()->routeIs('contact')" wire:navigate>
                    {{ __('Contact') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('wholesale.index')" :current="request()->routeIs('wholesale.index')"
                    wire:navigate>
                    {{ __('Wholesale') }}
                </flux:navlist.item>
                @auth

                    <flux:navlist.item :href="route('history')" :current="request()->routeIs('history')" wire:navigate>
                        {{ __('History') }}
                    </flux:navlist.item>
                    @if (in_array(Auth::user()->role, ['admin', 'sales-admin', 'accounting']))
                        <flux:navlist.item :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                            wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:navlist.item>
                    @endif
                @endauth
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />
    </flux:sidebar>

    {{ $slot }}

    @livewire('footer')
    @fluxScripts
</body>

</html>
