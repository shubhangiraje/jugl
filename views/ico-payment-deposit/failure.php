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
                        <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Es tut uns leid,') ?></div>
                        <div class="section-sub-title"><?= Yii::t('app', 'der Bezahlvorgang wurde nicht abgeschlossen.') ?></div>
                    </div>
                    <p class="text-center message-note-text"><?=\yii\helpers\Html::encode($model->details)?></p>
                    <div class="text-center">
                        <?= Html::a('Zurück', ['index'], ['class'=>'btn']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>