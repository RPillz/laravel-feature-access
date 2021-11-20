<?php

namespace RPillz\FeatureAccess\Traits;

use RPillz\FeatureAccess\Models\Feature;

trait HasFeatureAccess
{
    public function features()
    {
        return $this->morphMany(Feature::class, 'owner');
    }

    public function getFeature(string $slug)
    {
        return $this->features->where('feature', $slug)->first();
    }

    public function setFeaturePermission(string $slug, string $level, array $permission = null)
    {
        if (! is_array($permission)) {
            $permission = [];
        }

        $permission['level'] = $level;

        $this->features()->updateOrCreate([ 'feature' => $slug ], $permission);
    }

    public function getFeaturePermission(string $slug): array
    {
        if ($feature = $this->getFeature($slug)) { // if db record is found us that
            return $feature->permission();
        } elseif ($permission = config('feature-access.'.$slug)) { // or use default from config file
            unset($permission['levels']);

            return $permission;
        }

        return [];
    }

    public function featureAccess(string $slug, $access = null)
    {

        // check for super-admin email match
        if (auth()->user() && in_array(auth()->user()->email, config('feature-access.super_admin_access'))) {
            if (is_null($access)) {
                return config('feature-access.super_admin_permission');
            }

            return true;
        }

        if ($permission = $this->getFeaturePermission($slug)) {
            if ($access) {
                if (isset($permission[$access])) {
                    return $permission[$access];
                }

                return false;
            }

            return $permission;
        }

        return false;
    }

    public function canReadFeature($slug)
    {
        return $this->featureAccess($slug, 'read');
    }

    public function canViewFeature($slug)
    {
        return $this->featureAccess($slug, 'read');
    }

    public function canUpdateFeature($slug)
    {
        return $this->featureAccess($slug, 'update');
    }

    public function canEditFeature($slug)
    {
        return $this->featureAccess($slug, 'update');
    }

    public function canCreateFeature($slug)
    {
        return $this->featureAccess($slug, 'create');
    }

    public function canDestroyFeature($slug)
    {
        return $this->featureAccess($slug, 'destroy');
    }

    public function canDeleteFeature($slug)
    {
        return $this->featureAccess($slug, 'destroy');
    }
}
