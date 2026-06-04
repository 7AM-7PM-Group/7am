<flux:container
    class="mt-32 mb-8 min-h-[55vh] bg-white dark:bg-gray-700 py-8 px-4 md:px-8 lg:px-16 rounded-xl shadow-md w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Business Info') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your business information and settings') }}
        </flux:subheading>
    </div>


    @if (session()->has('success'))
        <div class="text-green-500 font-semibold text-sm">{{ session('success') }}</div>
    @endif
    <form wire:submit='save'>
        <div class="mt-10">
            <flux:separator text="Business Identity">
            </flux:separator>
            <div class="space-y-4">
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Business Name/Outlet Name" required wire:model.live='name'></flux:input>
                <flux:input only_number :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Business Registration Number" placeholder="NPWP, Tax ID, VAT, or Government-issued number"
                    required wire:model.live='npwp'>
                </flux:input>
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])" label="Address"
                    required wire:model.live='address'></flux:input>
            </div>
        </div>
        <div class="mt-12">
            <flux:separator text="Bank Information">
            </flux:separator>
            <div class="space-y-4">
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])" label="Bank Name"
                    required wire:model.live='bank'></flux:input>
                <flux:input only_number :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Account Number" required wire:model.live='account_number'></flux:input>
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])" label="Account Name"
                    required wire:model.live='account_name'></flux:input>
            </div>
        </div>

        <div class="mt-12">
            <flux:separator text="Representative">
            </flux:separator>
            <div class="space-y-4">
                <div class="w-full sm:w-80">
                    <flux:input aspect="16/10" type="file" label="Representative ID Card"
                        preview="{{ $preview }}" required wire:model.live='id_card'
                        :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])">
                    </flux:input>
                </div>
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Representative Name" required wire:model.live='representative'></flux:input>
                <flux:input :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Representative Phone Number" only_number required wire:model.live='phone'></flux:input>
            </div>
        </div>
        <div class="mt-12">
            <flux:separator text="Tenor">
            </flux:separator>
            <div class="space-y-4">
                <flux:select :readonly="in_array(Auth::user()->business, ['requested', 'accepted'])"
                    label="Request Tenor" required wire:model.live='tenor'>
                    <flux:select.option value="0">Pay after ordering</flux:select.option>
                    <flux:select.option value="7">7 Days</flux:select.option>
                    <flux:select.option value="14">14 Days</flux:select.option>
                    <flux:select.option value="30">30 Days</flux:select.option>
                </flux:select>
            </div>
        </div>
        @if (!in_array(Auth::user()->business, ['requested', 'accepted']))
            <div class="mt-4">
                <flux:button type="submit" variant="primary">Submit</flux:button>
            </div>
        @endif
    </form>
</flux:container>
