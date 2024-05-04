<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\AccountTrace;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\WarehouseAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AccountTraceController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'title' => 'Home',
            'subtitle' => 'Home',
            'warehouseaccount' => ChartOfAccount::whereIn('account_id', ['1', '2'])->where('warehouse_id', Auth()->user()->warehouse_id)->get(),
            'accounttrace' => AccountTrace::with('debt', 'cred')->where('warehouse_id', Auth()->user()->warehouse_id)->get(),
            'hqaccount' => ChartOfAccount::whereIn('account_id', ['1', '2'])->where('warehouse_id', 1)->get(),
        ]);
    }

    public function administrator()
    {
        if(Auth()->user()->role !== "Administrator") {
            return abort(403, 'Unauthorized action.');
        }

        $accountTrace = new AccountTrace();
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $transactions = $accountTrace->with(['debt', 'cred'])
            ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->groupBy('debt_code', 'cred_code', 'warehouse_id')
            ->get();
        
        $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])->get();

        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            // @ts-ignore
            $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        }

        $totalTransfer = [];
        $warehouse = Warehouse::get();
        foreach ($warehouse as $w) {
            $totalTransfer = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('description', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->where('description', 'Tarik Tunai')->sum('amount');
        $fee = AccountTrace::where('warehouse_id', $w->id)->whereBetween('date_issued', [$startDate, $endDate])->sum('fee_amount');
        $totalCash = $chartOfAccounts->whereIn('warehouse_id', $w->id)->where('account_id', 1)->sum('balance');

            $dailyreport[] = [
                'warehouse' => $w->w_name,
                'warehouse_id' => $w->id,
                'totalTransfer' => $totalTransfer,
                'totalTarikTunai' => $totalTarikTunai,
                'fee' => $fee,
                'endbalance' => $chartOfAccounts->whereIn('warehouse_id', $w->id)->sum('balance'),
                'revenue' => $chartOfAccounts->whereIn('warehouse_id', $w->id)->sum('balance'),
                'totalCash' => $totalCash,
                'totalBank' => $chartOfAccounts->whereIn('warehouse_id', $w->id)->where('account_id', 2)->sum('balance'),
                'warehouseaccount' => $chartOfAccounts->whereIn('account_id', ['1', '2'])->where('warehouse_id', $w->id),
            ];
            
        }

        return view('home.admin', [
            'title' => 'Administrator',
            'subtitle' => 'Administrator',
            'warehouse' => Warehouse::get(),
            'dailyreport' => $dailyreport,
            'chartOfAccounts' => $chartOfAccounts->whereIn('account_id', ['1', '2']),
        ]);
    }

    public function dailyreport()
    {
        $accountTrace = new AccountTrace();
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $transactions = $accountTrace->with(['debt', 'cred'])
            ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->groupBy('debt_code', 'cred_code', 'warehouse_id')
            ->get();
        
        $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])->get();

        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            // @ts-ignore
            $value->balance = ($value->account->status == "D") ? ($value->st_balance + $debit - $credit) : ($value->st_balance + $credit - $debit);
        }

        $totalTransfer = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('description', 'Transfer Uang')->sum('amount');
        $totalTarikTunai = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->where('description', 'Tarik Tunai')->sum('amount');
        $fee = AccountTrace::where('warehouse_id', Auth()->user()->warehouse_id)->whereBetween('date_issued', [$startDate, $endDate])->sum('fee_amount');

        return view('home.dailyreport', [
            'title' => 'Daily Report',
            'subtitle' => 'Daily Report',
            'totalTransfer' => $totalTransfer,
            'totalTarikTunai' => $totalTarikTunai,
            'fee' => $fee,
            'endbalance' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->groupBy('warehouse_id'),
            'revenue' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->groupBy('warehouse_id'),
            'totalCash' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->where('account_id', 1)->groupBy('warehouse_id'),
            'totalBank' => $chartOfAccounts->whereIn('warehouse_id', [Auth()->user()->warehouse_id])->where('account_id', 2)->groupBy('warehouse_id'),
            'warehouseaccount' => $chartOfAccounts->where('warehouse_id', Auth()->user()->warehouse_id),
        ]);
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

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $w_account;
        $accountTrace->cred_code = $request->account;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->description = "Transfer Uang";
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

        $accountTrace = new AccountTrace();
        $accountTrace->date_issued = $request->date_issued;
        $accountTrace->invoice = $accountTrace->invoice_journal();
        $accountTrace->debt_code = $request->account;
        $accountTrace->cred_code = $w_account;
        $accountTrace->amount = $request->amount;
        $accountTrace->fee_amount = $request->fee_amount;
        $accountTrace->description = "Tarik Tunai";
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect('/home')->with('success', 'Tarik Tunai added successfully.');
    }

    public function pengeluaran(Request $request)
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
        $accountTrace->user_id = Auth()->user()->id;
        $accountTrace->warehouse_id = Auth()->user()->warehouse_id;
        $accountTrace->save();

        return redirect('/home')->with('success', 'Mutasi added successfully.');
    }

    public function destroy($id)
    {
        $accountTrace = AccountTrace::find($id);
        $accountTrace->delete();
        return redirect('/home')->with('success', 'Data Deleted Successfully');
    }
}
