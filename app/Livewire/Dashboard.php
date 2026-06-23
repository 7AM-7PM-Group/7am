<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $title = 'B2B Dashboard';

    public $earningThisMonth;

    public $earningRatio;

    public $transactionThisMonth;

    public $transactionRatio;

    public $productSoldThisMonth;

    public $productSoldRatio;

    public $newUserThisMonth;

    public $newUserRatio;

    public $topProducts;

    public $topProduct;

    public $chartData;

    public $topMonthName;

    public $topYear;

    public $lastTransactions;

    public function mount()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Bulan ini
        $this->earningThisMonth = Transaction::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total');

        // Bulan lalu
        $totalLastMonth = Transaction::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('total');

        // Hitung persentase perubahan
        if ($totalLastMonth > 0) {
            $this->earningRatio = (($this->earningThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
        } else {
            $this->earningRatio = 0;
        }

        $this->transactionThisMonth = Transaction::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        // Bulan lalu
        $totalLastMonth = Transaction::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        // Hitung persentase perubahan
        if ($totalLastMonth > 0) {
            $this->transactionRatio = (($this->transactionThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
        } else {
            $this->transactionRatio = 0;
        }

        $this->productSoldThisMonth = TransactionItem::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('qty');

        // Bulan lalu
        $totalLastMonth = TransactionItem::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('qty');

        // Hitung persentase perubahan
        if ($totalLastMonth > 0) {
            $this->productSoldRatio = (($this->productSoldThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
        } else {
            $this->productSoldRatio = 0;
        }

        $this->newUserThisMonth = User::where('role', 'customer')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        // Bulan lalu
        $totalLastMonth = User::where('role', 'customer')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        // Hitung persentase perubahan
        if ($totalLastMonth > 0) {
            $this->newUserRatio = (($this->newUserThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
        } else {
            $this->newUserRatio = 0;
        }

        // --- Top 4 Produk Berdasarkan Total Penjualan ---
        // --- Top 4 Produk Berdasarkan Total Penjualan ---
        $this->topProducts = TransactionItem::select(
            'product_id',
            DB::raw('SUM(qty * price) as total_sales')
        )
            ->with('product:id,productName')
            ->groupBy('product_id')
            ->orderByDesc('total_sales')
            ->take(4)
            ->get();

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $monthFormat = "strftime('%Y-%m', created_at)";
        } else {
            $monthFormat = "DATE_FORMAT(created_at, '%Y-%m')";
        }

        $earnings = DB::table('transactions')
            ->select(
                DB::raw("$monthFormat as month"),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Siapkan array lengkap 12 bulan terakhir (kalau ada bulan tanpa transaksi)
        $months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths(11 - $i)->format('Y-m');
        });

        $this->chartData = $months->map(fn ($m) => [
            'month' => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'),
            'total' => $earnings[$m] ?? 0,
        ]);

        $topMonth = Transaction::selectRaw(
            Transaction::yearFormat().' as year, '.
                Transaction::onlyMonthFormat().' as month, '.
                'SUM(total) as total'
        )
            ->groupBy('year', 'month')
            ->orderByDesc('total')
            ->first();

        if ($topMonth) {
            $topMonth->name = Carbon::create($topMonth->year, $topMonth->month, 1)->translatedFormat('F Y');
        }

        $this->topMonthName = $topMonth?->name;

        // === Top Year (Tahun dengan Penjualan Tertinggi Sepanjang Waktu) ===
        $this->topYear = Transaction::selectRaw(
            Transaction::yearFormat().' as year, '.'
                SUM(total) as total'
        )
            ->groupBy('year')
            ->orderByDesc('total')
            ->first();

        $this->lastTransactions = Transaction::latest() // urut dari yang terbaru
            ->take(9)  // ambil 8 transaksi terakhir
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
