<div class="content" ng-controller="AppDownloadPopupCtrl as appDownloadPopupCtrl">
    <div ng-if="appDownloadData.isDownloadInfoPopup" class="close" ng-click="modalService.hide()"></div>

    <div class="app-download-popup-box">

        <div class="app-download-popup-title">
            <span><?= Yii::t('app', 'App runterladen') ?></span>
        </div>

        <p><?= Yii::t('app', 'Lade Dir jetzt die Jugl-App herunter und installiere sie auf Deinem Handy. Melde Dich mit Deiner Email-Adresse und Deinem Passwort an.') ?></p>

        <ul class="app-download-list">
            <li ng-if="device.os=='ios' || device.os!='android'">
                <div class="app-download-img">
                    <a href="https://itunes.apple.com/app/id978284701">
                        <img ng-src="/static/images/site/app_store.png" alt="app-store"/>
                    </a>
                </div>
            </li>
            <li ng-if="device.os=='android' || device.os!='ios'">
                <div class="app-download-img">
                    <a href="https://play.google.com/store/apps/details?id=com.kreado.jugl2&hl=de">
                        <img ng-src="/static/images/site/google_play.png" alt="google-play"/>
                    </a>
                </div>
            </li>
        </ul>

        <div ng-if="!appDownloadData.isDownloadInfoPopup" class="link-web-box">
            <a href="" ng-click="appDownloadPopupCtrl.saveDesktop()"><?= Yii::t('app', 'ODER WEITER MIT DER WEB-VERSION') ?></a>
        </div>
    </div>
</div>