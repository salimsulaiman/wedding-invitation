# AGENT.md — EverVow (Digital Wedding Invitation Platform)

> Reference document for AI coding agents (and humans) working on this codebase.
> Derived from: provided migrations, dashboard/builder screenshots, and stated stack decisions.

---

## 1. Project Overview

EverVow is a SaaS-style digital wedding invitation builder. Customers register, pick a theme,
fill in couple/event/gallery/gift details through a "Builder" UI, publish an invitation, and
share a public link. Guests visiting the public link can view the invitation, RSVP, and leave
well-wishes/gifts info is displayed (bank transfer / QRIS / physical gift address).

Two user roles exist (`users.role`): `admin` (manages themes, music, users) and `customer`
(builds and manages their own invitations).

---

## 2. Tech Stack

| Layer | Choice |
|---|---|
| Backend framework | Laravel 13 |
| Frontend | Vue 3 (Composition API) |
| Bridge | Inertia.js (server-driven SPA — **no separate API routes**) |
| Styling | Tailwind CSS |
| State/interactivity on public invitation page | Alpine.js (lightweight, for the public-facing themed invitation only — not the authenticated app) |
| Auth | Laravel built-in session auth (`sessions` table present, no Sanctum/API tokens needed since everything is Inertia) |
| DB | MySQL/MariaDB (based on migration syntax: `decimal`, `enum`, `foreignId`) |

**Important architectural rule:** This app does **not** use `routes/api.php` as the primary
data layer. All routes live in `routes/web.php` (optionally split into `routes/admin.php` /
`routes/customer.php` and required from `web.php`). Every route:

1. Is protected by appropriate middleware (`auth`, `verified`, role checks, `signed` for public
   invitation links if needed).
2. Delegates to a Controller method.
3. The Controller performs the action/query and returns `Inertia::render(...)` with props —
   **never** `response()->json()` for page-serving routes.
4. Form submissions from Vue use Inertia's `useForm()` / `router.post/put/delete`, which post
   back to the same `web.php` routes (still not JSON API endpoints in the REST sense, but
   Inertia visits).

If any true JSON endpoint is ever needed (e.g. a live map autocomplete proxy, or polling for
guest count), it should be treated as an explicit exception and documented inline in
`routes/web.php`, not assumed as the default pattern.

---

## 3. Folder Structure

```
evervow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ThemeController.php
│   │   │   │   ├── MusicController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Customer/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── InvitationController.php        // list / create / builder shell
│   │   │   │   ├── InvitationEventController.php    // akad/resepsi events
│   │   │   │   ├── InvitationGalleryController.php
│   │   │   │   ├── InvitationGiftController.php      // "Amplop Digital"
│   │   │   │   ├── LoveStoryController.php
│   │   │   │   ├── CoupleController.php              // "Mempelai & Acara"
│   │   │   │   └── GuestController.php               // "Kelola Tamu"
│   │   │   ├── Public/
│   │   │   │   ├── InvitationShowController.php      // public slug page
│   │   │   │   └── RsvpController.php                // guest RSVP + wishes
│   │   │   └── Auth/                                  // Breeze/Fortify-style, Inertia-based
│   │   ├── Middleware/
│   │   │   ├── EnsureUserIsAdmin.php
│   │   │   └── EnsureInvitationOwner.php              // authorize invitation_id belongs to auth user
│   │   └── Requests/
│   │       ├── StoreInvitationRequest.php
│   │       ├── UpdateCoupleRequest.php
│   │       ├── StoreInvitationEventRequest.php
│   │       ├── StoreGalleryRequest.php
│   │       ├── StoreLoveStoryRequest.php
│   │       ├── StoreGiftRequest.php
│   │       └── StoreRsvpRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Theme.php
│   │   ├── Music.php
│   │   ├── Invitation.php
│   │   ├── Couple.php
│   │   ├── InvitationEvent.php
│   │   ├── InvitationGallery.php
│   │   ├── LoveStory.php
│   │   ├── InvitationGift.php
│   │   └── Guest.php                                  // see §5.1 (inferred model)
│   ├── Policies/
│   │   └── InvitationPolicy.php
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── seeders/
│   │   ├── ThemeSeeder.php
│   │   ├── MusicSeeder.php
│   │   └── AdminUserSeeder.php
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── xxxx_xx_xx_create_themes_table.php
│       ├── xxxx_xx_xx_create_music_table.php
│       ├── xxxx_xx_xx_create_invitations_table.php
│       ├── xxxx_xx_xx_create_couples_table.php
│       ├── xxxx_xx_xx_create_invitation_events_table.php
│       ├── xxxx_xx_xx_create_invitation_galleries_table.php
│       ├── xxxx_xx_xx_create_love_stories_table.php
│       ├── xxxx_xx_xx_create_invitation_gifts_table.php
│       └── xxxx_xx_xx_create_guests_table.php          // to be added, see §5.1
├── public/
│   └── build/                                          // Vite output
├── resources/
│   ├── js/
│   │   ├── app.js                                      // Inertia app bootstrap
│   │   ├── ssr.js                                       // (optional, for public invitation SEO)
│   │   ├── Layouts/
│   │   │   ├── AuthenticatedLayout.vue                 // dashboard shell w/ sidebar (Image 1)
│   │   │   ├── GuestLayout.vue                          // login/register
│   │   │   └── BuilderLayout.vue                        // 2-col builder w/ live preview (Image 2)
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   └── Register.vue
│   │   │   ├── Dashboard.vue                            // "Halo, Test User!" screen
│   │   │   ├── Invitations/
│   │   │   │   ├── Index.vue                            // "Undangan Saya"
│   │   │   │   └── Builder.vue                          // orchestrates the 5 tabs below
│   │   │   ├── Admin/
│   │   │   │   ├── Themes/Index.vue
│   │   │   │   └── Music/Index.vue
│   │   │   └── Public/
│   │   │       └── Show.vue                             // public invitation renderer (per theme)
│   │   ├── Components/
│   │   │   ├── Builder/
│   │   │   │   ├── TabCoupleEvent.vue                   // "Mempelai & Acara"
│   │   │   │   ├── TabGallery.vue                       // "Galeri Foto"
│   │   │   │   ├── TabGift.vue                          // "Amplop Digital"
│   │   │   │   ├── TabTheme.vue                         // "Tema Desain"
│   │   │   │   ├── TabGuests.vue                        // "Kelola Tamu"
│   │   │   │   └── LivePreview.vue                       // phone-frame simulator
│   │   │   ├── Dashboard/
│   │   │   │   ├── WelcomeBanner.vue
│   │   │   │   └── StatCard.vue                          // Status/Tamu/Amplop cards
│   │   │   └── UI/                                       // buttons, inputs, tabs (shared)
│   │   └── Themes/                                       // per-theme Vue components for public page
│   │       └── Rustic/
│   │           └── Cover.vue
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── app.blade.php                                 // single Inertia root view
├── routes/
│   ├── web.php                                            // requires below
│   ├── admin.php
│   ├── customer.php
│   └── public.php                                          // slug-based invitation + rsvp routes
├── storage/
│   └── app/public/
│       ├── covers/
│       ├── galleries/
│       ├── couple-photos/
│       └── qris/
├── tests/
│   ├── Feature/
│   │   ├── InvitationBuilderTest.php
│   │   └── RsvpTest.php
│   └── Unit/
├── .env.example
├── composer.json
├── package.json
└── vite.config.js
```

---

## 4. Routing Convention (web.php example)

```php
// routes/web.php
Route::middleware('guest')->group(function () {
    require __DIR__.'/auth.php';
});

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__.'/customer.php';
});

Route::middleware(['auth', 'verified', EnsureUserIsAdmin::class])->group(function () {
    require __DIR__.'/admin.php';
});

require __DIR__.'/public.php'; // no auth — public invitation viewing + rsvp
```

```php
// routes/customer.php
Route::prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::get('invitations/{invitation}/builder', [InvitationController::class, 'builder'])
        ->middleware(EnsureInvitationOwner::class)
        ->name('invitations.builder');
    Route::put('invitations/{invitation}', [InvitationController::class, 'update'])
        ->middleware(EnsureInvitationOwner::class)
        ->name('invitations.update');

    Route::put('invitations/{invitation}/couple', [CoupleController::class, 'update'])->name('couple.update');
    Route::resource('invitations.events', InvitationEventController::class)->except(['show']);
    Route::resource('invitations.gallery', InvitationGalleryController::class)->except(['show']);
    Route::resource('invitations.love-stories', LoveStoryController::class)->except(['show']);
    Route::resource('invitations.gifts', InvitationGiftController::class)->except(['show']);
    Route::resource('invitations.guests', GuestController::class)->except(['show']);
});
```

Every controller method's happy path ends in `Inertia::render('Page/Name', [...])`, or a
`redirect()->route(...)` for store/update/destroy actions.

---

## 5. Database Schema (from supplied migrations)

- `users` — id, name, username (unique), email (unique), phone, password, `role` enum
  (admin/customer), is_active, soft deletes. Indexed on `role`, `is_active`.
- `password_reset_tokens`, `sessions` — Laravel defaults.
- `themes` — name, slug (unique), thumbnail, description, is_active, soft deletes.
- `music` — title, artist, file, duration, is_active, soft deletes.
- `invitations` — belongs to `user`, `theme`; nullable belongs to `music`. title, slug (unique),
  cover_title, cover_description, cover_image, `status` enum (draft/published), published_at,
  soft deletes. Indexed on `slug`, `status`.
- `couples` — one-to-one with `invitations` (unique FK). groom/bride name, full_name, photo,
  father, mother, instagram fields.
- `invitation_events` — belongs to `invitation`. title, `type` enum (akad/resepsi/other), date,
  start_time, end_time, venue, address, google_maps, latitude/longitude (decimal 10,7),
  sort_order.
- `invitation_galleries` — belongs to `invitation`. image, caption, sort_order.
- `love_stories` — belongs to `invitation`. title, description, story_date, image, sort_order.
- `invitation_gifts` — belongs to `invitation`. `type` enum (bank/qris/gift), bank_name,
  account_name, account_number, qris, receiver_address. Indexed on `type`.

### 5.1 Gap identified — a `guests` table is required but not in the supplied migrations

Both screenshots reference guest data that has no backing table yet:
- Dashboard: **"TOTAL TAMU UNDANGAN"** stat (currently 0).
- Builder: **"Kelola Tamu"** tab with a live count badge, and the public preview shows
  **"Kepada Yth. ... Tamu Kehormatan"**, implying named/personalized guest links.

Suggested migration to add:

```php
Schema::create('guests', function (Blueprint $table) {
    $table->id();

    $table->foreignId('invitation_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('name');
    $table->string('slug')->nullable(); // personalized link e.g. ?to=slug
    $table->string('phone')->nullable();
    $table->unsignedInteger('quota')->default(1); // seats/pax

    $table->enum('rsvp_status', ['pending', 'attending', 'not_attending'])->default('pending');
    $table->unsignedInteger('attending_count')->nullable();
    $table->text('wish_message')->nullable();
    $table->timestamp('rsvp_at')->nullable();

    $table->timestamps();

    $table->index('rsvp_status');
});
```

A companion "amplop digital" record of who sent money (**"TOTAL AMPLOP (KADO)"** stat) is not
in the migrations either — this likely aggregates from `invitation_gifts` config plus a log of
confirmations. If real gift *tracking* (not just displaying bank/QRIS details) is needed, add a
`gift_confirmations` table (invitation_id, guest_id nullable, sender_name, amount nullable,
method, note, timestamps).

---

## 6. Feature Modules (mapped from UI screenshots)

### 6.1 Dashboard (`/client/dashboard`) — Image 1
- Welcome banner with CTA "+ Buat Undangan Sekarang" → `invitations.create`.
- 3 stat cards: Status Undangan (draft/published), Total Tamu Undangan (`guests` count), Total
  Amplop/Kado (gift confirmations count or sum).
- Sidebar nav: Dashboard, Buat Undangan, Undangan Saya.

### 6.2 Builder (`/client/invitations/{invitation}/builder`) — Image 2
Two-column layout: left = tabbed form, right = sticky live phone-frame preview
(`LivePreview.vue`) that re-renders on every input change (Vue reactive props, no page reload).

Tabs, in order:
1. **Mempelai & Acara** → `couples` + `invitation_events` (type=resepsi shown in screenshot;
   akad presumably a second block above/below).
2. **Galeri Foto** → `invitation_galleries`.
3. **Amplop Digital** → `invitation_gifts`.
4. **Tema Desain** → select from `themes`.
5. **Kelola Tamu** ( count badge ) → `guests` (see §5.1).

Header shows current theme name and live guest count as global builder state. Footer action:
"Simpan & Publikasikan Undangan" → sets `invitations.status = published`,
`published_at = now()`.

### 6.3 Public invitation page (`/{invitation:slug}`)
Rendered per-theme (Alpine.js-driven interactions: cover reveal "Buka Undangan" button, music
autoplay toggle, countdown, RSVP form). Optional `?to={guest-slug}` query param personalizes the
"Kepada Yth." block by loading the matching `guests` row.

---

## 7. Conventions

- **Authorization**: every customer-scoped route must verify `invitation.user_id === auth()->id()`
  — via `EnsureInvitationOwner` middleware or a Policy (`InvitationPolicy@update`), not just a
  query filter, so agents don't accidentally leak IDOR bugs.
- **Validation**: dedicated `FormRequest` per resource; never inline `$request->validate()` in
  controllers for anything beyond trivial toggle actions.
- **File uploads**: store under `storage/app/public/{covers,galleries,couple-photos,qris}` via
  `Storage::disk('public')`, return the `Storage::url()` path in Inertia props — don't hardcode
  `/storage/...` string concatenation in Vue.
- **Soft deletes**: `themes`, `music`, `invitations` are soft-deletable — admin "delete" actions
  should stay reversible; hard-delete only via a separate force-delete admin action.
- **Slugs**: `invitations.slug` and `themes.slug` are unique — generate with `Str::slug()` +
  uniqueness-suffix on collision, not raw title lowercased.
- **Inertia partial reloads**: use `only: []` reloads for the Builder's tab-switching so
  switching tabs doesn't refetch the whole invitation payload every time.
- **Language**: UI copy is Bahasa Indonesia (see screenshots); keep field labels, flash
  messages, and validation messages in Bahasa Indonesia unless a locale switcher is added.

---

## 8. Open Questions for Product/Design (not resolved by supplied assets)

- Is `akad` event input a separate form block, or toggled via `invitation_events.type`
  dynamically in the same tab? (Screenshot only shows "Acara Resepsi" fields.)
- Does "Amplop Digital" need send/receive confirmation tracking, or is it purely
  informational display of bank/QRIS details? Affects whether `gift_confirmations` (§5.1) is
  needed.
- Is guest RSVP public (`/{slug}?to=...`) or does it require a signed URL per guest to prevent
  guessing/enumeration? Recommend Laravel signed routes for personalized guest links.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- tightenco/ziggy (ZIGGY) - v2
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v3
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
