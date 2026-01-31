<flux:modal id="promotion-modal" :title="$titles">
    {{-- <flux:input.file label="Image" type="file" old_image="{{ $oldImage }}" wire:model="image" /> --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-4">
            <flux:input label="Title" wire:model="title" />
            <flux:textarea label="Description" wire:model="description" />
        </div>
        <div class="space-y-4">
            <flux:input label="Order" type="number" wire:model="order" />
            <flux:checkbox label="Is Active?" wire:model="isActive" />
        </div>
    </div>
</flux:modal>
