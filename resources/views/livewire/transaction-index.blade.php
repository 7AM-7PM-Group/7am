<div class="space-y-4">
    <div class="py-2 px-4 w-full bg-white h-fit rounded dark:bg-gray-700">
        <div class="font-semibold md:text-lg">Transaction</div>
        <div class="">
            @if (session()->has('success'))
                <div class="text-green-400 text-sm">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="text-rose-400 text-sm">{{ session('error') }}</div>
            @endif
        </div>
        {{-- <div class="text-rose-400 text-sm">{{ session('error') }}</div> --}}
    </div>

    <flux:container-sidebar>
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-8 gap-4 items-end ">
            <div class=" sm:col-span-3 lg:col-span-3 xl:col-span-5">
                <flux:input placeholder="Search transactions..." label="Search" class="" type="text"
                    wire:change='updateDate' wire:model.live='search'>
                </flux:input>
            </div>
            <div class="lg:col-span-2 xl:col-span-1">
                <flux:input label="Shipping Date" type="date" wire:change='updateDate' wire:model.live='date'>
                </flux:input>
            </div>
            <div class="lg:col-span-2 xl:col-span-1">
                <flux:select class="lg:col-span-2 xl:col-span-1" label="Order Status" wire:change='updateDate'
                    wire:model.live='status'>
                    <option value="">All Status</option>
                    <option value="ordered">Ordered</option>
                    <option value="paid">Paid</option>
                    <option value="need_action">Need Action</option>
                    <option value="cancelled">Cancelled</option>
                </flux:select>
            </div>
            <flux:button variant="primary" color="rose" wire:click="resetFilters">Reset Filters</flux:button>
        </div>
        <div class="grid-cols-1 mt-4 grid gap-4">
            @forelse ($transactions as $item)
                <div class="bg-gray-200 dark:bg-gray-600 rounded p-4 gap-4" x-data={open:false}>
                    <div class="">
                        <div class="sr-only">Shipping Information</div>
                        <div class="">
                            <div class="text-sm md:text-md font-semibold">{{ $item->shipping->name }} /
                                {{ $item->shipping->phone }}</div>
                            <div class="mt-2 text-xs md:text-sm">{{ $item->shipping->address }}</div>
                        </div>
                    </div>
                    <flux:separator text="Product"></flux:separator>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($item->items as $key => $itm)
                            <div class="grid grid-cols-4 items-center" x-show="open||{{ $key }} == 0" x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90">
                                <div class="flex items-center gap-2">
                                    <div class="size-20 rounded bg-center bg-cover bg-no-repeat"
                                        style="background-image: url({{ $itm->product->image_url }})">
                                    </div>
                                    <div class="">
                                        <div class="font-semibold">{{ $itm->product->name }}</div>
                                        <div class="text-xs md:text-sm">{{ $itm->product->category->name }}</div>
                                    </div>
                                </div>
                                <div class="text-center">Rp.
                                    {{ number_format($itm->price, 0, ',', '.') }}
                                </div>
                                <div class="text-center">
                                    {{ $itm->qty }} Pcs
                                </div>
                                <div class="text-center">Rp.
                                    {{ number_format($itm->subtotal, 0, ',', '.') }}</div>
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
                    <flux:separator text="Shipping Info"></flux:separator>
                    <div class="mt-2 text-xs md:text-sm">
                        <div class="">Transaction Number: {{ $item->transaction_number }}</div>
                        <div class="">Order Time: {{ $item->created_at->format('Y-m-d H:i') }}</div>
                        <div class="">Shipping Date: {{ $item->shipping_date->format('Y-m-d') }}</div>
                        <div class="">Order Status: {{ $item->status }}</div>
                    </div>
                    <flux:separator text="Order summary"></flux:separator>
                    <div class="w-full rounded-sm mt-2 text-xs md:text-sm space-y-2 ">
                        <div class="flex justify-between items-center">
                            <div class="">Subtotal ({{ $item->items->count() }} items)</div>
                            <div class="">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="">Packaging Fee</div>
                            <div class="">Rp. {{ number_format($item->packaging_fee, 0, ',', '.') }}</div>
                        </div>
                        @if ($item->coupon)
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
                    </div>
                    <div class="">{{ $item->deleted_at }}</div>
                    <div class="flex justify-center flex-wrap gap-4 mt-4">

                        @if (
                            !$item->deleted_at &&
                                ($item->mekari_sync_status == 'pending' &&
                                    (now()->greaterThanOrEqualTo($item->created_at->addMinutes(5)) ||
                                        $item?->payment?->mekari_sync_status == 'paid')))
                            <flux:button size="sm" wire:click='importSalesOrderToESB({{ $item->id }})'>
                                Import
                                Sales Order to ESB
                            </flux:button>
                        @endif
                        @if (!$item->deleted_at && $item->mekari_sync_status == 'pending' && $item->payment == null)
                            <flux:button size="sm" wire:click='showCancelOrderModal({{ $item->id }})'
                                variant="danger">
                                Delete Order
                            </flux:button>
                        @endif
                        @if (
                            !$item->deleted_at &&
                                $item->mekari_sync_status == 'synced' &&
                                $item->payment &&
                                $item->payment->mekari_sync_status == 'pending')
                            <flux:button size="sm" wire:click='importPayment({{ $item->id }})'>Import
                                Payment to Jurnal
                            </flux:button>
                        @endif

                        {{-- <div class="">{{ $item->payment ?? 'null' }}</div> --}}

                        @if (!$item->payment)
                            <flux:button size="sm" wire:click="payTransaction({{ $item->id }})"
                                variant="primary">
                                Pay Transaction</flux:button>
                        @endif
                    </div>

                </div>
            @empty
                <div class="w-full h-96 flex justify-center items-center">No transactions found.</div>
            @endforelse
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>

            <flux:modal name="cancel-order">
                <div class="font-semibold">Cancel Order {{ $transaction_number ?? '' }}</div>
                <div class="">Are you sure you want to cancel this order?</div>
                <form wire:submit.prevent="cancelOrder" class="mt-4">
                    <flux:input wire:model.live="cancellation_reason" label="Cancellation Reason" required
                        type="text">
                    </flux:input>
                    <div class="flex justify-end">
                        <flux:button type="submit" variant="danger">
                            Confirm</flux:button>
                    </div>
                </form>
            </flux:modal>
        </div>
    </flux:container-sidebar>

    @livewire('transaction-pay')
</div>
