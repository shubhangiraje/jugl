<?php

$config = [
    'id' => 'Jugl',
    'basePath' => dirname(__DIR__),
    'language' => 'de',
    'bootstrap' => ['log'],
    'timeZone' => 'Europe/Berlin',
    'modules' => [
		'social' => [
		'class' => 'kartik\social\Module',
			'facebook' => [
				'appId' => '118475612161878',
				'secret' => 'bdc687178eecae44e90f1a9df24d2d1a',
			]
		],
        'gridview'=> [
            'class'=>'\kartik\grid\Module',
        ],
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'dd.MM.yyyy',
                \kartik\datecontrol\Module::FORMAT_TIME => 'HH:mm:ss a',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'dd.MM.yyyy HH:mm:ss',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
                \kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
            //'displayTimezone' => 'Europe/Berlin',

            // set your timezone for date saved to db
            //'saveTimezone' => 'Europe/Berlin',

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // use ajax conversion for processing dates from display format to save format.
            //'ajaxConversion' => true,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => ['type'=>2, 'pluginOptions'=>['autoclose'=>true]], // example
                \kartik\datecontrol\Module::FORMAT_DATETIME => [], // setup if needed
                \kartik\datecontrol\Module::FORMAT_TIME => [], // setup if needed
            ],

            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            /*
            'widgetSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => [
                    'class' => 'yii\jui\DatePicker', // example
                    'options' => [
                        'dateFormat' => 'php:d-M-Y',
                        'options' => ['class'=>'form-control'],
                    ]
                ]
            ]
            */
            // other settings
        ]
    ],
    'components' => [
        'formatter' => [
            'dateFormat'=>'php:d.m.Y',
			'defaultTimeZone'=>'Europe/Berlin'								  
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'view' => [
            'class' => 'app\components\View',
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                ],
            ],
        ],
        'assetManager' => [
            'linkAssets' => true
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Admin',
            'loginUrl' => ['admin-site/login'],
            'enableAutoLogin' => false,
            'authTimeout' => 10*60,
            'identityCookie' => ['name' => '_identity_admin', 'httpOnly' => true],
            'idParam' => '__id_admin',
            'authTimeoutParam' => '__expire_admin',
            'absoluteAuthTimeoutParam' => '__absoluteExpire_admin',
            'returnUrlParam' => '__returnUrl_admin'
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'loginUrl' => 'site/login',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
           'class' => 'app\components\Mailer',
           'messageClass'=>'app\components\MailMessage',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 5 : 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:401',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:400',
                    ],
                    'maxFileSize'=>10240,
                    'maxLogFiles'=>10
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['payment'],
                    'logFile' => '@runtime/logs/payment.log',
                    'maxFileSize'=>10240,
                    'maxLogFiles'=>10
                ],
                [
                    'class' => '\app\components\SimpleFileTarget',
                    'categories' => ['viewbonus'],
                    'logFile' => '@runtime/logs/viewbonus.log',
                    'maxFileSize'=>10240,
                    'maxLogFiles'=>10
                ],
                [
                    'class' => '\app\components\SimpleFileTarget',
                    'categories' => ['annecy'],
                    'logFile' => '@runtime/logs/annecy.log',
                    'maxFileSize'=>10240,
                    'maxLogFiles'=>10
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['sms'],
                    'logFile' => '@runtime/logs/sms.log',
                    'maxFileSize'=>10240,
                    'maxLogFiles'=>10
                ],
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['info'],
					'categories' => ['tradetracker'],
					'logFile' => '@app/runtime/logs/advertising/tradetracker.log',
					'maxFileSize' => 10240,
					'maxLogFiles' => 10,
				],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\GettextMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'useMoFile'=>false
                ],
				'kvsocial' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@vendor/kartik-v/yii2-social/messages',
					'sourceLanguage' => 'en'
				],

            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
			
            //'suffix'=> '/',
            'rules' => [
                '/<view:(agbs|datenschutz|impressum|index|nutzungsbedingungen|ueber-uns|wie-funktioniert|help)>' => 'site/view',
                '/'=>'site/index',
                '/my'=>'site/my',
                '/admin'=>'admin-site/index',
                '/become-member'=>'site/become-member',
                '/redirect-to-juglcoin'=>'site/redirect-to-juglcoin',
                '/login'=>'site/login',
                '/dummy'=>'dummy/index',
                '/ico-payment'=>'ico-payment/index',
                '/ico-payment-deposit'=>'ico-payment-deposit/index',

                '/app-view/all'=>'app-view/all',
                '/app-view/<view:[-a-z0-9_.]*>'=>'app-view/view',
                '/thumbs/<url:.*>' => 'site/generate-thumbnail',

                // map url to module/controller/action
                '/<module:[-a-zA-Z]+>/<controller:[-a-zA-Z0-9]+>/<action:[-a-zA-Z0-9]+>'=>'<module>/<controller>/<action>',
                '/<controller:[-a-zA-Z0-9]+>/<action:[-a-zA-Z0-9]+>'=>'<controller>/<action>',

                '/<module:[-a-zA-Z]+>'=>'<module>',
            ],
        ],
        'db' => [
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 60
        ],
        'sms' => [
            'class'=>'\app\components\SmsGate',
            //'key'=>'test_SGDGKxNQAEWwDWaJIe5XKRfD5',
            'key'=>'live_7jwH7LAyiTfmCyzlpVqC8VHuq',
            'from' => 'Jugl',
        ]

    ],
    'params' => [
        'emailFrom' => 'contact@jugl.net',
        'thumbsAlias' => '@webroot/thumbs',
        'thumbsUrl' => '/thumbs',
        'fileUrl' => '/files',
        'fileAlias' => '@webroot/files',
        'chatFileUrl' => '/chat_files',
        'chatFileAlias' => '@webroot/chat_files',
        'SystemAvatarFileId'=>1,
        'DeleteAvatarFileId'=>5,
        'paymentIDSecret'=>'xhDxSygspeXcapgyQcPXBwFNNsaJPBzFY26wtk6fhpwmsWD4',
        'emailValidationSecret'=>'c9aQv4N7WNtesJF5XwZuKPKbNhpuZUXU66ngjfDbeTb3E8HE',
        'thumbPrefixes' => [
            'files'=>[
                'urlPrefix'=>'/files/'
            ],
            'chatfiles'=>[
                'urlPrefix'=>'/chat_files/'
            ],
            'stimg'=>[
                'urlPrefix'=>'/static/images/'
            ],
            'all'=>[
                'urlPrefix'=>'/'
            ],
        ],
		'HBCISignKey'=>'3yctDg2as8FHEWbmpgvr8qxH',										  
        'thumbTypes' => [
            'adminImagePreview' => [
                'width' => 150,
                'height' => 150,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '90',
                'bgColor' => ['r'=>255,'g'=>255,'b'=>255,'a'=>0],
            ],
            'avatarSmall' => [
                'width' => 40,
                'height' => 40,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'avatar' => [
                'width' => 100,
                'height' => 100,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'imageBig' => [
                'width' => 480*2,
                'height' => 360*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndFill',
                'qualityJPG' => '85',
                'bgColor' => ['r'=>255,'g'=>255,'b'=>255,'a'=>0],
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'avatarBig' => [
                'width' => 298,
                'height' => 298,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'avatarMobile' => [
                'width' => 130,
                'height' => 130,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'avatarMobileProfile' => [
                'width' => 144*2,
                'height' => 144*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'validationSmall' => [
                'width' => 230,
                'height' => 215,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'chat' => [
                'width' => 400,
                'height' => 400,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'interest' => [
                'width' => 120*2,
                'height' => 120*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'interestMobile' => [
                'width' => 130,
                'height' => 130,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'interestSmall' => [
                'width' => 40*2,
                'height' => 40*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'searchRequest' => [
                'width' => 480*2,
                'height' => 400*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'searchRequestBlur' => [
                'width' => 480*2,
                'height' => 400*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                'filters' => array(
                    array('blur',50,30),
                ),
            ],
            'searchRequestMobile' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'searchRequestMobileBlur' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH+100000, -15)),
            ],
            'offer' => [
                'width' => 480*2,
                'height' => 400*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'offerMobile' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'fancybox' => [
                'width' => 1500,
                'height' => 1000,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'news' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'trollboxSmall' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'trollboxMedium' => [
                'width' => 480*2,
                'height' => 480*4,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'trollboxBig' => [
                'width' => 1920,
                'height' => 1080,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'photoSmall' => [
                'width' => 230,
                'height' => 215,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '90',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'infoCommentSmall' => [
                'width' => 90*2,
                'height' => 74*2,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'infoCommentMedium' => [
                'width' => 480*2,
                'height' => 480*4,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'infoCommentBig' => [
                'width' => 1920,
                'height' => 1080,
                'outputFormat' => 'jpg',
                'resizeMode' => 'max',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
            'tokenDepositGuarantee' => [
                'width' => 212,
                'height' => 212,
                'outputFormat' => 'jpg',
                'resizeMode' => 'resizeAndCrop',
                'qualityJPG' => '80',
                //'filters' => array(array(IMG_FILTER_SMOOTH, -15)),
            ],
        ],
        'chat'=>[
            'authorizationSecret'=>'WXeHj7YqgMh7t28aQ9smEGFZZkk4FSt6s5nQxUrm5XaMgvcj',
            'rpcUrl'=>'http://127.0.0.1:8080'
        ],
        'buyTokenSite'=>'http://juglcoin.com',
        'buyTokenUrl'=>'/ico-payment',
        'buyTokenDepositUrl'=>'/ico-payment-deposit',
        'languages'=>['de','en','ru']
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'=>'yii\debug\Module',
        'allowedIPs'=>['47.64.*.*','127.*.*.*','10.*.*.*','::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'allowedIPs'=>['47.64.*.*','127.*.*.*','10.*.*.*','::1'],

        'generators'=>[
            'modelPavimus'=>[
                'class'=>'\app\components\generators\model\Generator',
            ]
        ]

    ];
}


return $config;
