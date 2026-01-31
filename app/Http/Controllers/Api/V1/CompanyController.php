<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCompanyRequest;
use App\Http\Resources\Api\V1\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CompanyResource::collection(Company::query()->latest()->paginate());
    }

    public function store(StoreCompanyRequest $request): CompanyResource
    {
        $company = Company::create($request->validated());

        return CompanyResource::make($company);
    }

    public function show(Company $company): CompanyResource
    {
        return CompanyResource::make($company);
    }
}
