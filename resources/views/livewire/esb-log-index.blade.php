<div class="space-y-4">
    <flux:container-sidebar>
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <flux:session>{{ $title }}</flux:session>

                <div class="flex items-center gap-2">
                    <flux:button icon="arrow-path" wire:click="$refresh">
                        Refresh
                    </flux:button>
                    <flux:button icon="x-mark" variant="danger" wire:click="resetFilters">
                        Reset
                    </flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Total Logs</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ number_format($stats['total']) }}</div>
                </div>
                <div
                    class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/30">
                    <div class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Success</div>
                    <div class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-200">
                        {{ number_format($stats['success']) }}</div>
                </div>
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950/30">
                    <div class="text-xs font-medium uppercase text-red-700 dark:text-red-300">Failed</div>
                    <div class="mt-2 text-2xl font-semibold text-red-800 dark:text-red-200">
                        {{ number_format($stats['failed']) }}</div>
                </div>
                <div
                    class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950/30">
                    <div class="text-xs font-medium uppercase text-amber-700 dark:text-amber-300">Retried</div>
                    <div class="mt-2 text-2xl font-semibold text-amber-800 dark:text-amber-200">
                        {{ number_format($stats['retried']) }}</div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                    <div class="md:col-span-2">
                        <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass"
                            placeholder="Search URL, body, response, error..." />
                    </div>

                    <flux:select wire:model.live="method" placeholder="All Methods">
                        <flux:select.option value="">All Methods</flux:select.option>
                        @foreach ($filterOptions['methods'] as $option)
                            <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="success" placeholder="All Status">
                        <flux:select.option value="">All Status</flux:select.option>
                        <flux:select.option value="1">Success</flux:select.option>
                        <flux:select.option value="0">Failed</flux:select.option>
                    </flux:select>

                    <flux:select wire:model.live="request_type" placeholder="All Types">
                        <flux:select.option value="">All Types</flux:select.option>
                        @foreach ($filterOptions['request_types'] as $option)
                            <flux:select.option value="{{ $option }}">{{ str($option)->headline() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="perPage" placeholder="Per Page">
                        <flux:select.option value="10">10 / page</flux:select.option>
                        <flux:select.option value="25">25 / page</flux:select.option>
                        <flux:select.option value="50">50 / page</flux:select.option>
                        <flux:select.option value="100">100 / page</flux:select.option>
                    </flux:select>
                </div>

                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-5">
                    <flux:select wire:model.live="retried_with_refresh" placeholder="Any Retry">
                        <flux:select.option value="">Any Retry</flux:select.option>
                        <flux:select.option value="1">Retried</flux:select.option>
                        <flux:select.option value="0">Not Retried</flux:select.option>
                    </flux:select>

                    <flux:select wire:model.live="request_source" placeholder="All Sources">
                        <flux:select.option value="">All Sources</flux:select.option>
                        @foreach ($filterOptions['request_sources'] as $option)
                            <flux:select.option value="{{ $option }}">{{ str($option)->headline() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model.live.debounce.400ms="user_id" type="number" placeholder="User ID" />
                    <flux:input wire:model.live="created_after" type="date" />
                    <flux:input wire:model.live="created_before" type="date" />
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800">
                <table
                    class="w-full min-w-[1100px] divide-y divide-gray-200 border border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700/60">
                        <tr>
                            <th class="w-16 px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                ID</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Request</th>
                            <th
                                class="w-28 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Status</th>
                            <th
                                class="w-24 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Code</th>
                            <th
                                class="w-36 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Type</th>
                            <th
                                class="w-28 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Source</th>
                            <th
                                class="w-28 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                User</th>
                            <th
                                class="w-44 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Created</th>
                            <th
                                class="w-20 px-4 py-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Detail</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    #{{ $log->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="inline-flex min-w-14 justify-center rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-xs font-semibold text-gray-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
                                            {{ $log->method }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                                title="{{ $log->request_url }}">
                                                {{ $log->request_url }}
                                            </div>
                                            @if ($log->error_message)
                                                <div class="mt-1 truncate text-xs text-red-600 dark:text-red-300"
                                                    title="{{ $log->error_message }}">
                                                    {{ $log->error_message }}
                                                </div>
                                            @endif
                                            @if ($log->retried_with_refresh)
                                                <div
                                                    class="mt-1 text-xs font-medium text-amber-700 dark:text-amber-300">
                                                    Retried with refresh token</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($log->success)
                                        <span
                                            class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Success</span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-300">Failed</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ $log->response_code ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ str($log->request_type)->headline() }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ str($log->request_source)->headline() }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ $log->user_id ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button icon="eye" size="sm" variant="primary"
                                        wire:click="selectLog({{ $log->id }})"></flux:button>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="9"
                                    class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-300">
                                    No ESB logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <flux:modal name="esb-log-modal" class="w-[92vw] max-w-5xl">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">ESB Log
                                #{{ $selectedLog?->id }}</div>
                            <div class="mt-1 break-all text-sm text-gray-600 dark:text-gray-300">
                                {{ $selectedLog?->request_url }}</div>
                        </div>
                        <div class="shrink-0">
                            @if ($selectedLog?->success)
                                <span
                                    class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Success</span>
                            @else
                                <span
                                    class="inline-flex rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700 dark:bg-red-950 dark:text-red-300">Failed</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Method</div>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                {{ $selectedLog?->method }}</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Response Code</div>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                {{ $selectedLog?->response_code ?? '-' }}</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Expired At</div>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($selectedLog?->expired_at)->format('d M Y H:i') }}</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Created At</div>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($selectedLog?->created_at)->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    @if ($selectedLog?->error_message)
                        <div
                            class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200">
                            {{ $selectedLog?->error_message }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <div class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Request Body</div>
                            <pre
                                class="max-h-96 overflow-auto rounded-lg border border-gray-200 bg-gray-950 p-4 text-xs text-gray-100 dark:border-gray-700">{{ $this->formatJson($selectedLog?->request_body) }}</pre>
                        </div>
                        <div>
                            <div class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Response Body
                            </div>
                            <pre
                                class="max-h-96 overflow-auto rounded-lg border border-gray-200 bg-gray-950 p-4 text-xs text-gray-100 dark:border-gray-700">{{ $this->formatJson($selectedLog?->response_body) }}</pre>
                        </div>
                    </div>
                </div>
            </flux:modal>

            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </flux:container-sidebar>
</div>
