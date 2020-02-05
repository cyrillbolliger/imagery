const mix = require('laravel-mix');

mix.options({
    // extract styles into css file
    extractVueStyles: true,
    // load these styles for every component
    globalVueStyles: 'resources/sass/_global.scss'
});

// analyze bundle size
require('laravel-mix-bundle-analyzer');
if (!mix.inProduction()) {
    mix.bundleAnalyzer({
        analyzerPort: 9999
    });
}

// disable the os notifications if everything went well
mix.disableSuccessNotifications();

// load browsersync with this domain
mix.browserSync('imagery.test:8888');

// compile js
mix.js('resources/js/app.js', 'public/js').sourceMaps().version();

// compile scss
mix.sass('resources/sass/app.scss', 'public/css').sourceMaps().version();
