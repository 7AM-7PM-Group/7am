<?php

return [
    'esb_username' => env('ESB_USERNAME'),
    'esb_password' => env('ESB_PASSWORD'),

    'retention' => [
        'default_days' => env('ESB_LOG_RETENTION_DAYS', 30),
        'types' => [
            'sync_success' => env('ESB_LOG_RETENTION_SUCCESS_DAYS', 30),
            'sync_failed' => env('ESB_LOG_RETENTION_FAILED_DAYS', 90),
        ],
        'audit_days' => env('ESB_LOG_DELETION_AUDIT_RETENTION_DAYS', 365),
    ],

    'env' => env('ESB_ENV', 'staging'),

    'core' => [
        'int' => env('ESB_CORE_INT'),
        'staging' => env('ESB_CORE_STG'),
        'production' => env('ESB_CORE_PROD'),
    ],

    'fnb' => [
        'int' => env('ESB_FNB_INT'),
        'staging' => env('ESB_FNB_STG'),
        'production' => env('ESB_FNB_PROD'),
    ],
];