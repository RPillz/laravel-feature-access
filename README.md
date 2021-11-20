# Feature access for Laravel apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rpillz/featureaccess.svg?style=flat-square)](https://packagist.org/packages/rpillz/featureaccess)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rpillz/featureaccess/run-tests?label=tests)](https://github.com/rpillz/featureaccess/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rpillz/featureaccess/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rpillz/featureaccess/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rpillz/featureaccess.svg?style=flat-square)](https://packagist.org/packages/rpillz/featureaccess)

Add Plans (eg: Basic, Standard, Pro) and Feature restrictions (eg: Can Make 3 Pages, Can Upload Video) to your Laravel app.

Plans and corresponding features are hard-coded in a config file, but these defaults may be overridden with a database entry for a specific user. (ie: Your special friend who wants all the features, but, like, for free.)

Feature access can be assigned to any model (eg: User, Team) via a trait, which adds properties to use in your app logic, such as $user->canViewFeature('pictures-of-my-dog')

**Be Warned:** Don't assume I know what I'm doing. This is my first public package release, and I'll probably be making plenty of mistakes along the way.

## Installation

You can install the package via composer:

```bash
composer require rpillz/featureaccess
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="featureaccess_without_prefix-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="featureaccess_without_prefix-config"
```

This is an example of a feature mapped out in the config file:

```php
return [

    'sample-feature' => [ // begin with the feature key name. This is used to request permissions.
        'name' => 'Test Feature', // Human readable name
        'read' => true, // Can read/view items in this feature
        'edit' => true, // can edit/change/update items
        'create' => false, // can create new items
        'destroy' => false, // can destroy/delete items
        'limit' => 3, // limit on number of items allowed
        'levels' => [ // Override the base feature permissions with levels or packages (eg: basic, pro, plus)
            'pro' => [ // level/package key
                'name' => 'Extra Stuff!', // human readable name
                'create' => true, // this overrides base permission to create
                'limit' => 5, // limit is increased
                // other permissions will default to base feature definition (above)
            ],
        ]
    ],

];
```

## Usage

### Add Trait To Model

Add it to your User model, which would allow that user to access (or not access) features.

```php
use RPillz\FeatureAccess\Traits\HasFeatures;

class User extends Authenticatable
{

    use HasFeatures;

    ...
```

Your app may make use of Teams (a la Jetstream) in which case you may want to have the Team model using this trait, and accessing user permissions via their team.

```php
Auth::user()->currentTeam->canReadFeature('maple-syrup');
```

This trait can be applied to any Model, or even multiple models if you want to be able apply permissions to both Users and Teams individually. (There is no built-in permission inheritance in such a case, just one or the other.)

#### Use Trait Functions

An example, in a blade template, of adding a button to create a new Post, only if the current user is allowed to *create* on the feature *posts*.

```php
@if(Auth::user()->canCreateFeature('posts'))
    <button>Add New Post</button>
@endif
```

##### Set Permission

```php

$user->setFeaturePermission('sample-feature', 'basic'); // give this user 'basic' level access to 'feature_name'

$user->setFeaturePermission('sample-feature', 'pro'); // give this user 'pro' level access to 'feature_name'

$user->setFeaturePermission('sample-feature', 'pro', [ 'edit' => false ]); // give this user 'pro' level access to 'feature_name', but override the deafult setting to allow edits.

```

In the first example above, the user is granted *basic* level access to *sample-feature*. However, there is no *basic* level defined in the feature config file. Their permissions for *sample-feature* will default to the basic settings. These same default settings would be used if no level has been explicitly set for a user.

##### Check Permission

```php

$user->featureAccess('feature-name', 'permission-requested');

// alias functions

$user->canReadFeature('sample-feature'); // permission-requested = read
$user->canViewFeature('sample-feature'); // permission-requested = read

$user->canEditFeature('sample-feature'); // permission-requested = edit
$user->canUpdateFeature('sample-feature'); // permission-requested = edit

$user->canCreateFeature('sample-feature'); // permission-requested = create

$user->canDestroyFeature('sample-feature'); // permission-requested = destroy
$user->canDeleteFeature('sample-feature'); // permission-requested = destroy


```

### Super-Admin Permission

In the config file there is a **'super_admin_access'** array of email addresses. If the current user email matches one of these, all permission tests will return true. They get to do *everything*.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ryan Pilling](https://github.com/RPillz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
