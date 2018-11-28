<?php

return array(
    'default'=>array(
        // regex, on which domains deploy.php can show webinterface
        'allowWebInterfaceOnDomains'=>'.*\.loc22',
        'apiKeySeed'=>'zFHgmphHYDhWD5nfXFyq3YmP',
        // relative path to project root
        'rootDir'=>'../',

        // syncronization action config
        'sync'=>array(
            'default'=>array(
                'defaultTasks'=>array('default'),
                'ftpMaxParallelUploads'=>3,
                // change rights on file after upload
                // can be FALSE (no chmod), TRUE (as local file), '644' - set these rights
                'ftpChmodFile'=>false,
                // use ftp passive mode
                'ftpPassiveMode'=>true,
                'sshChmodNewDirs'=>0755,
                // rights for uploaded files
                'sshChmodFiles'=>0644,
                'doBackup'=>false,
                // path relative to project root
                'backupDir'=>'backups/',
                'ignoreFiles'=>
                    // main deploy config
                    '/deploy_config.php|'.
                    // deployer backup files
                    '/backups|'.
                    // vcs files
                    '/(.*/)?\.git|.*/\.(gitignore|svn)|'.
                    // ide files
                    '/\.(idea|settings|buildpath|project)|'.
                    // npm/grunt files
                    '/\.grunt|/node_modules|/package.json|/Gruntfile.js|'.
                    // os thumbs caching files
                    '.*/(Thumbs\.db|\.DS_Store)|'.
                    // ide backup files
                    '.*(~|\.bak)$|'.
                    // project specific file
                    '/aqbanking|'.
                    /*
                    '/vendor/smarty/smarty/(development|documentation)|'.
                    '/vendor/twbs/bootstrap/(docs|js/tests|less|test-infra|grunt)|'.
                    '/vendor/yiisoft/jquery-pjax/test|'.
                    '/vendor/yiisoft/yii2/messages/(?!(config.php|ru|de)).*|'.
                    '/vendor/yiisoft/yii2-debug|'.
                    '/vendor/yiisoft/yii2-gii|'.
                    '/vendor/swiftmailer/swiftmailer/(notes|doc|tests)|'.
                    '/vendor/cebe/markdown/tests|'.
                    '/vendor/tecnick.com/tcpdf/(examples|tools)|'.
                    '/vendor/tecnick.com/tcpdf/fonts/(?!(dejavusansb?i?\.)).*|'.
                    '/vendor/mpdf/mpdf/ttfonts/.* .*|'.
                    '/vendor/ezyang/htmlpurifier/(tests|smoketests|benchmarks|docs)|'.
*/
                    '/docs|/tests|/web/(.sass-cache|files|po|chat_files|thumbs|assets|app|node_modules|bower_components|bower.json|package.json|gulpfile.js)|/runtime'
                ,
            )
        ),

        // shell action config
        'shell'=>[
            'default'=>[
                'defaultTasks'=>['common'],
            ],

            // glob rules, relative to project root. May be string or array
            'common'=>[
                'commands'=>[
                    'cd web && gulp deploy'
                ]
            ],
        ],

        // clear action config
        'clear'=>[
            'default'=>[
                'defaultTasks'=>['all'],
            ],

            // glob rules, relative to project root. May be string or array
            'all'=>[
                'glob'=>[
                    'runtime/cache/*/*',
                    'runtime/cache/*'
                ],
            ],
        ],

    ),
/*
    'vro'=>array(
        'scriptUrl'=>'http://vro.jugl.net/deploy.php',
        // scriptDir relative to ftpRootDir
        'scriptDir'=>'web/',

        // actions to run when no actions specified in command line
        'defaultActions'=>array('shell','sync','clear') ,

        // syncronization action config
        'sync'=>array(
            'default'=>array(
                'transportClass'=>'FtpNonBlocking',
                'ftpHost'=>'ftp.loc',
                'ftpUsername'=>'deploy-jugl-vro',
                'ftpPassword'=>'deploy',
                // use '' for root or something like 'www/htdocs/'
                'ftpRootDir'=>'htdocs/',
                'backupDir'=>'backups/',
            )
        ),
    ),

*/
    'test'=>array(
        'scriptUrl'=>'http://test.jugl.net/deploy.php',
        // scriptDir relative to ftpRootDir
        'scriptDir'=>'web/',

        // actions to run when no actions specified in command line
        'defaultActions'=>array('shell','sync','clear') ,

        // syncronization action config
        'sync'=>array(
            'default'=>array(
                'transportClass'=>'FtpNonBlocking',
                'ftpHost'=>'ftp.loc',
                'ftpUsername'=>'deploy-jugl-test',
                'ftpPassword'=>'deploy',
                // use '' for root or something like 'www/htdocs/'
                'ftpRootDir'=>'htdocs/',
                'backupDir'=>'backups/',
            )
        ),
    ),
/*
    'prod'=>array(
        'scriptUrl'=>'http://jugl.net/deploy.php',
        // scriptDir relative to ftpRootDir
        'scriptDir'=>'web/',

        // actions to run when no actions specified in command line
        'defaultActions'=>array('shell','sync','clear') ,

        // syncronization action config
        'sync'=>array(
            'default'=>array(
                'transportClass'=>'FtpNonBlocking',
                'ftpHost'=>'ftp.loc',
                'ftpUsername'=>'deploy-jugl',
                'ftpPassword'=>'deploy',
                // use '' for root or something like 'www/htdocs/'
                'ftpRootDir'=>'htdocs/',
                'backupDir'=>'backups/',
            )
        ),
    )
*/
);
