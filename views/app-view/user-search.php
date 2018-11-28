<div id="friends-page" class="page-user-search">
    <div class="container">
        <div ng-click="showInfoPopup('view-user-search')" ng-class="{'blink':isOneShowInfoPopup('view-user-search')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Mitglieder suchen')?></h2>
        </div>


        <?php /*
        <div class="user-search-box">
            <div class="search-box-fields f1">
                <div class="search-box-field">
                    <label><span><?=Yii::t('app','Nutzername')?>:</span></label>
                    <div class="search-box-field-input">
                        <input type="text" ng-model="filter.name" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                    </div>
                </div>
            </div>
            <div class="search-box-fields f2">
                <div class="search-box-field">
                    <label><span><?=Yii::t('app','Plz / Ort')?>:</span></label>
                    <div class="search-box-field-input clearfix">
                        <div class="zip">
                            <input type="text" ng-model="filter.zip" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                        </div>
                        <div class="city">
                            <input type="text" ng-model="filter.city" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-box-fields f1">
                <div class="search-box-field">
                    <label><span><?=Yii::t('app', 'Geschlecht')?>:</span></label>
                    <div class="search-box-field-input">
                        <div class="radio" ng-repeat="(id,name) in sexes">
                            <input type="radio" value="{{id}}" i-check ng-model="filter.sex"/>
                            <label>{{name}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-box-fields f2">
                <div class="search-box-field">
                    <label><span><?=Yii::t('app','Alter')?>:</span></label>
                    <div class="search-box-field-input">
                        <div class="age">
                            <input type="text" ng-model="filter.age" integer ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }" /><br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        */ ?>

        <div class="user-search-box">
            <div class="team-change-search-form-box clearfix">
                <div class="team-change-search-form">
                    <div class="team-change-search-filed">
                        <label><span><?= Yii::t('app','Nutzername') ?>:</span></label>
                        <div class="field-box-input">
                            <input type="text" ng-model="filter.name">
                        </div>
                    </div>
                    <div class="team-change-search-filed">
                        <label><span><?= Yii::t('app','Alter') ?>:</span></label>
                        <div class="field-box-age clearfix">
                            <div class="field-box-input"><input ng-model="filter.ageFrom" type="text"><span><?= Yii::t('app','bis') ?></span></div>
                            <div class="field-box-input"><input ng-model="filter.ageTo" type="text"></div>
                        </div>
                    </div>
                    <div class="team-change-search-filed team-change-filter-single">
                        <label><span><?= Yii::t('app','Single') ?>:</span></label>
                        <div class="field-box-checkbox">
                            <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="filter.single">
                        </div>
                    </div>
                </div>
                <div class="team-change-search-form">
                    <div class="team-change-search-filed">
                        <label><span><?= Yii::t('app','Plz / Ort') ?>:</span></label>
                        <div class="field-box-input">
                            <input type="text" ng-model="filter.zip_city">
                        </div>
                    </div>
                    <div class="team-change-search-filed">
                        <label><span><?= Yii::t('app','Geschlecht') ?>:</span></label>
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="filter.sex" selectpicker>
                                <option value=""><?= Yii::t('app','Alle') ?></option>
                                <?php foreach(\app\models\User::getSexList() as $k=>$v) { ?>
                                    <option value="<?=$k?>"><?=$v?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
					<div class="team-change-search-filed">
                        <label><span><?= Yii::t('app','Land auswählen') ?>:</span></label>
                            <div class="field-box-select">
                                <multiselect ng-model="filter.country_ids" labels="labels"
								 options="countryArrayUserSearch" id-prop="id" display-prop="name" show-select-all="false" show-unselect-all="false" show-search="true" >
                                </multiselect>
                            </div>

                    </div>
                </div>
            </div>

        </div>

        <div ng-if="state.loading" class="loader-box">
            <div class="spinner"></div>
        </div>

        <div ng-if="users.showResults" class="account-box">
            <div class="account-friends-list clearfix" ng-if="users.users.length==0">
                <div class="user-search-empty-result"><?=Yii::t('app','Keine Benutzer gefunden')?></div>
            </div>
            <div class="account-friends-list clearfix" scroll-load="userSearchCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="users.hasMore" ng-if="users.users.length>0">
                <div class="account-friends-element" ng-repeat="user in users.users">
                    <?php include('user-box.php'); ?>
                </div>
            </div>
            <div class="bottom-corner"></div>
        </div>

    </div>
</div>
