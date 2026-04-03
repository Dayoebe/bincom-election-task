# Bincom Election Dashboard

Laravel 12 + Livewire + Tailwind CSS implementation of the interview task using the legacy schema in `bincom_test.sql`.

## Schema Summary

The app is built directly against the SQL dump and does not assume Laravel naming conventions.

- `polling_unit.uniqueid` is the polling unit primary key.
- `announced_pu_results.polling_unit_uniqueid` maps to `polling_unit.uniqueid`.
- `polling_unit.lga_id` maps to `lga.lga_id`, not `lga.uniqueid`.
- `polling_unit.uniquewardid` maps to `ward.uniqueid`.
- `ward.lga_id` maps to `lga.lga_id`.
- `announced_lga_results.lga_name` stores numeric LGA IDs as strings.
- The dump only contains Delta State LGAs with `state_id = 25`.

## Pages

- `Polling Unit Result`
  - Search and select a polling unit with announced results.
  - Display polling unit details, summed party scores, and raw announced rows.
- `LGA Result Summary`
  - Select an LGA and compute totals from `announced_pu_results`.
  - Optional comparison against `announced_lga_results`.
- `Add New Polling Unit Result`
  - Create a polling unit and insert all party result rows in one database transaction.

## Setup

1. Install PHP dependencies.

```bash
composer install
```

2. Install frontend dependencies.

```bash
npm install
```

3. Create the MySQL database and import the dump.

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bincomphptest"
mysql -u root bincomphptest < bincom_test.sql
```

4. Copy the environment file if needed and confirm the database settings.

```bash
cp .env.example .env
php artisan key:generate
```

Expected database settings:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bincomphptest
DB_USERNAME=root
DB_PASSWORD=
```

5. Build assets.

```bash
npm run build
```

6. Start the Laravel server.

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Installable Web App

- The app includes a web app manifest and service worker, so supported browsers can install it as a standalone app.
- The install metadata and visible brand icon are generated from `public/Oyetoke_Adedayo_E.png`.
- On desktop Chrome or Edge, open the app and use the `Install app` button in the navbar when it appears.
- On mobile Safari, use Share -> Add to Home Screen.

## Notes

- The app uses Query Builder / `DB` facade for all legacy election queries.
- The main LGA summary intentionally does not use `announced_lga_results`.
- Session, queue, and cache defaults are file-based so the app does not depend on Laravel migration tables for this interview task.
