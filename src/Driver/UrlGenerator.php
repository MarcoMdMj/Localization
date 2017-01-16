<?php

namespace MarcoMdMj\Localization\Driver;

use Illuminate\Routing\Router;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * UrlGenerator service.
 *
 * @package MarcoMdMj\Localization
 */
class UrlGenerator
{
    /**
     * Instance of the localization driver.
     *
     * @var DriverInterface
     */
    private $driver;

    /**
     * Instance of the Illuminate router service.
     *
     * @var Router
     */
    private $router;

    /**
     * Initialize the service.
     *
     * @param DriverInterface $driver
     * @param Router          $router
     */
    public function __construct(DriverInterface $driver, Router $router)
    {
        $this->driver = $driver;
        $this->router = $router;
    }

    /**
     * Return the driver instance.
     *
     * @return DriverInterface
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * Generate a localized version of the given route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  string|null $locale     Language of the generated URL.
     * @param  mixed       $parameters Parameters to be passed to the generator.
     * @return string
     */
    public function getLocalizedRoute($name = null, $locale = null, $parameters = [])
    {
        if (is_null($locale)) {
            $locale = $this->driver->currentLocale();
        }

        $this->driver->validateLocale($locale);

        $qs = false;

        if (is_null($name)) {
            $name = '::current';
            $qs = true;
        }

        // if (is_null($parameters)) {
        //     $parameters = $this->router->current()->parameters();
        // }

        $translatedRouteName = $this->getTranslatedRouteName($name, $locale);

        if (!$this->router->has($translatedRouteName)) {
            $this->registerTranslatedRoute($name, $locale, $qs);
        }

        return route($translatedRouteName, $parameters);
    }

    /**
     * Generate all the localized versions of the given route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  mixed       $parameters Parameters to be passed to the generator.
     * @return array
     */
    public function getLocalizedRoutes($name = null, $parameters = [])
    {
        $collection = [];

        foreach (array_keys($this->driver->getSupportedLocales()) as $locale) {
            $parameters = ( is_array($parameters) and array_key_exists($locale, $parameters) ) ? $parameters[$locale] : $parameters;

            $collection[$locale] = $this->getLocalizedRoute($name, $locale, $parameters);
        }

        return $collection;
    }

    /**
     * Generate and return the name of the localized routes.
     *
     * @param  string $name
     * @param  string $locale
     * @return string
     */
    private function getTranslatedRouteName($name, $locale)
    {
        return "$name::$locale";
    }

    /**
     * Generate, and register in the router, a localized route.
     *
     * @param string $name
     * @param string $locale
     * @param string $qs
     */
    private function registerTranslatedRoute($name, $locale, $qs)
    {
        $translatedUri = $this->getTranslatedUri($name, $locale);

        $translatedUrl = $this->driver->generateLocalizedUrl($translatedUri, $locale, $qs);

        $this->router->get($translatedUrl->path, [
            'domain' => $translatedUrl->domain,
            'as'     => $this->getTranslatedRouteName($name, $locale)
        ]);
    }

    /**
     * Return a route by its name with the uri segments translated.
     *
     * @param  string $name
     * @param  string $locale
     * @return string
     */
    private function getTranslatedUri($name, $locale)
    {
        $segments = $this->getUriSegments($name);

        return '/' . implode('/', $this->translateSegments($segments, $locale));
    }

    /**
     * Find a route by its name and return its segments as an array.
     *
     * @param  string $name
     * @return array
     */
    private function getUriSegments($name)
    {
        $uri = $this->getRouteByName($name)->uri();

        return array_filter(explode('/', $uri), function ($v) {
            return $v != '';
        });
    }

    /**
     * Translate the given segments to the locale specified.
     *
     * @param  array  $segments
     * @param  string $locale
     * @return array
     */
    private function translateSegments(array $segments, $locale)
    {
        if (count($segments) > 0) {
            foreach ($segments as &$segment) {
                if ($id = $this->findTranslationId($segment)) {
                    $segment = $this->trans($id, $locale);
                }
            }
            unset($segment);
        }

        return $segments;
    }

    /**
     * Find the translation id of the given $segment in the language file.
     *
     * @param  string $segment
     * @return string|bool
     */
    private function findTranslationId($segment)
    {
        return array_search($segment, (array) $this->trans());
    }

    /**
     * Return the translation for the given id.
     *
     * @param  null|string $id
     * @param  null|string $locale
     * @param  mixed       $parameters
     * @param  string      $domain
     * @return string
     */
    public function trans($id = null, $locale = null, $parameters = [], $domain = 'messages')
    {
        $id = is_null($id) ? 'routes' : "routes.$id";

        return app('translator')->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Load a route by its name.
     *
     * @param  string $name
     * @throws RouteNotFoundException
     * @return \Illuminate\Routing\Route
     */
    private function getRouteByName($name)
    {
        if (strcasecmp($name, '::current') === 0) {
            return $this->router->current();
        }

        if (!$this->router->has($name)) {
            throw new RouteNotFoundException("Route named \"$name\" was not found.");
        }

        return $this->router->getRoutes()->getByName($name);
    }
}