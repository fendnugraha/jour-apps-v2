<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\AccountTrace;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class AccountTraceController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        return view('home.index', [
            'title' => 'Home',
            'subtitle' => 'Home',
            'warehouseaccount' => ChartOfAccount::whereIn('account_id', ['1', '2'])->where('warehouse_id', Auth()->user()->warehouse_id)->get(),
            'accounttrace' => AccountTrace::with('debt', 'cred', 'sale')->whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', Auth()->user()->warehouse_id)->get(),
            'hqaccount' => ChartOfAccount::whereIn('account_id', ['1', '2'])->where('warehouse_id', 1)->get(),
            'product' => Product::all(),
        ]);
    }

    public function administrator()
    {
        if (Auth()->user()->role !== "Administrator") {
            return abort(403, 'Unauthorized action.');
        }

        $accountTrace = new AccountTrace();
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $transactions = $accountTrace
            ->selectRaw('debt_code, cred_code, SUM(amount) as total')
            ->whereBetween('date_issued', [Carbon::create(0000, 1, 1, 0, 0, 0)->startOfDay(), $endDate])
            ->groupBy('debt_code', 'cred_code')
            ->get();

        $chartOfAccounts = ChartOfAccount::with(['account'])->get();

        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            // @ts-ignore
            $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        }

        $totalTransfer = [];
        $sumtotalTransfer = 0;
        $sumtotalTarikTunai = 0;
        $sumtotalVcr = 0;
        $sumtotaldeposit = 0;
        $sumfee = 0;
        $sumtotalCash = 0;
        $sumtotalBank = 0;
        $sumendbalance = 0;

        $warehouse = Warehouse::get();
        foreach ($warehouse as $w) {
            $totalTransfer = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Transfer Uang')->sum('amount');
            $totalTarikTunai = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Tarik Tunai')->sum('amount');
            $totalVcr = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Voucher & SP')->sum('amount');
            $totaldeposit = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Deposit')->sum('amount');
            $fee = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->sum('fee_amount');
            $w_account = $chartOfAccounts->where('warehouse_id', $w->id)->pluck('acc_code');

            $penambahan = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('debt_code', $w_account)->get();
            $pengeluaran = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('cred_code', $w_account)->get();

            $totalCash = $chartOfAccounts->whereIn('warehouse_id', $w->id)->where('account_id', 1)->sum('balance');
            $totalBank = $chartOfAccounts->whereIn('warehouse_id', $w->id)->where('account_id', 2)->sum('balance');
            $endbalance = $chartOfAccounts->whereIn('warehouse_id', $w->id)->sum('balance');

            $dailyreport[] = [
                'warehouse' => $w->w_name,
                'warehouse_id' => $w->id,
                'totalTransfer' => $totalTransfer,
                'totalTarikTunai' => $totalTarikTunai,
                'totalVcr' => $totalVcr,
                'totaldeposit' => $totaldeposit,
                'penambahan' => $penambahan,
                'pengeluaran' => $pengeluaran,
                'fee' => $fee,
                'endbalance' => $endbalance,
                'totalCash' => $totalCash,
                'totalBank' => $totalBank,
                'warehouseaccount' => $chartOfAccounts->whereIn('account_id', ['1', '2'])->where('warehouse_id', $w->id),
            ];

            $sumtotalTransfer += $totalTransfer;
            $sumtotalTarikTunai += $totalTarikTunai;
            $sumtotalVcr += $totalVcr;
            $sumtotaldeposit += $totaldeposit;
            $sumfee += $fee;
            $sumtotalCash += $totalCash;
            $sumtotalBank += $totalBank;
            $sumendbalance += $endbalance;
        }

        return view('home.admin', [
            'title' => 'Administrator',
            'subtitle' => 'Administrator',
            'warehouse' => Warehouse::get(),
            'dailyreport' => $dailyreport,
            'chartOfAccounts' => $chartOfAccounts->whereIn('account_id', ['1', '2']),
            'expense' => $chartOfAccounts->whereIn('account_id', range(33, 45)),
            'sumtotalTransfer' => $sumtotalTransfer,
            'sumtotalTarikTunai' => $sumtotalTarikTunai,
            'sumtotalVcr' => $sumtotalVcr,
            'sumtotaldeposit' => $sumtotaldeposit,
            'sumfee' => $sumfee,
            'sumtotalCash' => $sumtotalCash,
            'sumtotalBank' => $sumtotalBank,
            'sumendbalance' => $sumendbalance,
        ]);
    }

    public function dailyreport()
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $transactions = $accountTrace->with(['debt', 'cred'])
            ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
            ->whereBetween('date_issued', [Carbon::create(0000, 1, 1, 0, 0, 0)->startOfDay(), $endDate])
            ->groupBy('debt_code', 'cred_code', 'warehouse_id')
            ->get();

        $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])->get();

        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            // @ts-ignore
            $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        }

        $totalTransfer = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Tarik Tunai')->sum('amount');
        $totalVcr = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Voucher & SP')->sum('amount');
        $totaldeposit = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Deposit')->sum('amount');
        $fee = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->sum('fee_amount');

        $w_account = $chartOfAccounts->where('warehouse_id', Auth()->user()->warehouse_id)->pluck('acc_code');
        // dd($w_account);
        // $actrace = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate]);
        // $sql = $actrace->whereIn('cred_code', $w_account)->toSql();
        // dd($sql);
        $penambahan = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('debt_code', $w_account)->get();
        $pengeluaran = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('cred_code', $w_account)->get();

        $vcr = Sale::with('product')
            ->selectRaw('SUM(cost * quantity) as total_cost, product_id, sum(quantity) as qty')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->where('warehouse_id', Auth()->user()->warehouse_id)
            ->groupBy('product_id')
            ->get();

        return view('home.dailyreport', [
            'title' => 'Daily Report',
            'subtitle' => 'Daily Report',
            'totalTransfer' => $totalTransfer,
            'totalTarikTunai' => $totalTarikTunai,
            'totalVcr' => $totalVcr,
            'totaldeposit' => $totaldeposit,
            'fee' => $fee,
            'endbalance' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->groupBy('warehouse_id'),
            'totalCash' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->where('account_id', 1)->groupBy('warehouse_id'),
            'totalBank' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->where('account_id', 2)->groupBy('warehouse_id'),
            'warehouseaccount' => $chartOfAccounts->where('warehouse_id', Auth()->user()->warehouse_id),
            'penambahan' => $penambahan,
            'pengeluaran' => $pengeluaran,
            'account' => $chartOfAccounts->where('warehouse_id', Auth()->user()->warehouse_id),
            'sales' => Sale::whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', Auth()->user()->warehouse_id)->get(),
            'vcr' => $vcr
        ]);
    }

    public function reportCabang(Request $request)
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $transactions = $accountTrace->with(['debt', 'cred'])
            ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
            ->whereBetween('date_issued', [Carbon::create(0000, 1, 1, 0, 0, 0)->startOfDay(), $endDate])
            ->groupBy('debt_code', 'cred_code', 'warehouse_id')
            ->get();

        $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])->get();

        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            // @ts-ignore
            $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        }

        $totalTransfer = AccountTrace::where('warehouse_id', $request->cabang)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = AccountTrace::where('warehouse_id', $request->cabang)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Tarik Tunai')->sum('amount');
        $totalVcr = AccountTrace::where('warehouse_id', $request->cabang)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Voucher & SP')->sum('amount');
        $totaldeposit = AccountTrace::where('warehouse_id', $request->cabang)->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Deposit')->sum('amount');
        $fee = AccountTrace::where('warehouse_id', $request->cabang)->whereBetween('date_issued', [$startDate, $endDate])->sum('fee_amount');

        $w_account = $chartOfAccounts->where('warehouse_id', $request->cabang)->pluck('acc_code');
        // dd($w_account);
        // $actrace = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate]);
        // $sql = $actrace->whereIn('cred_code', $w_account)->toSql();
        // dd($sql);
        $penambahan = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('debt_code', $w_account)->get();
        $pengeluaran = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('cred_code', $w_account)->get();

        $vcr = Sale::with('product')
            ->selectRaw('SUM(cost * quantity) as total_cost, product_id, sum(quantity) as qty')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->where('warehouse_id', $request->cabang)
            ->groupBy('product_id')
            ->get();

        return view('home.report', [
            'title' => 'Report Cabang',
            'subtitle' => 'Report Cabang',
            'totalTransfer' => $totalTransfer,
            'totalTarikTunai' => $totalTarikTunai,
            'totalVcr' => $totalVcr,
            'totaldeposit' => $totaldeposit,
            'fee' => $fee,
            'endbalance' => $chartOfAccounts->whereIn('warehouse_id', [$request->cabang])->groupBy('warehouse_id'),
            'totalCash' => $chartOfAccounts->whereIn('warehouse_id', [$request->cabang])->where('account_id', 1)->groupBy('warehouse_id'),
            'totalBank' => $chartOfAccounts->whereIn('warehouse_id', [$request->cabang])->where('account_id', 2)->groupBy('warehouse_id'),
            'warehouseaccount' => $chartOfAccounts->where('warehouse_id', $request->cabang),
            'penambahan' => $penambahan,
            'pengeluaran' => $pengeluaran,
            'account' => $chartOfAccounts->where('warehouse_id', $request->cabang),
            'sales' => Sale::whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', $request->cabang)->get(),
            'vcr' => $vcr,
            'warehouse' => Warehouse::all()
        ])->with($request->all());
    }

    public function addTransfer(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'amount' => 'required|numeric',
            'fee_amount' => 'required|numeric',
        ]);

        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;

        $description = $request->description ?? "Transfer Uang";

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $w_account;
        $accountTrace->cred_code = $request->account;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->description = $description;
        $accountTrace->trx_type = "Transfer Uang";
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect('/home')->with('success', 'Transfer added successfully.');
    }

    public function addTarikTunai(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'amount' => 'required|numeric',
            'fee_amount' => 'required|numeric',
        ]);

        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;

        $description = $request->description ?? "Tarik Tunai";

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->account;
        $accountTrace->cred_code = $w_account;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->description = $description;
        $accountTrace->trx_type = "Tarik Tunai";
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect('/home')->with('success', 'Tarik Tunai added successfully.');
    }

    public function mutasi(Request $request)
    {
        $request->validate([
            'debt' => 'required',
            'cred' => 'required',
            'amount' => 'required|numeric',
        ]);

        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->debt;
        $accountTrace->cred_code = $request->cred;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = 0;
        $accountTrace->description = "Mutasi Kas";
        $accountTrace->trx_type = "Mutasi Kas";
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect()->back()->with('success', 'Mutasi added successfully.');
    }

    public function pengeluaran(Request $request)
    {
        $request->validate([
            'debt' => 'required',
            'cred' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->debt;
        $accountTrace->cred_code = $request->cred;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = 0;
        $accountTrace->description = $request->description;
        $accountTrace->trx_type = "Pengeluaran";
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect()->back()->with('success', 'Pengeluaran added successfully.');
    }

    public function transaksi(Request $request)
    {
        $request->validate([
            'qty' => 'required|numeric',
            'jual' => 'required|numeric',
            'modal' => 'required|numeric',
            'trx_type' => 'required',
        ]);

        $modal = $request->modal * $request->qty;
        $jual = $request->jual * $request->qty;

        $description = $request->description ?? "Transaksi";
        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;
        $fee = $jual - $modal;
        $invoice = new AccountTrace();
        $invoice->invoice = $invoice->invoice_journal();

        if ($request->trx_type == "Voucher & SP" && $request->product_id == null) {
            return redirect()->back()->with('error', 'Please select product.');
        }

        try {
            DB::beginTransaction();
            $accountTrace = new AccountTrace();
            $accountTrace->date_issued = $request->date_issued;
            $accountTrace->invoice = $invoice->invoice;
            $accountTrace->debt_code = "10600-001";
            $accountTrace->cred_code = "10600-001";
            $accountTrace->amount = $modal;
            $accountTrace->fee_amount = $fee;
            $accountTrace->description = $description;
            $accountTrace->trx_type = $request->trx_type;
            $accountTrace->user_id = Auth()->user()->id;
            $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
            $accountTrace->save();

            if ($request->trx_type !== "Deposit") {

                $sale = new Sale();
                $sale->date_issued = $request->date_issued;
                $sale->invoice = $invoice->invoice;
                $sale->product_id = $request->product_id;
                $sale->quantity = $request->qty;
                $sale->price = $request->jual;
                $sale->cost = $request->modal;
                $sale->warehouse_id = Auth()->user()->warehouse_id;
                $sale->user_id = Auth()->user()->id;
                $sale->save();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transaction failed.');
        }

        return redirect()->back()->with('success', 'Transaksi penjualan added successfully.');
    }

    public function generalLedger(Request $request)
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $account_trace = $accountTrace->with('debt', 'cred', 'warehouse', 'user')->where('debt_code', $request->accounts)
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->orWhere('cred_code', $request->accounts)
            ->WhereBetween('date_issued', [$startDate, $endDate])
            ->orderBy('date_issued', 'asc')
            ->get();

        $debt_total = $account_trace->where('debt_code', $request->accounts)->sum('amount');
        $cred_total = $account_trace->where('cred_code', $request->accounts)->sum('amount');

        $initBalanceDate = Carbon::parse($startDate)->subDays(1)->endOfDay();
        $initBalance = $accountTrace->endBalanceBetweenDate($request->accounts, '0000-00-00', $initBalanceDate);
        $endBalance = $accountTrace->endBalanceBetweenDate($request->accounts, '0000-00-00', $endDate);
        // dd($endBalance);

        return view('home.history', [
            'title' => 'General Ledger',
            'active' => 'reports',
            'account_trace' => $account_trace,
            'account' => ChartOfAccount::with(['account'])->where('warehouse_id', Auth()->user()->warehouse_id)->orderBy('acc_code', 'asc')->get(),
            'debt_total' => $debt_total,
            'cred_total' => $cred_total,
            'initBalance' => $initBalance,
            'endBalance' => $endBalance,
            'status' => ChartOfAccount::with(['account'])->where('acc_code', $request->accounts)->first()->account->status
        ])->with($request->all());
    }

    public function edit($id)
    {
        $accountTrace = AccountTrace::find($id);

        return view('home.edit', [
            'title' => 'Edit Transaction',
            'active' => 'reports',
            'account' => ChartOfAccount::with(['account'])->where('warehouse_id', Auth()->user()->warehouse_id)->orderBy('acc_code', 'asc')->get(),
            'accountTrace' => $accountTrace,
            'warehouse_cash' => $accountTrace->warehouse->chartofaccount->acc_code,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'debt_code' => 'required',
            'cred_code' => 'required',
            'amount' => 'required|numeric',
            'fee_amount' => 'required|numeric',
        ]);
        $accountTrace = AccountTrace::find($id);
        $accountTrace->debt_code = $request->debt_code;
        $accountTrace->cred_code = $request->cred_code;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->save();
        return redirect('/home')->with('success', 'Data Updated Successfully');
    }

    public function destroy($id)
    {
        $accountTrace = AccountTrace::find($id);
        try {
            DB::beginTransaction();

            $accountTrace->delete();
            Sale::where('invoice', $accountTrace->invoice)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transaction failed.');
        }
        return redirect('/home')->with('success', 'Data Deleted Successfully');
    }
}
