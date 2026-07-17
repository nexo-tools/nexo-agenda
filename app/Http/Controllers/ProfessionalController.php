<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessionalRequest;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.professionals.index', [
            'professionals' => $request->user()->business->professionals()
                ->withCount('scheduleBlocks')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $professional = $request->user()->business->professionals()->create($request->only('name'));

        return redirect()
            ->route('professionals.edit', $professional)
            ->with('status', __('Profesional agregado. Ahora define sus horarios.'));
    }

    public function edit(Professional $professional): View
    {
        Gate::authorize('update', $professional);

        $blocksByDay = $professional->scheduleBlocks
            ->groupBy('weekday')
            ->map(fn ($blocks) => $blocks->map(fn ($b) => [
                'start' => substr((string) $b->start_time, 0, 5),
                'end' => substr((string) $b->end_time, 0, 5),
            ])->values());

        return view('app.professionals.edit', [
            'professional' => $professional,
            'blocksByDay' => $blocksByDay,
            'absences' => $professional->absences()->orderBy('starts_on')->get(),
        ]);
    }

    public function update(ProfessionalRequest $request, Professional $professional): RedirectResponse
    {
        Gate::authorize('update', $professional);

        DB::transaction(function () use ($request, $professional) {
            $professional->update([
                'name' => $request->string('name'),
                'is_active' => $request->boolean('is_active'),
            ]);

            $professional->scheduleBlocks()->delete();

            foreach ($request->collect('blocks') as $weekday => $ranges) {
                foreach ($ranges as $range) {
                    $professional->scheduleBlocks()->create([
                        'weekday' => (int) $weekday,
                        'start_time' => $range['start'],
                        'end_time' => $range['end'],
                    ]);
                }
            }
        });

        return redirect()->route('professionals.index')->with('status', __('Profesional actualizado.'));
    }

    public function destroy(Professional $professional): RedirectResponse
    {
        Gate::authorize('delete', $professional);

        $professional->delete();

        return redirect()->route('professionals.index')->with('status', __('Profesional eliminado.'));
    }
}
