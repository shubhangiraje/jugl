<div class="pay">
    <h3><?=Yii::t('app', 'Jugls-Guthaben aufladen');?></h3>

    <p ng-if="payInRequest.return_status=='SUCCESS'">
        <?= Yii::t('app', 'Dein Jugl-Konto wurde erfolgreich aufgeladen.') ?>
    </p>

    <p ng-if="payInRequest.return_status=='CANCEL'">
        <?= Yii::t('app', 'Die Transaktion zum Aufladen deines Jugl-Kontos wurde abgebrochen.') ?>
    </p>

    <p ng-if="payInRequest.return_status=='AWAITING'">
        ##{{payInRequest.return_status}}##
        ##{{payInRequest.confirm_status}}##
    </p>

    <p ng-if="payInRequest.return_status=='FAILURE'">
        ##{{payInRequest.return_status}}##
        ##{{payInRequest.confirm_status}}##
    </p>

    <p ng-if="payInRequest.return_status=='PENDING'">
        ##{{payInRequest.return_status}}##
        ##{{payInRequest.confirm_status}}##
    </p>

</div>
