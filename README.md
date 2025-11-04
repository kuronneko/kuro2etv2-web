# 🔐 Kuro — Lightweight Password Manager (Laravel + Filament)

> Friendly, small password-like manager with a custom encrypter algorithm, a Filament 3 admin, and a REST API (Laravel + Sanctum).

---

## 🚀 What is this

Kuro is a compact app to store text snippets (passwords, notes, credentials) per-user. It provides:

- A REST API (versioned: `/api/v1`) for programmatic access.
- A Filament 3 admin panel for managing categories and entries (`/admin`).
- A custom, project-specific encrypter (`app/Services/KuroEncrypterTool.php`) used to obfuscate stored text.
- Laravel Sanctum-based token authentication + an `x-api-key` header check for API requests.

This README documents installation, running, the API endpoints, and notes about the custom encrypter.

---

## 🧩 Features

- Per-user storage of entries (model: `File2e`) with optional `Category`.
- Create, read, update, delete operations over the REST API.
- Optionally request decrypted text from the API by passing `?text=decrypt`.
- Filament admin resources for `Category` and `File2e` (see `app/Filament/Resources`).

---

## 🛠️ Tech stack

- PHP + Laravel (project structure)
- Filament 3 for admin UI (`/admin`)
- Laravel Sanctum for token auth
- Vite + npm for front-end builds (if used)

---

## ⚙️ Quick setup (local)

Prereqs: PHP (>= 8.x depending on Laravel version), Composer, Node (npm), a database (MySQL, Postgres, Sqlite), and optionally Docker.

1. Clone the repository

```zsh
git clone <repo-url> kuro_et
cd kuro_et
```

2. Install PHP dependencies

```zsh
composer install
```

3. Copy env and set values

```zsh
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at least:

- `APP_URL` (e.g. http://localhost)
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `API_KEY` — required by the API (used by middleware `App\Http\Middleware\VerifyApiKey`).

Example additions to `.env`:

```env
APP_URL=http://localhost
API_KEY=your_api_key_here
```

4. Migrate the database

```zsh
php artisan migrate
php artisan db:seed   # optional — seeder currently doesn't create users by default
```

5. Install frontend deps (if you want to build assets)

```zsh
npm install
npm run build   # or `npm run dev` for dev server
```

6. Serve the app

```zsh
php artisan serve --host=0.0.0.0 --port=8000
```

### Docker / Laravel Sail (recommended for local Docker development)

This project can be run with Laravel Sail (a lightweight Docker wrapper). If you prefer Sail over raw Docker Compose, follow these steps.

Prereqs: Docker and Docker Compose installed on your machine.

1. Install Sail (if not already added) — from your project root:

```zsh
composer require laravel/sail --dev
php artisan sail:install  # optional: choose services (mysql, redis, etc.)
```

2. Build and start Sail

```zsh
./vendor/bin/sail up -d --build
```

3. Run migrations and seed (inside Sail)

```zsh
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

4. Artisan / Tinker inside Sail

```zsh
./vendor/bin/sail artisan tinker
```

5. Stopping Sail

```zsh
./vendor/bin/sail down
```

Tips:

- If you prefer an alias for convenience, add this to your shell (zsh):

```zsh
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

- Use the `sail` commands instead of `php artisan` or `npm` when working inside the container.
- Make sure the `API_KEY` env value is set in your `.env` used by Sail. You can copy the local `.env` or use Docker environment overrides.


7. Create an initial user (if not seeded)

Use Tinker to create a user you can log in with:

```zsh
php artisan tinker
>>> \App\Models\User::create([ 'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('secret123') ]);
```

You can now authenticate via the API (see below) or log into the Filament admin at `/admin`.

---

## 🔑 API overview

Base path: `/api/v1`

Global headers required for all API requests:

- `x-api-key: <API_KEY>` — must match `API_KEY` from your `.env` (middleware `VerifyApiKey`).
- `Accept: application/json`

Authentication: login returns a Sanctum token (in `access_token`), send it on subsequent requests as `Authorization: Bearer <token>`.

The API responses use consistent macros defined in `app/Providers/AppServiceProvider.php` and have the shape:

```json
{
  "status": "success|fail|error",
  "data": { /* payload (may be null) */ },
  "developer_message": "...",
  "user_message": "..."
}
```

### Auth

- POST /api/v1/auth/login

Request body (JSON):

```json
{
  "email": "admin@example.test",
  "password": "secret123"
}
```

Successful response (example):

```json
{
  "status": "success",
  "data": {
    "usuario": {
      "access_token": "<token>",
      "id": 1,
      "name": "Admin",
      "email": "admin@example.test"
    }
  },
  "developer_message": "Login OK",
  "user_message": "¡Bienvenido!"
}
```

Use the `access_token` for subsequent requests:

Header:

```
Authorization: Bearer <access_token>
```

### File2e (entries) — protected by `auth:sanctum`

All endpoints below are under `/api/v1/file2es` and require the `x-api-key` header + a valid Bearer token.

- GET `/api/v1/file2es/get-all`
  - Optional query: `?text=decrypt` — returns `text` decrypted using the project encrypter.

- GET `/api/v1/file2es/get/{file2e}`
  - Optional query: `?text=decrypt`

- POST `/api/v1/file2es/create`
  - Body JSON:
    - `name` (string, required)
    - `text` (string, required) — plain text; the server will encrypt it before saving
    - `category_id` (integer, optional)

- PUT `/api/v1/file2es/edit/{file2e}`
  - Body JSON: `name` and/or `text` (plain) — `text` will be encrypted on save.

- DELETE `/api/v1/file2es/delete/{file2e}`

Example: create a new entry

```zsh
curl -X POST "http://localhost:8000/api/v1/file2es/create" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "x-api-key: ${API_KEY}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -d '{"name":"My Gmail","text":"user:me@example.com\npass:abc123"}'
```

If you want the decrypted text back when reading:

```zsh
curl "http://localhost:8000/api/v1/file2es/get-all?text=decrypt" \
  -H "Accept: application/json" \
  -H "x-api-key: ${API_KEY}" \
  -H "Authorization: Bearer ${TOKEN}"
```

Response format for lists uses `File2eResource` and returns the entry fields; when `?text=decrypt` is set the `text` value will be decrypted for the response (server-side). See `app/Http/Controllers/Api/File2eController.php` and `app/Services/File2eActionService.php` for implementation.

---

## 🔐 The custom encrypter (IMPORTANT)

The project uses a homegrown obfuscation tool at `app/Services/KuroEncrypterTool.php`.

What it does (summary):

- Converts the input string into hexadecimal bytes.
- Splits the hex string in half and swaps the halves.
- Converts the swapped string again into hex and reverses the string.
- The reverse operation reverts these steps to obtain the original text.

Key methods:

- `saveTextToHex($text)` — obfuscates the plain text for storage.
- `loadHexToString($hex)` — recovers plain text from the obfuscated value.

Files of interest:

- `app/Services/KuroEncrypterTool.php` — algorithm implementation
- `app/Services/File2eActionService.php` — wiring: encrypt on store/update, decrypt on demand

---

## 🧭 Filament admin

- Admin panel path: `/admin` (configured in `App\Providers\Filament\AdminPanelProvider`).
- Filament resources available: `app/Filament/Resources/File2eResource.php`, `app/Filament/Resources/CategoryResource.php` and their pages (create/list/edit/view).

To access Filament you need a user in the `users` table. Create one with Tinker (see Quick setup step 7).

---

## 📁 Useful file references

- API routes: `routes/api.php`
- API controllers: `app/Http/Controllers/Api/AuthController.php`, `app/Http/Controllers/Api/File2eController.php`
- Models: `app/Models/File2e.php`, `app/Models/Category.php`, `app/Models/User.php`
- Services: `app/Services/AuthService.php`, `app/Services/File2eActionService.php`, `app/Services/KuroEncrypterTool.php`
- Response macros: `app/Providers/AppServiceProvider.php` (defines `response()->success/fail/error`)
- API key config: `config/app.php` -> `api_key` (env variable `API_KEY`)

---

## ✅ Next steps & recommendations

- Replace the custom obfuscation with a well-known, audited encryption approach if you intend to store real secrets.
- Add more API tests (happy-path + 401/403/validation errors) for endpoints in `routes/api.php`.
- Add a small integration example client (JS / curl snippets) and a Postman collection or OpenAPI spec for easier consumption.
