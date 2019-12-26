# Imagery - Easily generate images in the corporate design

## How to
* Install locally: 
    * clone this repo
    * install dependencies: `composer install`
    * copy `.env.example` to `.env` and configure your database connection and 
    your mailgateway
    * generate app key: `php artisan key:generate`
    * define an APP_HASH_SECRET (p.ex by using `openssl rand 128 | openssl sha256`)
    * set up the db tables: `php artisan migrate`
    * seed the database (only for testing!): `php artisan db:seed --class=DemoSeeder`
* Logins created by the demo seeder:
    * `superadmin@user.login`:`password`
    * `countryadmin@user.login`:`password`
    * `cantonadmin@user.login`:`password`
    * `localuser@user.login`:`password`
