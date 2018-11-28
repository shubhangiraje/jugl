<div id="registration-payment">
    <div class="container">

        <div ng-if="!isUpgrade && $state.current.name.indexOf('data')<0">
            <div class="registration-stage">
                <h2><?= Yii::t('app', 'Mitgliedschaft wählen') ?> </h2>
            </div>

            <div class="welcome-text">
                <p><?= Yii::t('app', 'Herzlichen Glückwunsch, so einfach und schnell hast Du zum ersten Mal mit der JuglApp Geld verdient. Du hast jetzt die Möglichkeit, mit der Auswahl des PremiumPlus-Pakets (für <b>{{(registeredByCode ? 0:VIPPlusPrice)|priceFormat}}€</b> für 12 Monate) zukünftig 10% mehr Gewinn zu machen, Dein Netzwerk leichter aufzubauen und zu strukturieren und Du erhältst fünfmal so viel für Deine Likes.') ?></p>
                <h2><?= Yii::t('app', 'Wähle nun Deine Mitgliedschaft') ?></h2>

                <p><?= Yii::t('app', 'Wir würden uns sehr freuen, wenn Du Dich für eine PremiumPlus-Mitgliedschaft entscheidest. Mit einem Beitrag von nur<br><b>{{(registeredByCode ? 0:VIPPlusPrice)|priceFormat}}€</b> <br> investierst Du nicht nur in Dich selbst und erhältst dadurch höhere Gewinne, sondern unterstützt auch den reibungslosen Betrieb der Plattform und somit auch Dein eigenes wirtschaftliches Netzwerk. Deine <b>{{(registeredByCode ? 0:VIPPlusPrice)|priceFormat}}€</b> sind eine Investition für\'s Leben.') ?></p>
                <p><?= Yii::t('app', 'Ein späteres Upgrade zu diesem Preis ist nicht mehr möglich.') ?></p>
                <p><?= Yii::t('app', 'Faire Konditionen: Einmalige Zahlung für 12 Monate, läuft automatisch aus, kann jederzeit von Dir verlängert werden.') ?></p>
                <p><?=Yii::t('app', 'Vielen Dank!')?></p>

                <?php /* ?>
                <p><?=Yii::t('app','Herzlichen Glückwunsch, so einfach und schnell hast Du zum ersten Mal mit der Juglapp Geld verdient. Du hast jetzt die Möglichkeit, mit der Auswahl eines Premiumpakets (für einmalig nur') ?>
				<b>{{(registeredByCode ? 0:VIPPrice)|priceFormat}} &euro;!</b><?=Yii::t('app',') zukünftig 10 % mehr Gewinn zu machen.') ?></p>
                <p><?=Yii::t('app','Ein späteres Upgrade zur Premium-Mitgliedschaft ist zu diesem Preis nicht möglich.') ?></p>
                <p><?=Yii::t('app','Einmalige Zahlung, kein Abo!') ?></p>

                <h2><?= Yii::t('app', 'Wähle nun Deine Mitgliedschaft') ?> </h2>
				<p><?= Yii::t('app', 'Wir würden uns sehr freuen, wenn Du Dich für eine PREMIUM Mitgliedschaft entscheidest. Mit einem einmaligen Beitrag von') ?>
				<?= Yii::t('app', 'nur') ?> </p>
				<p><b>{{(registeredByCode ? 0:VIPPrice)|priceFormat}} &euro;</b></p>

				<p><?= Yii::t('app', 'investierst Du nicht nur in Dich selbst und erhältst dadurch höhere Gewinne, sondern unterstützt den reibungslosen Betrieb der Plattform und somit auch Dein eigenes wirtschaftliches Netzwerk. Deine ');?>
				<p><b>{{(registeredByCode ? 0:VIPPrice)|priceFormat}} &euro;</b></p>
				<p><?= Yii::t('app', ' sind eine Investition für`s Leben. Ein späteres Upgrade zu diesem preis ist nicht mehr möglich.') ?> </p>
			    <p><?=Yii::t('app', 'Vielen Dank!')?></p>
                <?php */ ?>
            </div>
        </div>

        <div ng-if="$state.current.name.indexOf('data')<0" class="registration-payment-box">
            <div class="payment-packet-box">

                <div ng-if="!isUpgrade || currentPacket!='VIP'" class="payment-packet premium">
                    <div class="payment-packet-item" ng-class="{'active': packet=='VIP'}">
                        <div class="payment-packet-top">
                            <h2><?=Yii::t('app', 'Premium');?></h2>
                            <?=Yii::t('app','Maximal gewinnbringend'); ?>
                        </div>

                        <div class="payment-packet-price">{{(registeredByCode ? 0:VIPPrice)|priceFormat}}&euro;<span ng-if="!registeredByCode"><?= Yii::t('app', 'einmalig'); ?></span></div>
                        <ul class="payment-packet-list">
                            <li ng-if="!registeredByCode"><?= Yii::t('app', 'F&uuml;r einmalig {{VIPPrice|priceFormat}} € sicherst Du Dir gro&szlige Vorteile.'); ?></li>
                            <li><?= Yii::t('app', '<span class="red">PREMIUM</span> Gewinnaufteilung: Du bekommst von jedem verdienten Jugl 70 %. 1 % gehen an JuglApp und 29 % gehen an den, der Dich in das Netzwerk eingeladen hat.'); ?></li>
                            <li>
                                <?= Yii::t('app', 'Als H&auml;ndler oder Privatperson hast Du mehr M&ouml;glichkeiten Deine Zielgruppe pr&auml;zise nach Interessen zu definieren.'); ?>
                                <br>
                                <?= Yii::t('app', 'Unbegrenzt Mitglieder werben für Level 1. (Freigabe nach Prüfung)') ?>
                                <br>
                                <?= Yii::t('app', 'Mitglieder, die direkt über jugl.net kommen kannst Du in Dein Netzwerk aufnehmen.') ?>
                            </li>

                            <li><?= Yii::t('app', '<span class="red">PREMIUM Mitglieder</span> sind die interessanteren Gesch&auml;ftspartner f&uuml;r Unternehmen und private Nutzer im Netzwerk.<br/><span class="red">10% MEHR GEWINN!</span>'); ?></li>
                        </ul>
                        <!-- REVERT_PREMIUM_MONTH
                        <div class="vip-list" ng-class="{'vip-list-no-line': !isUpgrade && !registeredByCode}">
                            <div ng-repeat="packet in VIPList" class="field-box-radio" bs-has-classes>
                                <input type="radio" i-check ng-model="VIP.packet" value="{{packet.value}}" />
                                <label>{{packet.title}}</label>
                            </div>
                        </div>
                        -->

                        <div class="payment-packet-bottom">
                            <button ng-click="registrationPaymentCtrl.selectVIP()" class="btn btn-save" type="button"><?= Yii::t('app', 'Mitgliedschaft wählen'); ?></button>
                        </div>
                    </div>

                </div>

                <div class="payment-packet premium-plus">
                    <div class="payment-packet-item" ng-class="{'active': packet=='VIP_PLUS'}">
                        <div class="payment-packet-top">
                            <h2><?=Yii::t('app', 'Premium Plus');?></h2>
                            <?=Yii::t('app','Maximal gewinnbringend'); ?>
                        </div>

                        <div class="payment-packet-price">{{VIPPlusPrice|priceFormat}}&euro;<br/><span style="font-size: 19px">für 12 Monate</span></div>
                        <ul class="payment-packet-list">
                            <li><?= Yii::t('app', 'F&uuml;r {{VIPPlusPrice|priceFormat}}€ im Jahr sicherst Du Dir enorme Vorteile.'); ?></li>
                            <li><?= Yii::t('app', '<span class="red">PREMIUMPLUS</span> Gewinnaufteilung: Du bekommst von jedem verdienten Jugl 70%. 1% geht an JuglApp und 29% gehen an den, der Dich in das Netzwerk eingeladen hat.'); ?></li>
                            <li>
                                <?= Yii::t('app', 'Du hast mehr Möglichkeiten Deine Zielgruppe präzise nach Interessen zu definieren.'); ?>
                                <br>
                                <?= Yii::t('app', 'Du kannst unbegrenzt viele Mitglieder für Level 1 werben. (Freigabe nach Prüfung)') ?>
                                <br>
                                <?= Yii::t('app', 'Du kannst Mitglieder in Dein Netzwerk aufnehmen, die direkt über jugl.net kommen (Netzwerk aufbauen).') ?>
                                <br>
                                <?= Yii::t('app', 'Du erhältst höhere Boni für das Einladen neuer Mitglieder per Link (bis zu 30€) und aus dem Netzwerkaufbau (bis zu 20€).') ?>
                                <br>
                                <?= Yii::t('app', 'Dein Profil wirkt seriöser auf andere Nutzer.') ?>
                                <br>
                                <?= Yii::t('app', 'Du kannst einen Abwerbestopp für Mitglieder aus dem Netzwerkaufbau festlegen (mit deren Zustimmung).') ?>
                                <br>
                                <?= Yii::t('app', 'Du kannst Nutzer aus Level 1 anderen Teamleadern Deines Netzwerks zur Betreuung zuordnen.') ?>
                                <br>
                                <?= Yii::t('app', 'Du hast die Möglichkeit unbegrenzt private und gewerbliche Anzeigen zu schalten (Ausnahmen: Online Geld verdienen, konkurrierende Unternehmen)') ?>
                                <br>
                                <?= Yii::t('app', 'Du erhältst fünfmal so viel Geld für Deine Likes.') ?>
                            </li>

                            <li><?= Yii::t('app', '<span class="red">PREMIUMPLUS Mitglieder</span> sind die interessantesten Geschäftspartner für Unternehmen und private Nutzer im Netzwerk.'); ?></li>
                            <li><?= Yii::t('app', 'Die <span class="red">PREMIUMPLUS-Mitgliedschaft</span> läuft automatisch nach 12 Monaten aus und kann von Dir dann jederzeit verlängert werden.<br/>Kein Abo!'); ?></li>
                        </ul>
                        <!-- REVERT_PREMIUM_MONTH
                        <div class="vip-list" ng-class="{'vip-list-no-line': !isUpgrade && !registeredByCode}">
                            <div ng-repeat="packet in VIPList" class="field-box-radio" bs-has-classes>
                                <input type="radio" i-check ng-model="VIP.packet" value="{{packet.value}}" />
                                <label>{{packet.title}}</label>
                            </div>
                        </div>
                        -->

                        <div class="payment-packet-bottom">
                            <button ng-click="registrationPaymentCtrl.selectVIPPlus()" class="btn btn-gold" type="button"><?= Yii::t('app', 'Mitgliedschaft wählen'); ?></button>
                        </div>
                    </div>
                </div>

                <div ng-if="!isUpgrade && !registeredByCode" class="payment-packet standard">
                    <div class="payment-packet-item">
                        <div class="payment-packet-top">
                            <h2><?=Yii::t('app', 'Standard');?></h2>
                        </div>
                        <div class="payment-packet-body">
                            <div class="payment-packet-price">0&euro;</div>
                            <ul class="payment-packet-list">
                                <li><?= Yii::t('app', 'Du bekommst von jedem verdienten Jugl 60 %. 11 % gehen an JuglApp und 29 % gehen an den, der Dich in das Netzwerk eingeladen hat.'); ?></li>
                                <li><?= Yii::t('app', 'Unser Tipp: Für nur einmalig 3 € kannst du alle Vorteile von JuglApp uneingeschr&auml;nkt nutzen und Du unterst&uuml;tzt unsere Gemeinschaft und somit Dein eigenes wirtschaftliches Netzwerk. '); ?></li>
                                <li><?= Yii::t('app', 'ACHTUNG: Ein späteres Upgrade zu diesem Preis ist nicht mehr m&ouml;glich!'); ?></li>
                            </ul>
                        </div>
                        <div class="payment-packet-bottom">
                            <button ng-click="registrationPaymentCtrl.saveSTD()" class="btn btn-submit" type="button"><?= Yii::t('app', 'Mitgliedschaft wählen'); ?></button>
                        </div>
                    </div>
                </div>

            </div>

            <div ng-if="packet=='VIP' || packet=='VIP_PLUS'" class="method-payment-box" id="paymentMethod">
                <h2><?= Yii::t('app', 'W&auml;hle die gewünschte Zahlungsart:'); ?></h2>
                <ul class="method-payment-list">
                    <li><input type="radio" value="PAYONE_GIROPAY" i-check ng-model="VIP.payment_method" /><label><?= Yii::t('app', 'Giropay'); ?></label></li>
<!--
                    <li><input type="radio" value="PAYONE_PAYPAL" i-check ng-model="VIP.payment_method" /><label><?= Yii::t('app', 'Paypal'); ?></label></li>
-->
                    <li><input type="radio" value="PAYONE_CC" i-check ng-model="VIP.payment_method" /><label><?= Yii::t('app', 'Kreditkarte (Visa, Mastercard, American Express)'); ?></label></li>
                    <li><input type="radio" value="PAYONE_SOFORT" i-check ng-model="VIP.payment_method" /><label><?= Yii::t('app', 'Sofortüberweisung'); ?></label></li>
                    <li><input type="radio" value="ELV" i-check ng-model="VIP.payment_method" /><label><?= Yii::t('app', 'Banküberweisung'); ?></label></li>
                </ul>
                <p><?= Yii::t('app', 'Die Belastung Ihres Kreditkartenkontos erfolg i.d.R. 2-3 Werktage nach Abschluss der Bestellung. Ihre Kreditkartendaten k&ouml;nnen Sie auf n&auml;chsten Siete eingeben. Bitte weiter klicken.'); ?></p>
                <div class="method-payment-img">
                    <img src="/static/images/account/visa.png" alt="visa">
                    <img src="/static/images/account/mastercard.png" alt="mastercard">
                </div>
                <div class="method-payment-btn">
                    <button class="btn btn-submit" ng-disabled="VIP.saving" ng-click="registrationPaymentCtrl.saveVIP()"><?= Yii::t('app', 'Jetzt bezahlen'); ?></button>
                </div>
            </div>

            <div class="bottom-corner"></div>
        </div>

        <div class="registration-stage">
            <div ui-view></div>
        </div>

    </div>
</div>


