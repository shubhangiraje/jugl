<div id="friends-invitations-page">
    <div class="container">
        <div ng-click="showInfoPopup('view-invite')" ng-class="{'blink':isOneShowInfoPopup('view-invite')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Mach Deinen Freunden, Bekannten und Kollegen eine Freude...<br/>und Dir selbst auch!')?></h2>
            <?=Yii::t('app','Für jeden Freund, der sich durch Deine Einladung registriert, bekommst Du 100 Jugls (1€). Wenn Deine Freunde wiederum Freunde einladen, erhältst du jeweils 29% von deren Gewinn und wenn diese wiederum Freunde einladen, das Gleiche usw. usw. usw.')?>
        </div>

        <div class="friends-invitations-tabs">
            <div class="friends-invitations-tabs-link"><a ui-sref="friendsInvitation.invite" class="invite" ui-sref-active="active"><?=Yii::t('app','Freunde einladen')?></a></div>
            <div class="friends-invitations-tabs-link"><a ui-sref="friendsInvitation.invitations" class="invitations" ui-sref-active="active"><?=Yii::t('app','Überblick Einladungen')?></a></div>
            <div class="friends-invitations-tabs-link"><a ui-sref="friendsInvitation.regcodes" class="regcodes" ui-sref-active="active"><?=Yii::t('app','VIP-Codes kaufen')?></a></div>
        </div>
        <div ui-view class="friends-invitations-tabs-content">
        </div>
    </div>
</div>
