<?php

namespace App\Http\Controllers\Web\ImportStockPrices;

use App\Http\Controllers\Controller;
use App\Models\Company;

class ImportFormController extends Controller
{
    public function __invoke()
    {
        $companies = Company::query()
            ->orderByDesc('id')
            ->get(['id', 'name', 'symbol']);

        return view('imports.form', [
            'companies' => $companies,
        ]);
    }
}
