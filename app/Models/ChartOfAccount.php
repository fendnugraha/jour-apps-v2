<?php

namespace App\Models;

use App\Models\Account;
use App\Models\AccountTrace;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'acc_code',
        'acc_name',
        'account_id',
        'warehouse_id',
        'st_balance',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function account_trace()
    {
        return $this->hasMany(AccountTrace::class, 'debt_code', 'acc_code');
    }

    public function debt()
    {
        return $this->hasMany(AccountTrace::class, 'debt_code', 'acc_code');
    }

    public function cred()
    {
        return $this->hasMany(AccountTrace::class, 'cred_code', 'acc_code');
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function warehouseaccount()
    {
        return $this->belongsTo(WarehouseAccount::class);
    }

    public function acc_code($account_id)
    {
        $accounts = Account::find($account_id);

        $lastCode = DB::table('chart_of_accounts')
            ->select(DB::raw('MAX(RIGHT(acc_code,3)) AS lastCode'))
            ->where('account_id', $account_id)
            ->get();

        if ($lastCode[0]->lastCode != null) {
            $kd = $lastCode[0]->lastCode + 1;
        } else {
            $kd = "001";
        }

        return $accounts->code . '-' . \sprintf("%03s", $kd);
    }
}
