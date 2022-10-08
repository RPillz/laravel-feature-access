<?php

namespace RPillz\FeatureAccess\Traits;

use RPillz\FeatureAccess\Facades\FeatureAccess;
use RPillz\FeatureAccess\Models\FeatureAccess as FeatureAccessModel;

trait HasFeatureAccess
{
    /*
    Define the relationship to the feature_access table
    This allows you to overwrite the default feature config for each instance of a model
    */
    public function featureAccess()
    {
        return $this->morphMany(FeatureAccessModel::class, 'owner');
    }

    /*
    Pull the database row for this model, for a specific feature.
    */
    public function getFeature(string $feature_name)
    {
        return $this->featureAccess->where('feature', $feature_name)->first();
    }

    /*
    Save a custom override on a feature for this model.
    */
    public function setFeatureAccess(string $feature_name, string $level, array $permission = null)
    {
        if (! is_array($permission)) {
            $permission = [];
        }

        $permission['level'] = $level;

        $this->featureAccess()->updateOrCreate([ 'feature' => $feature_name ], $permission);

        $this->load('featureAccess');
    }

    /*
    Get this model's feature permission starting with the database, then an optional subscription check, and defaulting to the config file
    */
    public function getFeatureData(string $feature_name): ?array
    {
        // if db record is found use that
        if ($feature = $this->getFeature($feature_name)) {
            return $feature->permission();
        }

        // check for active subscription, and return that level access
        if (config('feature-access.subscriptions') && $level = $this->getFeatureSubscriptionLevel($feature_name)) {
            return FeatureAccess::getFeatureFromConfig($feature_name, $level);
        }

        // otherwise get the default from the config file
        return FeatureAccess::getFeatureFromConfig($feature_name);
    }

    /*
    Checks for an active subscription for this model, and returns the plan name.
    Written for Cashier by default. Replace this function in your Model for custom integrations.
    */
    public function getFeatureSubscriptionLevel(string $feature_name = null): ?string
    {
        if ($this->subscriptions) { // check that the relationship exists
            // check for the first active subscription.
            // Note: this will work in an app where users have only one subscription at a time.
            if ($subscription = $this->subscriptions()->active()->first()) {
                // The Cashier subscription name is probably "default" by default. Not super helpful.
                return $subscription->name;

                // It may be more helpful to return the subscription product code (eg: prod_123ABC789)
                // return $subscription->items()->first()->stripe_product;
            }
        }

        // If you're using Laravel Spark with this model as a Billable, you can get the plan name more simply.
        // return $this->sparkPlan()->name;

        return null;
    }

    /*
    The hard work is done.
    Below this line are conveniently named functions for ease of use and more readable code.
    */

    public function getFeatureLevel(string $feature_name): ?string
    {
        $data = $this->getFeatureData($feature_name);

        return isset($data['level']) ? $data['level'] : null;
    }

    public function getFeatureLimit(string $feature_name): ?string
    {
        $data = $this->getFeatureData($feature_name);

        return isset($data['limit']) ? $data['limit'] : null;
    }

    public function withinFeatureLimit(string $feature_name, int $count, int $add = 0): bool
    {
        $data = $this->getFeatureData($feature_name);

        if (isset($data['limit'])) {
            return $data['limit'] >= $count + $add ? true : false;
        }

        // no limit set
        return true;
    }

    public function canUseFeature(string $feature_name, $permission): bool
    {
        // super admins can do everything!
        if ($this->hasAllFeatures()) {
            return true;
        }

        if (! $feature = $this->getFeatureData($feature_name)) {
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

        if (! isset($this->$property)) {
            return false;
        }

        if (! in_array($this->$property, config('feature-access.super_admin_access'))) {
            return false;
        }

        return true;
    }

    public function hasAnyFeatures(): bool
    {
        if ($this->featureAccess->count() > 0) {
            return true;
        }

        return false;
    }
}
