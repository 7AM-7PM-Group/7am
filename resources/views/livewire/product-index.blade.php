<div class="space-y-4">
    <flux:session>All Product</flux:session>
    <flux:container-sidebar>
        <div class="flex justify-between gap-4 ">
            <flux:input wire:model.live='search' placeholder='Search a Product' size='sm'></flux:input>

            <flux:select wire:model.live='category' size="sm" placeholder="Category" class="w-48">
                <flux:select.option value="">All Categories</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->slug }}">{{ $category->categoryName }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live='sub_category' size="sm" placeholder="Sub Category" class="w-48">
                <flux:select.option value="">All Sub Categories</flux:select.option>
                @foreach ($subCategories as $sub)
                    <flux:select.option value="{{ $sub->slug }}">{{ $sub->subCategoryName }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:button variant="primary" wire:click='sync' icon="plus" size="sm">Sync</flux:button>
        </div>

        <div class="grid grid-cols-16 min-w-4xl font-semibold py-2 mt-4 gap-4">
            <div class="">#</div>
            <div class="col-span-3">Product</div>
            <div class="col-span-2 text-center">Code</div>
            <div class="col-span-2 text-center">Category</div>
            <div class="col-span-2 text-center">Sub Category</div>
            <div class="col-span-2 text-center">Base Price</div>
            <div class="col-span-2 text-center">MOQ</div>
            <div class="col-span-2 text-center">Action</div>
        </div>
        @foreach ($products as $key => $item)
            <div class="grid grid-cols-16 items-center min-w-4xl py-1 gap-4">
                <div class="">{{ $key + 1 }}</div>
                <div class="col-span-3 grid grid-cols-4 items-center gap-2">
                    <div class="aspect-square rounded bg-center bg-cover bg-no-repeat"
                        style="background-image: url({{ $item->image != '' ? asset('storage/' . $item->image) : asset('assets/No-Picture-Found.png') }})">
                    </div>
                    <div class="col-span-3">
                        {{ $item->productName }}
                    </div>
                </div>
                <div class="col-span-2 text-center">{{ $item->productCode }}</div>
                <div class="col-span-2 text-center">{{ $item->category->categoryName }}</div>
                <div class="col-span-2 text-center">{{ $item->subCategory->subCategoryName }}</div>
                <div class="col-span-2 text-center">Rp. {{ number_format($item->price, 0, ',', '.') }}</div>
                <div class="col-span-2 text-center">{{ $item['moq'] }}</div>
                <div class="col-span-2 justify-center flex gap-2">
                    <flux:tooltip content="Set MOQ">
                        <flux:button size="sm" icon="pencil-square" variant="primary" color="amber"
                            wire:click="openEditModal({{ $item['id'] }})"></flux:button>
                    </flux:tooltip>
                </div>
            </div>
        @endforeach


        <flux:modal name="edit-product-modal">
            <div class="mt-4">Edit Product</div>
            <form wire:submit='save'>
                <div class="">
                    <flux:input wire:key="product-img-{{ $productId }}" type="file" label="Image"
                        wire:model.live='image' preview="{{ $preview }}">
                    </flux:input>
                </div>
                <div class="">
                    <flux:textarea wire:model.live='description' label="Description"></flux:textarea>
                </div>
                <div class="mt-4">
                    <flux:input wire:model.live='moq' label="Minimum Order Quantity" type="number"></flux:input>
                </div>
                <div class="mt-4">
                    <flux:input wire:model.live='maximum_order'
                        description="Set to 0 if any order quantity can be delivered the next day"
                        label="Maximum Order for Next Day Shipping" type="number"></flux:input>
                </div>
                @if ($maximum_order > 0)
                    <div class="mt-4">
                        <flux:input wire:model.live='cutoff_time'
                            description="Maximum time for order to be eligible for next day shipping"
                            label="Cutoff Time" type="time"></flux:input>
                    </div>
                @endif
                <div class="flex justify-center mt-4">
                    <flux:button type="submit" variant="primary">Save</flux:button>
                </div>
            </form>
        </flux:modal>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </flux:container-sidebar>
</div>
