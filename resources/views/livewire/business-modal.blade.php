<div class="">
    <flux:modal name="business-modal">
        <form wire:submit='save' class="mt-4 space-y-4 md:min-w-lg">
            <div class="mt-4 text-center text-lg font-semibold">
                {{ $id ? 'Edit Business' : 'Add New Business' }}
            </div>
            <flux:separator text="Account Info"></flux:separator>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Email</div>
                <div class="w-2/3">: {{ $business?->user?->email }}</div>
            </div>
            <div class="flex gap-4 text-start items-center">
                <div class="w-1/3">Status</div>
                <div class="w-2/3 flex items-center"> :
                    <flux:select wire:model.live="status">
                        <flux:select.option value="" disabled selected>-- Choose status --</flux:select.option>
                        <flux:select.option value="approved">Approved</flux:select.option>
                        <flux:select.option value="rejected">Rejected</flux:select.option>
                    </flux:select>
                </div>
            </div>
            <flux:separator text="Business Info"></flux:separator>
            <div class="flex gap-4 text-start items-center">
                <div class="w-1/3">Business Name</div>
                <div class="w-2/3 flex items-center">
                    :
                    <flux:input wire:model.live="name" />
                </div>
            </div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Registered Number</div>
                <div class="w-2/3">: {{ $business?->npwp }}</div>
            </div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Registered Address</div>
                <div class="w-2/3">: {{ $business?->address }}</div>
            </div>

            <flux:separator text="Bank Info"></flux:separator>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Bank</div>
                <div class="w-2/3">: {{ $business?->bank }}</div>
            </div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Account Number</div>
                <div class="w-2/3">: {{ $business?->account_number }}</div>
            </div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Account Name</div>
                <div class="w-2/3">: {{ $business?->account_name }}</div>
            </div>

            <flux:separator text="Representative"></flux:separator>
            <div class="aspect-video bg-center bg-no-repeat bg-cover rounded"
                style="background-image: url('{{ asset('storage/' . $business?->id_card) }}')"></div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Representative</div>
                <div class="w-2/3">: {{ $business?->representative }}</div>
            </div>
            <div class="flex gap-4 text-start">
                <div class="w-1/3">Phone Number</div>
                <a target="_blank" href="https://wa.me/{{ $business?->phone }}" class="w-2/3">:
                    {{ $business?->phone }}</a>
            </div>

            <flux:separator text="Tenor">
            </flux:separator>
            <div class="space-y-4">
                <flux:select label=" Tenor" required wire:model.live='tenor'>
                    <flux:select.option value="0">Pay after ordering</flux:select.option>
                    <flux:select.option value="7">7 Days</flux:select.option>
                    <flux:select.option value="14">14 Days</flux:select.option>
                    <flux:select.option value="30">30 Days</flux:select.option>
                </flux:select>
                <flux:input label="Minimum Order (Rp)" type="number" wire:model.live='minimum_order' />
            </div>

            <div class="flex justify-center flex-wrap gap-4">
                <flux:button variant="primary" type="submit">Submit</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="sync-customer-modal">

    </flux:modal>
</div>
