<?php

return [
    'esb_username' => env('ESB_USERNAME'),
    'esb_password' => env('ESB_PASSWORD'),
    'esb_base_url' => env('ESB_BASE_URL', ""),
    'esb_environment' => env('ESB_ENVIRONMENT', 'sandbox'),

    'retention' => [
        'default_days' => env('ESB_LOG_RETENTION_DAYS', 30),
        'types' => [
            'sync_success' => env('ESB_LOG_RETENTION_SUCCESS_DAYS', 30),
            'sync_failed' => env('ESB_LOG_RETENTION_FAILED_DAYS', 90),
        ],
        'audit_days' => env('ESB_LOG_DELETION_AUDIT_RETENTION_DAYS', 365),
    ],
];
