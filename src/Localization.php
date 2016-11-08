<?php

namespace MarcoMdMj\Localization;

use MarcoMdMj\Localization\Traits\DriverHub;
use MarcoMdMj\Localization\Driver\UrlGenerator;
use MarcoMdMj\Localization\Traits\UrlGeneratorHub;
use MarcoMdMj\Localization\Driver\DriverInterface;

/**
 * Hub to package services.
 *
 * @package MarcoMdMj\Localization
 */
class Localization
{
    use DriverHub;
    use UrlGeneratorHub;

    /**
     * Localization constructor. Get the selected driver instance.
     *
     * @param DriverInterface $driver
     * @param UrlGenerator    $urlGenerator
     */
    public function __construct(DriverInterface $driver, UrlGenerator $urlGenerator)
    {
        $this->registerDriver($driver);
        $this->registerUrlGenerator($urlGenerator);
    }
}