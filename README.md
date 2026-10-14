this is a real-estate website Build by Abdulhadi Fathi 

## Running the website on your computer

### What you need installed first
- [PHP](https://www.php.net/downloads) 8.3 or newer
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/) (includes `npm`)
- [Git](https://git-scm.com/downloads)

### Setup (one time only)

Open a terminal and run these commands one at a time.

**1. Download the project and open its folder**
```
git clone https://github.com/abdelhady6168-gif/Real-estate-website.git
cd Real-estate-website
```

**2. Install the PHP packages**
```
composer install
```

**3. Create your settings file**

Windows:
```
copy .env.example .env
```
Mac / Linux:
```
cp .env.example .env
```

**4. Generate the app's secret key**
```
php artisan key:generate
```

**5. Create the empty database file**

Windows:
```
type nul > database\database.sqlite
```
Mac / Linux:
```
touch database/database.sqlite
```

**6. Build the database and fill it with sample properties**
```
php artisan migrate --seed
```

**7. Connect the photo folder**
```
php artisan storage:link
```
> On Windows, if this gives a "permission" error, run this instead:
> `mklink /J public\storage storage\app\public`

**8. Install and build the website's styling**
```
npm install
npm run build
```

### Starting the website
```
php artisan serve
```
Then open **http://127.0.0.1:8000** in your browser.
To stop it, press **Ctrl + C** in the terminal.

Next time, you only need `php artisan serve`; the setup is one time only.

### Logging in
| Account | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Regular user | user@example.com | password |

> Change the admin password before putting the site anywhere other people can reach it.
