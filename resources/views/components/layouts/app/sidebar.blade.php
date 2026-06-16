<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-neutral-100 dark:bg-gray-800">
    <flux:sidebar sticky stashable class="border-e border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            @if (in_array(Auth::user()->role, ['admin', 'accounting', 'sales-admin']))
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        wire:navigate>
                        {{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>
            @endif
            @if (in_array(Auth::user()->role, ['admin', 'accounting', 'outlet-admin']))
                <flux:navlist.group :heading="__('Reservations')" class="grid">
                    <flux:navlist.item :href="route('outlet.index')" :current="request()->routeIs('outlet.index')"
                        wire:navigate>{{ __('Outlets') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('reservation.index')"
                        :current="request()->routeIs('reservation.index')" wire:navigate>{{ __('Reservations') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Membership')" class="grid">
                    <flux:navlist.item :href="route('member.index')" :current="request()->routeIs('member.index')"
                        wire:navigate>{{ __('Members') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('type.index')" :current="request()->routeIs('type.index')"
                        wire:navigate>{{ __('Membership Types') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('card.index')" :current="request()->routeIs('card.index')"
                        wire:navigate>{{ __('Cards') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('redeem.index')" :current="request()->routeIs('redeem.index')"
                        wire:navigate>{{ __('Redeem Rewards') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            @endif
            @if (in_array(Auth::user()->role, ['admin', 'accounting', 'sales-admin']))
                <flux:navlist.group :heading="__('B2B')" class="grid">
                    <flux:navlist.item :href="route('business.index')" :current="request()->routeIs('business.index')"
                        wire:navigate>{{ __('Businessess') }}
                    </flux:navlist.item>

                    <flux:navlist.item :href="route('category.index')" :current="request()->routeIs('category.index')"
                        wire:navigate>{{ __('Categories') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('product.index')" :current="request()->routeIs('product.index')"
                        wire:navigate>{{ __('Products') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('pricelist.index')"
                        :current="request()->routeIs('pricelist.index')" wire:navigate>{{ __('Pricelists') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('minimum-order.index')"
                        :current="request()->routeIs('minimum-order.index')" wire:navigate>
                        {{ __('Minimum Orders') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('transaction.index')"
                        :current="request()->routeIs('transaction.index')" wire:navigate>{{ __('Orders') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('coupon.index')" :current="request()->routeIs('coupon.index')"
                        wire:navigate>{{ __('Coupons') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            @endif
            @if (in_array(Auth::user()->role, ['admin', 'accounting']))
                <flux:navlist.group :heading="__('Admin')" class="grid">
                    <flux:navlist.item :href="route('promotion.index')"
                        :current="request()->routeIs('promotion.index')" wire:navigate>
                        {{ __('Promotions') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('admin.index')" :current="request()->routeIs('admin.index')"
                        wire:navigate>
                        {{ __('Admins') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('esb.logs.index')" :current="request()->routeIs('esb.logs.index')"
                        wire:navigate>
                        {{ __('ESB Logs') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('setting.index')" :current="request()->routeIs('setting.index')"
                        wire:navigate>
                        {{ __('Settings') }}</flux:navlist.item>
                </flux:navlist.group>
            @endif
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" data-test="sidebar-menu-button" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
