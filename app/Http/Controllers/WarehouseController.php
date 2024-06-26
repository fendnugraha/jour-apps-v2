<?php

namespace App\Http\Controllers;

use App\Models\AccountTrace;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\WarehouseAccount;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouse = new Warehouse();

        return view('setting/warehouse/index', [
            'title' => 'Warehouse',
            'warehouse' => $warehouse->load('ChartOfAccount')->get(),
            'account' => ChartOfAccount::whereIn('account_id', [1, 2])->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'w_code' => 'required|size:3|alpha|unique:warehouses,w_code',
            'w_name' => 'required|min:3|max:90|unique:warehouses,w_name',
            'address' => 'required|max:160|min:3',
            'account' => 'required'
        ]);

        $request->user()->warehouse()->create([
            'w_code' => \strtoupper($request->w_code),
            'w_name' => $request->w_name,
            'address' => $request->address,
            'chart_of_account_id' => $request->account
        ]);

        return redirect('/setting/warehouses')->with('success', 'Warehouse Added');
    }

    public function edit($id)
    {
        $warehouse = Warehouse::find($id);

        return view('setting/warehouse/edit', [
            'title' => 'Edit Warehouse',
            'warehouse' => $warehouse,
            'account' => ChartOfAccount::whereIn('account_id', [1, 2])->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'w_code' => 'required|size:3|alpha|unique:warehouses,w_code,' . $id,
            'w_name' => 'required|min:3|max:90|unique:warehouses,w_name,' . $id,
            'address' => 'required|max:160|min:3',
            'account' => 'required'
        ]);

        $warehouse = Warehouse::find($id);

        $warehouse->update([
            'w_code' => $request->w_code,
            'w_name' => $request->w_name,
            'address' => $request->address,
            'chart_of_account_id' => $request->account
        ]);

        return redirect('/setting/warehouses')->with('success', 'Warehouse Updated');
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);

        $account_trace = AccountTrace::where('warehouse_id', $warehouse->id)->count();

        if ($account_trace > 0) {
            return redirect('/setting/warehouses')->with('error', 'Warehouse Cannot Be Deleted');
        }

        $warehouse->delete();

        return redirect('/setting/warehouses')->with('success', 'Warehouse Deleted');
    }

    public function details($id)
    {
        $warehouse = Warehouse::find($id);

        return view('setting/warehouse/detail', [
            'title' => 'Warehouse Detail',
            'warehouse' => $warehouse,
            'chartofaccounts' => ChartOfAccount::whereIn('account_id', [1, 2])->where('warehouse_id', 0)->get(),
            'warehouseaccount' => WarehouseAccount::where('warehouse_id', $id)->get()
        ]);
    }
}
