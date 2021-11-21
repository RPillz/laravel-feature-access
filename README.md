# Feature access for Laravel apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rpillz/featureaccess.svg?style=flat-square)](https://packagist.org/packages/rpillz/featureaccess)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rpillz/featureaccess/run-tests?label=tests)](https://github.com/rpillz/featureaccess/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rpillz/featureaccess/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rpillz/featureaccess/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rpillz/featureaccess.svg?style=flat-square)](https://packagist.org/packages/rpillz/featureaccess)

Add Plans (eg: Basic, Standard, Pro) and Feature restrictions (eg: Can Make 3 Pages, Can Upload Video) to your Laravel app.

Plans and corresponding features are hard-coded in a config file, but these defaults may be overridden with a database entry for a specific user. (ie: Your special friend who wants all the features, but, like, for free.)

Feature access can be assigned to any model (eg: User, Team) via a trait, which adds properties to use in your app logic, such as $user->canViewFeature('pictures-of-my-dog')

## Installation

You can install the package via composer:

```bash
composer require rpillz/laravel-feature-access
```

**Publish the config file.** This is where your features and tiers are defined.

```bash
php artisan vendor:publish --tag=feature-access-config
```

**Publish and run migrations.** This will create a *'features'* table for storing access information attached to your models.

```bash
php artisan vendor:publish --tag=feature-access-migrations
php artisan migrate
```

## Basic Usage

### 1. Define Features (and Tiers) in the Config

Start by defining your app features in the *feature-access.php* config file.

This is an example of a feature mapped out in the config file:

```php
return [

    'sample-feature' => [ // begin with the feature key name. This is used to request permissions.
        'name' => 'Test Feature', // Human readable name
        'read' => true, // Can read/view items in this feature
        'update' => false, // cannot edit/change/update items
        'create' => false, // cannot create new items
        'destroy' => false, // cannot destroy/delete items
        'limit' => 3, // limit on number of items allowed
        'levels' => [ // Override the base feature permissions with levels or packages (eg: basic, pro, plus)
            'pro' => [ // level/package key
                'name' => 'Extra Stuff!', // human readable name
                'update' => true, // pro level can edit/change/update items
                'create' => true, // pro level can create items
                'limit' => 5, // limit is increased
                // other permissions will default to base feature definition (above)
            ],
        ]
    ],

];
```

The base level of this feature will be the permission used for all users **and guests** unless they have been explicitly granted an upgraded access level, or override.

### 2. Add Trait To Model

In most cases, you would add this trait to your **User** model, which would allow that user to access (or not access) features.

```php
use RPillz\FeatureAccess\Traits\HasFeatureAccess;

class User extends Authenticatable
{

    use HasFeatureAccess;

    ...
```

### 3. Add Permission Checks to your App Logic

An example, in a blade template, of adding a button to create a new item, only if the current user is allowed to *create* on the feature *sample-feature*.

```php
@if(Auth::user()->canCreateFeature('sample-feature'))
    <button>Add New Sample Item</button>
@endif
```

### 4. Grant Higher Access To Users

You can set permission acces for your User (or any model) with the **setFeatureAccess()** method.

```php

$user->setFeatureAccess('sample-feature', 'basic'); // give this user 'basic' level access to 'sample-feature' (which does not exist)

$user->setFeatureAccess('sample-feature', 'pro'); // give this user 'pro' level access to 'sample-feature'

$user->setFeatureAccess('sample-feature', 'pro', [ 'update' => false ]); // give this user 'pro' level access to 'sample-feature', but override the default setting to allow edits just for this user.

```

In the first example above, the user is granted *basic* level access to *sample-feature*. However, there is no *basic* level defined in the feature config file. Their permissions for *sample-feature* will default to the basic settings. These same default settings would be used if no level has been explicitly set for a user.


## More Usage Options

### Using Team Model

Your app may make use of Teams (a la Jetstream) in which case you may want to have the **Team** model using this trait, and accessing user permissions via their team.

**Add the *HasFeatureAccess* trait to your Team model.** Now any user on that team will have access to the same features, through the team.

```php
Auth::user()->currentTeam->canReadFeature('maple-syrup');
```

This trait can be applied to any Model, or even multiple models if you want to be able apply permissions to both Users and Teams individually. (There is no built-in permission inheritance in such a case, just one or the other.)

### Using Other Models

You can grant feature access to any model using the *HasFeatureAccess* trait. In most cases, that model would somehow be associated with the authenticated user. However, it could be adapted to other uses:

- On a Tenant or Domain model, which would grant features depending upon the domain through which your app was being accessed.
- On a particular Page or Post to change what features are displayed in the layout.

You can be using this on multiple models within the same app, so you may bend it to your will!

### Methods to Check Permission On Your Model

```php

$user->canUseFeature('feature-name', 'permission-requested');

// alias functions

$user->canCreateFeature('sample-feature'); // permission-requested = create

$user->canReadFeature('sample-feature'); // permission-requested = read
$user->canViewFeature('sample-feature'); // permission-requested = read

$user->canUpdateFeature('sample-feature'); // permission-requested = update
$user->canEditFeature('sample-feature'); // permission-requested = update

$user->canDestroyFeature('sample-feature'); // permission-requested = destroy
$user->canDeleteFeature('sample-feature'); // permission-requested = destroy

// or get all the permission data for your model

$user->getFeatureData('sample-feature'); // return array

```

### Using The Facade

You can use the Facade methods to check permissions. However, **this only works for the User and/or Team models.** The upside is these will work whether or not a visitor is signed in to an account, and returns base access for guests.

```php

FeatureAccess::userCan(string $feature_name, string $permission); // check for permission to 'create', 'read', 'update', or 'destroy'

// shortcut aliases

FeatureAccess::userCanCreate('sample-feature'); // returns boolean true/false

FeatureAccess::userCanRead('sample-feature');
FeatureAccess::userCanView('sample-feature'); // alias of 'read' permission

FeatureAccess::userCanUpdate('sample-feature');
FeatureAccess::userCanEdit('sample-feature'); // alias of 'update' permission

FeatureAccess::userCanDestroy('sample-feature');
FeatureAccess::userCanDelete('sample-feature'); // alias of 'destroy' permission

// it also works for Teams! (a la Jetstream)

FeatureAccess::teamCan(string $feature_name, string $permission);

FeatureAccess::teamCanCreate('sample-feature'); // and all the other aliases as per above

```

If you need to get the full array of current permission access, you can use this:

```php

FeatureAccess::userFeature('sample-feature'); // returns array
FeatureAccess::teamFeature('sample-feature'); // returns array

```

### Super-Admin Permission

If the current model *property* matches anything in the array, all permission tests will return true. They get to do *everything*.

```php
return [

    // grant all access to models which match...
    'super_admin_property' => 'email', // this property...
    'super_admin_access' => [ // to any of these.
        'admin@example.com',
    ],

]
```


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
