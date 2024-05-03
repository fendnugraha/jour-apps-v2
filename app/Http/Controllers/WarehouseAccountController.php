<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\WarehouseAccount;
use Illuminate\Http\Request;
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
        
        $account = new WarehouseAccount();
        $account->warehouse_id = $request->warehouse_id;
        $account->chart_of_account_id = $request->account_id;
        $account->save();

        return redirect()->back()->with('success', 'Data Added Successfully');
    }

    public function destroy($id)
    {
        $account = WarehouseAccount::find($id);
        $account->delete();
        return redirect()->back();
    }
}
