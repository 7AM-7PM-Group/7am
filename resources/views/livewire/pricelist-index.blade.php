<div class="space-y-4">
    <flux:session class="header">
        {{ $title }}

        <x-slot name="button">
            <flux:button variant='primary' size='sm' icon="plus" wire:click='createPricelist()'>Add Pricelist
            </flux:button>
        </x-slot>
    </flux:session>
    <flux:container-sidebar>
        <div class="flex gap-4 font-semibold py-2 text-sm">
            <div class="w-1/5">PricelistID</div>
            <div class="w-3/5">Name</div>
            <div class="w-1/5 text-center">Action</div>
        </div>
        @foreach ($pricelists as $pricelist)
            <div class="flex gap-4 py-1">
                <div class="w-1/5">{{ $pricelist->PricelistID }}</div>
                <div class="w-3/5">{{ $pricelist->name }}</div>
                <div class="w-1/5 flex gap-2 justify-center">
                    <flux:tooltip content="Edit Pricelist">
                        <flux:button variant="primary" size="sm" color="amber" icon="pencil-square"
                            wire:click='editPricelist({{ $pricelist->id }})'>
                        </flux:button>
                    </flux:tooltip>
                    <flux:tooltip content="Delete Pricelist">
                        <flux:button variant="danger" size="sm" color="amber" icon="trash"
                            wire:click='showDeleteModal({{ $pricelist->id }})'>
                        </flux:button>
                    </flux:tooltip>
                </div>
            </div>
        @endforeach
    </flux:container-sidebar>

    <flux:modal name="delete-pricelist-modal">
        <div class="">Are you sure you want to delete pricelist {{ $pricelist?->name }}?</div>
        <div class="flex justify-end mt-4">
            <flux:button variant="danger" wire:click='deletePricelist({{ $id }})'>Confirm</flux:button>
        </div>
    </flux:modal>

    @livewire('pricelist-create')
</div>
