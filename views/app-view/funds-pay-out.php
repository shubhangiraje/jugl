<?php

use app\models\PayOutPayPalForm;
use app\models\PayOutELVForm;

?>

<div ng-if="user.validation_status!='SUCCESS'" class="pay">
    <div ng-if="user.validation_status!='AWAITING'" class="pay-block">
        <h3><?=Yii::t('app', 'Echtheitscheck');?></h3>
        <p><?=Yii::t('app', 'Die Echtheitsverifikation ist zu Deiner eigenen Sicherheit da. So weist Du die Identit&auml;t Deiner Person nach.');?></p>
        <form novalidate>
            <div class="validation_status_failure" pay-error ng-if="user.validation_status=='FAILURE'">
                <?=Yii::t('app', 'Deine Daten wurden überprüft. Das Ergebnis der Prüfung ist leider negativ. Grund: "{{user.validation_failure_reason}}"');?>
            </div>
            <input type="radio" name="validation_type" i-check ng-model="user.validation_type" value="PHOTOS"/><?=Yii::t('app', 'Beweisfoto & Kopie Deines Ausweis');?>
            <div ng-if="user.validation_type=='PHOTOS'" id="validationPassport">
                <p><?= Yii::t('app', 'Lade je ein Foto der Vorderseite und der Rückseite Deines Ausweises hoch. Mache außerdem ein Foto von Dir mit Deinem Ausweis (Vorderseite) und einem Zettel in der Hand, auf dem "jugl.net" und Dein Name steht.') ?></p>
                <div class="pay-upload-file">
                    <div class="spinner" ng-if="uploader.isUploading && itemUploadPhoto1File"></div>
                    <div ng-if="!itemUploadPhoto1File">
                        <div class="plus"></div>
                        <div class="upload-title"><?=Yii::t('app', 'Dein Ausweis Vorderseite');?></div>
                    </div>
                    <img ng-if="user.validation_photo1_file_id" ng-src="{{user.validationPhoto1File.thumbs.validationSmall}}" />
                    <input type="file" nv-file-select filters="imageFilter" uploader="uploader" options="validationPhoto1UploadOptions"/>
                </div>
                <div class="pay-upload-file">
                    <div class="spinner" ng-if="uploader.isUploading && itemUploadPhoto2File"></div>
                    <div ng-if="!itemUploadPhoto2File">
                        <div class="plus"></div>
                        <div class="upload-title"><?=Yii::t('app', 'Dein Ausweis Rückseite');?></div>
                    </div>
                    <img ng-if="user.validation_photo2_file_id" ng-src="{{user.validationPhoto2File.thumbs.validationSmall}}" />
                    <input type="file" nv-file-select filters="imageFilter" uploader="uploader" options="validationPhoto2UploadOptions"/>
                </div>

                <div class="pay-upload-file">
                    <div class="spinner" ng-if="uploader.isUploading && itemUploadPhoto3File"></div>
                    <div ng-if="!itemUploadPhoto3File">
                        <div class="plus"></div>
                        <div class="upload-title"><?=Yii::t('app', 'mit Dir, Deinem Ausweis und dem Zettel, auf dem "jugl.net" und Dein Name steht');?></div>
                    </div>
                    <img ng-if="user.validation_photo3_file_id" ng-src="{{user.validationPhoto3File.thumbs.validationSmall}}" />
                    <input type="file" nv-file-select filters="imageFilter" uploader="uploader" options="validationPhoto3UploadOptions"/>
                </div>

                <div ng-if="user.$allErrors" class="pay-has-error has-error-validation-photo">
                    <ul class="errors-list">
                        <li ng-repeat="error in user.$allErrors" ng-bind="error"></li>
                    </ul>
                </div>
                <div>
                    <button ng-disabled="user.saving" ng-click="fundsPayOutCtrl.saveValidation()"><?=Yii::t('app', 'Verifikation abschließen');?></button>
                </div>

            </div>
        </form>
    </div>
    <div class="validation_status_awaiting" ng-if="user.validation_status=='AWAITING'">
        <?=Yii::t('app', 'Ihre Daten werden baldm&ouml;glichst &uuml;berpr&uuml;ft. Sie werden von uns benachrichtigt.');?>
    </div>
</div>


<div ng-if="user.validation_status=='SUCCESS' && !payOutRequest.saved" class="pay">
    <h3><?=Yii::t('app', 'Jugls-Guthaben auszahlen');?></h3>
    <p><?=Yii::t('app', 'Hier hast Du die M&ouml;glichkeit, Dein Jugls-Guthaben auszuzahlen. W&auml;hle einfach die gew&uuml;nschte Anzahl der Jugls und best&auml;tige Deine Angaben.');?></p>

    <form novalidate>
        <div class="pay-block">
            <h3><?=Yii::t('app', 'W&auml;hle die gew&uuml;nschte Anzahl der Jugls:');?></h3>
            <div ng-repeat="packet in packets" class="pay-radio" ng-if="packet.jugl_sum < status.balance_earned">
                <input type="radio" name="packet_id" value="{{packet.id}}" i-check ng-model="payOutRequest.packet_id"/>{{packet.jugl_sum|priceFormat}}<jugl-currency></jugl-currency> ({{packet.currency_sum|priceFormat}}&euro;)
            </div>
            <p ng-if="packets[0].jugl_sum>status.balance_earned"><?= Yii::t('app', 'Du hast nicht genug Jugls um eine Auszahlung anzufordern.') ?></p>
        </div>

        <div ng-if="payOutRequest.packet_id">
            <div class="pay-block">
                <h3><?=Yii::t('app', 'W&auml;hle die gew&uuml;nschte Zahlungsart:');?></h3>
                <div>
<?php /*
                    <div class="pay-radio">
                        <input type="radio" name="method" value="PAYPAL" i-check ng-model="payOutRequest.payment_method"/><?=Yii::t('app', 'PayPal');?>
                    </div>
*/ ?>
                    <div class="pay-radio">
                        <input type="radio" name="method" value="ELV" i-check ng-model="payOutRequest.payment_method"/><?=Yii::t('app', 'Bank&uuml;berweisung');?>
                    </div>
                </div>
<?php /*
                <div ng-if="payOutRequest.payment_method=='PAYPAL'" class="pay-form">
                    <div class="pay-img">
                        <img src="/static/images/account/paypal.jpg" alt="paypal"/>
                    </div>

                    <div bs-has-classes>
                        <input type="text" ng-model="payOutRequest.details.email" placeholder="<?=PayOutPayPalForm::getEncodedAttributeLabel('email')?>"/>
                    </div>
                </div>
*/ ?>

                <div ng-if="payOutRequest.payment_method=='ELV'" class="pay-form">
                    <div bs-has-classes>
                        <input type="text" ng-model="payOutRequest.details.iban" placeholder="<?=PayOutELVForm::getEncodedAttributeLabel('iban')?>"/>
                    </div>
                    <div bs-has-classes>
                        <input type="text" ng-model="payOutRequest.details.bic" placeholder="<?=PayOutELVForm::getEncodedAttributeLabel('bic')?>"/>
                    </div>
                    <div bs-has-classes>
                        <input type="text" ng-model="payOutRequest.details.kontoinhaber" placeholder="<?=PayOutELVForm::getEncodedAttributeLabel('kontoinhaber')?>"/>
                    </div>
                </div>
                <div ng-if="payOutRequest.payment_method">
                    <div ng-if="payOutRequest.$allErrors" class="pay-has-error">
                        <ul class="errors-list">
                            <li ng-repeat="error in payOutRequest.$allErrors" ng-bind="error"></li>
                        </ul>
                    </div>
                    <div>
                        <button ng-disabled="payOutRequest.saving" ng-click="fundsPayOutCtrl.savePayOutRequest()"><?=Yii::t('app', 'Jetzt auszahlen');?></button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<div ng-if="payOutRequest.saved">
    <p><?=Yii::t('app','Your payout request saved. After processing payout by our staff you will see it in your balance log');?></p>
</div>
