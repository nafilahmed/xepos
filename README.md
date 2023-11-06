## Project Setup

```sh
cp .env.example .env
composer install
npm install
php artisan migrate --seed
```

### Create Database & Compile and Hot-Reload for Development

```sh
php artisan serve
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```
### Note:

```sh
Check your local port and set it to .env file 'SANCTUM_STATEFUL_DOMAINS' and add credentails Mailtrap in .env file.
```
