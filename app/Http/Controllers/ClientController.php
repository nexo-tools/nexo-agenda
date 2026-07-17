<?php

namespace App\Http\Controllers;

use App\Services\ClientDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{
    public function __construct(private readonly ClientDirectory $directory) {}

    public function index(Request $request): View
    {
        $business = $request->user()->business;
        $search = $request->string('q')->toString() ?: null;

        return view('app.clients.index', [
            'business' => $business,
            'clients' => $this->directory->forBusiness($business, $search),
            'search' => $search,
        ]);
    }

    public function show(Request $request): View
    {
        $business = $request->user()->business;
        $key = $request->string('key')->toString();

        abort_if($key === '', 404);

        $bookings = $business->bookings()
            ->whereRaw('COALESCE(client_email, client_phone, client_name) = ?', [$key])
            ->with(['service:id,name', 'professional:id,name'])
            ->orderByDesc('starts_at')
            ->get();

        abort_if($bookings->isEmpty(), 404);

        return view('app.clients.show', [
            'business' => $business,
            'bookings' => $bookings,
            'client' => $bookings->first(),
            'tz' => $business->timezone,
        ]);
    }

    public function exportClients(Request $request): StreamedResponse
    {
        $business = $request->user()->business;
        $clients = $this->directory->forBusiness($business);

        return $this->csv('clientes.csv', [
            ['nombre', 'email', 'telefono', 'turnos', 'asistidos', 'no_asistidos', 'cancelados', 'ultima_visita'],
            ...$clients->map(fn ($c) => [
                $c->name, $c->email, $c->phone, $c->total, $c->attended, $c->no_shows, $c->cancelled, $c->last_visit,
            ]),
        ]);
    }

    public function exportBookings(Request $request): StreamedResponse
    {
        $business = $request->user()->business;
        $tz = $business->timezone;

        $rows = $business->bookings()
            ->with(['service:id,name', 'professional:id,name'])
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($b) => [
                $b->starts_at->setTimezone($tz)->format('Y-m-d H:i'),
                $b->service->name,
                $b->professional->name,
                $b->client_name,
                $b->client_email,
                $b->client_phone,
                $b->status->value,
                $b->note,
            ]);

        return $this->csv('turnos.csv', [
            ['fecha', 'servicio', 'profesional', 'cliente', 'email', 'telefono', 'estado', 'nota'],
            ...$rows,
        ]);
    }

    /** @param  iterable<int, array<int, mixed>>  $rows */
    private function csv(string $filename, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\u{FEFF}"); // BOM so Excel opens UTF-8 correctly

            foreach ($rows as $row) {
                fputcsv($out, array_map(fn ($v) => $v === null ? '' : (string) $v, $row));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
