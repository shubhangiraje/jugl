<div class="interests">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Meine Interessen')?></h2>
            <?=Yii::t('app','Hier kannst Du Deine Interessen verwalten. Das Hinzuf&uuml;gen neuer Interessen ist ganz einfach: allgemeine Interessenkategorie w&auml;hlen -> Unterkategorie aussuchen -> das ganze durch zus&auml;tzliche Themenfilter pr&auml;zisieren. Durch Klicken auf ein Interesse siehst Du dann die entsprechende Interessenhierarchie.')?>
        </div>

        <div class="interests-content">
            <div class="interests-box clearfix"">
                <ul class="interests-step-title">
                    <li><?=Yii::t('app', '1. W&auml;hle allgemeine Interessenkategorie');?></li>
                </ul>

                <div class="interests-list clearfix">

                    <div ng-repeat="interest in interests|orderBy:'interest_sort'" class="interests-element-box">
                        <div class="interests-element-step">
                            <div class="interests-img">
                                <img ng-src="{{interest.interest_img}}" alt="{{interest.interest_title}}"/>
                            </div>
                            <div class="interests-info">
                                <div class="interests-title">{{::interest.interest_title}}</div>
                            </div>
                            <a ng-if="interest.isChildInterests" ui-sref="^.addStep2({id: interest.interest_id})" class="interests-link"></a>
                            <a ng-if="!interest.isChildInterests" ng-click="interestsAddStep1Ctrl.saveInterest(interest.interest_id)" href="" class="interests-link"></a>
                        </div>
                    </div>

                </div>

            </div>

            <div class="bottom-corner"></div>
        </div>

    </div>
</div>
