<?php

use Illuminate\Support\Facades\Auth;
use RPillz\FeatureAccess\Facades\FeatureAccess;

it('can get feature from facade', function () {
    $feature = config('feature-access.sample-feature');
    unset($feature['levels']);

    $facade_feature = FeatureAccess::getFeatureFromConfig('sample-feature');

    $this->assertEquals($feature, $facade_feature);
});

it('can get feature from facade that does not exist', function () {
    $missing_feature = FeatureAccess::getFeatureFromConfig('missing-feature');
    expect($missing_feature)->toBeNull();
});

it('can get feature with level from facade', function () {
    $feature = config('feature-access.sample-feature');
    unset($feature['levels']);
    $feature_level = config('feature-access.sample-feature.levels.pro');
    $combined_feature = array_merge($feature, $feature_level);
    $combined_feature['level'] = 'pro';

    $facade_feature = FeatureAccess::getFeatureFromConfig('sample-feature', 'pro');

    $this->assertEquals($combined_feature, $facade_feature);
});

it('can get base feature permission for guest', function () {
    $permission = FeatureAccess::guestCan('sample-feature', 'read');
    expect($permission)->toBeTrue();

    $permission = FeatureAccess::guestCan('sample-feature', 'destroy');
    expect($permission)->toBeFalse();
});

it('can get feature permission for user', function () {
    $this->loginWithFakeUser();

    $permission = FeatureAccess::userCan('sample-feature', 'read');
    expect($permission)->toBeTrue();

    $permission = FeatureAccess::userCan('sample-feature', 'create');
    expect($permission)->toBeFalse();

    Auth::user()->setFeatureAccess('sample-feature', 'pro');

    $permission = FeatureAccess::userCan('sample-feature', 'create');
    expect($permission)->toBeTrue();
});

it('can get feature permission for team', function () {
    $this->loginWithFakeUser();

    $permission = FeatureAccess::teamCan('sample-feature', 'read');
    expect($permission)->toBeTrue();

    $permission = FeatureAccess::teamCan('sample-feature', 'destroy');
    expect($permission)->toBeFalse();
})->skip(fn () => ! isset(Auth::user()->currentTeam), 'Only runs when user has currentTeam');
