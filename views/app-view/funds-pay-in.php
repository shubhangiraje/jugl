<div class="pay">
    <h3><?=Yii::t('app', 'Jugls-Guthaben aufladen');?></h3>
    <p><?=Yii::t('app', 'Hier hast Du die M&ouml;glichkeit, Dein Jugls-Guthaben aufzuladen. W&auml;hle einfach die gew&uuml;nschte Anzahl der Jugls und bezahle sicher, einfach und bequem mit der Zahlungsart Deiner Wahl.');?></p>
    <form ng-if="$state.current.name=='funds.payin'" novalidate>

<!--        <div class="pay-block">-->
<!--            <div class="pay-checkbox">-->
<!--                <input type="checkbox" i-check ng-model="payIn.checkPacket"/>-->
<!--                <label>--><?//= Yii::t('app', 'Hiermit bestätige ich, dass ich Kaufmann im Sinne des §1 HGB bin'); ?><!--</label>-->
<!--            </div>-->
<!--        </div>-->

        <div class="pay-block">
            <h3><?=Yii::t('app', 'W&auml;hle die gew&uuml;nschte Anzahl der Jugls:');?></h3>
            <div ng-repeat="packet in packets" class="pay-radio">
                <input type="radio" name="packet_id" value="{{packet.id}}" i-check ng-model="payInRequest.packet_id"/>{{packet.jugl_sum|priceFormat}}<jugl-currency></jugl-currency> ({{packet.currency_sum|priceFormat}}&euro;)
            </div>
        </div>

        <div ng-if="payInRequest.packet_id" class="pay-block">
            <h3><?=Yii::t('app', 'W&auml;hle die gew&uuml;nschte Zahlungsart:');?></h3>
            <div>
                <div class="pay-radio">
                    <input type="radio" name="method" value="PAYONE_GIROPAY" i-check ng-model="payInRequest.payment_method"/><?=Yii::t('app', 'Giropay');?>
                </div>
<!--
                <div class="pay-radio">
                    <input type="radio" name="method" value="PAYONE_PAYPAL" i-check ng-model="payInRequest.payment_method"/><?=Yii::t('app', 'PayPal');?>
                </div>
-->
                <div class="pay-radio">
                    <input type="radio" name="method" value="PAYONE_CC" i-check ng-model="payInRequest.payment_method"/><?=Yii::t('app', 'Kreditkarte (Visa, Mastercard, American Express)');?>
                </div>
                <div class="pay-radio">
                    <input type="radio" name="method" value="PAYONE_SOFORT" i-check ng-model="payInRequest.payment_method"/><?=Yii::t('app', 'Sofortüberweisung');?>
                </div>
                <div class="pay-radio">
                    <input type="radio" name="method" value="ELV" i-check ng-model="payInRequest.payment_method"/><?=Yii::t('app', 'Banküberweisung');?>
                </div>
                <p><?=Yii::t('app', 'Die Belastung Ihres Kreditkartenkontos erfolg i.d.R. 2-3 Werktage nach Abschluss der Bestellung. Ihre Kreditkartendaten k&ouml;nnen Sie auf n&auml;chsten Siete eingeben. Bitte weiter klicken.');?></p>

                <div class="pay-img">
                    <img src="/static/images/account/visa.png" alt="visa"/>
                    <img src="/static/images/account/mastercard.png" alt="mastercard"/>
                </div>

                <div ng-if="payInRequest.$allErrors" class="pay-has-error">
                    <ul class="errors-list">
                        <li ng-repeat="error in payInRequest.$allErrors" ng-bind="error"></li>
                    </ul>
                </div>

                <div>
                    <button ng-disabled="payInRequest.saving" ng-click="fundsPayInCtrl.savePayInRequest()"><?=Yii::t('app', 'Weiter');?></button>
                </div>
            </div>
        </div>
    </form>
    <div ui-view></div>
</div>
