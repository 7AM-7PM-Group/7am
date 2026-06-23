<div>
    <flux:container class="mt-4 min-h-[55vh] bg-white dark:bg-gray-700 py-4 px-4 md:px-8 lg:px-16 rounded-xl shadow-md">

        <div class="text-xl text-center md:text-2xl font-semibold md:leading-loose">Order History</div>
        <div class="text-sm text-center md:text-lg">Review your past orders and track their details.</div>

        <div class="grid-cols-1 mt-4 grid gap-4">
            @foreach ($transactions as $item)
                <div class="gap-4 bg-gray-100 dark:bg-gray-600 rounded-2xl p-4" x-data="{ open: false }">
                    <div class="">
                        <div class="sr-only">Shipping Information</div>
                        <div class=" p-4 rounded-xl border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 ">
                                {{-- 🟢 Kolom kiri: informasi penerima --}}
                                <div class="space-y-2 dark:text-neutral-300 text-sm md:text-base">
                                    <div class="flex">
                                        <div class="w-24 font-semibold">Nama</div>
                                        <div class="flex-1">: {{ $item->shipping->name }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 font-semibold">Phone</div>
                                        <div class="flex-1">: {{ $item->shipping->phone }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 font-semibold">Address</div>
                                        <div class="flex-1 leading-relaxed">: {{ $item->shipping->address }}</div>
                                    </div>
                                </div>

                                {{-- 🟣 Kolom kanan: detail pengiriman --}}
                                <div class="space-y-2 md:pl-6 dark:text-neutral-300 text-xs md:text-sm">
                                    <div class="flex">
                                        <div class="w-32 font-semibold">Order Date</div>
                                        <div class="flex-1">: {{ $item->created_at->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-32 font-semibold">Shipping Date</div>
                                        <div class="flex-1">: {{ $item->shipping_date->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-32 font-semibold">Order Status</div>
                                        <div class="flex-1">: {{ ucfirst($item->status) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <flux:separator text="Product"></flux:separator> --}}

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Header -->
                        <div
                            class="grid grid-cols-3 font-semibold text-gray-800 border-b border-gray-300 py-3 text-center dark:text-white">
                            <div class="col-span-2 text-left pl-4">Order</div>
                            <div class="md:grid md:grid-cols-3 hidden">
                                <div>Price</div>
                                <div>Qty</div>
                                <div>Total</div>
                            </div>
                        </div>

                        <!-- Item Rows -->
                        @foreach ($item->items as $key => $itm)
                            {{-- @dd($itm) --}}
                            <div class="grid grid-cols-3 items-center py-4 px-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 border-b border-gray-100 last:border-0"
                                x-show="open||{{ $key }} == 0" x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">
                                <!-- Product Info -->
                                <div class="col-span-2 flex items-center gap-3">
                                    <div class="w-20 h-20 rounded-lg bg-center bg-cover bg-no-repeat border border-gray-200 shadow-sm"
                                        style="background-image: url('{{ $itm->product->image_url ?? asset('assets/No-Picture-Found.png') }}')">
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 dark:text-white">
                                            {{ $itm->product->name ?? '' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-neutral-300">
                                            {{ $itm->product->category->name ?? '' }}</div>
                                    </div>
                                </div>

                                <div class="text-sm md:text-base">
                                    <!-- Price -->
                                    <div class="text-center text-gray-700 dark:text-neutral-300">
                                        Rp {{ number_format($itm->price, 0, ',', '.') }}
                                    </div>

                                    <!-- Qty -->
                                    <div class="text-center text-gray-700 dark:text-neutral-300">
                                        {{ $itm->qty }} pcs
                                    </div>

                                    <!-- Total -->
                                    <div class="text-center font-semibold text-[#4B2E05] dark:text-neutral-300">
                                        Rp {{ number_format($itm->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <flux:button icon="chevron-down" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                            x-on:click="open=true" x-show="!open" class="w-full" variant="primary"></flux:button>
                        <flux:button icon="chevron-up" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                            x-on:click="open=false" x-show="open" class="w-full" variant="primary"></flux:button>
                    </div>

                    <div class="w-full rounded-sm mt-8 text-xs md:text-sm space-y-2 ">
                        <div class="flex justify-between items-center">
                            <div class="">Subtotal ({{ $item->items->count() }} items)</div>
                            <div class="">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="">Packaging Fee</div>
                            <div class="">Rp. {{ number_format($item->packaging_fee, 0, ',', '.') }}</div>
                        </div>
                        @if ($item->usage)
                            <div class="flex justify-between items-center">
                                <div class="">Coupon</div>
                                <div class="">{{ $item->usage->coupon->code }}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="">Discount</div>
                                <div class="">Rp. {{ number_format($item->discount, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <div class="">Payment Status</div>
                            <div class="">
                                @if ($item->payment ?? false)
                                    Paid
                                @else
                                    Unpaid
                                @endif
                            </div>
                        </div>

                        @if ($item->payment ?? false)
                            <div class="flex justify-between items-center">
                                <div class="">Payment Method</div>
                                <div class="capitalize">{{ $item->payment->payment_type }}</div>
                            </div>
                        @endif
                        <flux:separator></flux:separator>
                        <div class="flex justify-between items-center">
                            <div class="">Total</div>
                            <div class="">Rp. {{ number_format($item->total, 0, ',', '.') }}</div>
                        </div>

                        @if (!$item->payment ?? false)
                            <flux:separator text="Action"></flux:separator>
                            <div class="flex justify-center gap-4">
                                <div class="mt-4 flex justify-center">
                                    <flux:button as href="{{ route('checkout', ['slug' => $item->slug]) }}"
                                        variant="primary" color="green">Pay</flux:button>
                                </div>
                                @php
                                    $canCancel = now()->lessThan($item->created_at->addMinutes(5));
                                @endphp
                                @if ($canCancel)
                                    <div class="mt-4 flex justify-center">
                                        <flux:modal.trigger name="cancelOrderModal{{ $item->id }}">
                                            <flux:button variant="danger">
                                                Cancel
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                    <flux:modal name="cancelOrderModal{{ $item->id }}">
                                        <div class="font-semibold">Cancel Order {{ $item->transaction_number }}</div>
                                        <div class="">Are you sure you want to cancel this order?</div>
                                        <div class="flex justify-end">
                                            <flux:button variant="danger"
                                                wire:click="cancelOrder('{{ $item->slug }}')">
                                                Confirm</flux:button>
                                        </div>
                                    </flux:modal>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            @if ($oldTransaction ?? false)

            {{-- @dd($oldTransaction) --}}

                @foreach ($oldTransaction as $item)
                    <div class="gap-4 bg-gray-100 dark:bg-gray-600 rounded-2xl p-4" x-data="{ open: false }">
                        <div class="">
                            <div class="sr-only">Shipping Information</div>
                            <div class=" p-4 rounded-xl border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 ">
                                    {{-- 🟢 Kolom kiri: informasi penerima --}}
                                    <div class="space-y-2 dark:text-neutral-300 text-sm md:text-base">
                                        <div class="flex">
                                            <div class="w-24 font-semibold">Nama</div>
                                            <div class="flex-1">: {{ $item->shipping->name }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-24 font-semibold">Phone</div>
                                            <div class="flex-1">: {{ $item->shipping->phone }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-24 font-semibold">Address</div>
                                            <div class="flex-1 leading-relaxed">: {{ $item->shipping->address }}</div>
                                        </div>
                                    </div>

                                    {{-- 🟣 Kolom kanan: detail pengiriman --}}
                                    <div class="space-y-2 md:pl-6 dark:text-neutral-300 text-xs md:text-sm">
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Order Date</div>
                                            <div class="flex-1">: {{ $item->created_at->format('Y-m-d') }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Shipping Date</div>
                                            <div class="flex-1">: {{ $item->shipping_date->format('Y-m-d') }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-semibold">Order Status</div>
                                            <div class="flex-1">: {{ ucfirst($item->status) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <flux:separator text="Product"></flux:separator> --}}

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Header -->
                            <div
                                class="grid grid-cols-3 font-semibold text-gray-800 border-b border-gray-300 py-3 text-center dark:text-white">
                                <div class="col-span-2 text-left pl-4">Order</div>
                                <div class="md:grid md:grid-cols-3 hidden">
                                    <div>Price</div>
                                    <div>Qty</div>
                                    <div>Total</div>
                                </div>
                            </div>

                            <!-- Item Rows -->
                            @foreach ($item->items as $key => $itm)
                                {{-- @dd($itm) --}}
                                <div class="grid grid-cols-3 items-center py-4 px-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 border-b border-gray-100 last:border-0"
                                    x-show="open||{{ $key }} == 0" x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95">
                                    <!-- Product Info -->
                                    <div class="col-span-2 flex items-center gap-3">
                                        <div class="w-20 h-20 rounded-lg bg-center bg-cover bg-no-repeat border border-gray-200 shadow-sm"
                                            style="background-image: url('{{ $itm->product->image_url ?? asset('assets/No-Picture-Found.png') }}')">
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800 dark:text-white">
                                                {{ $itm->product->name ?? '' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-neutral-300">
                                                {{ $itm->product->category->name ?? '' }}</div>
                                        </div>
                                    </div>

                                    <div class="text-sm md:text-base">
                                        <!-- Price -->
                                        <div class="text-center text-gray-700 dark:text-neutral-300">
                                            Rp {{ number_format($itm->price, 0, ',', '.') }}
                                        </div>

                                        <!-- Qty -->
                                        <div class="text-center text-gray-700 dark:text-neutral-300">
                                            {{ $itm->qty }} pcs
                                        </div>

                                        <!-- Total -->
                                        <div class="text-center font-semibold text-[#4B2E05] dark:text-neutral-300">
                                            Rp {{ number_format($itm->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <flux:button icon="chevron-down" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90" x-on:click="open=true" x-show="!open"
                                class="w-full" variant="primary"></flux:button>
                            <flux:button icon="chevron-up" x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90" x-on:click="open=false" x-show="open"
                                class="w-full" variant="primary"></flux:button>
                        </div>

                        <div class="w-full rounded-sm mt-8 text-xs md:text-sm space-y-2 ">
                            <div class="flex justify-between items-center">
                                <div class="">Subtotal ({{ $item->items->count() }} items)</div>
                                <div class="">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="">Packaging Fee</div>
                                <div class="">Rp. {{ number_format($item->packaging_fee, 0, ',', '.') }}</div>
                            </div>
                            @if ($item->usage)
                                <div class="flex justify-between items-center">
                                    <div class="">Coupon</div>
                                    <div class="">{{ $item->usage->coupon->code }}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="">Discount</div>
                                    <div class="">Rp. {{ number_format($item->discount, 0, ',', '.') }}</div>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <div class="">Payment Status</div>
                                <div class="">
                                    @if ($item->payment ?? false)
                                        Paid
                                    @else
                                        Unpaid
                                    @endif
                                </div>
                            </div>

                            @if ($item->payment ?? false)
                                <div class="flex justify-between items-center">
                                    <div class="">Payment Method</div>
                                    <div class="capitalize">{{ $item->payment->payment_type }}</div>
                                </div>
                            @endif
                            <flux:separator></flux:separator>
                            <div class="flex justify-between items-center">
                                <div class="">Total</div>
                                <div class="">Rp. {{ number_format($item->total, 0, ',', '.') }}</div>
                            </div>

                            @if (!$item->payment ?? false)
                                <flux:separator text="Action"></flux:separator>
                                <div class="flex justify-center gap-4">
                                    <div class="mt-4 flex justify-center">
                                        <flux:button as href="{{ route('checkout', ['slug' => $item->slug]) }}"
                                            variant="primary" color="green">Pay</flux:button>
                                    </div>
                                    @php
                                        $canCancel = now()->lessThan($item->created_at->addMinutes(5));
                                    @endphp
                                    @if ($canCancel)
                                        <div class="mt-4 flex justify-center">
                                            <flux:modal.trigger name="cancelOrderModal{{ $item->id }}">
                                                <flux:button variant="danger">
                                                    Cancel
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>
                                        <flux:modal name="cancelOrderModal{{ $item->id }}">
                                            <div class="font-semibold">Cancel Order {{ $item->transaction_number }}
                                            </div>
                                            <div class="">Are you sure you want to cancel this order?</div>
                                            <div class="flex justify-end">
                                                <flux:button variant="danger"
                                                    wire:click="cancelOrder('{{ $item->slug }}')">
                                                    Confirm</flux:button>
                                            </div>
                                        </flux:modal>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </flux:container>

    @livewire('newsletter')
</div>
