# Smart Crop Rotation

Smart Crop Rotation is a Laravel-based web application (starter) to manage and optimise crop rotations on farms. It helps plan sequences of crops to improve soil health, maximise yields and reduce pest/disease pressure. This README provides a clear, runnable starting point — installation, configuration, development and contribution steps — and placeholders where you can add project-specific details.

## Table of Contents

- [About](#about)
- [Key features](#key-features)
- [Prerequisites](#prerequisites)
- [Installation (local development)](#installation-local-development)
- [Configuration](#configuration)
- [Running the app](#running-the-app)
- [Database & Seeding](#database--seeding)
- [Testing](#testing)
- [Deployment notes](#deployment-notes)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## About

Smart Crop Rotation aims to provide a simple, practical way to plan crop rotations. Typical use cases include:

- Designing multi-year crop plans per field
- Tracking soil amendments and fertility goals across rotations
- Visualising rotation sequences and generating reports
- (Optional) Integrating with weather/soil data APIs to improve recommendations


## Key features (example)

- Create and manage fields/plots
- Define crops and crop families
- Build multi-year rotation plans
- Recommendations to avoid high-risk succession (same family)
- Export rotation plans (CSV / PDF)
- User management (roles: admin, agronomist, farmer)


## Prerequisites

- PHP 8.1+ (or the version your project requires)
- Composer
- Node.js + npm (if frontend assets are used)
- A database (MySQL, MariaDB, PostgreSQL, SQLite)
- Git

## Installation (local development)

1. Clone the repository:

```bash
git clone https://github.com/Osuald/Smart-crop-rotation.git
cd Smart-crop-rotation
```

2. Install PHP dependencies:

```bash
composer install
```

3. Copy the environment file and set values:

```bash
cp .env.example .env
# Edit .env: set DB_*, APP_URL, and any API keys
```

4. Generate application key:

```bash
php artisan key:generate
```

5. Install frontend dependencies (if present) and build assets:

```bash
npm install
npm run dev   # or `npm run build` for a production build
```

6. Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed   # optional: seeds example data
```

7. Start the local server:

```bash
php artisan serve
```

Open http://127.0.0.1:8000 in your browser.

## Configuration

- Database: update DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD in `.env`.
- Mail: configure MAIL_* variables to send emails.
- Storage: run `php artisan storage:link` to create the public storage symlink for uploaded files.
- Queues: configure QUEUE_CONNECTION (database, redis, etc.) if background jobs are used.

## Database & Seeding

- Migrations are in the `database/migrations` directory.
- Seeders are in `database/seeders` (run with `php artisan db:seed`).

Example: create an admin user (adjust according to your seeder implementation):

```bash
php artisan tinker
>>> \App\Models\User::factory()->create([
... 'name' => 'Admin',
... 'email' => 'admin@example.com',
... 'password' => bcrypt('password')
... ]);
```

## Testing

Run the test suite:

```bash
php artisan test
```

If you use Pest:

```bash
./vendor/bin/pest
```

## Deployment notes (production checklist)

- Use a process manager (Supervisor) for queues.
- Configure caching:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- Set proper file permissions on `storage` and `bootstrap/cache`.
- Use a robust web server (Nginx + PHP-FPM) and HTTPS.
- Back up your database and storage regularly.

## Troubleshooting

- "Class not found" after composer install: run `composer dump-autoload`.
- Permission errors: ensure `storage` and `bootstrap/cache` are writable by the web user.
- Migrations failing: check DB credentials in `.env` and run `php artisan migrate:fresh --seed` (only for local/dev).

## Contributing

Contributions are welcome! A suggested workflow:

1. Fork the repository.
2. Create a feature branch: `git checkout -b feat/your-feature`.
3. Implement changes and add tests.
4. Run tests locally.
5. Open a pull request describing your changes.

## Roadmap / TODOs

- Improve rotation recommendation algorithm (add soil/water constraints)
- Import/export rotation plans (CSV and GPX)
- Add user roles and permissions
- Mobile-friendly UI / PWA

## License

This project is open-sourced software licensed under the MIT license. See the LICENSE file for details.

## Contact

Maintainer: Osuald — https://github.com/Osuald

Report bugs or request features via GitHub Issues: https://github.com/Osuald/Smart-crop-rotation/issues

---
