<?php
/**
 *@copyright : Amusoftech Pvt. Ltd. < www.amusoftech.com >
 *@author	 : Ram Mohamad Singh < er.amudeep@gamil.com >
 */
$params = require (__DIR__ . '/params.php');

$config = [
    'id' => 'Admin Project',
    'name' => 'Admin Project',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'app\components\TBootstrap'
    ],
    'vendorPath' => VENDOR_PATH,
    'timeZone' => date_default_timezone_get(),
    'components' => [
        'request' => [
            'enableCsrfValidation' => defined('YII_TEST') ? false : true,
            'cookieValidationKey' => '__DUMMY__',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'settings' => [
            'class' => 'app\components\Settings'
        
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'user' => [
            'class' => 'app\components\WebUser'
        
        ],
        'mailer' => [
            'class' => 'app\components\TMailer',
            'useFileTransport' => YII_ENV == 'dev' ? true : false
        ],
        'log' => [
            'traceLevel' => defined('YII_DEBUG') ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ]
                ]
            ]
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
            'defaultTimeZone' => date_default_timezone_get(),
            'datetimeFormat' => 'php:Y-m-d h:i:s A',
            'dateFormat' => 'php:Y-m-d'
        ],
        'urlManager' => [
            'class' => 'app\components\TUrlManager',
            'rules' => [
                [
                    'pattern' => 'sitemap',
                    'route' => 'sitemap/default/index',
                    'suffix' => '.xml'
                ],
                [
                    'pattern' => 'contactus',
                    'route' => 'site/contact'
                ],
                'file/files/<file>' => 'file/files',
                '<action:policy|terms>' => 'site/<action>',
                '<controller:[A-Za-z-]+>/<id:\d+>/<title>' => '<controller>/view',
                '<controller:[A-Za-z-]+>/<id:\d+>' => '<controller>/view',
                '<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>/<title>' => '<controller>/<action>',
                '<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>' => '<controller>/<action>'
            ]
        ],
        'view' => [
            'theme' => [
                'class' => 'app\components\AppTheme',
                'name' => 'base', // 'admin_pro' 'new'
                'style' => 'success' // ['info',danger,warning,success]
            ]
        ]
    ],
    'params' => $params,
    'modules' => [
        'sitemap' => [
            'class' => 'app\modules\sitemap\Module',
            'models' => [
                // your models
                'app\models\Page'
            ],
            'urls' => [
                [
                    'loc' => '/',
                    'priority' => '1.0'
                ],
                [
                    'loc' => 'contactus'
                ]
            ],
            'enableGzip' => true
        ]
    ]
];

if (file_exists(DB_CONFIG_FILE_PATH)) {
    $config['components']['db'] = require (DB_CONFIG_FILE_PATH);
} else {
    $config['modules']['installer'] = [
        'class' => 'app\modules\installer\Module',
        'sqlfile' => [
            DB_BACKUP_FILE_PATH . '/install.sql'
        ],
        'exts' => [
            'imap'
        ]
    
    ];
    // define('MODE_INSTALL', true);
}
if (YII_ENV == 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';
    
    $config['modules']['tugii'] = [
        'class' => 'app\modules\tugii\Module'
    ];
    $config['modules']['installer'] = [
        'class' => 'app\modules\installer\Module'
    ];
    $config['modules']['backup'] = [
        'class' => 'app\modules\backup\Module'
    ];
    $config['components']['errorHandler'] = [
        'errorAction' => 'log/custom-error'
    ];
    
} else {
    $config['components']['errorHandler'] = [
        'errorAction' => 'log/custom-error'
    ];
}
return $config;