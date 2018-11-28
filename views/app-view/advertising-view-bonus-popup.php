<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="AdvertisingViewBonusPopupCtrl as advertisingViewBonusPopupCtrl">

    <div class="modal-bonus-box">
        
        <p><?= Yii::t('app', 'Dieses Popup schließt sich in') ?></p>
        <div class="modal-time">
            <div class="time-value">{{data.secondsLeft}}</div>
            <div class="time-title"><?= Yii::t('app', 'Sekunden') ?></div>
        </div>
        <p><?= Yii::t('app', 'von selbst und du erhältst automatisch deine Belohnung wenn du dir für diese Zeit die Bannerwerbung ansiehst. Nicht hinter jedem Banner ist eine Belohnung versteckt.') ?></p>
    </div>
</div>
