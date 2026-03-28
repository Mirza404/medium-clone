# Medium Clone

Summary
-------
This is a small Laravel application for publishing posts with categories, user profiles, follow/clap interactions, and email verification. The README below outlines what I did, how to run the project locally, where important code lives, and the video featuring a small run-through of the web page.

What I built
------------
- User registration, login and email verification flows using Laravel auth controllers (email verification required for some routes).
  - See [`App\Http\Controllers\Auth\RegisteredUserController`](app/Http/Controllers/Auth/RegisteredUserController.php) and [`App\Http\Controllers\Auth\VerifyEmailController`](app/Http/Controllers/Auth/VerifyEmailController.php).
- Posts with images, categories, slugs, and a read-time helper.
  - See [`App\Http\Controllers\PostController`](app/Http/Controllers/PostController.php) and [`App\Models\Post`](app/Models/Post.php).
- User profiles with avatar upload and bio editing.
  - See [`App\Http\Controllers\ProfileController`](app/Http/Controllers/ProfileController.php) and [`App\Models\User`](app/Models/User.php).
- Social interactions:
  - Following users: endpoints and blade components wired to `/follow/{user}` (client side uses axios).
  - Clapping posts: endpoints and a reactive component that posts to `/clap/{post}`.
  - See [`App\Http\Controllers\ClapController`](app/Http/Controllers/ClapController.php) and component [resources/views/components/clap-button.blade.php](resources/views/components/clap-button.blade.php).
- Media handling with Spatie MediaLibrary for post images and conversions.
  - Post media conversions are defined in [`App\Models\Post`](app/Models/Post.php).

Project structure (high level)
-------------------------------
- Routes: [routes/web.php](routes/web.php)
- HTTP controllers: [app/Http/Controllers](app/Http/Controllers)
  - Auth controllers: [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php), [app/Http/Controllers/Auth/VerifyEmailController.php](app/Http/Controllers/Auth/VerifyEmailController.php)
  - Post controller: [app/Http/Controllers/PostController.php](app/Http/Controllers/PostController.php)
  - Clap & follower controllers: [app/Http/Controllers/ClapController.php](app/Http/Controllers/ClapController.php), [app/Http/Controllers/FollowerController.php](app/Http/Controllers/FollowerController.php)
- Models: [app/Models/User.php](app/Models/User.php), [app/Models/Post.php](app/Models/Post.php)
- Views & components: [resources/views](resources/views)
  - Register / Login: [resources/views/auth/register.blade.php](resources/views/auth/register.blade.php) and [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php)
  - Post create & show: [resources/views/post/create.blade.php](resources/views/post/create.blade.php), [resources/views/post/show.blade.php](resources/views/post/show.blade.php)
  - Components: [resources/views/components/post-item.blade.php](resources/views/components/post-item.blade.php), [resources/views/components/clap-button.blade.php](resources/views/components/clap-button.blade.php), [resources/views/components/category-tabs.blade.php](resources/views/components/category-tabs.blade.php)
- DB migrations & seeders:
  - Users migration: [database/migrations/0001_01_01_000000_create_users_table.php](database/migrations/0001_01_01_000000_create_users_table.php)
  - Categories migration: [database/migrations/2025_07_30_103656_create_categories_table.php](database/migrations/2025_07_30_103656_create_categories_table.php)
  - Media table migration: [database/migrations/2025_08_12_165510_create_media_table.php](database/migrations/2025_08_12_165510_create_media_table.php)
  - Seeders: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php), [database/seeders/PostSeeder.php](database/seeders/PostSeeder.php)
  - Factories: [database/factories/PostFactory.php](database/factories/PostFactory.php), [database/factories/UserFactory.php](database/factories/UserFactory.php)

Important config & tooling
--------------------------
- Frontend: Blade [resources/js/app.jsx](resources/views) and [vite.config.js](vite.config.js).
- Tailwind CSS config: [tailwind.config.js](tailwind.config.js) and [postcss.config.js](postcss.config.js).
- Filesystems and storage: [config/filesystems.php](config/filesystems.php) — ensure `APP_URL` and storage link are configured (`php artisan storage:link`).
- Database config: [config/database.php](config/database.php).
- Logging: [config/logging.php](config/logging.php).
- Queue default driver set to database in [config/queue.php](config/queue.php).
- Tests: Pest + PHPUnit. See tests in [tests/Feature](tests/Feature) and [tests/Unit](tests/Unit). Example registration test: [tests/Feature/Auth/RegistrationTest.php](tests/Feature/Auth/RegistrationTest.php).

Quick local setup
-----------------
1. Copy .env and set credentials:
   - `cp .env.example .env`
   - Update DB and mail credentials.
2. Install dependencies:
   - `composer install`
   - `npm install`
3. Generate app key:
   - `php artisan key:generate`
4. Migrate and seed:
   - `php artisan migrate`
   - `php artisan db:seed`
5. Create storage symlink:
   - `php artisan storage:link`
6. Build assets / run dev server:
   - `npm run dev`
7. Serve app:
   - `php artisan serve` 
   or
   - `composer run dev`

Pre-commit checks
-----------------
- Husky installs automatically after `npm install` (via the `prepare` script) and blocks commits when formatting or builds fail.
- The `.husky/pre-commit` hook runs `./vendor/bin/pint --test` only on staged PHP files to ensure formatting, then `npm run build` to confirm the Vite build succeeds.
- Fix formatting with `npm run format` (or `./vendor/bin/pint`) and rerun the commit once both checks pass.

Continuous Integration
----------------------
- `.github/workflows/ci.yml` splits lint, format, build, and test stages into individual jobs so GitHub highlights the exact failure.
- Each job runs the same commands we use locally: Pint lint (`./vendor/bin/pint --test`), format validation (`npm run format -- --test`), asset builds (`npm run build`), and PHP tests (`php artisan test`) on SQLite with `.env.example`. The tests job also builds assets so the Vite manifest exists before Blade views render.

## Example Workflow for Post creation


Video Demo
--------------------------
[sample video.webm](https://github.com/user-attachments/assets/a90bf94c-361e-48fa-a35b-d2e3992f7cb3)
