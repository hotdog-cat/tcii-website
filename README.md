# Techtonic Concrete Industries Inc. — React + Laravel CMS

Complete React 19 frontend with Laravel 12/PHP and MySQL backend. The public React interface uses the exact approved Techtonic component structure, responsive stylesheet, color system, spacing, imagery, and typography.

## Included

- Elegant responsive public website matching the approved Techtonic design
- React and TypeScript frontend powered by Vite
- Laravel/PHP backend with MySQL
- Mobile navigation and Services dropdown
- Secure administrator login
- CRUD management for facilities, equipment, products, projects, team members, and Business Registry
- Publish/hide controls and display ordering
- JPG, PNG, and WebP image uploads (maximum 5 MB)
- Public contact form with database-backed administrator inbox
- Seeded company content and original website images
- MySQL schema and Laravel migrations
- Business Registry seeded as hidden

## Requirements

- PHP 8.2 or newer
- Composer 2
- Node.js 20 or newer and npm
- MySQL 8 or MariaDB 10.6+
- PHP extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo, BCMath, and GD

## Windows / XAMPP setup

1. Extract this project, then open the folder in VS Code.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and create a database named `techtonic_cms`.
4. In the VS Code terminal, run:

   ```powershell
   copy .env.example .env
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

5. For development, open two VS Code terminals.

   Terminal 1:

   ```powershell
   php artisan serve
   ```

   Terminal 2:

   ```powershell
   npm run dev
   ```

6. Open `http://127.0.0.1:8000`.
7. Administrator login: `http://127.0.0.1:8000/admin/login`

## Initial administrator account

- Email: `admin@techtonic.local`
- Password: `ChangeMe123!`

Change this password before production use. You can replace it from Tinker:

```powershell
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@techtonic.local')->first();
$user->password = Hash::make('Your-New-Strong-Password');
$user->save();
```

## MySQL configuration

Edit these values in `.env` if your MySQL setup is different:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techtonic_cms
DB_USERNAME=root
DB_PASSWORD=
```

The included `database/techtonic_cms.sql` documents the core schema. Laravel migrations are the recommended installation method because `php artisan migrate --seed` also creates the administrator and initial website content.

## Business Registry

Registry records are included but hidden by default. To show them later:

1. Sign in to the administrator dashboard.
2. Open **Business Registry**.
3. Edit each record.
4. Check **Publish this item on the public website**, then save.

The navigation link, page section, and footer link appear automatically when at least one Registry record is published.

## Production notes

- Point the web server document root to the project's `public` directory.
- Run `npm run build` before uploading or deploying.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and the correct `APP_URL`.
- Use a dedicated MySQL account instead of `root`.
- Configure HTTPS.
- Run `php artisan optimize` after deployment.
- Ensure `storage` and `bootstrap/cache` are writable by the web server.
