<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="OfferDetailsViewBonusPopupCtrl as offerDetailsViewBonusPopupCtrl">

    <div class="modal-bonus-box">
        <p><?= Yii::t('app', 'Vielen Dank für das Anschauen unseres Angebotes.') ?></p>
        <p><?= Yii::t('app', 'Dein Interesse wird mit') ?></p>
        <div class="modal-bonus">
            <span class="modal-bonus-left"></span>
                {{::offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency>
            <span class="modal-bonus-right"></span>
        </div>
        <p><?= Yii::t('app', 'Werbebonus belohnt, wenn Du innerhalb von ') ?></p>
        <div class="modal-time">
            <div class="time-value">{{data.secondsLeft}}</div>
            <div class="time-title"><?= Yii::t('app', 'Sekunden') ?></div>
        </div>
        <p><?= Yii::t('app', 'diesen Bonus abholst.') ?></p>
    </div>

    <div class="btn-box-modal-bonus">
        <button type="button" class="btn btn-submit" ng-disabled="data.saving" ng-click="offerDetailsViewBonusPopupCtrl.accept()"><?= Yii::t('app', 'Belohnung abholen') ?></button>
    </div>

</div>
