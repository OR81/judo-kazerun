<?php

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\ContactMessage;
use App\Models\Download;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\NewsletterSubscriber;

beforeEach(fn () => seedAll());

it('renders every public page', function (string $uri) {
    $this->get($uri)->assertOk();
})->with([
    '/',
    '/schedule',
    '/register',
    '/coaches',
    '/board',
    '/athletes',
    '/athletes/national-team',
    '/news',
    '/events',
    '/gallery',
    '/gallery/videos',
    '/downloads',
    '/contact',
    '/login',
]);

it('renders every detail page', function () {
    $this->get('/coaches/'.Coach::value('slug'))->assertOk();
    $this->get('/athletes/'.Athlete::value('slug'))->assertOk();
    $this->get('/news/'.News::value('slug'))->assertOk();
    $this->get('/events/'.Event::value('slug'))->assertOk();
    $this->get('/gallery/'.GalleryAlbum::value('slug'))->assertOk();
});

it('marks up pages as Persian and RTL', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<html lang="fa" dir="rtl"', false);
});

it('hides scheduled posts until their publish time', function () {
    $future = News::factory()->create([
        'title' => 'خبر زمان‌بندی‌شده',
        'slug' => 'future-post',
        'published_at' => now()->addWeek(),
    ]);

    $this->get('/news')->assertOk()->assertDontSee($future->title);
    $this->get('/news/'.$future->slug)->assertNotFound();
});

it('filters news by category and search term', function () {
    $article = News::query()->published()->with('category')->first();

    $this->get('/news?category='.$article->category->slug)
        ->assertOk()
        ->assertSee($article->category->name, false);

    $this->get('/news?q='.urlencode('مسابقات'))->assertOk();
});

it('paginates the news archive', function () {
    // 12 seeded articles at 9 per page, minus the featured hero.
    $this->get('/news')->assertOk();
    $this->get('/news?page=2')->assertOk();
});

it('increments the view counter when an article is read', function () {
    $article = News::query()->published()->first();
    $before = $article->views;

    $this->get('/news/'.$article->slug)->assertOk();

    expect($article->fresh()->views)->toBe($before + 1);
});

it('accepts a contact message', function () {
    $this->post('/contact', [
        'name' => 'رضا کاویانی',
        'phone' => '09171234567',
        'subject' => 'سؤال دربارهٔ ثبت‌نام',
        'message' => 'سلام، شرایط ثبت‌نام ردهٔ کودکان چگونه است؟',
    ])->assertRedirect()->assertSessionHas('success');

    expect(ContactMessage::where('name', 'رضا کاویانی')->exists())->toBeTrue();
});

it('accepts Persian digits in a contact phone number', function () {
    $this->post('/contact', [
        'name' => 'مریم اسدی',
        'phone' => '۰۹۱۷۱۲۳۴۵۶۷',
        'subject' => 'کلاس بانوان',
        'message' => 'آیا کلاس بانوان در روزهای پنج‌شنبه برگزار می‌شود؟',
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect(ContactMessage::where('phone', '09171234567')->exists())->toBeTrue();
});

it('rejects an invalid contact submission', function () {
    $this->post('/contact', ['name' => 'ا', 'subject' => '', 'message' => 'کوتاه'])
        ->assertSessionHasErrors(['name', 'subject', 'message']);
});

it('subscribes an address to the newsletter and tolerates re-subscribing', function () {
    $this->post('/newsletter', ['email' => 'sara@example.com'])
        ->assertRedirect()->assertSessionHas('success');

    $subscriber = NewsletterSubscriber::where('email', 'sara@example.com')->firstOrFail();
    expect($subscriber->token)->not->toBeEmpty();

    $subscriber->update(['unsubscribed_at' => now()]);

    $this->post('/newsletter', ['email' => 'sara@example.com'])->assertRedirect();

    expect($subscriber->fresh()->unsubscribed_at)->toBeNull();
});

it('unsubscribes through the token link', function () {
    $subscriber = NewsletterSubscriber::create(['email' => 'ali@example.com']);

    $this->get('/newsletter/'.$subscriber->token.'/unsubscribe')->assertRedirect('/');

    expect($subscriber->fresh()->unsubscribed_at)->not->toBeNull();
});

it('tells the visitor when a download has no file behind it yet', function () {
    $download = Download::query()->first();

    $this->from('/downloads')
        ->get('/downloads/'.$download->slug.'/file')
        ->assertRedirect('/downloads')
        ->assertSessionHas('warning');
});
