<?php

use yii\helpers\Html;

?>


<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
</header>

<div class="section">
    <div class="container">

        <?php if ($_REQUEST['id']!=2 && $_REQUEST['id']!=3) { ?>

        <div class="message-box">
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-head">
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Vielen Dank,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'der Bezahlvorgang wurde erfolgreich abgeschlossen!') ?></div>
                    </div>
                    <div class="text-center">
                        <?= Html::a('Zurück zum jugl.net', '#', ['class'=>'btn']) ?>
                    </div>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-head">
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Thank you very much,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'the payment has been processed successfully!') ?></div>
                    </div>
                    <div class="text-center">
                        <?= Html::a('Back to jugl.net', '#', ['class'=>'btn']) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>


        <?php if ($_REQUEST['id']==2) { ?>

        <div class="message-box">
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-head">
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Es tut uns leid,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'der Bezahlvorgang wurde nicht abgeschlossen.') ?></div>
                    </div>
                    <p class="text-center message-note-text"><?= Yii::t('app', 'Grund: Lorem ipsum dolor sit amet') ?></p>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-head">
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Sorry,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'the payment has not been processed.') ?></div>
                    </div>
                    <p class="text-center message-note-text"><?= Yii::t('app', 'Reason: Lorem ipsum dolor sit amet') ?></p>
                </div>
            </div>
        </div>

        <?php } ?>


        <?php if ($_REQUEST['id']==3) { ?>

        <div class="payment-data-box">

            <div class="row text-center">
                <div class="col-lg-12">

                    <span class="ico-title">Um den Vorgang abschließen zu können überweise den Betrag {sum} EUR an folgende Bankverbindung:</span><br/>
                    <br/>

                    <span class="ico-payment-data">
                        <b>Kontoinhaber:</b> JuglApp GmbH<br/>
                        <b>IBAN:</b> DE91 8607 0024 0199 9101 01<br/>
                        <b>BIC:</b> DEUTDEDBLEG<br/>
                    </span>

                    <br/>

                    Damit Deine Bezahlung richtig zugeordnet wird, bitte als Verwendungszweck <b>UNBEDINGT</b> folgenden Code angeben:<br/>
                    <br/>

                    <span class="ico-code"><b>{ext_code}</b></span>

                </div>
            </div>


        </div>


        <?php } ?>


    </div>
</div>
