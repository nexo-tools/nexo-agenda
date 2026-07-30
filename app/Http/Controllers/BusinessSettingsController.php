<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BusinessSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('app.settings.edit', [
            'business' => $request->user()->business,
        ]);
    }

    public function update(BusinessSettingsRequest $request): RedirectResponse
    {
        $business = $request->user()->business;

        $data = $request->safe()->only([
            'name', 'category', 'city', 'address', 'whatsapp_phone', 'description', 'brand_color',
        ]);
        $data['in_directory'] = $request->boolean('in_directory');

        if ($request->boolean('remove_logo') && $business->logo_path !== null) {
            Storage::disk('public')->delete($business->logo_path);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($business->logo_path !== null) {
                Storage::disk('public')->delete($business->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $business->update($data);

        return redirect()->route('settings.edit')->with('status', __('Settings saved.'));
    }
}
