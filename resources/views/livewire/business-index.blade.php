<div class="space-y-4">
    <div class="flex justify-start space-x-2">
        <flux:session>{{ $title }}</flux:session>
        {{-- <flux:button icon="plus" variant="primary" wire:click="$dispatch('openCreateBusinessModal')">
            Add Business
        </flux:button> --}}
    </div>

    @if (session('Success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">
            {{ session('Success') }}
        </div>
    @endif

    <flux:container-sidebar>
        <div class="flex gap-4">
            <div class="w-3/5">
                <flux:input wire:model.live='search' placeholder="Search a business ....">
                </flux:input>
            </div>
            <div class="w-1/5">
                <flux:select wire:model.live='sts'>
                    <flux:select.option value=''>All Status
                    </flux:select.option>
                    <flux:select.option value='pending'>Requested</flux:select.option>
                    <flux:select.option value='accepted'>Accepted</flux:select.option>
                    <flux:select.option value='rejected'>Rejected</flux:select.option>
                </flux:select>
            </div>
            <div class="w-1/4">
                <flux:button class="w-full!" wire:click='resetFilter' variant="danger">Reset</flux:button>
            </div>
        </div>
        <div class="space-y-2 mt-4 min-w-3xl">
            <div class="flex gap-4 py-2">
                <div class="w-10">#</div>
                <div class="w-1/6">Business Name</div>
                <div class="w-1/6 text-center">Registered Number</div>
                <div class="w-1/6 text-center">Set Category</div>
                <div class="w-1/6 text-center">Status</div>
                <div class="w-1/6 text-center">Tenor</div>
                <div class="w-1/6 text-center">Action</div>
            </div>
            @foreach ($businesses as $item)
                <div class="flex gap-4">
                    <div class="w-10">{{ $loop->iteration }}</div>
                    <div class="w-1/6">{{ $item->name }}</div>
                    <div class="w-1/6 text-center">{{ $item->npwp }}</div>
                    <div class="w-1/6 text-center">{{ $item->setCategory?->name ?? '' }}</div>

                    <div class="w-1/6 text-center">
                        {{ $item->status }}
                    </div>
                    <div class="w-1/6 text-center">
                        {{ $item->tenor == 0 ? 'Pay after ordering' : $item->tenor . ' Days' }}</div>
                    <div class="w-1/6 text-center flex gap-2 justify-center">
                        <flux:tooltip content="Edit Business">
                            <flux:button
                                wire:click="$dispatch('openEditBusinessModal', { id: {{ $item->id }} }).global"
                                icon="pencil-square" size="sm" variant="primary" color="amber">
                            </flux:button>
                        </flux:tooltip>

                        <flux:tooltip content="See Details">
                            <flux:button variant="primary" wire:click='openDetailModal({{ $item->id }})'
                                icon="eye" size="sm"></flux:button>
                        </flux:tooltip>
                        @if ($item->status == 'approved' && $item->customerID)
                            <flux:tooltip content="Update Customer in ESB">
                                <flux:button color="orange" variant="primary"
                                    wire:click='updateCustomerInESB({{ $item->id }})' icon="cloud-backup"
                                    size="sm"></flux:button>
                            </flux:tooltip>
                            <flux:tooltip content="Update Customer from ESB">
                                <flux:button color="blue" variant="primary"
                                    wire:click='updateCustomerFromESB({{ $item->id }})' icon="cloud-download"
                                    size="sm"></flux:button>
                            </flux:tooltip>
                        @elseif($item->status == 'approved' && !$item->customerID)
                            <flux:tooltip content="Import Customer to ESB">
                                <flux:button color="orange" variant="primary"
                                    wire:click='importCustomerToESB({{ $item->id }})' icon="cloud-upload"
                                    size="sm"></flux:button>
                            </flux:tooltip>
                            <flux:tooltip content="Sync Customer from ESB">
                                <flux:button color="blue" variant="primary"
                                    wire:click='syncCustomerFromESB({{ $item->id }})' icon="cloud-sync"
                                    size="sm"></flux:button>
                            </flux:tooltip>
                        @endif

                    </div>
                </div>
            @endforeach
            <flux:modal name="detail-business">
                <div class="mt-4 space-y-4 md:min-w-lg">
                    <div class="">
                        Detail of Business
                    </div>

                    <flux:separator text="Account Info"></flux:separator>
                    <div class="flex gap-4 text-start">
                        <div class="w-1/3">Email</div>
                        <div class="w-2/3">: {{ $business?->user?->email }}</div>
                    </div>
                    <div class="flex gap-4 text-start">
                        <div class="w-1/3">Status</div>
                        <div class="w-2/3">: {{ $business?->status }}</div>
                    </div>

                    <flux:separator text="Business Info"></flux:separator>
                    <div class="flex gap-4 text-start">
                        <div class="w-1/3">Business Name</div>
                        <div class="w-2/3">: {{ $business?->name }}</div>
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
                    <flux:separator text="Tenor"></flux:separator>
                    <div class="flex gap-4 text-start">
                        <div class="w-1/3">Tenor</div>
                        <div class="w-2/3">:
                            {{ $business?->tenor == 0 ? 'Pay after ordering' : $business->tenor . ' Days' }}
                        </div>
                    </div>
                </div>
            </flux:modal>
        </div>
    </flux:container-sidebar>

    @livewire('business-modal')
    @livewire('business-sync-from-esb')
</div>
