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
            <div class="w-1/5">Category Name</div>
            <div class="w-1/5 text-center">Category Type Name</div>
            <div class="w-1/5 text-center">Note</div>

            <div class="w-1/5 text-center ">
                Status
            </div>

            <div class="w-1/5 text-center">
                Action
            </div>
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
                        <div class="w-1/5">

                            <div class="font-medium text-gray-800">
                                {{ $item->categoryName }}
                            </div>

                        </div>
                        <div class="w-1/5 text-sm  text-gray-500">
                            {{ $item->categoryTypeName }}
                        </div>
                        <div class="w-1/5 text-sm  text-gray-500">
                            {{ $item->notes }}
                        </div>

                        {{-- Status --}}
                        <div class="w-1/5 flex justify-center">

                            <span
                                class="
                                    px-2.5 py-1
                                    rounded-full
                                    text-xs
                                    font-medium

                                    {{ $item->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}
                                ">
                                {{ $item->active ? 'Active' : 'Inactive' }}
                            </span>

                        </div>

                        {{-- Actions --}}
                        <div class="w-1/5 flex items-center justify-center gap-2">

                            @if ($item->active)
                                <flux:button wire:click="toggleStatus({{ $item->id }})" variant="primary"
                                    color="red" size="sm">
                                    Deactivate
                                </flux:button>
                            @else
                                <flux:button wire:click="toggleStatus({{ $item->id }})" variant="primary"
                                    color="green" size="sm">
                                    Activate
                                </flux:button>
                            @endif


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
