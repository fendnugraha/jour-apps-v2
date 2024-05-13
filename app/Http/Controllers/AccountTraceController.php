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
            'accounttrace' => AccountTrace::with('debt', 'cred', 'sale')->whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', Auth()->user()->warehouse_id)->orderBy('date_issued', 'desc')->get(),
            'hqaccount' => ChartOfAccount::whereIn('account_id', ['1', '2'])->where('warehouse_id', 1)->get(),
            'product' => Product::all(),
            'expense' => ChartOfAccount::whereIn('account_id', range(33, 45))->get(),

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

        $sumtotalTransfer = $accountTrace->where('trx_type', 'Transfer Uang')->sum('amount');
        $sumtotalTarikTunai = $accountTrace->where('trx_type', 'Tarik Tunai')->sum('amount');
        $sumtotalVcr = $accountTrace->where('trx_type', 'Voucher & SP')->sum('amount');
        $sumtotaldeposit = $accountTrace->where('trx_type', 'Deposit')->sum('amount');
        $sumfee = $accountTrace->where('fee_amount', '>', 0)->sum('fee_amount');
        $sumcost = $accountTrace->where('fee_amount', '<', 0)->sum('fee_amount');
        $sumtotalCash = $chartOfAccounts->whereIn('account_id', ['1'])->sum('balance');
        $sumtotalBank = $chartOfAccounts->whereIn('account_id', ['2'])->sum('balance');


        return view('home.admin', [
            'title' => 'Administrator',
            'subtitle' => 'Administrator',
            'warehouse' => Warehouse::get(),
            'chartOfAccounts' => $chartOfAccounts->whereIn('account_id', ['1', '2']),
            'expense' => $chartOfAccounts->whereIn('account_id', range(33, 45)),
            'sumtotalTransfer' => $sumtotalTransfer,
            'sumtotalTarikTunai' => $sumtotalTarikTunai,
            'sumtotalVcr' => $sumtotalVcr,
            'sumtotaldeposit' => $sumtotaldeposit,
            'sumfee' => $sumfee,
            'sumcost' => $sumcost,
            'sumtotalCash' => $sumtotalCash,
            'sumtotalBank' => $sumtotalBank,
            'sumendbalance' => $sumtotalCash + $sumtotalBank,
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
        $trx = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->get();
        $totalTransfer = $trx->where('trx_type', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = $trx->where('trx_type', 'Tarik Tunai')->sum('amount');
        $totalVcr = $trx->where('trx_type', 'Voucher & SP')->sum('amount');
        $totaldeposit = $trx->where('trx_type', 'Deposit')->sum('amount');
        $fee = $trx->where('fee_amount', '>', 0)->sum('fee_amount');

        $w_account = $chartOfAccounts->where('warehouse_id', Auth()->user()->warehouse_id)->pluck('acc_code');
        // dd($w_account);
        // $actrace = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate]);
        // $sql = $actrace->whereIn('cred_code', $w_account)->toSql();
        // dd($sql);
        $penambahan = AccountTrace::with(['debt', 'cred'])->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Mutasi Kas')->whereIn('debt_code', $w_account)->get();
        $pengeluaran = AccountTrace::with(['debt', 'cred'])->whereBetween('date_issued', [$startDate, $endDate])->where('trx_type', 'Mutasi Kas')->whereIn('cred_code', $w_account)->get();
        $cost = $trx->where('fee_amount', '<', 0);

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
            'vcr' => $vcr,
            'warehouses' => Warehouse::get(),
            'cost' => $cost
        ]);
    }

    public function reportCabang(Request $request)
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $cabang = $request->cabang !== null ? $request->cabang : Auth()->user()->warehouse_id;

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

        $trx = AccountTrace::where('warehouse_id', $cabang)->whereBetween('date_issued', [$startDate, $endDate])->get();

        $totalTransfer = $trx->where('trx_type', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = $trx->where('trx_type', 'Tarik Tunai')->sum('amount');
        $totalVcr = $trx->where('trx_type', 'Voucher & SP')->sum('amount');
        $totaldeposit = $trx->where('trx_type', 'Deposit')->sum('amount');
        $fee = $trx->where('fee_amount', '>', 0)->sum('fee_amount');
        $cost = $trx->where('fee_amount', '<', 0);

        $w_account = $chartOfAccounts->where('warehouse_id', $cabang)->pluck('acc_code');
        // dd($w_account);
        // $actrace = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate]);
        // $sql = $actrace->whereIn('cred_code', $w_account)->toSql();
        // dd($sql);
        $penambahan = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('debt_code', $w_account)->get();
        $pengeluaran = AccountTrace::with(['debt', 'cred'])->where('trx_type', 'Mutasi Kas')->whereBetween('date_issued', [$startDate, $endDate])->whereIn('cred_code', $w_account)->get();

        $vcr = Sale::with('product')
            ->selectRaw('SUM(cost * quantity) as total_cost, product_id, sum(quantity) as qty')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->where('warehouse_id', $cabang)
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
            'cost' => $cost,
            'endbalance' => $chartOfAccounts->whereIn('warehouse_id', [$cabang])->groupBy('warehouse_id'),
            'totalCash' => $chartOfAccounts->whereIn('warehouse_id', [$cabang])->where('account_id', 1)->groupBy('warehouse_id'),
            'totalBank' => $chartOfAccounts->whereIn('warehouse_id', [$cabang])->where('account_id', 2)->groupBy('warehouse_id'),
            'warehouseaccount' => $chartOfAccounts->where('warehouse_id', $cabang),
            'penambahan' => $penambahan,
            'pengeluaran' => $pengeluaran,
            'account' => $chartOfAccounts->where('warehouse_id', $cabang),
            'sales' => Sale::whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', $cabang)->get(),
            'vcr' => $vcr,
            'cabang' => $cabang,
            'warehouse' => Warehouse::all()
        ])->with($request->all());
    }

    public function customReport(Request $request)
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // $transactions = $accountTrace->with(['debt', 'cred'])
        //     ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
        //     ->whereBetween('date_issued', [Carbon::create(0000, 1, 1, 0, 0, 0)->startOfDay(), $endDate])
        //     ->groupBy('debt_code', 'cred_code', 'warehouse_id')
        //     ->get();

        // $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])->get();

        // foreach ($chartOfAccounts as $value) {
        //     $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
        //     $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

        //     // @ts-ignore
        //     $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        // }

        $totalTransaksi = $request->warehouse == null ? $accountTrace->whereBetween('date_issued', [$startDate, $endDate])->get() : $accountTrace->whereBetween('date_issued', [$startDate, $endDate])->where('warehouse_id', $request->warehouse)->get();

        $totalTransfer = $totalTransaksi->where('trx_type', 'Transfer Uang');
        $totalTarikTunai = $totalTransaksi->where('trx_type', 'Tarik Tunai');
        $totalVcr = $totalTransaksi->where('trx_type', 'Voucher & SP');
        $totaldeposit = $totalTransaksi->where('trx_type', 'Deposit');
        $totalBiaya = $totalTransaksi->where('trx_type', 'Pengeluaran');
        $totalLaba = $totalTransaksi->sum('fee_amount');

        $revenue = $accountTrace->whereBetween('date_issued', [$startDate, $endDate])
            ->selectRaw('SUM(amount) as total, warehouse_id, SUM(fee_amount) as sumfee')
            ->groupBy('warehouse_id')
            ->get();
        // dd($revenue);

        return view('home.customreport', [
            'title' => 'Custom Report',
            'subtitle' => 'Custom Report',
            'totalTransfer' => $totalTransfer,
            'totalTarikTunai' => $totalTarikTunai,
            'totalVcr' => $totalVcr,
            'totaldeposit' => $totaldeposit,
            'totalLaba' => $totalLaba,
            'warehouse_name' => $request->warehouse == null ? "Semua Cabang" : Warehouse::find($request->warehouse)->w_name,
            'warehouses' => Warehouse::all(),
            'totalBiaya' => $totalBiaya,
            'revenue' => $revenue
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

        return redirect('/home')->with('success', 'Transfer added successfully.')->with($request->account);
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
        $status = $request->status == 1 ? 1 : 2;

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->account;
        $accountTrace->cred_code = $w_account;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->status = $status;
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
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $w_account = Warehouse::with('chartofaccount')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chartofaccount->acc_code;

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->debt;
        $accountTrace->cred_code = $w_account;
        $accountTrace->amount = 0;
        $accountTrace->fee_amount = -$request->amount;
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

        $status = $request->status == 1 ? 1 : 2;

        $accountTrace = AccountTrace::find($id);
        $accountTrace->debt_code = $request->debt_code;
        $accountTrace->cred_code = $request->cred_code;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->status = $status;
        $accountTrace->description = $request->description;
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
