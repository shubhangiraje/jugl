<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="TrollboxMessageUpdateCtrl as trollboxMessageUpdateCtrl">
    <div class="trollbox-message-update-box">
        <div class="trollbox-form">
            <div class="forum-new-message-box">
                <div class="forum-new-message-image">
                    <div class="preview-upload-image" ng-if="trollboxMessage.image">
                        <img ng-src="{{trollboxMessage.image}}"/>
                        <button ng-click="trollboxMessageUpdateCtrl.deleteTrollboxImage()" class="btn-del-image"></button>
                    </div>
                    <div class="box-input-file" ng-if="!trollboxMessage.image">
                        <div class="spinner" ng-if="uploader.isUploading"></div>
                        <input type="file" nv-file-select filters="imageVideoFilter,queueLimit" uploader="uploader" options="fileUploadOptions" />
                    </div>
                </div>
                <div class="forum-new-message-image-notification"><?=Yii::t('app','Bild / Video hochladen')?></div>
                <div class="forum-new-message-smiles">
                    <div class="smiles" emoticons-tooltip emoticon-forum="true" emoticons-list="trollboxMessageUpdateCtrl.emoticonsList" message-text="trollboxMessage.text">
                        <div class="emoticons-tooltip">
                            <span ng-repeat="(emoticon,text) in trollboxMessageUpdateCtrl.emoticonsList" ng-bind="emoticon" class="emoticon"></span>
                        </div>
                    </div>
                </div>
            </div>

            <textarea placeholder="<?=Yii::t('app','Text eingeben')?>" ng-model="trollboxMessage.text"></textarea>

            <div class="trollbox-message-visibility-list">
                <div>
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_followers">
                    <label><?= Yii::t('app', 'An Abonnenten') ?></label>
                </div>
                <div>
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_contacts">
                    <label><?= Yii::t('app', 'An Kontakte') ?></label>
                </div>
                <div>
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_all">
                    <label><?= Yii::t('app', 'An Alle') ?></label>
                </div>
            </div>

            <div class="trollbox-message-select-category">
                <label><?= Yii::t('app', 'Kategorieauswahl') ?>:</label>
                <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                    <select ng-model="trollboxMessage.trollbox_category_id" selectpicker="{title:''}" ng-options="item.id as item.title for item in trollboxCategoryList">
                        <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                    </select>
                </div>
            </div>

            <ul class="errors-list" ng-if="trollboxMessage.$allErrors">
                <li ng-repeat="error in trollboxMessage.$allErrors">{{::error}}</li>
            </ul>

            <div class="trollbox-message-update-btn-box">
                <div class="btn btn-submit" ng-click="trollboxMessageUpdateCtrl.save()"><?=Yii::t('app','Speichern')?></div>
            </div>

        </div>
    </div>
</div>