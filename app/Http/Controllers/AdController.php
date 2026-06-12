<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Ad;
use App\Models\AdImage;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['create', 'store', 'edit', 'update', 'destroy', 'myAds']),
        ];
    }

    /**
     * Public listing page with search + filters.
     */
    public function index(Request $request): View
    {
        $sort = $request->query('sort', '');

        $ads = Ad::query()
            ->active()
            ->with(['primaryImage', 'category', 'district', 'city'])
            ->search($request->query('q'))
            ->when($request->filled('category'), fn ($q) => $q->inCategory($request->integer('category')))
            ->when($request->filled('district'), fn ($q) => $q->inDistrict($request->integer('district')))
            ->priceBetween($request->query('min_price'), $request->query('max_price'))
            ->when($request->query('condition'), fn ($q, $c) => $q->where('condition', $c))
            ->orderByDesc('is_featured')
            ->when($sort === 'price_asc',  fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'views',      fn ($q) => $q->orderByDesc('views'))
            ->when(! in_array($sort, ['price_asc', 'price_desc', 'views']), fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(20)
            ->withQueryString();

        $categories = Category::roots()->active()->orderBy('sort_order')->get();
        $districts = District::orderBy('name')->get();

        return view('ads.index', compact('ads', 'categories', 'districts'));
    }

    /**
     * Show the ad-posting form.
     */
    public function create(): View
    {
        $this->authorize('create', Ad::class);

        $categories = Category::roots()->active()->orderBy('sort_order')->get();
        $districts = District::orderBy('name')->get();

        return view('ads.create', [
            'categories' => $categories,
            'districts'  => $districts,
            'ad'         => null,
        ]);
    }

    /**
     * Persist a new ad with images and dynamic attributes.
     */
    public function store(StoreAdRequest $request): RedirectResponse
    {
        $this->authorize('create', Ad::class);

        $ad = DB::transaction(function () use ($request) {
            $data = $request->safe()->only([
                'category_id', 'district_id', 'city_id', 'title', 'description',
                'condition', 'price', 'is_negotiable', 'contact_name', 'contact_phone',
            ]);

            $ad = $request->user()->ads()->create($data + [
                'status'     => 'active',
                'expires_at' => now()->addDays(30),
            ]);

            $this->syncAttributes($ad, (array) $request->input('attributes', []));
            $this->storeImages($ad, $request->file('images', []));

            return $ad;
        });

        return redirect()
            ->route('ads.show', $ad)
            ->with('status', 'ඔබගේ දැන්වීම සාර්ථකව පළ කරන ලදී!');
    }

    /**
     * Single ad view: gallery, seller info, related ads.
     */
    public function show(Ad $ad): View
    {
        $this->authorize('view', $ad);

        $ad->loadMissing(['images', 'attributes', 'category.parent', 'district', 'city', 'user']);

        // Increment views without bumping updated_at.
        $ad->incrementQuietly('views');

        // Same subcategory first; fall back to sibling subcategories under the same parent.
        $related = Ad::query()
            ->active()
            ->where('category_id', $ad->category_id)
            ->whereKeyNot($ad->id)
            ->with(['primaryImage', 'city'])
            ->latest()
            ->take(6)
            ->get();

        if ($related->isEmpty() && $ad->category?->parent_id) {
            $siblingIds = \App\Models\Category::where('parent_id', $ad->category->parent_id)->pluck('id');
            $related = Ad::query()
                ->active()
                ->whereIn('category_id', $siblingIds)
                ->whereKeyNot($ad->id)
                ->with(['primaryImage', 'city'])
                ->latest()
                ->take(6)
                ->get();
        }

        return view('ads.show', compact('ad', 'related'));
    }

    /**
     * Edit form (owner only).
     */
    public function edit(Ad $ad): View
    {
        $this->authorize('update', $ad);

        $ad->loadMissing(['attributes', 'images', 'category']);

        $categories = Category::roots()->active()->orderBy('sort_order')->get();
        $districts = District::orderBy('name')->get();

        return view('ads.create', [
            'categories' => $categories,
            'districts'  => $districts,
            'ad'         => $ad,
        ]);
    }

    /**
     * Update an existing ad (owner only).
     */
    public function update(StoreAdRequest $request, Ad $ad): RedirectResponse
    {
        $this->authorize('update', $ad);

        DB::transaction(function () use ($request, $ad) {
            $ad->update($request->safe()->only([
                'category_id', 'district_id', 'city_id', 'title', 'description',
                'condition', 'price', 'is_negotiable', 'contact_name', 'contact_phone',
            ]));

            $this->syncAttributes($ad, (array) $request->input('attributes', []));

            if ($request->hasFile('images')) {
                $this->storeImages($ad, $request->file('images', []));
            }
        });

        return redirect()
            ->route('ads.show', $ad)
            ->with('status', 'දැන්වීම යාවත්කාලීන කරන ලදී.');
    }

    /**
     * Delete an ad (owner only). Soft delete + remove image files.
     */
    public function destroy(Ad $ad): RedirectResponse
    {
        $this->authorize('delete', $ad);

        $dir = public_path('ads/'.$ad->id);
        if (is_dir($dir)) {
            array_map('unlink', glob($dir.'/*') ?: []);
            @rmdir($dir);
        }

        $ad->delete();

        return redirect()
            ->route('ads.myAds')
            ->with('status', 'දැන්වීම ඉවත් කරන ලදී.');
    }

    /**
     * The authenticated user's own ads.
     */
    public function myAds(Request $request): View
    {
        $ads = $request->user()
            ->ads()
            ->with('primaryImage')
            ->latest()
            ->paginate(15);

        return view('ads.my-ads', compact('ads'));
    }

    /**
     * AJAX: subcategories for a parent category.
     */
    public function getSubcategories(Category $category): JsonResponse
    {
        return response()->json(
            $category->children()->active()->get(['id', 'name', 'name_si', 'type'])
        );
    }

    /**
     * AJAX: cities for a district.
     */
    public function getCities(District $district): JsonResponse
    {
        return response()->json(
            $district->cities()->get(['id', 'name', 'name_si'])
        );
    }

    /**
     * Save dynamic key/value attributes for an ad.
     */
    protected function syncAttributes(Ad $ad, array $attributes): void
    {
        $ad->attributes()->delete();

        foreach ($attributes as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $ad->attributes()->create([
                'attribute_key'   => Str::of($key)->limit(255, ''),
                'attribute_value' => (string) $value,
            ]);
        }
    }

    /**
     * Move uploaded images into public/ads/{ad_id}/ and record them.
     *
     * @param  array<int, UploadedFile>  $files
     */
    protected function storeImages(Ad $ad, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $dir = public_path('ads/'.$ad->id);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $hasPrimary = $ad->images()->where('is_primary', true)->exists();
        $sort = (int) $ad->images()->max('sort_order');

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $name = Str::uuid().'.'.$file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize() ?: null;
            $file->move($dir, $name);

            $ad->images()->create([
                'path'          => 'ads/'.$ad->id.'/'.$name,
                'original_name' => $originalName,
                'size'          => $size,
                'is_primary'    => ! $hasPrimary,
                'sort_order'    => ++$sort,
            ]);

            $hasPrimary = true;
        }
    }
}
