<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Mail\UserRegistrationInvite;
use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class CompanyUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        // Authorize all company & user owned company
        $this->authorize('viewAny', $company);

        $users = $company->users()->where('role_id', Role::COMPANY_OWNER->value)->get();

        return view('companies.users.index', [
            'company' => $company,
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company)
    {
        $this->authorize('create', $company);

        return view('companies.users.create', [
            'company' => $company
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, Company $company)
    {

        $this->authorize('create', $company);

        /*$company->users()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => Role::COMPANY_OWNER->value
        ]);
        return Redirect::route('companies.users.index', $company);*/

        $invitation = UserInvitation::create([
            'email' => $request->input('email'),
            'token' => Str::uuid(),
            'company_id' => $company->id,
            'role_id' => Role::COMPANY_OWNER->value
        ]);

        Mail::to($request->input('email'))->send(new UserRegistrationInvite($invitation));

        toastr()
            ->addSuccess('Mail sent successfully');

        return Redirect::route('companies.users.index', $company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, User $user)
    {
        $this->authorize('update', $company);

        return view('companies.users.edit', [
            'company' => $company,
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, Company $company, User $user)
    {
        $this->authorize('update', $company);

        $user->update($request->validated());

        return Redirect::route('companies.users.index', $company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, User $user)
    {
        $this->authorize('delete', $company);

        $user->delete();

        return Redirect::route('companies.users.index', $company);
    }
}
