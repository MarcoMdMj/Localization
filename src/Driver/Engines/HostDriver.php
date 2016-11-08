<?php

namespace MarcoMdMj\Localization\Driver\Engines;

use MarcoMdMj\Localization\Driver\DriverBase;
use MarcoMdMj\Localization\Driver\DriverInterface;
use MarcoMdMj\Localization\Exceptions\UnsupportedHostException;
use MarcoMdMj\Localization\Exceptions\UnsupportedLocaleException;

/**
 * Host driver fot the localization service.
 *
 * @package MarcoMdMj\Localization
 */
class HostDriver extends DriverBase implements DriverInterface
{
    /**
     * Name of the driver.
     *
     * @var string
     */
    public $name = 'host';

    /**
     * List of supported hosts.
     *
     * @var array
     */
    private $supportedHosts = null;

    /**
     * Detects the proper locale based on the current host.
     *
     * @throws UnsupportedHostException
     * @return bool|string
     */
    public function detectLocale()
    {
        $host = $this->getCurrentHost();
        $hosts = $this->getSupportedHosts();

        if ($locale = array_search($host, $hosts)) {
            return $locale;
        }

        if (!$this->redirectDefault()) {
            throw new UnsupportedHostException('The domain "' . $host . '" is not supported.');
        }

        return false;
    }

    /**
     * Get the full list of supported hosts.
     *
     * @return array
     */
    private function getSupportedHosts()
    {
        if (is_null($this->supportedHosts)) {
            $this->supportedHosts = array_map(function($locale) {
                return $locale['host'];
            }, $this->getSupportedLocales());
        }

        return $this->supportedHosts;
    }

    /**
     * Get the current host.
     *
     * @return string
     */
    private function getCurrentHost()
    {
        return $this->request->getHost();
    }

    /**
     * If the current host does not match with any of the supported hosts, a redirection should
     * be made to the host belonging to the default locale.
     *
     * @return bool|string
     */
    public function shouldRedirect()
    {
        if ($this->isDefault() and $this->redirectDefault()) {
            $locale = $this->currentLocale();

            $host = $this->getHostForLocale($locale);

            if (strcasecmp($host, $this->getCurrentHost()) <> 0) {
                return $this->redirectTo = preg_replace('#^([^:]+://)[^:/]+#i', "\${1}$host", $this->request->url());
            }
        }

        return $this->redirectTo = false;
    }

    /**
     * Generate the localized url for the given $uri.
     *
     * @param  string $uri
     * @param  string $locale
     * @param  bool   $query
     * @return object
     */
    public function generateLocalizedUrl($uri, $locale, $query = false)
    {
        !$query or $uri.= $this->getQueryString();

        return (object) [
            'path'   => $uri,
            'domain' => $this->getHostForLocale($locale)
        ];
    }

    /**
     * Return the host for the given locale.
     *
     * @param  string $locale
     * @return string
     * @throws UnsupportedLocaleException
     */
    private function getHostForLocale($locale)
    {
        $hosts = $this->getSupportedHosts();

        if (array_key_exists($locale, $hosts)) {
            return $hosts[$locale];
        }

        throw new UnsupportedLocaleException('The locale "' . $locale. '" is not supported.');
    }
}