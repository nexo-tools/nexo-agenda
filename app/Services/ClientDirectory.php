<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClientDirectory
{
    /**
     * Clients of a business, aggregated from its bookings. A client is keyed by
     * email when present, otherwise phone, otherwise name.
     *
     * Each row: key, name, email, phone, total, attended, no_shows, cancelled, last_visit.
     *
     * @return Collection<int, \stdClass>
     */
    public function forBusiness(Business $business, ?string $search = null): Collection
    {
        return DB::table('bookings')
            ->where('business_id', $business->id)
            ->selectRaw('COALESCE(client_email, client_phone, client_name) AS aggregate_key')
            ->selectRaw('MAX(client_name) AS name')
            ->selectRaw('MAX(client_email) AS email')
            ->selectRaw('MAX(client_phone) AS phone')
            ->selectRaw("SUM(CASE WHEN status != 'cancelled' THEN 1 ELSE 0 END) AS total")
            ->selectRaw("SUM(CASE WHEN status = 'attended' THEN 1 ELSE 0 END) AS attended")
            ->selectRaw("SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) AS no_shows")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled")
            ->selectRaw('MAX(starts_at) AS last_visit')
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('client_name', 'like', "%{$search}%")
                ->orWhere('client_email', 'like', "%{$search}%")
                ->orWhere('client_phone', 'like', "%{$search}%")))
            ->groupBy('aggregate_key')
            ->orderByDesc('last_visit')
            ->get()
            ->map(function (object $row): \stdClass {
                $client = new \stdClass;
                $client->key = (string) $row->aggregate_key;
                $client->name = (string) $row->name;
                $client->email = is_string($row->email) ? $row->email : null;
                $client->phone = is_string($row->phone) ? $row->phone : null;
                $client->total = (int) $row->total;
                $client->attended = (int) $row->attended;
                $client->no_shows = (int) $row->no_shows;
                $client->cancelled = (int) $row->cancelled;
                $client->last_visit = is_string($row->last_visit) ? $row->last_visit : null;

                return $client;
            });
    }
}
