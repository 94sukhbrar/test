<?php
/**
 *@copyright : Amusoftech Pvt. Ltd. < www.amusoftech.com >
 *@author	 : Ram Mohamad Singh < er.amudeep@gamil.com >
 */
if (php_sapi_name () != "cli") {
	echo 'Please run this file from command line interface !!!';
	exit ();
}
$params = require (__DIR__ . '/params.php');

$config = [ 
		'id' => 'Admin Project',
		'name' => 'Admin Project',
		'basePath' => dirname ( __DIR__ ),
		'bootstrap' => [ 
				'log' 
		],
		'vendorPath' => VENDOR_PATH,
		'timeZone' => date_default_timezone_get (),
		'controllerNamespace' => 'app\commands',
		
		'components' => [ 
				'urlManager' => [ 
						'class' => 'app\components\TUrlManager',
				      //'baseUrl' => (YII_ENV == 'dev') ? 'http://localhost/yii2-base-admin-panel-api' : '',
						'enablePrettyUrl' => true,
						'showScriptName' => false 
				],
				'cache' => [ 
						'class' => 'yii\caching\FileCache' 
				],
				'log' => [ 
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
				'mailer' => [ 
						'class' => 'app\components\TMailer',
						'useFileTransport' => YII_ENV == 'dev' ? true : false 
				] 
		
		],
		'params' => $params 
];

if (file_exists ( DB_CONFIG_FILE_PATH )) {
	$config ['components'] ['db'] = require (DB_CONFIG_FILE_PATH);
}

$config ['modules'] ['installer'] = [ 
		'class' => 'app\modules\installer\Module',
		'sqlfile' => [ 
				DB_BACKUP_FILE_PATH . '/install.sql' 
		],
		'exts' => [ 
				'imap' 
		] 
];
$config ['modules'] ['backup'] = [ 
		'class' => 'app\modules\backup\Module' 
];
return $config;
