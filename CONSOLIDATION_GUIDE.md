# Noble Nest Academy - Structure Consolidation Guide

## Current Issue: 3-Layer App Structure

The repository currently has a confusing 3-layer structure:

```
noblenest-academy/          ← Root meta-layer
├── app/Http/               ← Partial app (Controllers only)
├── resources/views/        ← Duplicate views (admin/)
├── scaffolding/lms/        ← Source scaffolding code
└── noblenest-academy/      ← The ACTUAL runnable Laravel app
    ├── app/                ← Full app with all models
    ├── config/
    ├── database/
    └── ...
```

## Recommended Structure

After consolidation, the structure should be:

```
noblenest-academy/
├── app/
│   ├── Helpers/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
└── ...
```

## Consolidation Steps

### Step 1: Backup
```bash
git checkout -b backup/pre-consolidation
git push origin backup/pre-consolidation
```

### Step 2: Identify Source of Truth
The `noblenest-academy/noblenest-academy/` directory is the actual runnable app.
The `scaffolding/lms/` contains the original scaffold templates.

### Step 3: Move Inner App to Root Level
```bash
# From the repository root
mv noblenest-academy noblenest-academy-nested
mv noblenest-academy-nested/noblenest-academy/* .
rm -rf noblenest-academy-nested
```

### Step 4: Clean Up Duplicates
```bash
# Remove duplicate/scaffolding directories
rm -rf scaffolding/
rm -rf app/Http/  # Root level partial app
rm -rf resources/views/admin/  # If duplicated
```

### Step 5: Update Git
```bash
git add -A
git commit -m "Consolidate app structure to single Laravel installation"
```

### Step 6: Verify
```bash
cd noblenest-academy  # Now at root
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Files Already Fixed

The following critical issues have been addressed in the current structure:

1. ✅ **Admin self-registration** - Removed Admin role from registration form
2. ✅ **Tailwind conflict** - Replaced with Bootstrap CSS
3. ✅ **Stripe webhook verification** - Added signature verification
4. ✅ **Language switch security** - Added allowlist validation
5. ✅ **Missing packages** - Added to composer.json
6. ✅ **Course::activities bug** - Fixed relationship implementation
7. ✅ **ChildProfile model** - Created separate model for COPPA compliance
8. ✅ **Lesson model** - Already exists, Module relationship updated
9. ✅ **AI service integration** - Created AIAssistantService
10. ✅ **Security tests** - Added comprehensive test suite
11. ✅ **I18n caching** - Added Redis caching support

## Post-Consolidation Checklist

- [ ] Run `composer install`
- [ ] Run `npm install`
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] Run `php artisan test`
- [ ] Verify all routes work
- [ ] Remove this guide file
