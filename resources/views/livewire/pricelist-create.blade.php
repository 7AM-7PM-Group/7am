<div class="space-y-4">
    <flux:modal dismissible name="create-pricelist-modal">
        <form wire:submit='save' class="space-y-4">
            <div class="text-sm font-semibold">{{ $title }}</div>
            <flux:input wire:model='PricelistID' type="number" label="PricelistID"></flux:input>
            <flux:input wire:model='name' label="Name"></flux:input>
            <div class="flex justify-center">
                <flux:button variant="primary" type="submit">Submit</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
