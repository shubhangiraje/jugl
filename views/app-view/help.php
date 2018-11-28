<?php if(Yii::$app->controller->id == 'app-view') { ?>

<div id="help-page">
    <div class="container clearfix">
        <h1><?=Yii::t('app','Hilfe')?></h1>
        <div class="account-column">
            <div class="account-box help">
                <h2 class="no-icon"><?=Yii::t('app','Was ist jugl.net?')?></h2>
                <accordion close-others="true">
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                </accordion>
                <div class="bottom-corner"></div>
            </div>

            <div class="account-box help">
                <h2 class="no-icon"><?=Yii::t('app','Was ist jugl.net?')?></h2>
                <accordion close-others="true">
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                </accordion>
                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="account-column">
            <div class="account-box">
                <h2 class="no-icon"><?=Yii::t('app','Mein Netzwerk')?></h2>
                <accordion close-others="true">
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                    <accordion-group>
                        <accordion-heading>
                            <i class="icon" ng-class="{'opened': $parent.isOpen}"></i>
                            <?=Yii::t('app','I can have markup, too2!')?>
                        </accordion-heading>
                        <?=Yii::t('app','This content is straight in the template.')?>
                    </accordion-group>
                </accordion>
                <div class="bottom-corner"></div>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h1><?=Yii::t('app','Hilfe')?></h1>
        </div>

        <div class="help-box clearfix">
            <div class="help-box-column">
                <div class="help-item-box">
                    <h2><?=Yii::t('app','Was ist jugl.net?')?></h2>
                    <div class="accordion">
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-1"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-1" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-2"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-2" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
                <div class="help-item-box">
                    <h2><?=Yii::t('app','Was ist jugl.net?')?></h2>
                    <div class="accordion">
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-3"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-3" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-4"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-4" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
            </div>
            <div class="help-box-column">
                <div class="help-item-box">
                    <h2><?=Yii::t('app','Mein Netzwerk')?></h2>
                    <div class="accordion">
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-5"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-5" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-6"><i></i><?=Yii::t('app','I can have markup, too2!')?></a>
                            <div id="accordion-6" class="accordion-section-content">
                                <?=Yii::t('app','This content is straight in the template.')?>
                            </div>
                        </div>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>