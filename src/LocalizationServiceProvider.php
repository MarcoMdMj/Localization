<?php

namespace MarcoMdMj\Localization;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class LocalizationServiceProvider
 *
 * @package MarcoMdMj\Localization
 */
class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The drivers that handle localization.
     *
     * @var array
     */
    private $drivers = [
        'path' => \MarcoMdMj\Localization\Driver\Engines\PathDriver::class,
        'host' => \MarcoMdMj\Localization\Driver\Engines\HostDriver::class
    ];

    /**
     * Bootstrap any application services and register the middleware.
     *
     * @param  Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/localization.php' => config_path('localization.php'),
        ], 'config');

        $router->aliasMiddleware('localization.redirect', \MarcoMdMj\Localization\Middleware\LocalizationRedirect::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/localization.php', 'localization');

        $this->registerService();

        $this->initializeService();

        $this->registerFacade();
    }

    /**
     * Register the localization service.
     *
     * @return void
     */
    private function registerService()
    {
        $this->loadDriver();

        $this->app->singleton(Localization::class);
        $this->app->singleton(Driver\UrlGenerator::class);
    }

    /**
     * Register the localization driver.
     *
     * @return void
     */
    private function loadDriver()
    {
        $driver = $this->app->config->get('localization.driver');

        if (!array_key_exists($driver, $this->drivers)) {
            $driver = 'host';
        }

        $driver = $this->drivers[$driver];

        $this->app->singleton($driver);
        $this->app->bind(Driver\DriverInterface::class, $driver);
    }

    /**
     * Initialize the localization service.
     *
     * @return void
     */
    private function initializeService()
    {
        $locale = $this->app[Localization::class]->initialize();

        setlocale(LC_ALL, $locale);
        
        $this->app->config->set('app.locale', $locale);
    }

    /**
     * Register the localization facade.
     *
     * @return void
     */
    private function registerFacade()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias(
            $this->app->config->get('localization.facade', 'Localization'),
            Facade\Localization::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Localization::class];
    }
}