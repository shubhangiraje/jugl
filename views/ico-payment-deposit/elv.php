<?php

use yii\helpers\Html;

?>

<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
</header>

<div class="section">
    <div class="container">

        <div class="payment-data-box">

            <div class="row text-center">
                <div class="col-lg-12">

                    <?=$data['message']?>
                    <br/><br/>
                    <div class="text-center">
                        <?php
                        switch (Yii::$app->request->getHostName()) {
                            case 'test.juglcoin.com':
                                $url='http://test.jugl.net';
                                break;
                            case 'juglcoin.com':
                                $url='https://jugl.net';
                                break;
                            case 'jugl-ext.loc22':
                                $url='http://jugl.loc22';
                                break;
                        };
                        $url.='/site/login?PHPSESSID='.session_id();
                        ?>
                        <?= Html::a('Zurück zum jugl.net', $url, ['class'=>'btn']) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
