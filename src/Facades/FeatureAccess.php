<?php

namespace RPillz\FeatureAccess\Facades;

use Illuminate\Support\Facades\Auth;
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

    public static function getFeatureFromConfig(string $feature_name, string $level = null): ?array
    {
        $feature = config('feature-access.'.$feature_name);
        unset($feature['levels']);



        if (! is_null($level)) {
            $feature_level = config('feature-access.'.$feature_name.'.levels.'.$level);
            if (is_array($feature_level)) {
                $feature = array_merge($feature, $feature_level);
                $feature['level'] = $level;
            }
        }

        return $feature;
    }

    //////////////////
    // User methods

    public static function guestCan(string $feature_name, string $permission): bool
    {
        $feature = FeatureAccess::getFeatureFromConfig($feature_name);

        return $feature[$permission] ?: false;
    }

    public static function userCan(string $feature_name, string $permission): bool
    {
        if (! Auth::user()) { // not logged in, return base feature permission
            return FeatureAccess::guestCan($feature_name, $permission);
        }

        return Auth::user()->canUseFeature($feature_name, $permission);
    }

    public static function userFeature(string $feature_name): ?array
    {
        if (! Auth::user()) { // not logged in, return base feature permission
            return FeatureAccess::getFeatureFromConfig($feature_name);
        }

        return Auth::user()->getFeatureData($feature_name);
    }

    // alias methods for User

    public static function userCanCreate(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'create');
    }

    public static function userCanRead(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'read');
    }

    public static function userCanView(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'read');
    }

    public static function userCanUpdate(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'update');
    }

    public static function userCanEdit(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'update');
    }

    public static function userCanDestroy(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'destroy');
    }

    public static function userCanDelete(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'destroy');
    }

    ////////////////////
    // Team methods
    // works with Jetstream teams

    public static function teamCan(string $feature_name, string $permission): bool
    {
        if (! Auth::user()) { // not logged in, return base feature permission
            return FeatureAccess::guestCan($feature_name, $permission);
        }

        if (! isset(Auth::user()->currentTeam)) { // no current team
            return false;
        }

        return Auth::user()->currentTeam->canUseFeature($feature_name, $permission);
    }

    public static function teamFeature(string $feature_name): ?array
    {
        if (! Auth::user() || ! isset(Auth::user()->currentTeam)) { // not logged in, return base feature permission
            return FeatureAccess::getFeatureFromConfig($feature_name);
        }

        return Auth::user()->currentTeam->getFeatureData($feature_name);
    }

    // alias methods for Team

    public static function teamCanCreate(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'create');
    }

    public static function teamCanRead(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'read');
    }

    public static function teamCanView(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'read');
    }

    public static function teamCanUpdate(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'update');
    }

    public static function teamCanEdit(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'update');
    }

    public static function teamCanDestroy(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'destroy');
    }

    public static function teamCanDelete(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'destroy');
    }
}
