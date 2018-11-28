<div class="add-offer searches">
    <div class="container">

        <div ng-click="showInfoPopup('view-searches-offer-add')" ng-class="{'blink':isOneShowInfoPopup('view-searches-offer-add')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Angebot abgeben')?></h2>
            <p><?= Yii::t('app', 'Gib hier Dein Angebot ab. Klicke auf <span class="icon-info-popup"></span> für weitere Informationen.') ?></p>
        </div>

        <div class="box-details-offer clearfix">
            <div class="details-column">
                <div class="details-info">
                    <h2><?= Yii::t('app', 'Details'); ?></h2>
                    <div class="details-info-box">
                        <h3><?= Yii::t('app', 'Wähle aus, was Du anbieten kannst:'); ?></h3>
                        <ul class="list-details-info">
                            <li ng-repeat="paramValue in searchRequestOffer.searchRequestOfferParamValues" once-if="paramValue.value">
                                <input type="checkbox" i-check ng-model="paramValue.match">
                                <span class="details-param">{{::paramValue.title}}:</span>
                                <span class="details-value">{{::paramValue.value}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
            </div>

            <div class="details-column">
                <div class="details-info-others">
                    <div class="details-offer-description">
                        <textarea placeholder="<?= Yii::t('app', 'Beschreibe Dein Angebot'); ?>" ng-model="searchRequestOffer.description"></textarea>
                    </div>

                    <div class="details-offer-photo">
                        <label><?= Yii::t('app', 'Fotos (falls vorhanden):')?></label>

                        <div class="details-photo-box">
                            <div ng-repeat="file in searchRequestOffer.files" class="preview-upload-image">
                                <img ng-src="{{file.thumbs.imageBig}}" width="100" />
                                <button ng-click="searchRequestOfferAddCtrl.deleteFile(file.id)" class="btn-del-image"></button>
                            </div>
                            <div class="box-input-file">
                                <div class="spinner" ng-if="uploader.isUploading"></div>
                                <span class="icon-input-file" ng-if="!uploader.isUploading"></span>
                                <input type="file" nv-file-select filters="imageFilter" uploader="uploader" options="fileUploadOptions" multiple />
                            </div>
                        </div>
                    </div>

                    <div class="details-offer-price clearfix">
                        <label><?= Yii::t('app', 'Preis:') ?></label>
                        <div class="field-box-offer-price">
                            <div class="price-box">
                                <label><?= Yii::t('app', 'ab')?></label>
                                <div class="price-box-input">
                                    <input type="text" price-validator ng-model="searchRequestOffer.price_from"><span>€</span>
                                </div>
                            </div>
                            <div class="price-box">
                                <label><?= Yii::t('app', 'bis')?></label>
                                <div class="price-box-input">
                                    <input type="text" price-validator ng-model="searchRequestOffer.price_to"><span>€</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="box-details-description box-details-once-accept clearfix">
            <h2><?=Yii::t('app','Angebotdetails (werden bis zur Annahme des Angebots unleserlich bzw. verschwommen dargestellt)')?></h2>

            <div class="details-description clearfix">
                <div class="box-details-once-accept-left">
                    <div class="details-offer-description">
                        <textarea placeholder="<?= Yii::t('app', 'Beschreibe Dein Angebot'); ?>" ng-model="searchRequestOffer.details"></textarea>
                    </div>
                </div>

                <div class="box-details-once-accept-right">
                    <div class="details-offer-photo">
                        <label><?= Yii::t('app', 'Fotos (falls vorhanden):')?></label>

                        <div class="details-photo-box">
                            <div ng-repeat="file in searchRequestOffer.details_files" class="preview-upload-image">
                                <img ng-src="{{file.thumbs.imageBig}}" width="100" />
                                <button ng-click="searchRequestOfferAddCtrl.detailsDeleteFile(file.id)" class="btn-del-image"></button>
                            </div>
                            <div class="box-input-file">
                                <div class="spinner" ng-if="detailsUploader.isUploading"></div>
                                <span class="icon-input-file" ng-if="!detailsUploader.isUploading"></span>
                                <input type="file" nv-file-select filters="imageFilter" uploader="detailsUploader" options="detailsFileUploadOptions" multiple />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <ul class="errors-list" ng-if="searchRequestOffer.$allErrors">
            <li ng-repeat="error in searchRequestOffer.$allErrors" ng-bind="error"></li>
        </ul>

        <div class="details-offer-buttons">
            <a ui-sref="searches.details({id:searchRequestOffer.search_request_id})" class="link-back-details"><?= Yii::t('app', 'Zur&uuml;ck') ?></a>
            <a href="" class="btn btn-submit" ng-disabled="searchRequestOffer.saving" ng-click="searchRequestOfferAddCtrl.save()"><?= Yii::t('app', 'Angebot senden') ?></a>
        </div>


    </div>
</div>