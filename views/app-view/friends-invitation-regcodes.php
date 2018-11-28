<div class="invatiation-regcodes-text">
    <p><?=Yii::t('app','Hier hast Du die Möglichkeit, VIP-Codes für kostenfreie PREMIUM-Registrierungen zu erwerben und im Form eines Gutscheins Deinen Freunden, Bekannten und Kollegen zu schenken. Somit werden Deine Freunde in der Lage sein, sich bei jugl.net kostenfrei als PREMIUM-Mitglieder zu registrieren. Auch besonders gut geeignet für Firmen, die sich bei jugl.net registrieren wollen. Damit kannst Du ihnen den Einstieg wesentlich erleichtern. Sie sind dadurch auch automatisch in Deinem Netzwerk.')?></p>
    <p><?=Yii::t('app','Bitte beachte, dass ein VIP-Code immer nur für eine Registrierung gütig ist.')?></p>
    <h3><?=Yii::t('app','Wähle die gewünschte Anzahl der VIP-Сodes:')?></h3>
</div>

<div class="invatiation-regcodes-packets">
    <div class="invatiation-regcodes-packet" ng-class="{disabled: status.balance<packet.sum}" ng-repeat="packet in packets">
        <div class="invatiation-regcodes-packet-inner">
            <input type="radio" i-check ng-disabled="status.balance<packet.sum" name="packet_id" value="{{packet.id}}" ng-model="buyRegcodesPacket.packet_id"/>
            <label>{{packet.registration_codes_count}} <?= Yii::t('app','VIP-Сodes für'); ?> {{packet.sum|priceFormat}}<jugl-currency></jugl-currency></label>
            <div ng-show="status.balance<packet.sum" class="invatiation-regcodes-packet-payin"><a ui-sref="funds.payin"><?= Yii::t('app','Guthaben aufladen'); ?></a></div>
        </div>
    </div>
</div>

<div class="invitations-regcodes-button"><button ng-disabled="!buyRegcodesPacket.packet_id || buyRegcodesPacket.saving" ng-click="friendsInvitationRegcodesCtrl.buyRegcodesPacket()"><?= Yii::t('app', 'Jetzt kaufen') ?></button></div>

<div ng-if="regcodes.regcodes.length > 0" class="invitations-regcodes-bottom">
    <h3><?=Yii::t('app','Erworbene VIP-Сodes')?></h3>
    <?php /* <p><?=Yii::t('app', 'Um den VIP-code einzugeben, müssen Deine Freunde entweder auf den „Jetzt anmelden“-Button auf <a href="http://jugl.net">www.jugl.net</a> klicken oder falls sie die App schon installiert haben, im Login-Bildschirm den „Registrieren“-Button klicken.')?></p> */ ?>
</div>

<div ng-if="regcodes.regcodes.length > 0" class="account-info-table">
    <table responsive-table scroll-load="friendsInvitationRegcodesCtrl.moreRegcodesLoad" scroll-load-visible="0.7" scroll-load-has-more="regcodes.hasMore">
        <thead>
            <tr>
                <th><?=Yii::t('app','VIP-Сodes')?></th>
                <th><?=Yii::t('app','Erstellt am')?></th>
                <th><?=Yii::t('app','Status')?></th>
                <th><?=Yii::t('app','Akzeptiert von')?></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="regcode in regcodes.regcodes">
                <td>{{::regcode.code}}</td>
                <td>{{::regcode.dt | date : 'dd.MM.yyyy HH:mm'}}</td>
                <td>
                    <span ng-if="regcode.referralUser"><?=Yii::t('app','Akzeptiert')?></span>
                    <span ng-if="!regcode.referralUser"><?=Yii::t('app','Offen')?></span>
                </td>
                <td>
                    <a ui-sref="userProfile({id: regcode.referralUser.id})">
                        <img ng-src="{{regcode.referralUser.avatarSmall}}" ng-if="regcode.referralUser.avatar" class="user-avatar" alt="" />
                    </a>
                    {{::regcode.referralUser | userName}}
                </td>
            </tr>
        </tbody>
    </table>
</div>
