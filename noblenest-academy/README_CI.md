# GitHub Actions — Laravel CI

This pack adds a ready-to-run CI workflow for your repository.

## Files
- `.github/workflows/laravel-ci.yml` — runs on push/PR to `main` on PHP 8.2 and 8.3.
- `phpunit.xml.dist` — safe defaults for PHPUnit.
- `tests/Feature/SmokeTest.php` — minimal test to ensure the app boots.

## What the workflow does
- Checks out code
- Installs PHP and Composer deps
- Copies `.env.example` to `.env`, generates key
- Uses **SQLite** and runs `php artisan migrate`
- Runs tests: `php artisan test`

## How to use
1. Drop these files into your project root.
2. Commit & push to `main`:
   ```bash
   git add .github/workflows/laravel-ci.yml phpunit.xml.dist tests/Feature/SmokeTest.php
   git commit -m "Add GitHub Actions CI for Laravel"
   git push
   ```
3. See the workflow under **Actions** tab on GitHub.

Tip: Add more tests under `tests/Feature` and `tests/Unit`.
