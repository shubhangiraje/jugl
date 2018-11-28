<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');


unset($config['components']['request']);
unset($config['components']['view']);
unset($config['components']['admin']);
unset($config['components']['user']);
unset($config['components']['assetManager']);
unset($config['components']['errorHandler']);




