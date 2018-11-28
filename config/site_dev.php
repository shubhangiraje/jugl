<?php

define('SITE_ID',preg_replace('/\.php$/','',basename(__FILE__)));

$config=\yii\helpers\ArrayHelper::merge($config, [
	 'aliases' => [
		'@bower' => '@vendor/bower-asset',
	        '@npm'   => '@vendor/npm-asset',
  		],
    'components'=> [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=jugl_test',
            'username' => 'root',
	        'password' => 'root',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'useFileTransport' => false,
            'transport' => [
                // 'class' => 'Swift_MailTransport'
                'class' => 'Swift_SmtpTransport',
                'host' => 'jugl.net',
                'username' => 'contact@jugl.net',
                'password' => '23Evm4Y427',
                'port' => '587',
            ],
        ],
        'request' => [
            'cookieValidationKey' => '%N^})dWZu2q#^[R,;*g<rU_AACzD]R9X',
        ],
        'sms' => [
            'debug' => 1
        ]
    ],
    'timeZone' => 'Europe/Minsk',
    'params'=>[
        'fileProtectionCode'=>'ZG"-VPk$;!x?[%M]nN$55^k`T:p_{X>W',
        'fileIdProtectionCode'=>'*T&A)WA8ker"8pAzD]yX+/m!3r@py2`[',
        'chatFileProtectionCode'=>'kY=0i|:7gt3vOv:8^8PZ_=S!4*%lK6ob',
        'chatFileIdProtectionCode'=>'06G6eB6xI5M%0a08Y1694eU5055DB7jW',
        'extApiAuthKey'=>'V33fDY+aQgU@7UY=f?EWWUf*&-cA!PmH',
        'chat'=>[
            'connect'=>'http://localhost:3000'
        ],
        'PayOne'=>[
            'portalid'=>'2018843',
            'aid'=>'27045',
            'key'=>'41o7wvl91H361z9m',
            'mode'=>'test',
            'currency'=>'EUR',
            'url'=>'https://secure.pay1.de/frontend/'
        ],

        'Wirecard'=>[
            'customerId'=>'D200001',
            'shopId'=>'',
            'secret'=>'B8AKTPWBRMNBV455FG6M2DANE99WU2',
        ],
        'WirecardGiropay'=>[
            'customerId'=>'D296983',
            'shopId'=>'',
            'secret'=>'000000316E23E76F',
        ],

        'Wirecard'=>[
            'customerId'=>'D296983',
            'shopId'=>'',
            'secret'=>'9VH4CDSBJ5YBM5J3VZ55XK6ZAVV4BDMJ84MKGDV2HUB4XU5SU9WRT2R4WK67',
        ],
        'buyTokenSite'=>'http://jugl-ext.loc22',
    ],
]);


