# Laravel CRUD Starter

A reusable Laravel starter template. It's not built for one specific website — it's a **pattern** you can point at any idea: a blog, a shop, a booking system, an internal tool. The plumbing (auth, layout, styling, the layered architecture) is already there; you fill in the modules your app actually needs.

---

## What is already provided (do NOT change these)

| Layer | Files | What it does |
| --- | --- | --- |
| **Auth** | `app/Http/Controllers/AuthController.php`, `resources/views/auth/*` | Working login, registration, logout, dashboard |
| **Example Controllers** | `app/Http/Controllers/{Category,Item}Controller.php` | Receive the request, call a service, return a view |
| **Example Services** | `app/Services/{Category,Item}Service.php` | All the data access / business logic |
| **Example DTOs** | `app/DTOs/{Category,Item}DTO.php` | Plain objects that carry one record's data |
| **Migrations** | `database/migrations/2025_01_01_*` | Create the `categories` / `items` tables |
| **Seeder** | `database/seeders/DatabaseSeeder.php` | Fills the database with sample data |
| **Stylesheet** | `public/css/app.css` | Ready-made CSS classes you may use for styling — reskin the whole app by editing the `:root` variables at the top |

## What YOU build

| Layer | Files | Outcome |
| --- | --- | --- |
| **Routes** | `routes/web.php` | Wire up the Category/Item controllers, and any of your own |
| **Layout** | `resources/views/layouts/app.blade.php` | The shared page frame (nav, header, flash messages) |
| **Components** | `resources/views/components/{button,form-input,card}.blade.php` | Reusable `<x-...>` tags |
| **Module views** | `resources/views/{categories,items}/{index,create,edit}.blade.php` | The 6 CRUD screens for the two example modules |
| **Your own modules** | whatever your app needs | Repeat the same pattern |

Every one of those files already exists with a **comment inside telling you exactly what to build and what data it receives** — open them and follow the `TODO`.

---

## Getting started

```bash
# 1. install PHP dependencies
composer install

# 2. create your environment file and generate the app key
cp .env.example .env
php artisan key:generate

# 3. create the (empty) SQLite database file
touch database/database.sqlite

# 4. create the tables and load the sample data
php artisan migrate:fresh --seed

# 5. run the app
php artisan serve
```

Then open **http://127.0.0.1:8000**. (Pages will be blank until you build the views — that's expected!)

### Windows notes

- Step 3 (`touch database/database.sqlite`) doesn't work in plain `cmd`/PowerShell. Just create an empty file at `database/database.sqlite` in File Explorer (or run `type nul > database\database.sqlite` / `New-Item database\database.sqlite`).
- If you're working from a **OneDrive-synced folder**, occasional file locks (especially on `database.sqlite` while the dev server is running) are a known OneDrive quirk — pause syncing for that folder, or move the project outside OneDrive, if you hit odd "database is locked" errors.
- Make sure `.env` actually exists (copied from `.env.example`) and has a non-empty `APP_KEY` before running `migrate` — a blank key is the most common cause of a broken first run.

---

## Your task list

1. **Routes** (`routes/web.php`) — register a resource route for `categories` and `items`.
2. **Layout** — build `layouts/app.blade.php` with a nav bar and `@yield('content')`.
3. **Components** — build `<x-button>`, `<x-form-input>`, `<x-card>`.
4. **Categories** → **Items** — build the `index`, `create`, and `edit` view for each. Do Categories first (it's the simpler, standalone module); Items adds a related dropdown once Categories works.
5. **Your own modules** — once the pattern clicks, copy it for whatever your app actually needs.

---

## Data contract (quick reference)

Your views receive **objects** (DTOs) and **arrays**. Read each value with a getter method, e.g. `{{ $item->getTitle() }}`.

**DTO getters**

| DTO | Methods |
| --- | --- |
| `CategoryDTO` | `getId() getName()` |
| `ItemDTO` | `getId() getTitle() getDescription() getCategoryId() getCategoryName()` |

**Form field names** (what each `store`/`update` expects — use them as your input `name=""`)

| Module | Fields |
| --- | --- |
| Categories | `name` |
| Items | `title`, `description`, `category_id` |

**Remember for every form:** add `@csrf`. For edit/update forms also add `@method('PUT')`, and for delete buttons use a small `POST` form with `@method('DELETE')`.

---

## Renaming this project for a new site

When you spin up a new website from this template:

1. Update `APP_NAME` in `.env` (and `.env.example` if you're keeping this as your own template going forward).
2. Update `name` / `description` in `composer.json`.
3. Rename or remove the `categories` / `items` example modules once you've learned the pattern from them (or leave them as reference and just stop routing to them).
4. Recolor `public/css/app.css` by editing the `:root` variables — everything else in the stylesheet reads from them.
