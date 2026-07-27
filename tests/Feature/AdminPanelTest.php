<?php

use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {
    seedAll();
    $this->admin = User::where('role', UserRole::Admin)->firstOrFail();
});

it('keeps guests out of the panel', function () {
    $this->get('/admin')->assertRedirect();
});

it('lets an administrator in', function () {
    $this->actingAs($this->admin)->get('/admin')->assertOk();
});

it('renders every resource index and create page', function () {
    $this->actingAs($this->admin);

    $panel = Filament::getPanel('admin');
    Filament::setCurrentPanel($panel);

    $checked = 0;

    foreach ($panel->getResources() as $resource) {
        foreach (['index', 'create'] as $page) {
            if (! array_key_exists($page, $resource::getPages())) {
                continue;
            }

            $url = $resource::getUrl($page);

            expect($this->get($url)->status())
                // Name the resource in the failure, not just the status code.
                ->toBe(200, class_basename($resource)." [{$page}] did not render");

            $checked++;
        }
    }

    expect($checked)->toBeGreaterThan(20);
});

it('renders the panel in Persian and RTL', function () {
    $this->actingAs($this->admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('هیئت جودو کازرون', false);
});

it('badges the enrollments awaiting a decision', function () {
    $waiting = Enrollment::query()
        ->whereIn('status', [EnrollmentStatus::Pending->value, EnrollmentStatus::Paid->value])
        ->count();

    expect(EnrollmentResource::getNavigationBadge())
        ->toBe($waiting > 0 ? fa_number($waiting) : null);
});

it('serves the self-hosted admin font without a CDN', function () {
    expect(file_exists(public_path('css/vazirmatn.css')))->toBeTrue()
        ->and(file_exists(public_path('fonts/vazirmatn-var.woff2')))->toBeTrue();

    $css = file_get_contents(public_path('css/vazirmatn.css'));

    expect($css)->toContain('/fonts/vazirmatn-var.woff2')
        ->and($css)->not->toContain('http');
});
