<?php
return [
    'adminEmail' => 'admin@example.com',
    'semantic_url' => 'http://semantic.tvod.vn/api/film',
    'semantic_url2' => 'http://se.tvod.vn',
    'site_id' => 5,
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'sms_proxy' => [
        'url' => 'http://10.84.73.6:13013/cgi-bin/sendsms',
        'username' => 'tester',
        'password' => 'foobar',
        'debug' => false
    ],
    'access_private' => [
        'user_name' => 'msp_private',
        'password' => 'Msp!@123',
        'ip_privates' => [
            '192.0.0.0/8',
            '10.0.0.0/8',
            '10.84.0.0/16',
            '127.0.0.0/16',
        ],
    ],
    'tvod1Only' => false
];
