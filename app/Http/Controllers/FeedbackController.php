<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\FeedbackReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        return view('help.contact', ['types' => FeedbackReport::TYPES]);
    }

    public function store(FeedbackRequest $request): RedirectResponse
    {
        FeedbackReport::create($request->validated());

        return redirect()->route('contact')
            ->with('status', __('¡Gracias! Recibimos tu mensaje.'));
    }
}
