<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Testimonials Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the testimonials package.
    | You can customize various aspects of how testimonials are displayed
    | and managed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'default_limit' => 10,
    'default_order' => 'list_order',
    'auto_approve' => false,
    'require_moderation' => true,

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    */
    'display' => [
        'show_customer_name' => true,
        'show_links' => true,
        'show_dates' => true,
        'truncate_description' => 150,
        'allow_html' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour in seconds
        'key_prefix' => 'testimonials_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => 12,
        'max_per_page' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 5,
            'decay_minutes' => 60,
        ],
        'spam_protection' => [
            'enabled' => true,
            'honeypot_field' => 'website',
            'min_description_length' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'admin_email' => env('TESTIMONIALS_ADMIN_EMAIL'),
        'notify_admin' => true,
        'notify_customer' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => true,
        'rate_limit' => 60, // requests per minute
        'version' => 'v1',
    ],
];
