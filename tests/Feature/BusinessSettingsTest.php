<?php

use App\Models\Business;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->business = Business::factory()->create(['slug' => 'ajustes-test']);
    $this->owner = $this->business->user;
});

function updateSettings(array $overrides = []): TestResponse
{
    return test()->actingAs(test()->owner)->put('/app/settings', [
        'name' => test()->business->name,
        'category' => test()->business->category,
        'city' => test()->business->city,
        ...$overrides,
    ]);
}

it('shows the settings form', function () {
    $this->actingAs($this->owner)
        ->get('/app/settings')
        ->assertOk()
        ->assertSee('Ajustes del negocio')
        ->assertSee($this->business->name);
});

it('updates business data and brand color', function () {
    updateSettings([
        'name' => 'Nuevo Nombre',
        'description' => 'La mejor barbería.',
        'brand_color' => '#7c3aed',
    ])->assertRedirect('/app/settings');

    $this->business->refresh();
    expect($this->business->name)->toBe('Nuevo Nombre')
        ->and($this->business->brand_color)->toBe('#7c3aed')
        ->and($this->business->slug)->toBe('ajustes-test'); // slug never changes
});

it('rejects an invalid color', function () {
    updateSettings(['brand_color' => 'rojo'])->assertSessionHasErrors('brand_color');
});

it('uploads and removes a logo', function () {
    Storage::fake('public');

    // 1x1 transparent PNG — avoids needing the GD extension in the test runner.
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

    updateSettings(['logo' => UploadedFile::fake()->createWithContent('logo.png', $png)]);

    $path = $this->business->refresh()->logo_path;
    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);

    updateSettings(['remove_logo' => '1']);

    expect($this->business->refresh()->logo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('applies the accent color on the public page', function () {
    $this->business->update(['brand_color' => '#7c3aed']);

    $this->get('/ajustes-test')
        ->assertOk()
        ->assertSee('#7c3aed', false);
});

it('derives the whole brand scale from the accent, in both themes', function () {
    $this->business->update(['brand_color' => '#7c3aed']);

    $html = (string) $this->get('/ajustes-test')->assertOk()->getContent();

    // The scale is re-derived, not three utilities patched with !important: the
    // slot hover ring and the tinted chips used to stay teal on a page whose CTA
    // already wore the business colour.
    expect($html)->toContain('--accent: #7c3aed')
        ->toContain('--color-brand-100')
        ->toContain('--color-brand-500')
        ->and($html)->not->toContain('!important');

    // Dark mode gets its own tints. Forcing the light accent into dark left a
    // dark accent (say #134e4a) unreadable on a dark page.
    expect($html)->toContain(':root[data-theme="dark"]');
});

it('uses dark text on light accents and white on dark ones', function () {
    $this->business->update(['brand_color' => '#f9e79f']);
    expect($this->business->accentTextColor())->toBe('#0f172a');

    $this->business->update(['brand_color' => '#1e293b']);
    expect($this->business->accentTextColor())->toBe('#ffffff');
});
