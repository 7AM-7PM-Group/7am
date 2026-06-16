<flux:modal dismissible="{{ false }}" name="sync-customer-from-esb-modal">
    <div class="">{{ $title }}</div>
    <form class="space-y-4" wire:submit='save'>
        <flux:separator></flux:separator>
        <div class="mt-4">
            <flux:input wire:model='name' readonly label="Business Name"></flux:input>
        </div>
        <flux:separator></flux:separator>
        <div class="mt-4">
            <flux:select wire:model='customerID' label="Select Customer on ESB">
                @foreach ($customerList as $customerID => $customerName)
                    <flux:select.option value="{{ $customerID }}">{{ $customerName }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="mt-4">
            <flux:button type="submit" variant="primary">Save</flux:button>
        </div>
    </form>
</flux:modal>
