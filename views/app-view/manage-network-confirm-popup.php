<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="ManageNetworkConfirmPopupCtrl as manageNetworkConfirmPopupCtrl">

    <div class="manage-network-confirm-box">
        <p><?= Yii::t('app', '<span>{{users.src_name}}</span>in die Struktur von<span>{{users.dst_name}}</span>verschieben?') ?></p>
    </div>

    <div class="buttons">
        <div ng-click="manageNetworkConfirmPopupCtrl.save()" ng-disabled="users.saving" class="ok"><?= Yii::t('app', 'Ja') ?></div>
        <div ng-click="modalService.hide()" class="cancel"><?= Yii::t('app', 'Nein') ?></div>
    </div>

</div>