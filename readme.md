**μmapper** is a tool for establishing mappings between controlled vocabularies.
The tool has been developed as part of <a href="http://www.ub.uio.no/om/prosjekter/deweymapping/index.html">a project</a>
investigating the feasibility of mapping *Realfagstermer* to Dewey. Realfagstermer is the controlled vocabulary
of the <a href="http://www.ub.uio.no/english/about/organisation/ureal/">University of Oslo Science Library</a>.

Installation:
1. `composer install`
2. `bower install`
3. Set `key` in `app/config/app.php`
4. Update database config in `app/config/database.php` (Note that μmapper requires MySQL/MariaDB)
5. Update OAuth settings in `app/config/packages/artdarek/oauth-4-laravel/config.php`
6. Create database tables: `php artisan migrate --seed`

Note that I'm interested in getting in touch with others working with mapping. Feel free to send me an email if you are :)

![μmapper 0.1](https://hostr.co/file/GzS0904J8Lik/moccamapper-0.1.png)

