<?php

namespace MarcoMdMj\Localization\Traits;

use MarcoMdMj\Localization\Driver\DriverInterface;

/**
 * Hub for the driver service.
 *
 * @package MarcoMdMj\Localization
 */
trait DriverHub
{
    /**
     * Store an instance of the selected driver.
     *
     * @var DriverInterface
     */
    private $driver;

    /**
     * Import an instance of the DriverInterface.
     *
     * @var DriverInterface
     */
    private function registerDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
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
     * Detect and return the proper locale.
     *
     * @return string
     */
    public function initialize()
    {
        return $this->driver->render();
    }

    /**
     * Return the detected locale.
     *
     * @return mixed
     */
    public function currentLocale()
    {
        return $this->driver->currentLocale();
    }

    /**
     * Should the request be redirected?
     *
     * @return mixed
     */
    public function shouldRedirect()
    {
        return $this->driver->shouldRedirect();
    }

    /**
     * Check if the current driver matches $name.
     *
     * @param $name
     * @return bool
     */
    public function driverIs($name)
    {
        return $this->driver->is($name);
    }

    /**
     * Return, if driver is path, the route prefix.
     *
     * @return string|null
     */
    public function prefix()
    {
        return $this->driverIs('path') ? $this->driver->prefix() : null;
    }

    /**
     * Return the name of the middleware.
     *
     * @return string|null
     */
    public function middleware()
    {
        return 'localization.redirect';
    }
}