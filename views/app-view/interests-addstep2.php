<div class="interests">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Meine Interessen')?></h2>
            <?=Yii::t('app','Hier kannst Du Deine Interessen verwalten. Das Hinzuf&uuml;gen neuer Interessen ist ganz einfach: allgemeine Interessenkategorie w&auml;hlen -> Unterkategorie aussuchen -> das ganze durch zus&auml;tzliche Themenfilter pr&auml;zisieren. Durch Klicken auf ein Interesse siehst Du dann die entsprechende Interessenhierarchie.')?>
        </div>

        <div class="interests-content">
            <div class="interests-box">
                <ul class="interests-step-title">
                    <li>1.&nbsp;{{level_interests.level1_title}}</li>
                    <li>
                        <?=Yii::t('app', '2. W&auml;hle Unterkategorie');?>
                        <div class="interests-btn-box">
                            <div ng-if="type=='addInterest'" ng-click="interestsAddStep2Ctrl.save(level_interests.level1_id)" class="btn btn-save"><?=Yii::t('app','Fertig');?></div>
                            <div ng-if="type=='addSearch' || type=='addOffer'" ng-click="interestsAddStep2Ctrl.add(level_interests.level1_id)" class="btn btn-save"><?=Yii::t('app','Fertig');?></div>
                            <a href="" onclick="window.history.back(); return false;" class="btn btn-submit"><?=Yii::t('app','Zur&uuml;ck');?></a>
                        </div>
                    </li>
                </ul>
                <div class="interests-list clearfix"">

                    <div ng-repeat="interest in interests|orderBy:'interest_sort'" class="interests-element-box">
                        <div class="interests-element-step">
                            <div class="interests-img">
                                <img ng-src="{{interest.interest_img}}" alt="{{interest.interest_title}}"/>
                            </div>
                            <div class="interests-info">
                                <div class="interests-title">{{interest.interest_title}}</div>
                            </div>
                            <a ng-if="interest.isChildInterests" ui-sref="^.addStep3({id: interest.interest_id})" class="interests-link"></a>
                            <a ng-if="!interest.isChildInterests" ng-click="interestsAddStep2Ctrl.saveInterest(level_interests.level1_id, interest.interest_id)" href="" class="interests-link"></a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bottom-corner"></div>
        </div>

    </div>
</div>
