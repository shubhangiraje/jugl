<div id="earn-money">
    <div class="container clearfix">
        <div ng-click="showInfoPopup('view-earn-money')" ng-class="{'blink':isOneShowInfoPopup('view-earn-money')}" class="info-popup-btn"></div>

        <h2><?=Yii::t('app','Wie kann ich mit der Jugl APP Geld verdienen?')?></h2>

        <div class="earn-money-content">
            <div class="earn-money-box">
                <p><?= Yii::t('app', '1. Ich lade meine Freunde ein und baue mir damit mein Netzwerk auf.') ?></p>
                <a ui-sref="friendsInvitation.invite" class="earn-money-link btn btn-submit"><?= Yii::t('app', 'Freunde einladen / Netzwerk aufbauen') ?></a>
            </div>

            <div class="earn-money-box">
                <p><?= Yii::t('app', '2. Ich gebe meine Interessen an (von der Zahnbürste bis zum Luxus Auto) und bleibe dabei anonym und bekomme vorwiegend von Händlern aber auch von Privatpersonen, auf meinen Interessen basierende Produktangebote (Werbung). Für jede gelesene Werbung verdienst Du Jugls') ?> (<span class="jugl-icon-light"></span>).</p>
                <a ui-sref="offers.index" class="earn-money-link btn btn-submit"><?= Yii::t('app', 'Kaufen / verkaufen, Interessen angeben') ?></a>
            </div>

            <div class="earn-money-box">
                <p><?= Yii::t('app', '3. Vermittlung, Recherche, Assistenz. Egal was Du oder andere suchen. Du bekommst dafür Vermittlungsprovision oder profitierst von den Angeboten.') ?></p>
                <a ui-sref="searches.index" class="earn-money-link btn btn-submit"><?= Yii::t('app', 'Suchauftrag erstellen / recherchieren / vermitteln') ?></a>
            </div>

            <div class="earn-money-box">
                <p><?= Yii::t('app', '4. ExtraCash - der Name ist Programm. Sammle noch mehr Jugls bei unseren Partnern und verschaffe Dir so einen waschechten Booster für Dein Konto.') ?></p>
                <a href="https://offerwall.annecy.media/?country=<?= strtoupper(Yii::$app->user->identity->countryShortName) ?>&platform=desktop&token=acda9842-3f24-4ea6-aa19-592a974877ba&user_id=<?= Yii::$app->user->id ?>" class="earn-money-link btn btn-submit" target="_blank"><?= Yii::t('app', 'Weitere Details') ?></a>
            </div>

        </div>

    </div>
</div>
