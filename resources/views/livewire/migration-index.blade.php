<div class="space-y-4">
    <flux:session>{{ $title }}</flux:session>
    <flux:container-sidebar>
        <div class="flex gap-4 py-2 text-sm font-semibold">
            <div class="">#</div>
            <div class="w-1/4">Process</div>
            <div class="w-1/4 text-center">Status</div>
            <div class="w-1/4 text-center">Runned_at</div>
            <div class="w-1/4 text-center">Action</div>
        </div>
        @foreach ($migrations as $key => $item)
            <div class="flex gap-4 py-1">
                <div class="">{{ $key + 1 }}</div>
                <div class="w-1/4">{{ $item->process }}</div>
                <div class="w-1/4 text-center">{{ $item->status ? 'Migrated' : 'Unmigrated' }}</div>
                <div class="w-1/4 text-center">{{ $item->running_at?->format('d M Y H:i') }}</div>
                <div class="w-1/4 text-center flex justify-center">
                    @if (!$item->status)
                        <flux:button variant="primary" size="sm"
                            wire:click='runningMigration({{ $item->id }})'>Running {{ $item->id }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @endforeach
    </flux:container-sidebar>
</div>
