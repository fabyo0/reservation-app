<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\Guide\StoreGuideRequest;
use App\Http\Requests\Guide\UpdateGuideRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;


class CompanyGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        $this->authorize('viewAny', $company);

        $guides = $company->users()->where('role_id', Role::GUIDE->value)->get();

        return view('companies.guides.index', [
            'guides' => $guides,
            'company' => $company
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company)
    {
        $this->authorize('create', $company);

        return view('companies.guides.create', [
            'company' => $company
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuideRequest $request, Company $company)
    {
        $this->authorize('create', $company);

        //TODO: company guide create
        $company->users()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => Role::GUIDE->value
        ]);

        return Redirect::route('companies.guides.index', $company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, User $guide)
    {
        return view('companies.guides.edit', [
            'company' => $company,
            'guide' => $guide
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuideRequest $request, Company $company, User $guide)
    {
        $this->authorize('update', $company);

        $guide->update($request->validate());

        return Redirect::route('companies.guides.index', $company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, User $guide)
    {
        $this->authorize('delete', $company);
        $guide->delete();

        return Redirect::route('companies.guides.index');
    }
}
