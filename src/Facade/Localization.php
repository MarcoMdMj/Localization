<?php

namespace MarcoMdMj\Localization\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Localization Facade
 *
 * @package MarcoMdMj\Localization
 */
class Localization extends Facade
{
    /**
     * Return the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \MarcoMdMj\Localization\Localization::class;
    }
}

