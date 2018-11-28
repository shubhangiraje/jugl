<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="UserAgbPopupCtrl as userAgbPopupCtrl">

    <div class="modal-adb-box" scroll-config="{contentWidth: '0', autoReinitialise: true}" id="popup-scroll" scroll-pane auto-height-popup>
        <div class="modal-agb-title"><?= Yii::t('app','AGB') ?></div>
        <div class="modal-agb-content description-text" ng-bind-html="offer.user.agb"></div>
    </div>

</div>
