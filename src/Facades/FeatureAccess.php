<?php

namespace RPillz\FeatureAccess\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RPillz\FeatureAccess\FeatureAccess
 */
class FeatureAccess extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'featureaccess';
    }
}
