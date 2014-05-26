**μmapper** er et administrativt verktøy for å arbeide med og publisere overganger fra ett strukturert vokabular
til ett eller flere andre.

Verktøyet er utviklet for å etablere overganger fra Realfagstermer til Tekord og Dewey.

1. `composer install`
2. `bower install`
3. Sett `key` i `app/config/app.php`
4. Legg inn databaseoppsett i `app/config/database.php` (μmapper krever MySQL/MariaDB)
5. Legg inn OAuth-innstillinger i `app/config/packages/artdarek/oauth-4-laravel/config.php`
6. Opprett databasetabeller: `php artisan migrate --seed`

![μmapper 0.1](https://hostr.co/file/GzS0904J8Lik/moccamapper-0.1.png)

