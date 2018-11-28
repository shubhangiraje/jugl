<div class="content" ng-controller="ValidationPhonePopupCtrl as validationPhonePopupCtrl">

    <div class="validation-phone-popup-box">
        <h2><?= Yii::t('app', 'Bitte eins noch!') ?></h2>
        <p><?= Yii::t('app','Verifiziere Dein Profil per SMS, damit Du Deine Identität bestätigst und wir Deinem Teamleader den Einladungsbonus auszahlen können.') ?></p>

        <div class="buttons">
            <div class="btn-line" ng-click="validationPhonePopupCtrl.gotoValidationPhone()"><?= Yii::t('app','Jetzt verifizieren') ?></div>
            <div class="cancel" ng-click="validationPhonePopupCtrl.close()"><?= Yii::t('app','Später erinnern') ?></div>
        </div>

    </div>

</div>