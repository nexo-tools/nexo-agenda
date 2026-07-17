<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));

            $user->business()->create([
                ...$request->safe()->only(['category', 'city', 'whatsapp_phone']),
                'name' => $request->string('business_name'),
                'slug' => Business::uniqueSlugFor($request->string('business_name')->toString()),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
