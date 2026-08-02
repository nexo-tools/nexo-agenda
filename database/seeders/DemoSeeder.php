<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a ready-to-explore demo business. Development only — logging in as
 * demo@nexoagenda.test / password lands on a populated dashboard.
 *
 * Run with: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('DemoSeeder is disabled in production.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'demo@nexoagenda.test'],
            ['name' => 'Demo Nexo', 'password' => Hash::make('password')],
        );

        // Start clean so re-running is idempotent.
        $user->business?->delete();

        $business = $user->business()->create([
            'name' => 'Estudio Nexo',
            'slug' => 'estudio-nexo',
            'category' => 'peluqueria',
            'city' => 'Palermo, CABA',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'whatsapp_phone' => '+5491122334455',
            'address' => 'Av. Santa Fe 1234',
            'description' => 'Cortes, color y peinados. Reserva online en segundos.',
            'in_directory' => true,
            'brand_color' => '#0d9488',
        ]);

        $services = collect([
            ['name' => 'Corte clásico', 'duration_minutes' => 30, 'price' => 800000],
            ['name' => 'Corte + barba', 'duration_minutes' => 45, 'price' => 1100000],
            ['name' => 'Color', 'duration_minutes' => 90, 'price' => 2500000],
        ])->map(fn (array $attributes) => $business->services()->create([
            ...$attributes,
            'mode' => 'in_person',
            'buffer_minutes' => 0,
            'min_notice_hours' => 2,
            'cancellation_hours' => 12,
            'max_advance_days' => 60,
            'is_active' => true,
        ]));

        $professionals = collect(['Ana', 'Luis'])->map(fn (string $name) => Professional::factory()
            ->for($business)
            ->withWeekdaySchedule('09:00', '19:00')
            ->create(['name' => $name]));

        $this->seedBookings($business, $services, $professionals);

        $this->command->info('Demo listo: demo@nexoagenda.test / password → /estudio-nexo');
    }

    /**
     * @param  Collection<int, Service>  $services
     * @param  Collection<int, Professional>  $professionals
     */
    private function seedBookings(Business $business, Collection $services, Collection $professionals): void
    {
        $tz = $business->timezone;
        $today = CarbonImmutable::now($tz)->startOfDay();

        // The professional and the service come from the round-robin below, so
        // the ORDER of this list is what assigns them: even index → Ana, odd →
        // Luis. The last four entries were appended rather than inserted for
        // exactly that reason — inserting would have reassigned every booking
        // that came before them.
        //
        // Today's rows exist because the dashboard opens on the day view: with
        // nothing at offset 0 the first screen an owner (or a screenshot) sees
        // is an empty agenda, which is the one thing the landing must not show.
        $plan = [
            [1, '10:00', BookingStatus::Confirmed, 'Sofía Ramírez'],
            [1, '11:30', BookingStatus::Confirmed, 'Martín Díaz'],
            [-2, '15:00', BookingStatus::Attended, 'Carla Núñez'],
            [-5, '16:30', BookingStatus::Attended, 'Diego Torres'],
            [-3, '12:00', BookingStatus::NoShow, 'Paula Vega'],
            [0, '11:30', BookingStatus::Confirmed, 'Lucía Ferrer'],   // Luis
            [0, '10:00', BookingStatus::Confirmed, 'Tomás Aguirre'],  // Ana
            [2, '14:00', BookingStatus::Confirmed, 'Valeria Sosa'],   // Luis
            [0, '15:30', BookingStatus::Confirmed, 'Nicolás Peña'],   // Ana
        ];

        foreach ($plan as $index => [$dayOffset, $time, $status, $client]) {
            $service = $services[$index % $services->count()];
            $professional = $professionals[$index % $professionals->count()];
            $start = $today->addDays($dayOffset)->setTimeFromTimeString($time);

            $booking = $business->bookings()->create([
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'client_name' => $client,
                'client_email' => 'cliente'.$index.'@example.com',
                'client_phone' => '+54911'.(20000000 + $index),
                'starts_at' => $start->utc(),
                'ends_at' => $start->addMinutes($service->duration_minutes)->utc(),
                'status' => $status,
                'management_token' => Booking::newManagementToken()['hash'],
            ]);

            if ($status === BookingStatus::Attended) {
                $business->reviews()->create([
                    'booking_id' => $booking->id,
                    'rating' => 5,
                    'comment' => 'Excelente atención, muy recomendable.',
                    'client_name' => $client,
                    'is_hidden' => false,
                ]);
            }
        }
    }
}
