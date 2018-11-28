<div class="searches searchIndex">
    <div class="container">

        <div ng-click="showInfoPopup('view-searches-search')" ng-class="{'blink':isOneShowInfoPopup('view-searches-search')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Was suchen andere')?></h2>
        </div>

        <div class="searches-content clearfix">
            <div class="searches-filter-content">
                <div class="searches-filter-box clearfix">
                    <ul class="searches-filter-list">
                        <li class="searches-filter-item">
                            <label><?=Yii::t('app','Interessenkategorie')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select ng-model="filter.level1_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter:{parent_id:null}">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-if="filter.level1_interest_id && (interests | filter: {parent_id:filter.level1_interest_id}).length>0" class="searches-filter-item">
                            <label><?=Yii::t('app','Unterkategorie')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select id="level2_interest_id" class="filter-select-refresh" ng-model="filter.level2_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter: {parent_id:filter.level1_interest_id} : searchRequestSearchCtrl.filterInterestComparator">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-if="filter.level2_interest_id && (interests | filter: {parent_id:filter.level2_interest_id}).length>0" class="searches-filter-item">
                            <label><?=Yii::t('app','Themenfilter')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select id="level3_interest_id" class="filter-select-refresh" ng-model="filter.level3_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter: {parent_id:filter.level2_interest_id} : searchRequestSearchCtrl.filterInterestComparator">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-repeat="param in params | filter:searchRequestSearchCtrl.paramFilter" class="searches-filter-item">
                            <label>{{param.title}}</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select ng-model="filter.params[param.id]" selectpicker="{title:''}" ng-options="value.id as value.title for value in param.values">
                                </select>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>


            <div class="searches-result-content">
                <div class="searches-result-box" scroll-load="searchRequestSearchCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
                    <div class="sort-content clearfix">
                      
						<div class="sort-box">
                            <label><?=Yii::t('app', 'Sortieren nach');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="filter.sort" selectpicker>
                                    <option value="create_dt"><?= Yii::t('app', 'Datum') ?></option>
                                    <option value="relevancy"><?= Yii::t('app', 'Relevance') ?></option>
                                    <option value="bonus"><?= Yii::t('app', 'Vermittlungsbonus') ?></option>
                                    <option value="rating"><?= Yii::t('app', 'Rating') ?></option>
                                </select>
                            </div>
                        </div>
						<div class="sort-box country">
                            <label><?=Yii::t('app', 'Land auswählen');?>:</label>
                            <div class="searches-filter-list">
                                <div class="field-box-select filter-select multiselect-box">
                                    <multiselect ng-model="currentCountry" labels="labels"
                                         options="countryList" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="results.items.length == 0" class="result-empty-text">
                        <?= Yii::t('app', 'Leider gibt es momentan keine Suchaufträge anderer Mitglieder, die Deinen angegebenen Interessen oder Deinem Filter entsprechen.') ?>
                    </div>

                    <div ng-repeat="item in results.items" class="found-item-box">
                        <div class="found-image-box">
                            <a ui-sref="searches.details({id:item.id})">
                                <div class="found-image">
                                    <img ng-src="{{::item.image}}" alt=""/>
                                </div>
                            </a>
                        </div>
                        <div class="found-description-box">
                            <div class="found-title-info"><?= Yii::t('app', 'Ich suche:') ?></div>
                            <div class="found-title">
                                <h2><a ui-sref="searches.details({id:item.id})">{{::item.title}}</a></h2>
                            </div>
                            <div class="found-text">
                                <p ng-bind-html="item.description|linky"></p>
                            </div>
                            <ul class="found-category">
                                <li>{{::item.level1Interest}}</li>
                                <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                                <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                            </ul>
                        </div>
                        <div class="found-info-wrap clearfix">
                            <div class="found-info-box">
                                <div ng-if="item.price_to" class="found-price">{{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</div>
                                <div ng-if="!item.price_to"class="found-price">{{::item.price_from|priceFormat}} &euro;</div>

                                <div once-if="item.bonus" class="found-offer-param"><?= Yii::t('app', 'Für die Vermittlung eines passenden Angebots zahle ich:') ?> <span class="found-offer-value">{{::item.bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                <div class="found-place">{{::item.zip}} {{::item.city}}</div>
                                <div class="found-user-box">
                                    <a ui-sref="userProfile({id: item.user.id})"><div class="found-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div></a>
                                    <div class="found-user-name">{{::item.user|userName}} <div ng-click="updateCountry(item.user.id,results.items)" id="{{::item.user.flag}}" class="flag flag-32 flag-{{item.user.flag}}"></div></div>
                                    <div class="offer-user-rating">
                                        <div class="star-rating">
                                            <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                        </div>
                                        <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                        <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                        <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                    </div>
                                </div>

                                <ul class="found-item-statistic">
                                    <li class="clearfix">
                                        <div class="title"> <?= Yii::t('app', 'Angebote'); ?></div>
                                        <div class="value">
                                            {{::item.count_total|default:0}}
                                            <a ng-if="item.count_total>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <div class="title"> <?= Yii::t('app', 'Abgelehnt'); ?></div>
                                        <div class="value">
                                            {{::item.count_rejected|default:0}}
                                            <a ng-if="item.count_rejected>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <div class="title"> <?= Yii::t('app', 'Angenommen'); ?></div>
                                        <div class="value">
                                            {{::item.count_accepted|default:0}}
                                            <a ng-if="item.count_accepted>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                        </div>
                                    </li>
                                </ul>


                            </div>

                            <div class="found-status-box clearfix">
                                <div class="found-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                                <div class="found-relevance relevance">
                                    <?= Yii::t('app', 'Relevance'); ?>
                                    <div class="relevance-percent">{{::item.relevancy}}%</div>
                                </div>
                                <div class="found-favorite favorite" ng-if="!item.favorite" ng-click="searchRequestSearchCtrl.addFavorite(item.id)" ng-class="{'favorite-false': !item.favorite}">
                                    <?= Yii::t('app', 'Merken'); ?>
                                </div>
                                <div class="found-favorite favorite" ng-if="item.favorite" ng-class="{'favorite-true': item.favorite}"><?= Yii::t('app', 'Gemerkt'); ?></div>
                            </div>

                        </div>
                    </div>
<?php /*
                    <div class="page-numbers-box">
                        <ul class="page-numbers">
                            <li class="page-number start"><a href=""></a></li>
                            <li class="page-number prev"><a href=""></a></li>
                            <li class="page-number"><a href=""> 1 </a></li>
                            <li class="page-number current"><a href=""> 2 </a></li>
                            <li class="page-number"><a href=""> 3 </a></li>
                            <li class="page-number"><a href=""> 4 </a></li>
                            <li class="page-number dots"> ... </li>
                            <li class="page-number next"><a href=""></a></li>
                            <li class="page-number end"><a href=""></a></li>
                        </ul>
                    </div>
*/ ?>
                    <div class="bottom-corner"></div>
                </div>
            </div>


        </div>

    </div>
</div>
