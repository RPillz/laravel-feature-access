<?php

it('has sample feature', function () {
    $feature = config('feature-access.sample-feature');
    unset($feature['levels']);

    $user_feature = $this->testUser->getFeatureData('sample-feature');

    $this->assertEquals($feature, $user_feature);
});

it('has sample feature with pro level', function () {
    $feature = config('feature-access.sample-feature');
    unset($feature['levels']);

    $feature_level = config('feature-access.sample-feature.levels.pro');

    $combined_feature = array_merge($feature, $feature_level);

    $combined_feature['level'] = 'pro';

    $this->testUser->setFeatureAccess('sample-feature', 'pro');

    $user_feature = $this->testUser->getFeatureData('sample-feature');

    $this->assertEquals($combined_feature, $user_feature);
});

it('has permission to read sample feature', function () {
    $can_read = config('feature-access.sample-feature.read');
    expect($can_read)->toBeTrue();

    $user_can_read = $this->testUser->canReadFeature('sample-feature');
    expect($user_can_read)->toBeTrue();
});

it('has permission to create sample feature only at pro level', function () {
    $can_create = config('feature-access.sample-feature.create');
    expect($can_create)->toBeFalse();

    $pro_can_create = config('feature-access.sample-feature.levels.pro.create');
    expect($pro_can_create)->toBeTrue();

    $user_can_create = $this->testUser->canCreateFeature('sample-feature');
    expect($user_can_create)->toBeFalse();

    $this->testUser->setFeatureAccess('sample-feature', 'pro');

    // $this->testUser->refresh();

    $pro_user_can_create = $this->testUser->canCreateFeature('sample-feature');
    expect($pro_user_can_create)->toBeTrue();
});

it('has feature level', function () {
    $this->testUser->setFeatureAccess('sample-feature', 'tested');

    $user_level = $this->testUser->getFeatureLevel('sample-feature');

    $this->assertEquals($user_level, 'tested');
});

it('has permission for super admin', function () {
    $user_can_create = $this->testUser->canCreateFeature('sample-feature');
    expect($user_can_create)->toBeFalse();

    $admin_can_create = $this->testAdmin->canCreateFeature('sample-feature');
    expect($admin_can_create)->toBeTrue();
});

it('checks limits on feature', function () {
    $check_limit = $this->testUser->getFeatureLimit('sample-feature');
    $this->assertEquals($check_limit, 3);

    $under_limit = $this->testUser->withinFeatureLimit('sample-feature', 1);
    expect($under_limit)->toBeTrue();

    $at_limit = $this->testUser->withinFeatureLimit('sample-feature', 3);
    expect($at_limit)->toBeTrue();

    $over_limit = $this->testUser->withinFeatureLimit('sample-feature', 4);
    expect($over_limit)->toBeFalse();

    $will_be_over_limit = $this->testUser->withinFeatureLimit('sample-feature', 1, 5);
    expect($will_be_over_limit)->toBeFalse();
});

it('checks for a feature upgraded by user subscription', function () {
    $default_limit = $this->testUser->getFeatureLimit('sample-feature');
    $this->assertEquals($default_limit, 3);

    config([ 'feature-access.subscriptions' => true ]);

    $pro_limit = $this->testUser->getFeatureLimit('sample-feature');
    $this->assertEquals($pro_limit, 5);
});

it('can check if any features are set for the user', function () {
    expect($this->testUser->hasAnyFeatures())->toBeFalse();

    $this->testUser->setFeatureAccess('sample-feature', 'pro');

    expect($this->testUser->hasAnyFeatures())->toBeTrue();
});
