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
                // Find the WarehouseAccount by ID
                $account = WarehouseAccount::find($id);

                // Check if the account exists before attempting to delete it
                if ($account) {
                    // Delete the WarehouseAccount
                    $account->delete();

                    // Find the corresponding ChartOfAccount and update its warehouse_id
                    $ChartOfAccount = ChartOfAccount::find($account->chart_of_account_id);
                    if ($ChartOfAccount) {
                        $ChartOfAccount->update([
                            'warehouse_id' => 0,
                        ]);
                    } else {
                        // Handle the case where the corresponding ChartOfAccount is not found
                        throw new \Exception('Corresponding ChartOfAccount not found.');
                    }
                } else {
                    // Handle the case where the WarehouseAccount is not found
                    throw new \Exception('WarehouseAccount not found.');
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }


        return redirect()->back()->with('success', 'Data Deleted Successfully');
    }
}
