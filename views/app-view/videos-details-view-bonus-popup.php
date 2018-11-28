<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="VideoDetailsViewBonusPopupCtrl as videoDetailsViewBonusPopupCtrl">
    <div class="modal-bonus-box">
        <p><?= Yii::t('app', 'Vielen Dank für das Anschauen des Videos.') ?></p>
        <p><?= Yii::t('app', 'Dafür erhältst Du von uns') ?></p>
        <div class="modal-bonus">
            <span class="modal-bonus-left"></span>
                {{::video.bonus|priceFormat}} <jugl-currency></jugl-currency>
            <span class="modal-bonus-right"></span>
        </div>
        <p><?= Yii::t('app', 'wenn Du innerhalb von') ?></p>
        <div class="modal-time">
            <div class="time-value">{{data.secondsLeft}}</div>
            <div class="time-title"><?= Yii::t('app', 'Sekunden') ?></div>
        </div>
        <p><?= Yii::t('app', 'diesen Bonus abholst.') ?></p>
    </div>
    <div class="btn-box-modal-bonus">
        <button type="button" class="btn btn-submit" ng-disabled="data.saving" ng-click="videoDetailsViewBonusPopupCtrl.accept()"><?= Yii::t('app', 'Belohnung abholen') ?></button>
    </div>
</div>

