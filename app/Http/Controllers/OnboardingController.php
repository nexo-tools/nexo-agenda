<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingRequest;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /** Create-your-business form; owners who already have one skip it. */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->business !== null) {
            return redirect()->route('dashboard');
        }

        return view('app.onboarding.create');
    }

    public function store(OnboardingRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->business !== null) {
            return redirect()->route('dashboard');
        }

        // Mirrors RegisteredUserController's business creation.
        $user->business()->create([
            ...$request->safe()->only(['category', 'city', 'whatsapp_phone']),
            'name' => $request->string('business_name'),
            'slug' => Business::uniqueSlugFor($request->string('business_name')->toString()),
        ]);

        return redirect()->route('dashboard');
    }
}
