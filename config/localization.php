<?php

/**
 * Configuration options for the Localization service.
 *
 * @package MarcoMdMj\Localization
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Localization driver
    |--------------------------------------------------------------------------
    |
    | This defines the type of driver used for localization tasks, which will
    | dictate the way it detects and builds localized routes.
    |
    | Supported: "host", "path"
    |
    */

    'driver' => 'path',

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | The list of the supported locales must be defined here. The key for each
    | language should be a valid ISO 639-1 code, since it will be passed to
    | Laravel's own translator engine. For each language, a random mixed
    | array of information may be included. If the selected driver is
    | "host", a 'host' key is required for each language, with the
    | matching hostname as value (With or without www. prefix).
    |
    */

    'locales' => [
        'es' => [
            // 'host' => 'host_for_es_locale.tld'
        ],
        'en' => [
            // 'host' => 'host_for_en_locale.tld'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
    | The default locale used when a language was not detected by the driver
    | engine.
    |
    */

    'default' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Redirect to default
    |--------------------------------------------------------------------------
    |
    | If set to true, the request will be redirected to the default locale URL
    | when the driver engine was not able to detect the language.
    |
    */

    'redirectDefault' => true,

    /*
    |--------------------------------------------------------------------------
    | Path driver options
    |--------------------------------------------------------------------------
    |
    | Configuration options for the path driver:
    |
    |  - hideDefault: Include the default locale slug in the URI.
    |
    */

    'path' => [
        'hideDefault' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Host driver options
    |--------------------------------------------------------------------------
    |
    | Configuration options for the host driver:
    |
    |  null
    |
    */

    'host' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Facade name
    |--------------------------------------------------------------------------
    |
    | The name of the facade used for easy and fast access to Localization
    | services.
    |
    */

    'facade' => 'Localization'
];