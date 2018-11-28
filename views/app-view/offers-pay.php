<div class="offers-pay">
    <div class="container">
        <h2><?=Yii::t('app','Angebot bezahlen')?></h2>

        <div class="offer-pay-box">
            <div class="offer-pay-title">{{offer.title}}</div>
            <div class="offer-pay-title"><?=Yii::t('app','Preis')?>: &euro;{{offerRequest.bet_price|priceFormat}}</div>

            <div ng-if="offer.delivery_days" class="offer-pay-title">
                <?=Yii::t('app','Lieferzeit')?>: {{offer.delivery_days}} <?=Yii::t('app','Tage')?>
            </div>

            <p><?=Yii::t('app','Bitte bezahle das  angenommene Angebot wie folgt')?>:</p>

            <div class="offer-pay-usage-box">
                <h3><?=Yii::t('app','Verwendungszweck')?>:</h3>
                <span>{{offer.request.pay_tx_id}}</span>
            </div>

            <div ng-repeat="bankData in bankDatas" ng-if="offer.pay_allow_bank && (data.showAllBankDatas || $index==0)" class="bank-datas-box" ng-class="{'active':pay.payment_method=='bank_data_'+$index}">
                <div class="offer-pay-radio">
                    <input type="radio" i-check name="payment_method" ng-model='pay.payment_method' value="bank_data_{{$index}}"/>
                </div>

                <h3><?=Yii::t('app','Per Bank&uuml;berweisung an')?></h3>
                <div class="bank-data-transfer"><?=Yii::t('app','IBAN')?>: <span>{{bankData.iban}}</span></div>
                <div class="bank-data-transfer"><?=Yii::t('app','BIC')?>: <span>{{bankData.bic}}</span></div>
                <div class="bank-data-transfer"><?=Yii::t('app','Kontoinhaber')?>: <span>{{bankData.owner}}</span></div>
            </div>

            <div class="show-more-btn-box">
                <button class="show-more-btn btn btn-save" ng-if="bankDatas.length>1" ng-click="data.showAllBankDatas=!data.showAllBankDatas" ng-class="{'show-more-btn-open': data.showAllBankDatas}" ><?=Yii::t('app','Weitere Bankverbindingen zeigen')?></button>
            </div>


            <div ng-if="paypal_email!='' && paypal_email!==null && offer.pay_allow_paypal" class="bank-datas-box" ng-class="{'active':pay.payment_method=='PAYPAL'}">
                <div class="offer-pay-radio">
                    <input type="radio" i-check name="payment_method" ng-model='pay.payment_method' value="PAYPAL"/>
                </div>
                <h3><?=Yii::t('app','per PayPal an')?></h3>
                <div class="bank-data-email"><?=Yii::t('app','E-mail')?>: <span>{{paypal_email}}</span></div>
                <div class="bank-data-text">
                    <p><?= Yii::t('app', 'Bitte logge Dich bei PayPal ein und wähle "Geld senden" aus. Dort klickst du auf "Waren oder Dienstleistungen bezahlen" und gibst "{{paypal_email}}" als Empfänger ein. Du profitierst so automatisch vom Käuferschutz von PayPal.') ?></p>
                    <p><?= Yii::t('app', 'Nach der Testphase werden wir diese Funktion auch in Juglapp integrieren.') ?></p>
                    <a href="https://www.paypal.com/signin/" target="_blank"><?= Yii::t('app', 'Jetzt mit PayPal verbinden') ?></a>
                </div>
            </div>

            <div class="bank-datas-box" ng-if="offer.pay_allow_jugl" ng-class="{'active':pay.payment_method=='JUGLS'}">
                <div class="offer-pay-radio">
                    <input type="radio" i-check name="payment_method" ng-model='pay.payment_method' value="JUGLS"/>
                </div>
                <h3><?=Yii::t('app','per Jugls')?></h3>
                <div class="bank-data-email">{{offer.price_jugls|priceFormat}} <jugl-currency></jugl-currency></div>
            </div>

            <div class="bank-datas-box" ng-if="offer.pay_allow_pod" ng-class="{'active':pay.payment_method=='POD'}">
                <div class="offer-pay-radio">
                    <input type="radio" i-check name="payment_method" ng-model='pay.payment_method' value="POD"/>
                </div>
                <h3><?=Yii::t('app','Barzahlung bei Abholung')?></h3>
            </div>

            <div ng-if="pay.payment_method!='POD'">
                <h2><?=Yii::t('app','Deine Lieferadresse')?></h2>

                <div class="bank-datas-box offers-pay-delivery" ng-class="{'active':pay.delivery_address=='address'}">
                    <div class="offer-pay-radio">
                        <input type="radio" i-check name="delivery_address" ng-model='pay.delivery_address' value="address"/>
                    </div>
                    <div class="pay-delivery-address-box">
                        <div class="pay-delivery-street-house clearfix">
                            <div class="pay-delivery-street">
                                <div class="field-box-input"><input type="text" placeholder="<?=Yii::t('app','Strasse')?>" ng-model="pay.address_street"/></div>
                            </div>
                            <div class="pay-delivery-house">
                                <div class="field-box-input"><input type="text" placeholder="<?=Yii::t('app','Hausnummer')?>" ng-model="pay.address_house_number"/></div>
                            </div>
                        </div>
                        <div class="pay-delivery-zip-city clearfix">
                            <div class="pay-delivery-zip">
                                <div class="field-box-input"><input type="text" placeholder="<?=Yii::t('app','Plz')?>" ng-model="pay.address_zip"/></div>
                            </div>
                            <div class="pay-delivery-city">
                                <div class="field-box-input"><input type="text" placeholder="<?=Yii::t('app','Ort')?>" ng-model="pay.address_city"/></div>
                            </div>
                        </div>
                    </div>
                </div>


                <div ng-if="data.showAllDeliveryAddresses" class="bank-datas-box" ng-repeat="address in deliveryAddresses" ng-class="{'active':pay.delivery_address=='delivery_address_'+$index}">
                    <div class="offer-pay-radio">
                        <input type="radio" i-check name="delivery_address" ng-model='pay.delivery_address' value="delivery_address_{{$index}}"/>
                    </div>
                    <div class="bank-data-transfer"><?=Yii::t('app','Strasse')?>: <span>{{address.street}}</span></div>
                    <div class="bank-data-transfer"><?=Yii::t('app','Hausnummer')?>: <span>{{address.house_number}}</span></div>
                    <div class="bank-data-transfer"><?=Yii::t('app','Plz')?>: <span>{{address.zip}}</span></div>
                    <div class="bank-data-transfer"><?=Yii::t('app','Ort')?>: <span>{{address.city}}</span></div>
                </div>


                <div class="show-more-btn-box">
                    <button ng-if="deliveryAddresses.length>0" class="show-more-btn btn btn-save" ng-click="data.showAllDeliveryAddresses=!data.showAllDeliveryAddresses" ng-class="{'show-more-btn-open': data.showAllDeliveryAddresses}"><?=Yii::t('app','Weitere Liferadressen zeigen')?></button>
                </div>
            </div>



            <ul class="errors-list" ng-if="pay.$allErrors">
                <li ng-repeat="error in pay.$allErrors" ng-bind="error"></li>
            </ul>

            <div class="offer-pay-btn">
                <button ng-if="pay.payment_method!='POD'" class="btn btn-submit" ng-click="offerPayCtrl.pay()"><?=Yii::t('app','Als Bezahlt markieren')?></button>
                <button ng-if="pay.payment_method=='POD'" class="btn btn-submit" ng-click="offerPayCtrl.pay()"><?=Yii::t('app','Händler benachrichtigen')?></button>
            </div>

        </div>

    </div>
</div>







