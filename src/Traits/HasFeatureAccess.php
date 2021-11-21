<?php

namespace RPillz\FeatureAccess\Traits;

use Illuminate\Support\Facades\Auth;
use RPillz\FeatureAccess\Models\FeatureAccess as FeatureAccessModel;
use RPillz\FeatureAccess\Facades\FeatureAccess;

trait HasFeatureAccess
{
    public function featureAccess()
    {
        return $this->morphMany(FeatureAccessModel::class, 'owner');
    }

    public function getFeature(string $feature_name)
    {
        return $this->featureAccess->where('feature', $feature_name)->first();
    }

    public function setFeatureAccess(string $feature_name, string $level, array $permission = null)
    {
        if (! is_array($permission)) {
            $permission = [];
        }

        $permission['level'] = $level;

        $this->featureAccess()->updateOrCreate([ 'feature' => $feature_name ], $permission);

        $this->load('featureAccess');
    }

    public function getFeatureData(string $feature_name): ?array
    {
        if ($feature = $this->getFeature($feature_name)) { // if db record is found us that
            return $feature->permission();
        }

        return FeatureAccess::getFeatureFromConfig($feature_name);
    }

    public function getFeatureLevel(string $feature_name): string
    {
        $feature = $this->getFeature($feature_name);

        if (! $feature) {
            return null;
        }

        return $feature->level;
    }

    public function canUseFeature(string $feature_name, $permission): bool
    {

        // super admins can do everything!
        if ($this->hasAllFeatures()){
            return true;
        }

        if (!$feature = $this->getFeatureData($feature_name)) {
            return false;
        }

        if (isset($feature[$permission])) {
            return $feature[$permission];
        }

        return false;
    }

    public function canReadFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'read');
    }

    public function canViewFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'read');
    }

    public function canUpdateFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'update');
    }

    public function canEditFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'update');
    }

    public function canCreateFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'create');
    }

    public function canDestroyFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'destroy');
    }

    public function canDeleteFeature($feature_name)
    {
        return $this->canUseFeature($feature_name, 'destroy');
    }

    public function hasAllFeatures(): bool
    {

        $property = config('feature-access.super_admin_property', 'email');

        if (! isset($this->$property)){
            return false;
        }

        if (! in_array($this->$property, config('feature-access.super_admin_access'))) {
            return false;
        }

        return true;
    }
}
