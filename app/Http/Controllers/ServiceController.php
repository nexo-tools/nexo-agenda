<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.services.index', [
            'services' => $request->user()->business->services()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('app.services.create');
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $request->user()->business->services()->create($request->validated());

        return redirect()->route('services.index')->with('status', __('Service created.'));
    }

    public function edit(Service $service): View
    {
        Gate::authorize('update', $service);

        return view('app.services.edit', ['service' => $service]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        Gate::authorize('update', $service);

        $service->update($request->validated());

        return redirect()->route('services.index')->with('status', __('Service updated.'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()->route('services.index')->with('status', __('Service deleted.'));
    }
}
