<div class="space-y-4">
    <flux:session>{{ $title }}</flux:session>
    <flux:container-sidebar>
        <div class="flex justify-end">
            <flux:button size="sm" wire:click='openCreateModal' variant="primary">
                Add Promotion
            </flux:button>
        </div>
        <div class="flex w-full mt-4 gap-4 py-2 font-semibold">
            <div class="w-10">#</div>
            <div class="w-1/4">Title</div>
            <div class="w-1/4 text-center">Description</div>
            <div class="w-1/4 text-center">Order</div>
            <div class="w-1/4 text-center">active?</div>
            <div class="w-1/4 text-center">Action</div>
        </div>
        @foreach ($promotions as $key => $item)
            <div class="flex w-full items-center gap-4 py-2">
                <div class="w-10">{{ $key + 1 }}</div>
                <div class="w-1/4">{{ $item->title }}</div>
                <div class="w-1/4 text-center ">{{ $item->description }}</div>
                <div class="w-1/4 text-center ">{{ $item->order }}</div>
                <div class="w-1/4 text-center ">{{ $item->is_active ? 'Yes' : 'No' }}</div>
                <div class="w-1/4 text-center flex justify-center gap-4">
                    <flux:button size="sm" variant="primary" color="amber"
                        wire:click="openEditModal({{ $item->id }})">
                        Edit
                    </flux:button>
                    <flux:modal.trigger name="delete-promotion-{{ $item->id }}">
                        <flux:button size="sm" variant="danger">Delete
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
            <flux:modal name="delete-promotion-{{ $item->id }}">
                <div class="text-lg font-semibold">Delete Promotion</div>
                <div class="mt-2">
                    Are you sure you want to delete the promotion "{{ $item->title }}"?
                </div>
                <div class="flex justify-end gap-4 mt-4">
                    <flux:modal.close>
                        <flux:button size="sm" variant="primary" color="gray">Cancel
                        </flux:button>
                    </flux:modal.close>
                    <flux:button size="sm" variant="danger" wire:click="delete({{ $item->id }})">Delete
                    </flux:button>
                </div>

            </flux:modal>
        @endforeach
    </flux:container-sidebar>
    @livewire('promotion-create')
</div>
