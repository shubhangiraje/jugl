<div class="interests">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Meine Interessen')?></h2>
        </div>

        <div class="interests-content">
            <div class="interests-box clearfix"">
                <div class="interests-tree-btn-box">
                    <a href="" onclick="window.history.back(); return false;" class="btn btn-submit"><?=Yii::t('app','Zur&uuml;ck zur Interessen&uuml;bersicht');?></a>
                </div>

                <div class="interests-tree-box clearfix">
                    <div class="interests-tree-top">
                        <div class="interests-tree-top-img">
                            <img ng-src="{{interests.interest_img}}" alt="{{interests.interest_title}}"/>
                        </div>
                        <div class="interests-tree-top-title">{{interests.interest_title}}</div>
                    </div>

                    <div class="interests-tree">
                        <ul class="interests-tree-level1">
                            <li ng-repeat="interestLevel2 in interests.level2_interests|orderBy:'interest_sort'">
                                <div class="interests-tree-category-box">
                                    <div class="interests-tree-category-img">
                                        <img ng-src="{{interestLevel2.interest_img}}" alt="{{interestLevel2.interest_title}}"/>
                                    </div>
                                    <div class="interests-tree-category-title">{{interestLevel2.interest_title}}</div>
                                </div>
                                <ul class="interests-tree-level2" ng-if="interestLevel2.level3_interests">
                                    <li ng-repeat="interestLevel3 in interestLevel2.level3_interests">
                                        <div class="interests-tree-brand-box clearfix">
                                            <div ng-click="userInterestsUpdateCtrl.deleteFromLevel3Interest(interests.interest_id, interestLevel2.interest_id, interestLevel3.interest_id)" class="interests-tree-del-icon"></div>
                                            <div class="interests-tree-brand-title">{{interestLevel3.interest_title}}</div>
                                        </div>
                                    </li>

                                    <li class="interests-tree-add">
                                        <a ui-sref="^.addStep3({id: interestLevel2.interest_id})" class="interests-tree-add-icon"></a>
                                    </li>
                                </ul>

                                <ul class="interests-tree-level2" ng-if="!interestLevel2.level3_interests">
                                    <li class="interests-tree-add no-interest">
                                        <a ui-sref="^.addStep3({id: interestLevel2.interest_id})" class="interests-tree-add-icon"></a>
                                        <div ng-click="userInterestsUpdateCtrl.deleteFromLevel2Interest(interests.interest_id, interestLevel2.interest_id)" class="interests-tree-del-icon"></div>
                                    </li>
                                </ul>

                            </li>

                            <li class="interests-tree-no">
                                <a ui-sref="^.addStep2({id: interests.interest_id})" class="interests-tree-add-icon"></a>
                            </li>
                        </ul>
                    </div>



                </div>

            </div>
            <div class="bottom-corner"></div>
        </div>

    </div>
</div>