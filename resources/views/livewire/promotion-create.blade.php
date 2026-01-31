<div class="">
    <flux:modal name="promotion-modal" :title="$titles">
        <form wire:submit.prevent="save">
            <div class="font-semibold text-lg">{{ $titles }}</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:input required="{{ $id ?? false }}" aspect="4/3" label="Image" type="file"
                    preview="{{ $oldImage }}" wire:model.live="image" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input required label="Title" wire:model.live="title" />
                <flux:input required label="Order" type="number" wire:model.live="order" />
                <div class="md:col-span-2">
                    <flux:textarea label="Description" wire:model.live="description" />
                </div>
            </div>
            <div class="mt-4">
                <flux:checkbox label="Is Active?" wire:model.live="is_active" />
            </div>
            <div class="mt-4 flex justify-center gap-4">
                <flux:button type="submit" variant="primary">
                    Submit </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
