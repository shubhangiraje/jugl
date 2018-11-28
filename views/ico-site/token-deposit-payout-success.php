<?php

use yii\helpers\Html;

//die('here3');
?>


<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
</header>

<div class="section">
    <div class="container">

        <div class="message-box">
            <div class="row text-center">
                <div class="col-lg-12">
                    <?php // JUGLS PAYOUT CASE ?>
                    <?php if ($model->payout_balance_log_id) { ?>
                        <div class="section-head">
                            <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Auszahlung erfolgreich') ?></div>
                            <div class="section-sub-title"><?= Yii::t('app', 'Deine Anfrage bezgl. der Auszahlung des Tokenbetrags wurde erfolgreich verarbeitet') ?></div>
                        </div>
                        <div class="text-center">
                            <?= Html::a('Zurück', ['ico-site/dashboard'], ['class'=>'btn']) ?>
                        </div>
                    <?php } ?>

                    <?php // EUR PAYOUT CASE ?>
                    <?php if ($model->payout_pay_out_request_id) { ?>
                        <div class="section-head">
                            <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Auszahlungsanfrage eingegangen') ?></div>
                            <div class="section-sub-title"><?= Yii::t('app', 'Deine Anfrage bezgl. der Auszahlung des Tokenbetrags ist bei uns eingegangen und wird schnellstmöglich durch unser Team geprüft') ?></div>
                        </div>
                        <div class="text-center">
                            <?= Html::a('Zurück', ['ico-site/dashboard'], ['class'=>'btn']) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
</div>
