<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function index(Request $request, ?string $category = null): View
    {
        abort_if($category !== null && ! in_array($category, config('nexo.categories'), true), 404);

        $search = $request->string('q')->toString();
        $city = $request->string('ciudad')->toString();
        $category ??= $request->string('categoria')->toString() ?: null;

        $businesses = Business::query()
            ->where('in_directory', true)
            ->withAvg('visibleReviews as rating', 'rating')
            ->withCount('visibleReviews as rating_count')
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($city !== '', fn ($query) => $query->where('city', 'like', "%{$city}%"))
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('rating')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('public.directory', [
            'businesses' => $businesses,
            'category' => $category,
            'city' => $city,
            'search' => $search,
            'cities' => Business::where('in_directory', true)->distinct()->orderBy('city')->pluck('city'),
        ]);
    }
}
