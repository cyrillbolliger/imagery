const mix = require('laravel-mix');

// disable the os notifications if everything went well
mix.disableSuccessNotifications();

// load browsersync with this domain
mix.browserSync('imagery.test:8888');

// compile js
mix.js('resources/js/app.js', 'public/js');
//.sourceMaps();

// compile scss
mix.sass('resources/sass/app.scss', 'public/css');

// webpack stuff
mix.webpackConfig({
    module: {
        rules: [{
            test: /\.scss$/,
            use: [{
                loader: "sass-loader",
                options: {
                    data: '@import "app";',
                    includePaths: [
                        path.resolve(__dirname, 'resources/sass')
                    ]
                }
            }]
        }]
    }
});
