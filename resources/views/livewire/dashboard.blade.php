<div class="space-y-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded dark:bg-gray-700 p-4 bg-gray-50">
            <div class="p-1 rounded-full bg-gray-100 dark:bg-gray-800 w-fit">
                <flux:icon.dollar-sign class="size-10!"></flux:icon.dollar-sign>
            </div>
            <div class="mt-5 md:text-xl font-semibold">
                Rp. {{ number_format($earningThisMonth, 0, ',', '.') }}
            </div>
            <div class=" {{ $earningRatio < 0 ? 'text-rose-500' : 'text-green-400' }}">
                {{ number_format($earningRatio, 0, ',', '.') }}% {{ $earningRatio < 0 ? 'less' : 'more' }} from last month
            </div>
        </div>
        <div class="rounded dark:bg-gray-700 p-4 bg-sky-50">
            <div class="p-1 rounded-full bg-gray-100 dark:bg-gray-800 w-fit">
                <flux:icon.dollar-sign class="size-10!"></flux:icon.dollar-sign>
            </div>
            <div class="mt-5 md:text-xl font-semibold">
                {{ number_format($transactionThisMonth, 0, ',', '.') }}
            </div>
            <div class=" {{ $transactionRatio < 0 ? 'text-rose-500' : 'text-green-400' }}">
                {{ number_format($transactionRatio, 0, ',', '.') }}% {{ $transactionRatio < 0 ? 'less' : 'more' }} from last month
            </div>
        </div>
        <div class="rounded dark:bg-gray-700 p-4 bg-teal-50">
            <div class="p-1 rounded-full bg-gray-100 dark:bg-gray-800 w-fit">
                <flux:icon.dollar-sign class="size-10!"></flux:icon.dollar-sign>
            </div>
            <div class="mt-5 md:text-xl font-semibold">
                {{ number_format($productSoldThisMonth, 0, ',', '.') }}
            </div>
            <div class=" {{ $productSoldRatio < 0 ? 'text-rose-500' : 'text-green-400' }}">
                {{ number_format($productSoldRatio, 0, ',', '.') }}% {{ $productSoldRatio < 0 ? 'less' : 'more' }} from last month
            </div>
        </div>
        <div class="rounded dark:bg-gray-700 p-4 bg-fuchsia-50">
            <div class="p-1 rounded-full bg-gray-100 dark:bg-gray-800 w-fit">
                <flux:icon.users class="size-10!"></flux:icon.users>
            </div>
            <div class="mt-5 md:text-xl font-semibold">
                {{ number_format($newUserThisMonth, 0, ',', '.') }}
            </div>
            <div class=" {{ $newUserRatio < 0 ? 'text-rose-500' : 'text-green-400' }}">
                {{ number_format($newUserRatio, 0, ',', '.') }}% {{ $newUserRatio < 0 ? 'less' : 'more' }} from last month
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-700 min-h-96! space-y-4 p-4 rounded">
        <div class="text-lg md:text-xl font-semibold">Last Transaction</div>

        <div class="grid-cols-1 md:grid-cols-3 mt-4 grid gap-4">
            @foreach ($lastTransactions as $item)
                <div class="bg-gray-200 dark:bg-gray-700 rounded p-4 gap-4" x-data={open:false}>
                    <div class="">
                        <div class="sr-only">Shipping Information</div>
                        <div class="">
                            <div class="text-sm md:text-md font-semibold">{{ $item->shipping->name }} /
                                {{ $item->shipping->phone }}</div>
                            <div class="mt-2 text-xs md:text-sm">{{ $item->shipping->address }}</div>
                        </div>
                        <flux:separator></flux:separator>
                        <div class="flex justify-between items-center">
                            <div class="">Total</div>
                            <div class="">Rp. {{ number_format($item->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-700 space-y-4 p-4 rounded">
            <div class="text-lg md:text-xl font-semibold">Top Sold Products</div>
            @foreach ($topProducts as $item)
                <div
                    class="py-2 bg-gradient-to-r flex justify-between from-amber-300 to-75% to-transparent dark:from-gray-900 px-4 rounded-lg w-[{{ ($item->total_sales / $topProducts->first()->total_sales) * 100 }}%] ">
                    <div class="">{{ $item->product->name }}</div>
                    <div class="">
                        Rp. {{ number_format($item->total_sales, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-span-2 bg-white dark:bg-gray-700 space-y-4 p-4 rounded">
            <div class="text-lg md:text-xl font-semibold">Earnings Growth</div>
            <canvas id="earningChart" class="h-72!"></canvas>
        </div>
        <div class="space-y-4 bg-white dark:bg-gray-700 p-4 rounded">
            <div class="text-lg md:text-xl font-semibold">Top Month</div>
            <div class="text-2xl md:text-3xl font-bold">{{ $topMonthName }}</div>
            <div class="text-lg md:text-xl font-semibold">Top Year</div>
            <div class="text-2xl md:text-3xl font-bold">{{ $topYear?->year }}</div>


        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById('earningChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData->pluck('month')),
                datasets: [{
                    label: 'Total Penjualan',
                    data: @json($chartData->pluck('total')),
                    borderColor: 'rgba(37, 99, 235, 1)',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
