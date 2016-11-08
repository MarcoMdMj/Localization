<?php

namespace MarcoMdMj\Localization\Driver;

/**
 * Interface DriverInterface
 * @package MarcoMdMj\Localization\Driver
 */
interface DriverInterface
{
    /**
     * Must return the detected locale, or false.
     *
     * @return mixed
     */
    public function detectLocale();

    /**
     * Return the destination url if a redirection needs to be made. Otherwise,
     * return false.
     *
     * @return bool|string
     */
    public function shouldRedirect();

    /**
     * Generate the localized url as an object for the given $uri. This object
     * must content two properties: domain and path.
     *
     * @param  string $uri
     * @param  string $locale
     * @param  bool   $query
     * @return object
     */
    public function generateLocalizedUrl($uri, $locale, $query = false);
}