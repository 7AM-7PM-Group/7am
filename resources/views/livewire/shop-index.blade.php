{{-- @dd($products) --}}
<div>
    <flux:secondary-hero url="assets/shop/banner.png" text="Our Products"
        description="Discover our delightful range of freshly baked breads and pastries, crafted to bring joy to your taste buds every morning.">
    </flux:secondary-hero>

    <flux:container>
        <div class="flex justify-center gap-2 mt-10">
            {{-- <button wire:click='toogleFilter' class="cursor-pointer px-4 rounded-xl hover:bg-[#CFAF8D] text-sm bg-[#D4A373]">Filter</button> --}}
            <flux:input icon="magnifying-glass" wire:model.live='search' class="w-1/2!"
                :placeholder="'Search Our Product Here'"></flux:input>
            {{-- <flux:button icon="" variant="primary"></flux:button> --}}
        </div>
    </flux:container>


    <flux:container class="mt-4">
        <div class="flex flex-wrap md:flex-nowrap gap-4">
            <div class="w-full md:sticky top-24 md:w-1/4 bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md h-fit">
                <div class="font-semibold">Filters</div>

                <div class="mt-4">Price Range</div>
                <div class="flex items-center gap-4">
                    <flux:input wire:model.live='min' max="{{ $max }}" type="number" step="500"
                        min="0" :placeholder="'Min'"></flux:input>
                    <div class="">-</div>
                    <flux:input wire:model.live='max' min="{{ $min }}" type="number" step="500"
                        min="0" :placeholder="'Max'"></flux:input>
                </div>

                <div class="mt-4 mb-2">Category</div>
                <flux:radio.group wire:model.live='category' class="">
                    @foreach ($categories as $item)
                        <flux:radio :label="$item->categoryName" :value="$item->slug"></flux:radio>
                    @endforeach
                </flux:radio.group>

                <div class="flex gap-4 mt-4 justify-start">
                    <button wire:click='resetFilter'
                        class="cursor-pointer w-full py-1 rounded-md hover:bg-[#CFAF8D] text-sm bg-[#D4A373]">Reset</button>
                </div>
            </div>

            <div class="w-full transition-all bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md md:w-3/4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 items-start">
                    @forelse ($products as $item)
                        <button wire:click="openShowModal('{{ $item->productID }}')" class="text-center">
                            <img class="aspect-square"
                                src="{{ $item['image'] != '' ? $item['image'] : asset('assets/No-Picture-Found.png') }}"
                                alt="Chocolate Croissant" class="rounded-lg shadow mb-2">

                            <div class="px-2 mt-2">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="font-semibold text-md text-left">{{ $item->productName }}</p>
                                </div>
                                <p class="text-[#D4A373] text-left">Rp.
                                    {{ number_format($item->display_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </button>
                    @empty
                        <div
                            class="h-80 px-4 md:px-12 flex justify-center items-center text-lg md:text-xl text-gray-600 col-span-3">
                            No products match your current filter. Please adjust your selection to explore our full
                            range of Breads and Pastries.
                        </div>
                    @endforelse
                </div>
                {{-- pagination --}}
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </flux:container>

    @livewire('shop-show')
    @livewire('newsletter')
</div>
