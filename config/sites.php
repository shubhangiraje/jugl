<?php

$selector=$_SERVER['HTTP_HOST'] ? 'domain:'.$_SERVER['HTTP_HOST']:'host:'.gethostname().realpath(__DIR__.'/../');

switch($selector) {
    case 'domain:jugl.loc22':
    case 'host:jugl.loc22/var/www/jugl':
    //case 'host:webserver1404/var/www/Jugl_Rep/www/htdocs':
    case 'host:webserver1404/var/www/Jugl_Rep/htdocs':
        define('YII_DEBUG', true);
        define('YII_ENV', 'dev');
        $siteConfigFile='site_dev.php';
        break;
    case 'domain:h2398871.stratoserver.net':
    case 'domain:jugl.net':
    //case 'domain:juglapp.de':
    //case 'domain:dev.jugl.net':
    case 'host:server/var/www/jugl/htdocs':
        define('YII_DEBUG',  $_SERVER['REMOTE_ADDR']=='178.172.245.24');
        define('YII_ENV', 'prod');
        $siteConfigFile='site_prod.php';
        break;
    case 'domain:test.jugl.net':
    case 'host:server/var/www/jugl_test/htdocs':
	case 'host:server/var/www/jugl_test_vro/htdocs':
        define('YII_DEBUG', true);
        define('YII_ENV', 'dev');
        $siteConfigFile='site_test.php';
        break;
}

if (!$siteConfigFile) die("selector '$selector' is unknown");

return $siteConfigFile;
