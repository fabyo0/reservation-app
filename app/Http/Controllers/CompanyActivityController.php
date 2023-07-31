<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Enums\Role;
use App\Models\User;
use App\Models\Company;
use Intervention\Image\Facades\Image;


//TODO: Add unit test

class CompanyActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        $this->authorize('viewAny', $company);

        $company->load('activities');

        return view('activities.index', [
            'company' => $company
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company)
    {
        $this->authorize('create', $company);

        $guides = User::query()->where('company_id', $company->id)
            ->where('role_id', Role::GUIDE->value)
            ->pluck('name', 'id');

        return view('activities.create', [
            'guides' => $guides,
            'company' => $company
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request, Company $company)
    {
        $this->authorize('create', $company);

        $fileName = $this->uploadImage($request);

        Activity::create($request->validated() + [
                'company_id' => $company->id,
                'photo' => $fileName,
            ]);

        return to_route('companies.activities.index', $company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, Activity $activity)
    {

        $this->authorize('update', $company);

        $guides = User::query()->where('company_id', $company->id)
            ->where('role_id', Role::GUIDE->value)
            ->pluck('name', 'id');

        return view('activities.edit', [
            'guides' => $guides,
            'activity' => $activity,
            'company' => $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Company $company, Activity $activity)
    {
        $this->authorize('update', $company);

        /*if ($request->hasFile('image')) {
            $path = $request->file('image')->store('activities', 'public');

            // Photo already exits delete
            if ($activity->photo) {
                Storage::disk('public')->delete($activity->photo);
            }
        }*/

        $fileName = $this->uploadImage($request);

        $activity->update($request->validated() + [
                'photo' => $fileName ?? $activity->photo,
            ]);

        return Redirect::route('companies.activities.index', $company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Activity $activity)
    {
        $this->authorize('delete', $company);

        $activity->delete();

        return Redirect::route('companies.activities.index');
    }

    private function uploadImage(StoreActivityRequest|UpdateActivityRequest $request): string|null
    {
        // İstekte 'image' dosyası yoksa, yükleme yapmadan null döndürülür.
        if (! $request->hasFile('image')) {
            return null;
        }

        // Dosya yolu oluşturulur ve yüklenen resim bu yola kaydedilir.
        $filename = $request->file('image')->store(options: 'activities');

        // Resim Intervention Image kütüphanesi kullanılarak açılır ve boyutu değiştirilir.
        $img = Image::make(Storage::disk('activities')->get($filename))
            ->resize(274, 274, function ($constraint) {
                $constraint->aspectRatio();
            });

        // İşlenmiş resim 'thumbs' klasörü altına kaydedilir.
        Storage::disk('activities')->put('thumbs/' . $request->file('image')->hashName(), $img->stream());

        // Yüklenen resmin dosya yolu (filename) döndürülür.
        return $filename;
    }
}
