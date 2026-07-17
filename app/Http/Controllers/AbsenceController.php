<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AbsenceController extends Controller
{
    public function store(Request $request, Professional $professional): RedirectResponse
    {
        Gate::authorize('update', $professional);

        $validated = $request->validate([
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $professional->absences()->create($validated);

        return back()->with('status', __('Ausencia registrada.'));
    }

    public function destroy(Absence $absence): RedirectResponse
    {
        Gate::authorize('update', $absence->professional);

        $absence->delete();

        return back()->with('status', __('Ausencia eliminada.'));
    }
}
