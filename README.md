# Imagery
_Easily generate images in the corporate design of the swiss [GREENS](https://gruene.ch)._

[![Crowdin](https://badges.crowdin.net/cdgruenech/localized.svg)](https://crowdin.com/project/cdgruenech)

## What?
This tools aims to provide a simple way to generate images conforming the 
corporate design rules. It's designed to be so simple to use, that no further 
instructions are needed and no corporate design rules can be broken.

### Why?
Not everybody has the software and skills to create corporate design images on 
his own. And it's not everybodys hobby to learn all the rules of the corporate 
design.


## Contributing ...
... is cool, simple and helps to make the 🌍 a better place 🤩

### Getting started
1. Install [docker](https://store.docker.com/search?offering=community&type=edition) and [docker-compose](https://docs.docker.com/compose/install/).
1. Clone this repo `git clone https://github.com/cyrillbolliger/imagery`
1. `cd` into the folder containing the repo
1. Execute `bash install.sh` and have a ☕️ while it installs.
1. Visit [localhost:8000/](http://localhost:8000/)
1. Use `superadmin@user.login` and `password` to login.
1. As the font used in the corporate design is proprietary, you'll need to get a 
   licenced copy of the Sanuk font (fat and bold). Store it as follows:
   ```
   storage
     |-- app
         |-- fonts
             |-- SanukOT-Bold.otf
             |-- SanutOT-Fat.otf
   ```
1. The indesign templates bundles for the logo suffer the limitations of the
   proprietary Sanuk font as well. If you've got a licenced copy of the Sanuk 
   font, we can provide you the bundles. Store them as follows:
   ```
   storage
     |-- app
         |-- vector_logo_templates_indesign
             |-- gruene
                 |-- ...
             |-- gruene-les-vertes
                 |-- ...
             |-- les-vertes
                 |-- ...
   ```


### The Stack
Using a Lamp stack on docker, the tool is built with [Laravel](https://laravel.com/).
The frontend is built with [VueJS](https://vuejs.org/) and the image processing 
is done with the [canvas](https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API)
element. It's all bundled by [Webpack](https://webpack.js.org/) using 
[Laravel Mix](https://laravel-mix.com/). Have a look at the `package.json`
if you want to dig deeper.

### Tooling
#### Docker Cheat Sheet
- Start up: `docker-compose up -d`
- Shut down: `docker-compose stop`
- Execute CLI commands (enter container): `docker exec -it imagery bash`. 
  Use `exit` to escape the container.

#### PHPUnit
All tests are based on PHPUnit. It may be used as follows:
1. Make sure your containers are up and running.
1. Run `docker exec imagery vendor/bin/phpunit`.

#### MySQL
Use the handy [phpMyAdmin](http://localhost:8010) or access the mysql CLI using
`docker exec -it imagery_mysql bash -c 'mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} imagery'` 

#### Node / Yarn / Webpack
The node container is watching the js, css and scss files and building the assets.
- Access the watching container using `docker exec -it imagery_node bash`.
- Get the build output `docker attach imagery_node`.

#### Composer
The PHP Composer runs directly on the `imagery` container.
- Access it using `docker exec imagery composer YOUR_COMPOSER_SUBCOMMAND`.

#### Mailhog
All mail you send out of the application will be caught by Mailhog. Access it
on [localhost:8020](http://localhost:8020)

#### Crowdin
All translation is done with [crowdin](https://crowdin.com). To translate 
in-context visit [localhost:8000/?translate=true&lang=zu](https://localhost:8000/?translate=true&lang=zu).

**Crowdin integration**

See the Github Action [`.github/workflows/l10n.yml`](.github/workflows/l10n.yml).

* **Github to Crowdin**: on every push to the `dev` branch all strings are added
  to crowdin.
* **Crowdin to Github**: Crowdin pushes new translations to the `l10n_crowdin`
  branch on any push to `dev` and on every pull request on `dev` or `master`.
  To get the new translations merge `l10n_crowdin` in your branch.

### Logins
Logins created by the demo seeder:
* `superadmin@user.login`:`password`
* `countryadmin@user.login`:`password`
* `cantonadmin@user.login`:`password`
* `localuser@user.login`:`password`


## Github Actions
We use actions to test the application and for the localization.

### Testing

See the Github Action [`.github/workflows/tests.yml`](.github/workflows/tests.yml).

#### `proprietary.tar.gz.enc`
The tests rely on the proprietary fonts. Bundle them using the following 
command (executed in the project root):
```
tar -zcv \
    storage/app/fonts \ 
    storage/app/vector_logo_templates_indesign \
    | openssl enc \
      -e -aes256 \
      -md sha512 \
      -pbkdf2 -iter 100000 \
      -pass file:proprietary.key \
      -out proprietary.tar.gz.enc
```
The files will be automatically decrypted by the  `Add proprietary files` step (cf. `.github/workflows/tests.yml`).
The key is stored as a 
[repository secret](https://docs.github.com/en/actions/reference/encrypted-secrets#creating-encrypted-secrets-for-a-repository). 

To decrypt and unpack the bundle manually:
```
openssl enc \
    -d -aes256 \
    -md sha512 \
    -pbkdf2 -iter 100000 \
    -pass file:proprietary.key \
    -in proprietary.tar.gz.enc \
    | tar -xzv
```

#### Localization
See the [crowdin](#crowdin) section.


## More Docs

* Single-Sign on [docs/sso.md](docs/sso.md).
* How to add a logo [docs/logo.md](docs/logo.md).
