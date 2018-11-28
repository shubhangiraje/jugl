<div class="close" ng-click="modalService.hide()"></div>
<div class="content">

    <div class="offers-bet-box-popup">
        <p><?= Yii::t('app', 'Du bist mit <span class="offers-bet-price-popup">{{modalService.data.price|priceFormat}} €</span> <br/> nicht der Höchstbietende.') ?></p>
        <p><?= Yii::t('app', 'Möchtest du dein Gebot erhöhen?') ?></p>
        <p><b><?= Yii::t('app', 'Aktuelles Höchstgebot') ?>:</b>&nbsp;<span class="offers-bet-price-popup">{{modalService.data.best_price|priceFormat}} &euro;</span></p>

        <div class="offers-bet-box-btn-popup">
            <button class="btn btn-save" type="button" ng-click="modalService.data.cancel()"><?= Yii::t('app', 'Gebot erhöhen') ?></button>
            <button class="btn btn-submit" type="button" ng-click="modalService.data.ok()"><?= Yii::t('app', 'Gebot bestätigen {{modalService.data.price|priceFormat}} €');?></button>
        </div>

        <div class="offers-bet-note-popup"><?= Yii::t('app', 'Der Verkäufer hat die Möglichkeit, sich unabhängig von der Höhe des Gebots einen Käufer auszusuchen') ?></div>
    </div>

</div>