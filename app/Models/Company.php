<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'symbol',
    ];

    public function stockPrices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockPrice::class);
    }

    public function stockImports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockImport::class);
    }
}
