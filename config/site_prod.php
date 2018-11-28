<?php

define('SITE_ID',preg_replace('/\.php$/','',basename(__FILE__)));

$config=\yii\helpers\ArrayHelper::merge($config, [
    'components'=> [
      //  'db' => require('site_prod_db.php'),
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
                 //'class' => 'Swift_MailTransport'
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
    ],
    'timeZone' => 'Europe/Berlin',
    'params'=>[
        'useFacebookPixel'=>true,
        'fileProtectionCode'=>'ZG"-VPk$;!x?[%M]nN$55^k`T:p_{X>W',
        'fileIdProtectionCode'=>'*T&A)WA8ker"8pAzD]yX+/m!3r@py2`[',
        'chatFileProtectionCode'=>'R5Ydp!c3_4v%8B;-a|50+;!8.*K65~:U',
        'chatFileIdProtectionCode'=>'6+!_3%^8%|c+_+:%V^=+y3D~~3~~0+|V',
        'extApiAuthKey'=>'_xRse2h^HRvp2sGSNnvn?V8bcQRTkUhU',
        'chat'=>[
            'connect'=>'https://jugl.loc22'
        ],
        'PayOne'=>[
            'portalid'=>'2018843',
            'aid'=>'27045',
            'key'=>'41o7wvl91H361z9m',
            'mode'=>'test',
            'currency'=>'EUR',
            'url'=>'https://secure.pay1.de/frontend/'
        ],
        /*
        'Wirecard'=>[
            'customerId'=>'D200001',
            'shopId'=>'',
            'secret'=>'B8AKTPWBRMNBV455FG6M2DANE99WU2',
        ],
            */
        'Wirecard'=>[
            'customerId'=>'D296983',
            'shopId'=>'',
            'secret'=>'9VH4CDSBJ5YBM5J3VZ55XK6ZAVV4BDMJ84MKGDV2HUB4XU5SU9WRT2R4WK67',
        ],
    ],
]);


