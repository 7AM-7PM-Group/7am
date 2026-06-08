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

                            @if ($item->subCategories->count())
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $item->subCategories->count() }}
                                    subs
                                </div>
                            @endif

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

                            <flux:button wire:click="toggleOpen({{ $open != $item->id ? $item->id : 0 }})"
                                icon="{{ $open != $item->id ? 'chevron-down' : 'chevron-up' }}" size="sm"
                                variant="ghost" />
                        </div>

                    </div>

                    {{-- Subcategories --}}
                    @if ($open == $item->id)
                        <div
                            class="
                                mt-4
                                ml-8
                                border-l
                                pl-4
                                space-y-2
                            ">

                            @foreach ($item->subCategories as $index => $sub)
                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        rounded-lg
                                        bg-gray-50
                                        px-4
                                        py-2
                                    ">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="
                                                text-xs
                                                text-gray-400
                                                w-5
                                            ">
                                            {{ $index + 1 }}
                                        </div>

                                        <div
                                            class="
                                                text-sm
                                                text-gray-700
                                            ">
                                            {{ $sub->subCategoryName }}
                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>
                    @endif

                </div>
            @endforeach

        </div>

    </flux:container-sidebar>

</div>
