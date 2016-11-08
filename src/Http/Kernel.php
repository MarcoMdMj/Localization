<?php

namespace MarcoMdMj\Localization\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Custom kernel to use when path driver is used. It helps to prevent
 * the segment(n) problem caused by the locale slug in the uri.
 *
 * @package MarcoMdMj\Localization\Http
 */
class Kernel extends HttpKernel
{
    private $localizationConfig;

    /**
     * Parse the request, then proceed to Laravel's native kernel.
     *
     * @param Application $app
     * @param Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->shiftLocaleSlugFromPath();

        parent::__construct($app, $router);
    }

    /**
     * Extracts locale slug from request uri.
     *
     * @return void
     */
    private function shiftLocaleSlugFromPath()
    {
        $this->lcConfig();

        if (!$this->driverIsPath()) {
            return;
        }

        $regex = '/^\/(' . implode('|', $this->getLocales()) . ')(?:\/|$)/';
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        if (preg_match($regex, $uri, $matches)) {
            $_SERVER['REQUEST_URI'] = preg_replace($regex, '/', $uri);

            define('LCL_CODE', $matches[1]);
        }
    }

    /**
     * Load the configuration settings.
     *
     * @param  string $param
     * @return void|string
     */
    private function lcConfig($param = null)
    {
        if (!is_null($param)) {
            return $this->localizationConfig[$param];
        }

        $config = [
            'default'   => __DIR__.'/../../config/localization.php',
            'published' => base_path('config/localization.php')
        ];

        $this->localizationConfig = include $config[file_exists($config['published']) ? 'published' : 'default'];
    }

    /**
     * Check if the selected driver is path.
     *
     * @return array
     */
    private function driverIsPath()
    {
        return strcasecmp($this->lcConfig('driver'), 'path') == 0;
    }

    /**
     * Return the set of supported locales. Exclude the default locale if the
     * hideDefault directive is set true.
     *
     * @return array
     */
    private function getLocales()
    {
        $locales = $this->lcConfig('locales');
        $default = $this->lcConfig('default');

        if (
            array_key_exists($default, $locales)
            and $this->lcConfig('path')['hideDefault']
        ) {
            unset($locales[$default]);
        }

        return array_keys($locales);
    }
}