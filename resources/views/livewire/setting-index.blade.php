<div class="space-y-4">
    <flux:session>{{ $title }}</flux:session>
    <flux:container-sidebar>
        <form wire:submit='save'>
            <div class="grid grid-cols-1 gap-4 mt-4">

                {{-- @dd($state) --}}
                @foreach ($state['settings'] as $key => $item)
                    <flux:input wire:model.live="state.settings.{{ $key }}.value" label="{{ $item['key'] }}"
                        type="{{ $item['type'] }}" />
                @endforeach
            </div>

            <div class="flex justify-center mt-4">
                <flux:button type="submit" variant="primary">Submit</flux:button>
            </div>
        </form>
    </flux:container-sidebar>
</div>
