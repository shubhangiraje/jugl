<div class="content" ng-controller="FriendsInvitationPopupCtrl as friendsInvitationPopupCtrl">
    <div class="friends-invite-popup-box">
        <h2><?= Yii::t('app','Jetzt kommt der spannende Teil!') ?></h2>
        <p><?= Yii::t('app','Damit unsere Gemeinschaft groß und erfolgreich wird, lade möglichst viele Freunde und Bekannte ein, egal ob 5, 50 oder 500. Gemeinsam erreichen wir unser Ziel, passiv und mit viel Spaß Geld zu verdienen.') ?></p>
        <p><?= Yii::t('app','Du baust Dir jetzt Dein Netzwerk auf und bist zukünftig an allen Umsätzen und Gewinnen beteiligt. Je größer und stärker Dein Netzwerk ist, desto mehr Gewinn und Spaß wirst Du haben.') ?></p>
        <p><?= Yii::t('app','Lade Deine Freunde ein, bevor es ein anderer macht!') ?></p>

        <div class="friends-invite-popup-btn-box">
            <button class="btn btn-save" ui-sref="friendsInvitation.invite" ng-click="friendsInvitationPopupCtrl.saveFriendsInvitationPopup()"><?= Yii::t('app', 'Jetzt Freunde & Bekannte einladen') ?></button>
            <button class="btn btn-submit" ng-click="friendsInvitationPopupCtrl.saveFriendsInvitationPopup()"><?= Yii::t('app', 'Später erinnern') ?></button>
        </div>
    </div>
</div>