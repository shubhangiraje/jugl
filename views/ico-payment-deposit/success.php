<?php

use yii\helpers\Html;

?>


<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
</header>

<div class="section">
    <div class="container">

        <div class="message-box">
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-head">
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Vielen Dank,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'der Bezahlvorgang wurde erfolgreich abgeschlossen!') ?></div>
                    </div>

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
