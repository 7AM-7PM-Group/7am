<div class="space-y-4">
    <flux:session>{{ $title }}</flux:session>
    <flux:container-sidebar class="">
        <div class="flex justify-end">
            <flux:button variant="primary" icon="plus" wire:click="$dispatch('createModal').global">Add Admin
            </flux:button>
        </div>
        <div class="mt-4">
            <div class="flex gap-4 min-w-2xl font-semibold py-2">
                <div class="">#</div>
                <div class="w-1/5">Name</div>
                <div class="w-1/5 text-center">Email</div>
                <div class="w-1/5 text-center">Role</div>
                <div class="w-1/5 text-center">Verified</div>
                <div class="w-1/5 text-center">Action</div>
            </div>

            @foreach ($admins as $item)
                <div class="flex gap-4 min-w-2xl py-2">
                    <div class="">{{ $loop->iteration }}</div>
                    <div class="w-1/5">{{ $item->name }}</div>
                    <div class="w-1/5 text-center">{{ $item->email }}</div>
                    <div class="w-1/5 text-center">{{ $item->role }}</div>
                    <div class="w-1/5 text-center flex gap-2 justify-center">
                        @if ($item->email_verified_at)
                            <flux:icon icon="check-check"></flux:icon>
                        @else
                            <flux:icon icon="x"></flux:icon>
                        @endif
                    </div>
                    <div class="w-1/5 text-center flex gap-2 justify-center">
                        <flux:tooltip content="Edit Admin">
                            <flux:button size="sm" icon='pencil-square' variant="primary" color="amber"
                                wire:click="$dispatch('editModal', { id: {{ $item->id }} }).global"></flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="Delete Outlet">
                            <flux:modal.trigger name="delete-{{ $item->id }}">
                                <flux:button size="sm" icon='trash' variant="danger"></flux:button>
                            </flux:modal.trigger>
                        </flux:tooltip>
                    </div>
                </div>
                <flux:modal name="delete-{{ $item->id }}">
                    <div class="font-semibold ">Delete admin {{ $item->name }}</div>
                    <div class="">This action will permanently remove {{ $item->name }}.</div>
                    <div class="flex mt-4 justify-end">
                        <flux:button wire:click='delete({{ $item->id }})' variant="danger">Delete
                        </flux:button>
                    </div>
                </flux:modal>
            @endforeach
        </div>
    </flux:container-sidebar>
    @livewire('admin-create')
</div>
