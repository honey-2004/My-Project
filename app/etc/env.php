<?php
return [
    'cache_types' => [
        'compiled_config' => 1,
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1
    ],
    'backend' => [
        'frontName' => 'admin_qebn78'
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'NmtXkQrj3UeUxCU9uLg4af3LutyjTFX0'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => 'a3c_'
            ],
            'page_cache' => [
                'id_prefix' => 'a3c_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => '886587ccaecec7ed53ffb9c4e69de4d2'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'magento246',
                'username' => 'pmauser',
                'password' => 'iovista@123',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'default',
    'session' => [
        'save' => 'files'
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'downloadable_domains' => [
        'local.magento246.com'
    ],
    'install' => [
        'date' => 'Thu, 01 Jan 2026 11:23:56 +0000'
    ]
];
