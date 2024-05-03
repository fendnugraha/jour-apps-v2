<?php

namespace App\Http\Controllers;

use App\Models\AccountTrace;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTraceController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'title' => 'Home',
            'subtitle' => 'Home',
        ]);
    }

    public function dailyreport()
    {
        return view('home.dailyreport', [
            'title' => 'Daily Report',
            'subtitle' => 'Daily Report',
        ]);
    }

    public function addTransfer(Request $request)
    {
        $request->validate([
            'account' => 'required',
            'amount' => 'required|numeric',
            'fee_amount' => 'required|numeric',
        ]);
        
        $w_account = Warehouse::with('chart_of_account')->Where('id', Auth()->user()->warehouse_id)->first();
        $w_account = $w_account->chart_of_account->acc_code;

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
}
