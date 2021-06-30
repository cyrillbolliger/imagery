const mix = require('laravel-mix');

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
mix.browserSync('localhost:8000');

// compile js
mix.js('resources/js/app.js', 'public/js').vue({
    // extract styles into css file
    extractStyles: true,
    // load these styles for every component
    globalStyles: 'resources/sass/_global.scss'
}).sourceMaps().version();

// compile scss
mix.sass('resources/sass/app.scss', 'public/css').sourceMaps().version();

// expose material icons
mix.copy(['node_modules/bootstrap-material-design-icons/css'], 'public/css');
mix.copy(['node_modules/bootstrap-material-design-icons/fonts/MaterialIcons-Regular*'], 'public/fonts');

// expose select 2 css
mix.copy(['node_modules/vue-search-select/dist/VueSearchSelect.css'], 'public/css');

// expose vue animate styles
mix.copy(['node_modules/vue2-animate/dist'], 'public/css');
