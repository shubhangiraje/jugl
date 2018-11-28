<?php

use Yii;

?>

<div id="all-functions">
    <div class="container clearfix">
        <h1><?=Yii::t('app','Alle Funktionen')?></h1>
        <div class="box">
            <h2><?=Yii::t('app','Funktionen des Portals im Überblick')?></h2>
            <div class="box-text clearfix">
                <p><?=Yii::t('app','Hier findest Du eine Liste mit allen Funktionen des Portals. Somit hast du immer den besten Überblick über alles, was Du hier tun kannst. Durchs Klicken auf die entsprechende Funktion wird diese aufgerufen.')?></p>

                <ul class="list-link-functions">
                    <li><a href="site/logout"><?= Yii::t('app', 'Abmelden') ?></a></li>
                    <li><a ui-sref="friendsInvitation.invitations"><?= Yii::t('app', 'Einladungshistorie') ?></a></li>
                    <li><a ui-sref="friendsInvitation.invite"><?= Yii::t('app', 'Freunde einladen') ?></a></li>
                    <li><a ui-sref="earn-money"><?= Yii::t('app', 'Geld verdienen') ?></a></li>
                    <li><a ui-sref="dealsCompleted"><?= Yii::t('app', 'Geschäfte und Bewertungen') ?></a></li>
                    <li><a ui-sref="help"><?= Yii::t('app', 'Hilfe') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.goViewParamFilter('dealsCompleted', 'offer|1')"><?= Yii::t('app', 'Ich habe abgemahnt') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.goViewParamFilter('dealsCompleted', 'offer_request|1')"><?= Yii::t('app', 'Ich wurde abgemahnt') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.accountDeletePopup()"><?= Yii::t('app', 'Jugl Account löschen') ?></a></li>
                    <li><a ui-sref="funds.payin"><?= Yii::t('app', 'Jugl-Punkte kaufen') ?></a></li>
                    <li><a ui-sref="funds.payout"><?= Yii::t('app', 'Jugl-Punkte verkaufen') ?></a></li>
                    <li><a ui-sref="friends"><?= Yii::t('app', 'Kontaktübersicht') ?></a></li>
                    <li><a ui-sref="friends"><?= Yii::t('app', 'Kontakt entfernen') ?></a></li>
                    <li><a ui-sref="userSearch"><?= Yii::t('app', 'Kontakt hinzufügen') ?></a></li>
                    <li><a ui-sref="funds.log"><?= Yii::t('app', 'Kontostand') ?></a></li>
                    <li><a ng-click="messenger.showChat(true)"><?= Yii::t('app', 'Kurznachricht schreiben') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.goViewParamFilter('offers.search', 'AUCTION')"><?= Yii::t('app', 'Laufende Auktionen') ?></a></li>
                    <li><a ui-sref="offers.myRequests"><?= Yii::t('app', 'Meine Gebote') ?></a></li>
                    <li><a ui-sref="funds.log"><?= Yii::t('app', 'Mein Jugl-Konto') ?></a></li>
                    <li><a ui-sref="network"><?= Yii::t('app', 'Mein netzwerk') ?></a></li>
                    <li><a ui-sref="profile"><?= Yii::t('app', 'Mein profil') ?></a></li>
                    <li><a ui-sref="activityList"><?= Yii::t('app', 'Meine Aktivitäten') ?></a></li>
					<!--<li><a ui-sref="videos.search"><?//= Yii::t('app', 'Videos anschauen') ?></a></li>-->
                </ul>

                <ul class="list-link-functions">
                    <li><a ui-sref="searches.myList"><?= Yii::t('app', 'Meine Anzeigen') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.goViewParamFilter('offers.myList', 'TYPE_AUCTION')"><?= Yii::t('app', 'Meine Angebote im Bieterverfahren') ?></a></li>
                    <li><a ng-click="allFunctionsCtrl.goViewParamFilter('offers.myList', 'TYPE_AUCTION')"><?= Yii::t('app', 'Meine Auktionen') ?></a></li>
                    <li><a ui-sref="interests.index"><?= Yii::t('app', 'Meine Interessen') ?></a></li>
                    <li><a ui-sref="dealsCompleted"><?= Yii::t('app', 'Meine Käufe') ?></a></li>
                    <li><a ui-sref="searches.add"><?= Yii::t('app', 'Menschliche Suchmaschine') ?></a></li>
                    <li><a ui-sref="favorites"><?= Yii::t('app', 'Merkzettel') ?></a></li>
                    <li><a ng-click="messenger.showChat(true)"><?= Yii::t('app', 'Messenger') ?></a></li>
                    <li><a ui-sref="userSearch"><?= Yii::t('app', 'Mitgliedersuche') ?></a></li>
                    <li><a ui-sref="friendsInvitation.invite"><?= Yii::t('app', 'Netzwerk erweitern') ?></a></li>
                    <li><a ui-sref="profile"><?= Yii::t('app', 'Persönliche Daten bearbeiten') ?></a></li>
                    <li><a ui-sref="profile"><?= Yii::t('app', 'Privatsphäreeinstellungen') ?></a></li>
                    <li><a ui-sref="profile"><?= Yii::t('app', 'Profilbild hochladen/ändern') ?></a></li>
                    <li><a ui-sref="searches.search"><?= Yii::t('app', 'Rechercheauftrag annehmen') ?></a></li>
                    <li><a ui-sref="searches.index"><?= Yii::t('app', 'Recherche beauftragen') ?></a></li>
                    <li><a ng-click="showInfoPopup('view-earn-money')"><?= Yii::t('app', 'So funktioniert Jugl') ?></a></li>
                    <li><a ui-sref="searches.add"><?= Yii::t('app', 'Suchanzeige aufgeben') ?></a></li>
                    <li><a ui-sref="searches.search"><?= Yii::t('app', 'Was suchen andere?') ?></a></li>
                    <li><a ui-sref="searches.myList"><?= Yii::t('app', 'Was wird mir angeboten?') ?></a></li>
                    <li><a ui-sref="offers.add"><?= Yii::t('app', 'Werbung schalten') ?></a></li>
                    <li><a ui-sref="offers.myRequests"><?= Yii::t('app', 'Worauf habe ich geboten?') ?></a></li>
                    <li><a ui-sref="searches.index"><?= Yii::t('app', 'Zielgruppe definieren') ?></a></li>
                </ul>


            </div>

            <div class="bottom-corner"></div>
        </div>
    </div>
</div>


