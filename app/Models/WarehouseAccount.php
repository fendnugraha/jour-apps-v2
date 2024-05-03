<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseAccount extends Model
{
    use HasFactory;

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function account()
    {
        return $this->hasMany(Account::class);
    }

    public function ChartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
