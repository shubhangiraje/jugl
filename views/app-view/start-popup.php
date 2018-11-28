<div class="content" ng-controller="StartPopupCtrl as startPopupCtrl">
    <div scroll-pane scroll-config="{contentWidth: '0', autoReinitialise: true}" auto-height-popup id="start-popup-scroll" class="start-popup-box">
        <h2><?= Yii::t('app', 'Herzlich willkommen!') ?></h2>
        <p><?= Yii::t('app', 'Ergänze Dein Profil und lade danach Deine Freunde ein.<br>Du bist zukünftig an allen Umsätzen in Deinem Netzwerk beteiligt.') ?></p>
        <p><?= Yii::t('app', 'Wenn Du mal nicht weiter weißt, drücke einfach auf das jeweilige <span class="icon-info-popup"></span> - Symbol, dann geht ein Erklärungstext auf.') ?></p>
        <p style="color:red;line-height: 22px;"><?= Yii::t('app', '
		Die <span class="icon-info-popup"></span> - Symbole blinken so lange,<br>
				bis Du sie Dir angesehen hast.<br>
				So kannst Du Dir sicher sein,<br>
				alle Tipps gelesen zu haben!
		') ?></p>
        <p><?= Yii::t('app', 'Oder Du fragst denjenigen, der Dich zu Jugl.net eingeladen hat.<br>Sollte auch das nicht helfen, komme einfach zu uns ins Jugl-Forum, dort werden wir gerne all Deine Fragen beantworten.') ?></p>
        <p><?= Yii::t('app', 'Damit der, der Dich eingeladen hat, seine Punkte erhält, musst Du in der App aktiv werden, eine Mitgliedschaft auswählen und Dich per SMS verifizieren.') ?></p>
        <button class="btn btn-save" ng-click="startPopupCtrl.saveShowStartPopup()"><?= Yii::t('app', 'Jetzt kann\'s losgehen!') ?></button>
    </div>
</div>