<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Europe/Zurich',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
        App\Providers\UserFederationServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'          => Illuminate\Support\Facades\App::class,
        'Arr'          => Illuminate\Support\Arr::class,
        'Artisan'      => Illuminate\Support\Facades\Artisan::class,
        'Auth'         => Illuminate\Support\Facades\Auth::class,
        'Blade'        => Illuminate\Support\Facades\Blade::class,
        'Broadcast'    => Illuminate\Support\Facades\Broadcast::class,
        'Bus'          => Illuminate\Support\Facades\Bus::class,
        'Cache'        => Illuminate\Support\Facades\Cache::class,
        'Config'       => Illuminate\Support\Facades\Config::class,
        'Cookie'       => Illuminate\Support\Facades\Cookie::class,
        'Crypt'        => Illuminate\Support\Facades\Crypt::class,
        'DB'           => Illuminate\Support\Facades\DB::class,
        'Eloquent'     => Illuminate\Database\Eloquent\Model::class,
        'Event'        => Illuminate\Support\Facades\Event::class,
        'File'         => Illuminate\Support\Facades\File::class,
        'Gate'         => Illuminate\Support\Facades\Gate::class,
        'Hash'         => Illuminate\Support\Facades\Hash::class,
        'Lang'         => Illuminate\Support\Facades\Lang::class,
        'Log'          => Illuminate\Support\Facades\Log::class,
        'Mail'         => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password'     => Illuminate\Support\Facades\Password::class,
        'Queue'        => Illuminate\Support\Facades\Queue::class,
        'Redirect'     => Illuminate\Support\Facades\Redirect::class,
        'Redis'        => Illuminate\Support\Facades\Redis::class,
        'Request'      => Illuminate\Support\Facades\Request::class,
        'Response'     => Illuminate\Support\Facades\Response::class,
        'Route'        => Illuminate\Support\Facades\Route::class,
        'Schema'       => Illuminate\Support\Facades\Schema::class,
        'Session'      => Illuminate\Support\Facades\Session::class,
        'Storage'      => Illuminate\Support\Facades\Storage::class,
        'Str'          => Illuminate\Support\Str::class,
        'URL'          => Illuminate\Support\Facades\URL::class,
        'Validator'    => Illuminate\Support\Facades\Validator::class,
        'View'         => Illuminate\Support\Facades\View::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Images Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to store images. For the default disk,
    | it resides in {{project_root}}/storage/app/{{images_dir}}.
    |
    */

    'image_dir' => 'images',

    /*
    |--------------------------------------------------------------------------
    | Base Logo Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to store the base logos. For the default
    | disk, it resides in {{project_root}}/storage/app/{{base_logo_dir}}.
    |
    */

    'base_logo_dir' => 'base_logos',

    /*
    |--------------------------------------------------------------------------
    | Logo Cache Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to cache the generated logos. For the
    | default disk, it resides in
    | {{project_root}}/storage/app/{{logo_cache_dir}}.
    |
    */

    'logo_cache_dir' => 'logo_cache',

    /*
    |--------------------------------------------------------------------------
    | Reference Logo Directory
    |--------------------------------------------------------------------------
    |
    | The directory that contains the reference logos that can be used to
    | compare with the generated ones. For the default disk, it resides in
    | {{project_root}}/storage/app/{{reference_logo_dir}}.
    |
    */

    'reference_logo_dir' => 'reference_logos',

    /*
    |--------------------------------------------------------------------------
    | Add reference logo overlay (for visual debugging only)
    |--------------------------------------------------------------------------
    |
    | The generated logo is masked with the reference logo specified in the logo
    | implementation. Not all implementations do have a reference logo.
    |
    */

    'logo_debug_overlay' => env('APP_LOGO_OVERLAY', false),

    /*
    |--------------------------------------------------------------------------
    | Logo default width
    |--------------------------------------------------------------------------
    |
    | The default width of logos served by the FileController.
    |
    */

    'logo_width' => 2500,

    /*
    |--------------------------------------------------------------------------
    | Protected Font Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to store fonts non accessible for the
    | public. For the default disk, it resides in
    | {{project_root}}/storage/app/{{protected_fonts_dir}}.
    |
    */

    'protected_fonts_dir' => 'fonts',

    /*
    |--------------------------------------------------------------------------
    | Uploads Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to store uploads. For the default disk,
    | it resides in {{project_root}}/storage/app/{{uploads_dir}}.
    |
    */

    'uploads_dir' => 'temp'.DIRECTORY_SEPARATOR.'uploads',

    /*
    |--------------------------------------------------------------------------
    | Packages Directory
    |--------------------------------------------------------------------------
    |
    | The directory that will be used to store the logo packages. For the
    | default disk, it resides in {{project_root}}/storage/app/{{packages_dir}}.
    |
    */

    'packages_dir' => 'logo_package_cache',

    /*
    |--------------------------------------------------------------------------
    | Packages Directory
    |--------------------------------------------------------------------------
    |
    | The directory that contains the logo templates. For the default disk, it
    | this will be {{project_root}}/storage/app/{{logo_template_path}}.
    |
    */

    'logo_template_path' => 'vector_logo_templates_indesign',

    /*
    |--------------------------------------------------------------------------
    | Uploads TTL
    |--------------------------------------------------------------------------
    |
    | Time in seconds until an uploaded file can be removed.
    |
    */

    'uploads_ttl' => 900, // 15 min

    /*
    |--------------------------------------------------------------------------
    | Max upload file size
    |--------------------------------------------------------------------------
    |
    | The upload will stop, if the file size is exceeded. File size in Megabyte.
    |
    */

    'uploads_max_file_size' => (float) env('APP_MAX_UPLOAD_SIZE', 8.0),

    /*
    |--------------------------------------------------------------------------
    | Max size of uploaded chunk
    |--------------------------------------------------------------------------
    |
    | The chunk will be rejected, if the max size is exceeded. Size in Megabyte.
    |
    */

    'uploads_max_chunk_size' => (float) env('APP_MAX_CHUNK_SIZE', 1.0),

    /*
    |--------------------------------------------------------------------------
    | Hash secret
    |--------------------------------------------------------------------------
    |
    | Secret value to make hashes hard to guess, even if the original input is
    | known. Treat this as a sensitive value.
    |
    */

    'hash_secret' => env('APP_HASH_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Admin email
    |--------------------------------------------------------------------------
    |
    | Notifications for login requests will be sent to this email.
    |
    */

    'admin_email' => env('APP_ADMIN_EMAIL'),

];
