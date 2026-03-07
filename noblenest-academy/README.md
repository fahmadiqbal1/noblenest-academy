# Noble Nest Academy — Laravel Application

## Quick Start

### Prerequisites

- PHP 8.2+ (extensions: `pdo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- [Composer](https://getcomposer.org) 2.x
- MySQL 8.0+ (or MariaDB 10.6+)
- Node.js 18+ & npm (for front-end assets)

### Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Create and configure your environment file
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noblenest
DB_USERNAME=noblenest
DB_PASSWORD=your_password
```

Create the database in MySQL first:

```sql
CREATE DATABASE noblenest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'noblenest'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON noblenest.* TO 'noblenest'@'localhost';
FLUSH PRIVILEGES;
```

```bash
# 3. Run all migrations and seed demo users
php artisan migrate --seed

# 4. (Optional) Build front-end assets
npm install && npm run build

# 5. Start the dev server — visit http://localhost:8000
php artisan serve
```

## Default Login Credentials

All passwords are **`Password1!`**

| Role    | Email                       |
|---------|-----------------------------|
| Admin   | admin@noblenest.test        |
| Teacher | teacher@noblenest.test      |
| Parent  | parent@noblenest.test       |
| Student | student@noblenest.test      |

---



Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
