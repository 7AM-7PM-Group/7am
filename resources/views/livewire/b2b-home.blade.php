<div>
    <div class="bg-center relative bg-no-repeat bg-cover w-full h-screen flex"
        style="background-image: url({{ asset('assets/b2b/landing/banner.png') }}); background-position: 50% 45%;">
        <flux:container class="flex h-full items-center justify-start">
            <div
                class="md:w-5/12 w-9/12 h-fit bg-white dark:bg-gray-700 rounded-lg p-10 backdrop-blur-xs flex flex-col justify-center space-y-4">
                <div class="text-xl md:text-3xl text-black dark:text-white font-semibold md:leading-[1.4]">Empower Your
                    Business Purchasing</div>
                <div class="text-sm md:text-lg text-neutral-800 dark:text-neutral-300">
                    Streamline every order with a platform built for speed, reliability, and control.
                    Bringing <span class="text-[#D4A373]">clarity</span> and <span
                        class="text-[#D4A373]">confidence</span> to every order you make.
                </div>

                <a class="bg-[#D4A373] mt-2 hover:bg-[#b8875c] transition text-white py-2 text-center w-[120px] rounded"
                    href="{{ route('shop.index') }}">Shop Now</a>
            </div>
        </flux:container>
    </div>
    @livewire('feature')

    <flux:container class="mt-4 md:mt-[180px] bg-white dark:bg-gray-700 p-8 rounded-lg shadow-lg mb-16">
        <div class="text-xl text-center md:text-2xl lg:text-4xl font-semibold md:leading-loose">Explore Our Product
            Range</div>
        <div class="text-sm dark:text-neutral-300 text-center md:text-lg">Browse through our complete catalog of
            business-ready products
            designed to support your company’s needs.</div>
        <div class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-2 md:grid-cols-3 items-start">
            @foreach ($products as $item)
                <button wire:click="openShowModal('{{ $item->productID }}')" class="text-center mx-1 my-4">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                        class="rounded-lg aspect-square shadow mb-2">

                    <div class="px-2">
                        <div class="flex justify-between items-center mb-2">
                            <p class="font-semibold text-xl text-left">{{ $item->productName }}</p>
                        </div>
                        <p class="text-[#D4A373] text-left">Rp. {{ number_format($item->display_price, 0, ',', '.') }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>

        <div class="flex justify-end mt-4">
            <a href="{{ route('shop.index') }}"
                class="bg-[#D4A373] text-white flex items-center gap-2 py-2 px-4 rounded-md">
                <span>Explore More</span>
                <flux:icon.arrow-long-right class="">
                </flux:icon.arrow-long-right>
            </a>
        </div>
    </flux:container>

    @livewire('shop-show')
    @livewire('newsletter')


</div>
