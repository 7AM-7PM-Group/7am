<div>
    <flux:container>
        <div class="text-center font-semibold text-xl md:text-2xl mt-4">My Cart</div>
        <div class="text-center md:text-lg">One Step to Finish Your Order</div>
    </flux:container>

    <flux:container class="mt-4">
        <div class="bg-[#E8E1D7] min-h-[55vh] dark:bg-gray-800 flex flex-col items-center">
            <div class="max-w-7xl w-full grid grid-cols-1 gap-8">

                {{-- Left Section – Cart Items --}}
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 flex-1 shadow">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="font-medium text-gray-800 dark:text-neutral-300 mb-2">Items</h4>
                    </div>

                    <div
                        class="grid grid-cols-6 font-semibold text-center text-xs md:text-sm border-b mt-4 border-gray-300 pb-2 text-gray-800 dark:text-white">
                        <div class="col-span-4 md:col-span-2">Product</div>
                        <div class="col-span-2 md:col-span-1">Price</div>
                        <div class="col-span-2 md:col-span-1">Qty</div>
                        <div class="col-span-2 md:col-span-1">Total</div>
                        <div class="col-span-2 md:col-span-1">Action</div>
                    </div>

                    @forelse ($carts as $key => $item)
                        <div
                            class="grid grid-cols-6 items-center py-4 border-b text-xs md:text-sm border-gray-200 text-center">
                            <button class="flex items-center gap-2 mb-4 md:mb-0 col-span-4 md:col-span-2 text-left"
                                wire:click="openShowModal('{{ $item->product?->productID ?? '' }}')">
                                <div class="size-20 rounded bg-center bg-cover bg-no-repeat"
                                    style="background-image: url({{ $item->product->image_url }})">
                                </div>
                                <div class="">
                                    <div class="font-semibold text-[#4B2E05] dark:text-white">
                                        {{ $item->product?->productName ?? 'Product' }}</div>
                                    <div class="text-xs md:text-sm dark:text-neutral-300">
                                        {{ $item->product?->category?->categoryName ?? '' }}</div>
                                </div>
                            </button>
                            <div class="text-gray-800 col-span-2 md:col-span-1 font-medium dark:text-neutral-300">Rp.
                                {{ number_format($item->product?->display_price ?? 0, 0, ',', '.') }}
                            </div>

                            <div class="flex items-center col-span-2 md:col-span-1 justify-center gap-2">
                                <button wire:click='minus({{ $item->id }})' icon="minus"
                                    class="bg-[#B68B62] dark:bg-gray-600 px-1 text-white aspect-square h-5 md:h-7 rounded-md text-sm flex justify-center items-center font-bold cursor-pointer">
                                    <flux:icon icon="minus" class="w-3 md:w-5" />
                                </button>
                                <input wire:model.live='qty.{{ $key }}.qty'
                                    class="w-12 text-center! border rounded-md aspect-square h-5 md:h-7"
                                    wire:change='change' min="{{ $item->product?->moq ?? 1 }}" type="number"></input>
                                <button wire:click='plus({{ $item->id }})' icon="plus"
                                    class="bg-[#B68B62] dark:bg-gray-600 px-1 text-white aspect-square h-5 md:h-7 rounded-md text-sm flex justify-center items-center font-bold cursor-pointer">
                                    <flux:icon icon="plus" class="w-3 md:w-5" />
                                </button>
                            </div>

                            <div class="flex items-center col-span-2 md:col-span-1 justify-center font-medium">
                                Rp. {{ number_format(($item->product?->display_price ?? 0) * $item->qty, 0, ',', '.') }}
                            </div>

                            <div class="text-center col-span-2 md:col-span-1">
                                <flux:button size="sm" wire:click='delete({{ $item->id }})' variant="danger"
                                    icon="trash"></flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="flex justify-center items-center w-full h-80 font-semibold text-gray-500">
                            Looks like your cart is lonely. Add products to make it happy!
                        </div>
                    @endforelse
                </div>

                {{-- Right Section – Totals --}}
                <div class="grid md:grid-cols-3 gap-4 grid-cols-1">
                    <div class="bg-white dark:bg-gray-700 rounded-2xl md:col-span-2 p-6 w-full  shadow h-fit">
                        <h4 class="font-medium text-gray-800 dark:text-neutral-300 mb-2">Shipping</h4>
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden mb-3">
                            <label class="flex-1">
                                <input type="radio" name="fulfillment" value="delivery" wire:model.live="fulfillment"
                                    class="sr-only peer" checked>
                                <div
                                    class="py-2 text-center cursor-pointer transition-colors bg-gray-100 text-gray-700 peer-checked:bg-[#B68B62] dark:peer-checked:bg-gray-500 peer-checked:text-white">
                                    Delivery
                                </div>
                            </label>

                            <label class="flex-1">
                                <input type="radio" name="fulfillment" value="pickup" wire:model.live="fulfillment"
                                    class="sr-only peer">
                                <div
                                    class="py-2 text-center cursor-pointer transition-colors bg-gray-100 text-gray-700 peer-checked:bg-[#B68B62] dark:peer-checked:bg-gray-500 peer-checked:text-white">
                                    Pick Up
                                </div>
                            </label>
                        </div>

                        <div class="border border-[#B68B62] dark:border-white rounded-lg p-4 mb-6">
                            @if ($fulfillment === 'delivery')
                                <div class="flex justify-between items-center">
                                    <div class="">
                                        <h3 class="font-semibold mb-2">
                                            Your Address
                                        </h3>
                                        <div class="text-sm md:text-md">{{ $address?->name ?? '' }} /
                                            {{ $address?->phone ?? '' }}
                                        </div>
                                        <div class="mt-2 text-sm md:text-md">Phone : {{ $address?->address ?? '' }}
                                        </div>
                                        <div class="text-xs md:text-sm">{{ $address?->regency?->name ?? '' }} -
                                            {{ $address?->district?->name ?? '' }} -
                                            {{ $address?->village?->name ?? '' }}</div>
                                    </div>
                                    <flux:modal.trigger class="trigger" name="address">
                                        <button
                                            class="text-[#B68B62] dark:text-white text-sm font-medium hover:underline">Change
                                            Address</button>
                                    </flux:modal.trigger>
                                </div>
                            @else
                                <div class="flex justify-between items-center">
                                    <div class="">
                                        <h3 class="font-semibold mb-2">
                                            Our Outlet
                                        </h3>
                                        <div class="text-sm md:text-md">{{ $outlet?->name ?? '' }} /
                                            {{ $outlet?->address ?? '' }}
                                        </div>
                                        {{-- <div class="mt-2 text-sm md:text-md">Phone : {{ $address?->phone ?? '' }} </div> --}}
                                    </div>
                                    <flux:modal.trigger class="trigger" name="outlet">
                                        <button
                                            class="text-[#B68B62] dark:text-white text-sm font-medium hover:underline">Change
                                            Outlet</button>
                                    </flux:modal.trigger>
                                </div>
                            @endif
                        </div>

                        <flux:modal name="address">
                            <div class="md:text-lg font-semibold">Choose Your Address</div>
                            @foreach ($addresses as $item)
                                <div wire:click='changeAddress({{ $item->id }})'
                                    class=" cursor-pointer border mt-4 p-4  {{ $item->id == ($address?->id ?? null) ? 'border-mine-200 bg-mine-200/5' : 'border-gray-200' }} rounded-lg w-full space-y-4">
                                    <div class="">
                                        <div class="text-sm md:text-md font-semibold">{{ $item->name }} /
                                            {{ $item->phone }}</div>
                                        <div class="mt-2">{{ $item->address }}</div>
                                    </div>
                                    <a href="" class="underline underline-offset-2">Edit address</a>
                                </div>
                            @endforeach
                        </flux:modal>

                        <flux:modal name="outlet">
                            <div class="md:text-lg font-semibold">Choose Our Outlet</div>
                            @foreach ($outlets as $item)
                                <div wire:click='changeOutlet({{ $item->id }})'
                                    class=" cursor-pointer border mt-4 p-4  {{ $item->id == ($outlet?->id ?? null) ? 'border-mine-200 bg-mine-200/5' : 'border-gray-200' }} rounded-lg w-full space-y-4">
                                    <div class="">
                                        <div class="text-sm md:text-md font-semibold">{{ $item->name }}</div>
                                        <div class="mt-2">{{ $item->address }}</div>
                                    </div>
                                    {{-- <a href="" class="underline underline-offset-2">Edit address</a> --}}
                                </div>
                            @endforeach
                        </flux:modal>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 w-full  shadow h-fit">
                        <h4 class="font-medium text-gray-800 dark:text-neutral-300 mb-2">More Information</h4>

                        <div class="mt-4">
                            <flux:input wire:model='shipping_date' type="date" label="Shipping Date" required
                                min="{{ $min }}"></flux:input>
                        </div>

                        <div class="mt-4">
                            <flux:textarea wire:model='note' label="Note"></flux:textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </flux:container>

    <div class="sticky bottom-0 mt-8 left-0 space-x-4 right-0 ">
        <flux:container class="">
            <div
                class="rounded-2xl shadow-black dark:shadow-white shadow-sm  flex flex-col md:flex-row bg-[#E8E1D7] dark:bg-gray-700  items-center p-4 justify-between">
                <div class="w-full md:w-1/2 *:">
                    <h3 class=" md:text-xl font-semibold">Order Summary</h3>

                    <div class="mt-4">
                        @if (session()->has('error'))
                            <div class="text-red-500 text-sm md:text-base font-semibold">{{ session('error') }}</div>
                        @endif



                        @if (session()->has('info'))
                            <div class="mt-4 text-sm md:text-base font-semibold text-sky-400">
                                {{ session('info') }}
                            </div>
                        @endif
                    </div>

                    <div class="flex text-sm md:text-base justify-between mt-4 text-gray-800 mb-3">
                        <div class="dark:text-white">SubTotal ({{ $carts->count() }} items)</div>
                        <div class="dark:text-white">Rp. {{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex text-sm md:text-base justify-between text-gray-800 mb-3">
                        <div class="dark:text-white">Packaging Fee</div>
                        <div class="dark:text-white">Rp. {{ number_format($packaging_fee, 0, ',', '.') }}</div>
                    </div>

                    @if ($c ?? false)
                        <div class="flex text-sm md:text-base justify-between text-gray-800 mb-3">
                            <div class="dark:text-white">Coupon</div>
                            <div class="dark:text-white">{{ $c->code }}</div>
                        </div>
                        <div class="flex text-sm md:text-base justify-between text-gray-800 mb-3">
                            <div class="dark:text-white">Discount</div>
                            <div class="dark:text-white">Rp. {{ number_format($this->countDiscount(), 0, ',', '.') }}
                            </div>
                        </div>
                    @endif

                    <flux:separator></flux:separator>
                    <div class="flex text-sm md:text-base justify-between  font-semibold  text-gray-800 mb-3">
                        <div class="dark:text-white">Total</div>
                        <div class="dark:text-white">Rp.
                            {{ number_format($subtotal + $packaging_fee - $this->countDiscount(), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 mt-4 md:mt-0 md:ps-4">
                    <flux:modal.trigger name="checkoutModal">
                        <button
                            class="bg-[#B68B62] hover:bg-[#9c724e] text-white w-full py-2 font-medium rounded-md text-sm transition">
                            Checkout
                        </button>
                    </flux:modal.trigger>
                </div>
            </div>
        </flux:container>
    </div>

    <flux:modal name="checkoutModal">
        <div class="font-semibold">Checkout</div>
        <div class="">Are you sure you want to proceed with this checkout?
        </div>
        <div class="">

            You can still cancel the order within 5 minutes after checkout.</div>
        <div class="flex justify-end mt-4">
            <flux:button variant="primary" wire:click='checkout' class="me-2">
                Checkout
            </flux:button>
        </div>
    </flux:modal>

    @livewire('shop-show')
    @livewire('newsletter')
</div>
