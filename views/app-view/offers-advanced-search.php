<div class="searches">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Inserate durchsuchen')?></h2>
            <p><?=Yii::t('app','Hier kannst Du alle Inserate, die es bei Jugl.net gibt, durchsuchen. Hier bekommst Du jedoch keinen Werbebonus für das Ansehen der Anzeigen.')?></p>
        </div>

        <div class="interests-advanced-search-box">
            <div class="interests-advanced-search-top clearfix">
                <input type="text" ng-model="data.filter.advanced.distance" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                <div class="interests-advanced-search-top-buttons">
                    <a ng-click="offerAdvancedSearch.search()" class="btn btn-submit"><?=Yii::t('app','Suchen')?></a>&nbsp;
                    <a ng-click="data.filter.advancedEnabled=!data.filter.advancedEnabled" class="btn btn-save"><?=Yii::t('app','Erweitere Suche')?></a>&nbsp;
                    <a ng-click="data.filter={advanced:{}}" class="btn btn-reset"><?=Yii::t('app','Filter zurücksetzen')?></a>
                </div>
            </div>
            <div ng-if="data.filter.advancedEnabled" class="interests-advanced-search-columns clearfix">
                <div class="interests-advanced-search-column">
                    <div class="interests-advanced-search-field clearfix">
                        <label><span><?=Yii::t('app','Preis')?>:</span></label>
                        <div class="interests-advanced-search-field-input price">
                            <input type="text" ng-model="data.filter.advanced.price_from" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                            <span class="interests-advanced-search-from"><?=Yii::t('app','bis')?></span>
                            <input type="text" ng-model="data.filter.advanced.price_to" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                        </div>
                    </div>
                    <div class="interests-advanced-search-field clearfix">
                        <label><span><?=Yii::t('app','Ort')?>:</span></label>
                        <div class="interests-advanced-search-field-input">
                            <input type="text" ng-model="data.filter.advanced.city" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                        </div>
                    </div>
                </div>
                <div class="interests-advanced-search-column">
                    <div class="interests-advanced-search-field clearfix">
                        <label><span><?=Yii::t('app','Land')?>:</span></label>
                        <div class="interests-advanced-search-field-input field-box-select" dropdown-toggle select-click>
                            <select ng-model="data.filter.advanced.country_id" ng-options="item.id as item.country for item in data.countries" selectpicker>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="interests-advanced-search-field clearfix">
                        <label><span><?=Yii::t('app','Umkreissuche')?>:</span></label>
                        <div class="interests-advanced-search-field-input distance">
                            <input type="text" ng-model="data.filter.advanced.distance" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                            <span class="interests-advanced-search-distance"><?=Yii::t('app','km')?></span>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="interests-content advanced-search">
            <div class="interests-box clearfix">
                <div class="interests-list">

                    <div ng-repeat="interest in data.interests | orderBy:interest_sort" class="interests-element-box">
                        <div class="interests-element">
                            <div class="interests-img">
                                <img ng-src="{{interest.interest_img}}" alt="{{interest.interest_title}}"/>
                            </div>
                            <div class="interests-info">
                                <div class="interests-title">{{interest.interest_title}}</div>
                                <div ng-if="interest.count_level2" class="interests-count">{{interest.count_level2}} <?=Yii::t('app',' Unterkategorien')?></div>
                                <div ng-if="interest.count_level3" class="interests-count">{{interest.count_level3}} <?=Yii::t('app',' Themenfilter')?></div>
                            </div>
                            <div ng-if="interest.count_offers>0" class="interest-count-offers-box">
                                <div class="interest-count-offers">{{::interest.count_offers}}</div>
                            </div>
                            <div class="interest-check">
                                <input type="checkbox" i-check ng-model="data.interestsChecks[interest.interest_id]">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="bottom-corner"></div>
        </div>
        <div class="text-center">
            <a ui-sref="offers.add" class="btn btn-submit"><?=Yii::t('app','Verkaufen / Werbung schalten')?></a>
            <a ui-sref="offers.myList" class="btn btn-submit"><?=Yii::t('app','Meine Anzeigen / Werbung verwalten')?></a>
        </div>
    </div>
</div>
