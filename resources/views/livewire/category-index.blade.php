<div class="space-y-4">

    <flux:session>
        {{ $title }}

        <x-slot name="button">
            <flux:button wire:click="sync" icon="refresh-cw" variant="primary" size="sm">
                Sync
            </flux:button>
        </x-slot>
    </flux:session>

    <flux:container-sidebar>
        {{-- Header --}}
        <div class="flex gap-4 border-b min-w-lg w-full pb-3 text-sm font-semibold text-gray-500">
            <div class="w-10">#</div>
            <div class="w-1/3">Category Name</div>
            <div class="w-1/3 text-center">Category Type Name</div>
            <div class="w-1/3 text-center">Note</div>


        </div>

        {{-- Category List --}}
        <div class="divide-y">

            @foreach ($categories as $key => $item)
                <div class="py-3">

                    {{-- Main Row --}}
                    <div class="flex gap-4 items-center">

                        {{-- Number --}}
                        <div class="w-10 text-sm text-gray-500">
                            {{ $item->categoryID }}
                        </div>

                        {{-- Category Name --}}
                        <div class="w-1/3">

                            <div class="font-medium text-gray-800">
                                {{ $item->categoryName }}
                            </div>

                        </div>
                        <div class="w-1/3 text-sm text-center  text-gray-500">
                            {{ $item->categoryTypeName }}
                        </div>
                        <div class="w-1/3 text-sm text-center  text-gray-500">
                            {{ $item->notes }}
                        </div>





                    </div>

                </div>
            @endforeach
        </div>
    </flux:container-sidebar>

    <flux:container-sidebar>
        <div class="flex gap-4 border-b min-w-lg w-full pb-3 text-sm font-semibold text-gray-500">
            <div class="w-10">#</div>
            <div class="w-1/2">Sub Category Name</div>
            <div class="w-1/2 text-center">Note</div>
        </div>

        @foreach ($subCategories as $index => $sub)
            <div class="flex gap-4 border-b py-3 min-w-lg w-full">
                <div class="w-10">{{ $index + 1 }}</div>
                <div class="w-1/2">{{ $sub->subCategoryName }}</div>
                <div class="w-1/2 text-center">{{ $sub->notes ?? '-' }}</div>
            </div>
        @endforeach
    </flux:container-sidebar>


</div>
