<?php

if (!function_exists('localization')) {
    /**
     * Retrieve the localization service from the container.
     *
     * @return \MarcoMdMj\Localization\Localization
     */
    function localization() {
        return app(MarcoMdMj\Localization\Localization::class);
    }
}

if (!function_exists('locale')) {
    /**
     * Shortcut for retrieving the current locale.
     *
     * @return string
     */
    function locale() {
        return localization()->currentLocale();
    }
}

if (!function_exists('lCurrent')) {
    /**
     * Shortcut for retrieving the current locale.
     *
     * @return string
     */
    function lCurrent() {
        return localization()->currentLocale();
    }
}

if (!function_exists('lPrefix')) {
    /**
     * Shortcut for retrieving the proper route prefix (When path driver is being used).
     *
     * @return string|null
     */
    function lPrefix() {
        return localization()->prefix();
    }
}

if (!function_exists('lMiddleware')) {
    /**
     * Shortcut for retrieving the name of the redirect middleware.
     *
     * @return string
     */
    function lMiddleware() {
        return localization()->middleware();
    }
}

if (!function_exists('lTrans')) {
    /**
     * Shortcut for translating route segments to the specified language.
     *
     * @param  string      $id         Route keyword to be translated.
     * @param  string|null $locale     Language to make the translation.
     * @param  mixed       $parameters Parameters to be passed to the translator.
     * @return string
     */
    function lTrans($id, $locale = null, $parameters = []) {
        return localization()->trans($id, $locale, $parameters);
    }
}

if (!function_exists('lRoute')) {
    /**
     * Shortcut for generating the localized version of the requested route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  array       $parameters Parameters to be passed to the generator.
     * @return string
     */
    function lRoute($name = null, $parameters = []) {
        return localization()->route($name, null, $parameters);
    }
}

if (!function_exists('localizedRoute')) {
    /**
     * Shortcut for generating the localized version of the requested route in the
     * specified locale.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  string|null $locale     Language of the generated URL.
     * @param  array       $parameters Parameters to be passed to the generator.
     * @return string
     */
    function localizedRoute($name = null, $locale = null, $parameters = []) {
        return localization()->route($name, $locale, $parameters);
    }
}

if (!function_exists('localizedRoutes')) {
    /**
     * Shortcut for generating the localized versions of the given route.
     *
     * @param  string|null $name       Name of the route that will be translated.
     * @param  mixed       $parameters Parameters to be passed to the generator.
     * @return array
     */
    function localizedRoutes($name = null, $parameters = null) {
        return localization()->routes($name, $parameters);
    }
}