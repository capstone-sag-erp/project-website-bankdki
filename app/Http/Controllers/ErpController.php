<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaction;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErpController extends Controller
{
    // ==================== NASABAH ====================
    public function indexNasabah(Request $request)
    {
        $query = Nasabah::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('no_ktp', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $nasabah = $query->latest()->paginate(15);
        return view('erp.nasabah.index', compact('nasabah'));
    }

    public function createNasabah()
    {
        return view('erp.nasabah.create');
    }

    public function storeNasabah(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:nasabah,no_ktp',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'tanggal_daftar' => 'required|date',
        ]);

        Nasabah::create($request->all());

        return redirect()->route('erp.nasabah.index')->with('success', 'Data nasabah berhasil ditambahkan.');
    }

    public function showNasabah(Nasabah $nasabah)
    {
        $nasabah->load(['transactions.productType', 'crmLeads', 'tikets']);
        return view('erp.nasabah.show', compact('nasabah'));
    }

    public function editNasabah(Nasabah $nasabah)
    {
        return view('erp.nasabah.edit', compact('nasabah'));
    }

    public function updateNasabah(Request $request, Nasabah $nasabah)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:nasabah,no_ktp,'.$nasabah->id_nasabah.',id_nasabah',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'tanggal_daftar' => 'required|date',
        ]);

        $nasabah->update($request->all());

        return redirect()->route('erp.nasabah.show', $nasabah->id_nasabah)->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function destroyNasabah(Nasabah $nasabah)
    {
        $nasabah->delete();
        return redirect()->route('erp.nasabah.index')->with('success', 'Data nasabah berhasil dihapus.');
    }

    // ==================== TRANSAKSI ====================
    public function indexTransaksi(Request $request)
    {
        $query = Transaction::with(['nasabah', 'productType']);

        if ($request->filled('search')) {
            $query->whereHas('nasabah', function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);
        $productTypes = ProductType::all();

        return view('erp.transaksi.index', compact('transactions', 'productTypes'));
    }

    public function createTransaksi()
    {
        $nasabah = Nasabah::all();
        $productTypes = ProductType::all();
        return view('erp.transaksi.create', compact('nasabah', 'productTypes'));
    }

    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id_nasabah',
            'product_type_id' => 'required|exists:product_types,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'status' => 'required|in:completed,pending,cancelled',
            'notes' => 'nullable|string',
        ]);

        Transaction::create($request->all());

        return redirect()->route('erp.transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function editTransaksi(Transaction $transaction)
    {
        $nasabah = Nasabah::all();
        $productTypes = ProductType::all();
        return view('erp.transaksi.edit', compact('transaction', 'nasabah', 'productTypes'));
    }

    public function updateTransaksi(Request $request, Transaction $transaction)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabah,id_nasabah',
            'product_type_id' => 'required|exists:product_types,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'status' => 'required|in:completed,pending,cancelled',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($request->all());

        return redirect()->route('erp.transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroyTransaksi(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('erp.transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    // ==================== REKAP & ANALISIS ====================
    public function rekap(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Total revenue & transaksi
        $totalRevenue = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('status', 'completed')
            ->sum('amount');

        $totalTransactions = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('status', 'completed')
            ->count();

        // Produk terlaris
        $topProducts = Transaction::with('productType')
            ->selectRaw('product_type_id, SUM(amount) as total, COUNT(*) as count')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('status', 'completed')
            ->groupBy('product_type_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Trend per bulan (12 bulan terakhir)
        $trendData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Transaction::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $trendData[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return view('erp.rekap', compact('totalRevenue', 'totalTransactions', 'topProducts', 'trendData', 'year', 'month'));
    }
}
