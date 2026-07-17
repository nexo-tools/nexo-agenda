<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Services\IcsFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class FeedController extends Controller
{
    public function professional(string $token): Response
    {
        $professional = Professional::where('feed_token', $token)->firstOrFail();

        return response((new IcsFile)->forProfessional($professional), 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="agenda.ics"',
        ]);
    }

    public function regenerate(Request $request, Professional $professional): RedirectResponse
    {
        Gate::authorize('update', $professional);

        $professional->regenerateFeedToken();

        return back()->with('status', __('Enlace del calendario regenerado. El anterior dejó de funcionar.'));
    }
}
