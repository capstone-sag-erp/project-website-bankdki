<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ProductType;
use App\Models\KpiSalesMonthly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Total Revenue & Transaksi (tahun ini)
        $totalRevenue = Transaction::whereYear('transaction_date', $year)
            ->where('status', 'completed')
            ->sum('amount');

        $totalTransactions = Transaction::whereYear('transaction_date', $year)
            ->where('status', 'completed')
            ->count();

        // Produk Terlaris (tahun ini)
        $topProducts = Transaction::with('productType')
            ->selectRaw('product_type_id, SUM(amount) as total_revenue, COUNT(*) as total_count')
            ->whereYear('transaction_date', $year)
            ->where('status', 'completed')
            ->groupBy('product_type_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Grafik Trend per Bulan (12 bulan)
        $trendMonthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = Transaction::whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $m)
                ->where('status', 'completed')
                ->sum('amount');
            
            $trendMonthly[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'revenue' => (float) $revenue,
            ];
        }

        // Grafik Kontribusi per Produk (Pie Chart)
        $productContribution = Transaction::with('productType')
            ->selectRaw('product_type_id, SUM(amount) as total')
            ->whereYear('transaction_date', $year)
            ->where('status', 'completed')
            ->groupBy('product_type_id')
            ->get()
            ->map(function($item) {
                return [
                    'product' => $item->productType->name ?? 'Unknown',
                    'total' => (float) $item->total,
                ];
            });

        // Tabel Detail KPI per Bulan/Produk
        $kpiDetails = KpiSalesMonthly::with('productType')
            ->where('year', $year)
            ->orderBy('month')
            ->orderBy('product_type_id')
            ->get();

        // Progress vs Target (aggregate)
        $totalTarget = KpiSalesMonthly::where('year', $year)->sum('target_revenue');
        $progressPercentage = $totalTarget > 0 ? ($totalRevenue / $totalTarget) * 100 : 0;

        return view('kpi.index', compact(
            'totalRevenue',
            'totalTransactions',
            'topProducts',
            'trendMonthly',
            'productContribution',
            'kpiDetails',
            'totalTarget',
            'progressPercentage',
            'year'
        ));
    }

    // Update KPI Monthly (untuk manager)
    public function updateKpiMonthly(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'product_type_id' => 'required|exists:product_types,id',
            'target_revenue' => 'nullable|numeric|min:0',
        ]);

        // Hitung actual dari transactions
        $actualRevenue = Transaction::whereYear('transaction_date', $request->year)
            ->whereMonth('transaction_date', $request->month)
            ->where('product_type_id', $request->product_type_id)
            ->where('status', 'completed')
            ->sum('amount');

        $actualCount = Transaction::whereYear('transaction_date', $request->year)
            ->whereMonth('transaction_date', $request->month)
            ->where('product_type_id', $request->product_type_id)
            ->where('status', 'completed')
            ->count();

        KpiSalesMonthly::updateOrCreate(
            [
                'year' => $request->year,
                'month' => $request->month,
                'product_type_id' => $request->product_type_id,
            ],
            [
                'total_revenue' => $actualRevenue,
                'total_transactions' => $actualCount,
                'target_revenue' => $request->target_revenue,
            ]
        );

        return redirect()->back()->with('success', 'KPI berhasil diperbarui.');
    }
}
