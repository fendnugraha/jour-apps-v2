<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\AccountTrace;
use App\Models\ChartOfAccount;

class DisplayCashBank extends Component
{
    public function render()
    {
        $endDate = Carbon::now()->endOfDay();
        $transactions = AccountTrace::with(['debt', 'cred'])
            ->selectRaw('debt_code, cred_code, SUM(amount) as total, warehouse_id')
            ->whereBetween('date_issued', [Carbon::create(0000, 1, 1, 0, 0, 0)->startOfDay(), $endDate])
            ->groupBy('debt_code', 'cred_code', 'warehouse_id')
            ->get();

        // Retrieve chart of accounts with related data
        $chartOfAccounts = ChartOfAccount::with(['account', 'warehouse'])
            ->orderBy('acc_code', 'asc')
            ->get();

        // Calculate balances for each account
        foreach ($chartOfAccounts as $value) {
            $debit = $transactions->where('debt_code', $value->acc_code)->sum('total');
            $credit = $transactions->where('cred_code', $value->acc_code)->sum('total');

            $balance = ($value->account->status == "D")
                ? ($value->st_balance + $debit - $credit)
                : ($value->st_balance + $credit - $debit);

            $value->balance = $balance;
        }

        $userWarehouseId = Auth()->user()->warehouse_id;

        return view('livewire.display-cash-bank', [
            'warehouseaccount' => $chartOfAccounts->where('warehouse_id', $userWarehouseId),
        ]);
    }
}
