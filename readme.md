**μmapper** is a collaborative database for crosswalks between controlled vocabularies,
developed to support [a project](http://www.ub.uio.no/om/prosjekter/deweymapping/index.html)
investigating strategies for mapping [Realfagstermer](http://www.ub.uio.no/om/tjenester/emneord/realfagstermer.html) to Dewey. Realfagstermer is the controlled subject headings vocabulary
of the <a href="http://www.ub.uio.no/english/about/organisation/ureal/">University of Oslo Science Library</a>.

It's currently deployed at [mapper.biblionaut.net](https://mapper.biblionaut.net/), but don't expect that URL to be stable.

Our crosswalks (work-in-progress) are published [at DataHub](http://datahub.io/dataset/realfagstermer).

Installation:

1. `composer install`
2. `bower install`
3. Set `key` in `app/config/app.php`
4. Update database config in `app/config/database.php` (Note that μmapper requires MySQL/MariaDB)
5. Update OAuth settings in `app/config/packages/artdarek/oauth-4-laravel/config.php`
6. Create database tables: `php artisan migrate --seed`

![μmapper 0.1](https://hostr.co/file/WU5DQH6vY5wm/Skjermbilde2014-06-24kl.16.53.44.png)

