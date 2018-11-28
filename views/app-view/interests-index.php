<div class="interests">
    <div class="container">
        <div ng-if="$state.current.name=='interests.index'" ng-click="showInfoPopup('view-interests-offer')" ng-class="{'blink':isOneShowInfoPopup('view-interests-offer')}" class="info-popup-btn"></div>
        <div ng-if="$state.current.name=='interests-searches.index'" ng-click="showInfoPopup('view-interests-search-request')" ng-class="{'blink':isOneShowInfoPopup('view-interests-search-request')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Meine Interessen')?></h2>
            <span ng-if="$state.current.name=='interests.index'"><?=Yii::t('app','Hier kannst Du Deine Interessen verwalten. Das Hinzuf&uuml;gen neuer Interessen ist ganz einfach: allgemeine Interessenkategorie w&auml;hlen -> Unterkategorie aussuchen -> das ganze durch zus&auml;tzliche Themenfilter pr&auml;zisieren. Durch Klicken auf ein Interesse siehst Du dann die entsprechende Interessenhierarchie.')?></span>
            <span ng-if="$state.current.name=='interests-searches.index'"><?= Yii::t('app','Gib hier die Bereiche an, in denen Du für andere Mitglieder Aufträge oder Recherchern ausführen möchtest.'); ?></span>
        </div>

        <div class="interests-content">
            <div class="interests-box clearfix">
                <div class="interests-list">

                    <div ng-repeat="interest in interests|orderBy:'interest_sort'" class="interests-element-box">
                        <div class="interests-element">
                            <div ng-click="userInterestsCtrl.deleteFromInterest(interest.interest_id)" class="interests-del"></div>
                            <a ui-sref="^.update({id: interest.interest_id})" class="interests-edit"></a>
                            <div class="interests-img">
                                <img ng-src="{{interest.interest_img}}" alt="{{interest.interest_title}}"/>
                            </div>
                            <div class="interests-info">
                                <div class="interests-title">{{interest.interest_title}}</div>
                                <div ng-if="interest.count_level2" class="interests-count">{{interest.count_level2}} <?=Yii::t('app',' Unterkategorien')?></div>
                                <div ng-if="interest.count_level3" class="interests-count">{{interest.count_level3}} <?=Yii::t('app',' Themenfilter')?></div>
                            </div>
                        </div>
                    </div>

                    <div class="interests-element-box">
                        <div class="interests-element interests-add">
                            <div class="interests-icon-add"><span class="circle-icon-add"></span></div>
                            <div class="interests-info">
                                <div class="interests-text-add"><?=Yii::t('app','Neues Interesse hinzuf&uuml;gen')?></div>
                            </div>
                            <a ui-sref="^.addStep1" class="interests-link"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-corner"></div>
        </div>

    </div>
</div>