<?php

namespace MarcoMdMj\Localization\Driver\Engines;

use MarcoMdMj\Localization\Driver\DriverBase;
use MarcoMdMj\Localization\Driver\DriverInterface;

/**
 * Path driver fot the localization service.
 *
 * @package MarcoMdMj\Localization
 */
class PathDriver extends DriverBase implements DriverInterface
{
    /**
     * Name of the driver.
     *
     * @var string
     */
    public $name = 'path';

    /**
     * Detects the proper locale based on the requested URI path.
     *
     * @return bool|string
     */
    public function detectLocale()
    {
        return $this->definedInCustomKernel() ? : $this->loadFromRequestUri();
    }

    /**
     * Return the detected locale in the custom package kernel, if found.
     * Otherwise, return false.
     *
     * @return string|bool
     */
    private function definedInCustomKernel()
    {
        return defined('LCL_CODE') ? LCL_CODE : false;
    }

    /**
     * Extract the locale from the first segment of the current URI.
     *
     * @return bool|string
     */
    private function loadFromRequestUri()
    {
        $detectedLocale = $this->request->segment(1);

        if (
            array_key_exists($detectedLocale, $this->getSupportedLocales())
            and (!$this->isDefault($detectedLocale) or !$this->hideDefault())
        ) {
            return $detectedLocale;
        }

        return false;
    }

    /**
     * Return the hideDefault configuration setting.
     *
     * @return bool
     */
    private function hideDefault()
    {
        return config('localization.path.hideDefault');
    }

    /**
     * Return the proper prefix (if any) to read and generate the routes.
     * It should be called in the laravel's web routes file.
     *
     * @return null|string
     */
    public function prefix()
    {
        if (
            $this->definedInCustomKernel()
            or ($this->isDefault() and $this->routeWasAcceptedOnlyForRedirection() != $this->hideDefault())
        ) {
            return null;
        }

        return $this->locale;
    }

    /**
     * Return true if the route was accepted only to make a redirection. This
     * special condition, applied only to the default locale, happens when
     * the hideDefault directive setting is set to TRUE, but the locale
     * slug IS PRESENT on the requested uri, or vice versa, and also
     * requires that the redirectDefault is set to true.
     *
     * @return bool
     */
    private function routeWasAcceptedOnlyForRedirection()
    {
        return (
            $this->isDefault()
            and $this->redirectDefault()
            and $this->hideDefault() == $this->isLocaleSlugInUri()
        );
    }

    /**
     * Return true if the current locale slug is present in the requested URI.
     *
     * @return bool
     */
    private function isLocaleSlugInUri()
    {
        return
            $this->definedInCustomKernel()
            or strcasecmp($this->request->segment(1), $this->locale) === 0;
    }

    /**
     * @param $uri
     * @param $locale
     * @param bool $query
     * @return object
     */
    public function generateLocalizedUrl($uri, $locale, $query = false)
    {
        $translatedUri = $this->addLocaleSlug($locale) . $this->getCleanUri($uri);

        !$query or $translatedUri.= $this->getQueryString();

        return (object) [
            'path'   => $translatedUri,
            'domain' => null
        ];
    }

    /**
     * @param $locale
     * @return null|string
     */
    private function addLocaleSlug($locale)
    {
        if ($this->isDefault($locale) and $this->hideDefault()) {
            return null;
        }

        return '/' . $locale;
    }

    /**
     * When the current locale is the default, but the requested URI is not
     * coherent with the value of the hideDefault directive setting, the
     * proper URI is returned for making a redirection. Otherwise, it
     * returns false.
     *
     * @return bool|string
     */
    public function shouldRedirect()
    {
        if (
            $this->isDefault() and $this->redirectDefault()
        ) {
            if ($this->hideDefault() and $this->isLocaleSlugInUri()) {
                $pattern = '#^/(' . $this->locale . ')(?:/|$)#i';
                $uri = $this->request->getRequestUri();

                return $this->redirectTo = preg_replace($pattern, '/', $uri);
            }

            if (!$this->hideDefault() and !$this->isLocaleSlugInUri()) {
                return $this->redirectTo = '/' . $this->locale . $this->request->getRequestUri();
            }
        }

        return $this->redirectTo = false;
    }

    /**
     * Return the given URI with the locale slug removed (if present).
     *
     * @param  string $uri
     * @return mixed
     */
    private function getCleanUri($uri)
    {
        if (
            !$this->definedInCustomKernel()
            and (
                $this->isDefault() and $this->isLocaleSlugInUri()
                or !$this->loadedFromConfig()
            )
        ) {
            $uri = preg_replace('#^(/[^/]+)#i', '', $uri);
        }

        return $uri;
    }
}