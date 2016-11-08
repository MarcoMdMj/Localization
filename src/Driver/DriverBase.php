<?php

namespace MarcoMdMj\Localization\Driver;

use Illuminate\Http\Request;
use MarcoMdMj\Localization\Exceptions\UnsupportedLocaleException;

/**
 * Base driver. Engines should extend this class.
 *
 * @package MarcoMdMj\Localization
 */
abstract class DriverBase
{
    /**
     * Detected locale.
     *
     * @var
     */
    protected $locale;

    /**
     * Default locale (From config).
     *
     * @var null
     */
    private $default = null;

    /**
     * Has the locale been loaded from the default configuration?
     *
     * @var bool
     */
    private $configLoaded = false;

    /**
     * Needs to make a redirection?
     *
     * @var bool
     */
    protected $redirectTo = false;

    /**
     * Illuminate Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Engine constructor. Get a Request instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Detect the locale.
     *
     * @return string
     */
    public function render()
    {
        $locale = $this->detectLocale();

        if (!$this->isValid($locale)) {
            $locale = $this->loadFromConfig();
        }

        return $this->locale = $locale;
    }

    /**
     * Check if the given $locale is valid.
     *
     * @param  string|bool $locale
     * @return bool
     */
    public function isValid($locale)
    {
        return $locale and array_key_exists($locale, $this->getSupportedLocales());
    }

    /**
     * Get the default locale from the config.
     *
     * @return string
     */
    protected function loadFromConfig()
    {
        $this->loadedFromConfig(true);

        return $this->getDefault();
    }

    /**
     * Check if, or set that, the locale has been taken from the config.
     *
     * @param  null|bool $is
     * @return bool
     */
    protected function loadedFromConfig($is = null)
    {
        if (!is_null($is)) {
            $this->configLoaded = $is;
        }

        return $this->configLoaded;
    }

    /**
     * Check if the current locale (or $locale) matches the config default.
     *
     * @param  null|string $locale
     * @return bool
     */
    protected function isDefault($locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->locale;
        }

        return strcasecmp($locale, $this->getDefault()) === 0;
    }

    /**
     * Get the default locale from the config.
     *
     * @return string
     */
    protected function getDefault()
    {
        if (is_null($this->default)) {
            $this->default = config('localization.default');
        }

        return $this->default;
    }

    /**
     * Return the current locale.
     *
     * @return string
     */
    public function currentLocale()
    {
        return $this->locale;
    }

    /**
     * Check if the given $locale is valid. If not, throw an exception.
     *
     * @param  string $locale
     * @throws UnsupportedLocaleException
     */
    public function validateLocale($locale)
    {
        if (!$this->isValid($locale)) {
            throw new UnsupportedLocaleException("Locale code \"$locale\" is not valid and cannot be used.");
        }
    }

    /**
     * Return the full set of supported locales.
     *
     * @return array
     */
    public function getSupportedLocales()
    {
        return config('localization.locales');
    }

    /**
     * Check the redirect option from config.
     *
     * @return bool
     */
    protected function redirectDefault()
    {
        return config('localization.redirectDefault');
    }

    /**
     * Check and return the query string.
     *
     * @return string
     */
    protected function getQueryString()
    {
        if (null !== $qs = $this->request->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $qs;
    }

    /**
     * Check if the given driver name matches with the loaded driver.
     *
     * @param  $name
     * @return string
     */
    public function is($name)
    {
        return strcasecmp($name, $this->name) === 0;
    }
}