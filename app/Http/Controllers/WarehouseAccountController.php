<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\WarehouseAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WarehouseAccountController extends Controller
{
    public function index()
    {
        return view('setting.warehouse.index', [
            'title' => 'Warehouse Accounts',
            'accounts' => ChartOfAccount::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
        ]);
        
        try {
            DB::transaction(function () use ($request) {
                
        $account = new WarehouseAccount();
        $account->warehouse_id = $request->warehouse_id;
        $account->chart_of_account_id = $request->account_id;
        $account->save();

        $ChartOfAccount = ChartOfAccount::find($request->account_id);
        $ChartOfAccount->update([
            'warehouse_id' => $request->warehouse_id,
        ]);

        });
        return redirect()->back()->with('success', 'Data Added Successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
        $account = WarehouseAccount::find($id);
        $account->delete();

        $ChartOfAccount = ChartOfAccount::find($account->chart_of_account_id);
        $ChartOfAccount->update([
            'warehouse_id' => 1,
        ]);

        });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back();
    }
}
