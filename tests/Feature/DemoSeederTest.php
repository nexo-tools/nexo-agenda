<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DemoSeeder;

it('seeds a populated demo business', function () {
    $this->seed(DemoSeeder::class);

    $user = User::where('email', 'demo@nexoagenda.test')->first();
    expect($user)->not->toBeNull();

    $business = Business::where('slug', 'estudio-nexo')->first();
    expect($business)->not->toBeNull();
    expect($business->services()->count())->toBe(3);
    expect($business->professionals()->count())->toBe(2);
    expect($business->bookings()->count())->toBe(5);
    expect(Review::count())->toBe(2);
});

it('is idempotent when run twice', function () {
    $this->seed(DemoSeeder::class);
    $this->seed(DemoSeeder::class);

    expect(User::where('email', 'demo@nexoagenda.test')->count())->toBe(1);
    expect(Business::where('slug', 'estudio-nexo')->count())->toBe(1);
    expect(Booking::count())->toBe(5);
});
