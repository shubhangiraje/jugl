<div class="container">
    <div class="welcome-text">
        <h2><?=Yii::t('app','Team wechseln')?></h2>
    </div>

    <div class="team-change-head-box">
        <p><?= Yii::t('app','Hier hast Du die Möglichkeit, innerhalb von noch') ?></p>

        <div class="team-change-countdown" server-countdown="status.teamChangeFinishTime" server-countdown-with-days="true"></div>

        <p><?= Yii::t('app','Dein Team zu wechseln') ?></p>
        <p><?= Yii::t('app','In wessen Team möchtest Du wechseln?') ?></p>
    </div>

    <div class="team-change-search-box">
        <div class="team-change-search-form-box clearfix">
            <div class="team-change-search-form">
                <div class="team-change-search-filed">
                    <label><span><?= Yii::t('app','Nutzername') ?>:</span></label>
                    <div class="field-box-input">
                        <input type="text" ng-model="filter.name">
                    </div>
                </div>
                <div class="team-change-search-filed">
                    <label><span><?= Yii::t('app','Plz / Ort') ?>:</span></label>
                    <div class="field-box-input">
                        <input type="text" ng-model="filter.zip_city">
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
                    <label><span><?= Yii::t('app','Alter') ?>:</span></label>
                    <div class="field-box-age clearfix">
                        <div class="field-box-input"><input ng-model="filter.ageFrom" type="text"><span><?= Yii::t('app','bis') ?></span></div>
                        <div class="field-box-input"><input ng-model="filter.ageTo" type="text"></div>
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
                <div class="team-change-search-filed team-change-filter-single">
                    <label><span><?= Yii::t('app','Beste Bewertungen zuerst anzeigen') ?>:</span></label>
                    <div class="field-box-checkbox">
                        <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="filter.rating">
                    </div>
                </div>
            </div>
        </div>
<!--
        <a href="" class="btn btn-submit"><?= Yii::t('app', 'Suchen') ?></a>
-->
    </div>

    <div ng-if="items" class="team-change-search-results">
        <h2><?= Yii::t('app','Gefunden') ?>: <span>{{searchUserCount}}</span></h2>

        <div class="team-change-search-results-box clearfix" scroll-load="teamChangeUserSearchCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="hasMore">

            <div ng-repeat="item in items" class="team-change-search-user-item-box">
                <div class="team-change-search-user-item">

                    <div class="found-user-box clearfix">
                        <a ui-sref="userProfile({id: item.id})">
                            <div class="found-user-avatar"><img ng-src="{{::item.avatarSmall}}" alt=""/></div>
                        </a>
                        <div class="found-user-name">{{::item|userName}}</div>
                        <div class="offer-user-rating">
                            <div class="dt"><?= Yii::t('app','Mitglied seit'); ?> {{item.registration_dt|date:"dd.MM.yyyy"}}</div>
                        </div>
                    </div>

                    <div class="team-change-user-rating-box">
                        <div class="team-change-rating">
                            <div class="star-rating">
                                <span once-style="{width:(+item.team_rating)+'%'}"></span>
                            </div>
                            <a ui-sref="userProfile({id: item.id})"><?= Yii::t('app','Teamleaderbewertungen ansehen') ?></a>
                        </div>
                        <div class="team-change-rating">
                            <div class="star-rating">
                                <span once-style="{width:(+item.rating)+'%'}"></span>
                            </div>
                            <a ui-sref="userProfile({id: item.id})"><?= Yii::t('app','Kaufbewertungen ansehen') ?></a>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="" ng-if="!item.invitation_sent" ng-click="teamChangeUserSearchCtrl.requestTeamChange(item)" class="btn btn-submit"><?= Yii::t('app','Teamwechsel anfragen') ?></a>
                        <a ng-if="item.invitation_sent" class="btn btn-dark-blue"><?= Yii::t('app','Teamwechsel bereits beantragt') ?></a>
                    </div>

                </div>
            </div>
            
        </div>

        <div class="bottom-corner"></div>
    </div>




</div>