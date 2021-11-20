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
        'create' => true,
        'read' => true,
        'update' => true,
        'destroy' => true,
        'limit' => 99
    ],

    'sample-feature' => [ // begin with the feature key name. This is used to request permissions.
        'name' => 'Test Feature', // Human readable name
        'create' => false, // can create new items
        'read' => true, // Can read/view items in this feature
        'update' => true, // can edit/change/update items
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

    'pages' => [
        'name' => 'Page Manager',
        'create' => false,
        'read' => true,
        'update' => false,
        'destroy' => false,
        'limit' => 0,
        'levels' => [
            'basic' => [
                'name' => 'Single Page',
                'create' => true,
                'update' => true,
                'destroy' => true,
                'limit' => 1,
            ],
            'pro' => [
                'name' => 'Three Pages',
                'create' => true,
                'update' => true,
                'destroy' => true,
                'limit' => 3,
            ]
        ]
    ],


];
