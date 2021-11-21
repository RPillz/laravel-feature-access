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

    static function getFeatureFromConfig(string $feature_name, string $level = null): ?array
    {
        $feature = config('feature-access.'.$feature_name);
        unset($feature['levels']);

        if (!is_null($level)){
            $feature_level = config('feature-access.'.$feature_name.'.levels.'.$level);
            $feature = array_merge($feature, $feature_level);
            $feature['level'] = $level;
        }

        return $feature;
    }

    //////////////////
    // User methods

    static function guestCan(string $feature_name, string $permission): bool
    {
        $feature = FeatureAccess::getFeatureFromConfig($feature_name);

        return $feature[$permission] ?: false;
    }

    static function userCan(string $feature_name, string $permission): bool
    {
        if (!Auth::user()){ // not logged in, return base feature permission
            return FeatureAccess::guestCan($feature_name, $permission);
        }

        return Auth::user()->canUseFeature($feature_name, $permission);
    }

    static function userFeature(string $feature_name): ?array
    {
        if (!Auth::user()){ // not logged in, return base feature permission
            return FeatureAccess::getFeatureFromConfig($feature_name);
        }

        return Auth::user()->getFeatureData($feature_name);
    }

    // alias methods for User

    static function userCanCreate(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'create');
    }

    static function userCanRead(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'read');
    }

    static function userCanView(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'read');
    }

    static function userCanUpdate(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'update');
    }

    static function userCanEdit(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'update');
    }

    static function userCanDestroy(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'destroy');
    }

    static function userCanDelete(string $feature_name): bool
    {
        return FeatureAccess::userCan($feature_name, 'destroy');
    }

    ////////////////////
    // Team methods
    // works with Jetstream teams

    static function teamCan(string $feature_name, string $permission): bool
    {
        if (!Auth::user()){ // not logged in, return base feature permission
            return FeatureAccess::guestCan($feature_name, $permission);
        }

        if (! isset(Auth::user()->currentTeam)){ // no current team
            return false;
        }

        return Auth::user()->currentTeam->canUseFeature($feature_name, $permission);
    }

    static function teamFeature(string $feature_name): ?array
    {
        if (!Auth::user() || ! isset(Auth::user()->currentTeam)){ // not logged in, return base feature permission
            return FeatureAccess::getFeatureFromConfig($feature_name);
        }

        return Auth::user()->currentTeam->getFeatureData($feature_name);
    }

    // alias methods for Team

    static function teamCanCreate(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'create');
    }

    static function teamCanRead(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'read');
    }

    static function teamCanView(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'read');
    }

    static function teamCanUpdate(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'update');
    }

    static function teamCanEdit(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'update');
    }

    static function teamCanDestroy(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'destroy');
    }

    static function teamCanDelete(string $feature_name): bool
    {
        return FeatureAccess::teamCan($feature_name, 'destroy');
    }

}
