<?php

namespace RPillz\FeatureAccess;

use RPillz\FeatureAccess\Commands\FeatureAccessCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FeatureAccessServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('featureaccess')
            ->hasConfigFile('feature-access')
            // ->hasViews()
            // ->hasCommand(FeatureAccessCommand::class)
            ->hasMigration('create_feature_access_table');
    }
}
