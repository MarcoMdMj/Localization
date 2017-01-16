<?php

namespace MarcoMdMj\Localization\Traits;

use MarcoMdMj\Localization\Driver\UrlGenerator;

/**
 * Hub for the UrlGenerator service.
 *
 * @package MarcoMdMj\Localization
 */
trait UrlGeneratorHub
{
    /**
     * Store an instance of the URLGenerator.
     *
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * Import an instance of the URLGenerator.
     *
     * @var UrlGenerator
     */
    private function registerUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Return the instance of the UrlGenerator.
     *
     * @return UrlGenerator
     */
    public function urlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * Translate a route segment to the specified language.
     *
     * @param  string      $id         Route keyword to be translated.
     * @param  string|null $locale     Language to make the translation.
     * @param  mixed       $parameters Parameters to be passed to the translator.
     * @return string
     */
    public function trans($id, $locale = null, $parameters = [])
    {
        return $this->urlGenerator->trans($id, $locale, $parameters);
    }

    /**
     * Generate a localized version of the given route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  string|null $locale     Language of the generated URL.
     * @param  mixed       $parameters Parameters to be passed to the generator.
     * @return string
     */
    public function route($name = null, $locale = null, $parameters = [])
    {
        return $this->urlGenerator->getLocalizedRoute($name, $locale, $parameters);
    }

    /**
     * Generate all the localized versions of the given route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  mixed       $parameters Parameters to be passed to the generator.
     * @return array
     */
    public function routes($name = null, $parameters = [])
    {
        return $this->urlGenerator->getLocalizedRoutes($name, $parameters);
    }
}