<div id="friends-funds-page">
    <div class="container">
        <div ng-click="showInfoPopup('view-funds-web')" ng-class="{'blink':isOneShowInfoPopup('view-funds-web')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Mein Guthaben')?></h2>
            <?=Yii::t('app','Hier siehst Du Deinen aktuellen Kontostand bei jugl.net und eine Übersicht aller getätigten Transaktionen. Ausserdem kannst Du hier Dein Konto mit Jugls aufladen sowie Dir die verdienten Jugls auszahlen lassen.')?>
        </div>

        <div class="friends-invitations-tabs">
            <div class="friends-invitations-tabs-link"><a ui-sref="funds.log" class="log" ui-sref-active="active"><?=Yii::t('app','Kontostand')?></a></div>
<?php /*
            <div class="friends-invitations-tabs-link"><a ui-sref="funds.log-token" class="log" ui-sref-active="active"><?=Yii::t('app','Tokenstand')?></a></div>
            <div class="friends-invitations-tabs-link"><a ui-sref="funds.token-deposit" class="log" ui-sref-active="active"><?=Yii::t('app','Tokens festgelegt')?></a></div>
*/ ?>
            <div class="friends-invitations-tabs-link"><a ui-sref="funds.payin" class="payin" ui-sref-active="active"><?=Yii::t('app','Jugls aufladen')?></a></div>
            <div class="friends-invitations-tabs-link"><a ui-sref="funds.payout" class="payout" ui-sref-active="active"><?=Yii::t('app','Jugls auszahlen')?></a></div>
        </div>
        <div ui-view class="friends-invitations-tabs-content">
        </div>
    </div>
</div>
