<?php

/*
 * List of features in your app
 */
return [

    // array of email addresses for users who get permission to everything
    'super_admin_access' => [
        'admin@example.com',
    ],

    'super_admin_permission' => [
        'name' => 'Super-Admin Access',
        'read' => true,
        'edit' => true,
        'create' => true,
        'destroy' => true,
        'limit' => 99
    ],

    'sample-feature' => [ // begin with the feature key name. This is used to request permissions.
        'name' => 'Test Feature', // Human readable name
        'read' => true, // Can read/view items in this feature
        'edit' => true, // can edit/change/update items
        'create' => false, // can create new items
        'destroy' => false, // can destroy/delete items
        'limit' => 3, // limit on number of items allowed
        'levels' => [ // Override the base feature permissions with levels or packages (eg: basic, pro, plus)
            'pro' => [ // level/package key
                'name' => 'Extra!', // human readable name
                'create' => true, // this overrides base permission to create
                'limit' => 5, // limit is increased
                // other permissions will default to base feature definition (above)
            ],
        ]
    ],

    'pages' => [
        'name' => 'Page Manager',
        'read' => true,
        'edit' => false,
        'create' => false,
        'destroy' => false,
        'limit' => 0,
        'levels' => [
            'basic' => [
                'name' => 'Single Page',
                'edit' => true,
                'create' => true,
                'destroy' => true,
                'limit' => 1,
            ],
            'pro' => [
                'name' => 'Three Pages',
                'edit' => true,
                'create' => true,
                'destroy' => true,
                'limit' => 3,
            ]
        ]
    ],


];
