<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\GridView;
use app\components\EDateTime;
use app\components\ActiveForm;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Users'),
        'url'=>['admin-site/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Users')) ?>
    </h1>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method'=>'get',
        'fieldConfig'=>[
            'template'=>"{label}\n<div class=\"col-sm-6\">{input}</div>",
            'labelOptions'=>['class'=>'col-sm-6 control-label'],
            'inputOptions'=>['class'=>'form-control'],
        ]
    ]); ?>

    <style>
        form label.control-label {
            padding-right: 0;
            padding-left: 0;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'first_name') ?>
                <?= $form->field($searchModel, 'uf_offer_request_completed_interest_id')->dropDownList(\app\components\Helper::addEmptyValue(\app\models\Interest::getLevel1List(\app\models\Interest::TYPE_OFFER))) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'last_name') ?>
                <?= $form->field($searchModel, 'uf_age_from') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'nick_name') ?>
                <?= $form->field($searchModel, 'uf_age_to') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'email') ?>
                <?= $form->field($searchModel, 'uf_member_from') ?>
            </div>
            <div class="col-sm-2">
                <?php
                    $statusList=\app\components\Helper::addEmptyValue($searchModel->getExtStatusList());
                    //unset($statusList[\app\models\User::STATUS_DELETED]);
                ?>

                <?= $form->field($searchModel, 'status')->dropDownList($statusList) ?>
                <?= $form->field($searchModel, 'uf_member_to') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_sex')->radioList(\app\models\Offer::getSexList()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_country_id')->dropDownList(\app\components\Helper::addEmptyValue(\app\models\Country::getList())) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_city') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_zip') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_distance_km') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_offer_year_turnover_from') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_offer_year_turnover_to') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($searchModel, 'uf_offers_view_buy_ratio_from',[
                    'template'=>"{label}\n<div class=\"col-sm-3\">{input}</div>",
                    'labelOptions'=>['class'=>'col-sm-9 control-label'],
                ]) ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($searchModel, 'uf_offers_view_buy_ratio_to',[
                    'template'=>"{label}\n<div class=\"col-sm-9\">{input}</div>",
                    'labelOptions'=>['label'=>Yii::t('app','bis'),'class'=>'col-sm-3 control-label'],
                ]) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($searchModel, 'uf_messages_per_day_from',[
                    'template'=>"{label}\n<div class=\"col-sm-3\">{input}</div>",
                    'labelOptions'=>['class'=>'col-sm-9 control-label'],
                ]) ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($searchModel, 'uf_messages_per_day_to',[
                    'template'=>"{label}\n<div class=\"col-sm-9\">{input}</div>",
                    'labelOptions'=>['label'=>Yii::t('app','bis'),'class'=>'col-sm-3 control-label'],
                ]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'uf_active_search_requests_from') ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'company_name') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'invited',[
                    'template'=>"{label}\n<div class=\"col-sm-6\" style='padding-top:7px;'>{input}</div>",
                    'labelOptions'=>['class'=>'col-sm-6 control-label'],
                ])->checkbox([],false) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'invited_by',[
                    'template'=>"{label}\n<div class=\"col-sm-5\">{input}</div>",
                    'labelOptions'=>['class'=>'col-sm-7 control-label'],
                ]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($searchModel, 'registration_ip',[
                    'template'=>"{label}\n<div class=\"col-sm-5\">{input}</div>",
                    'labelOptions'=>['class'=>'col-sm-7 control-label'],
                ]) ?>
            </div>

            <div class="col-sm-2">
                <?= $form->field($searchModel, 'status_action')->dropDownList(\app\models\UserSearch::getStatusActionList(), ['prompt'=>'']) ?>
                <ul class="status-action-list">
                    <li class="status-action-user-blocked"><?= Yii::t('app','Geblockt') ?></li>
                    <li class="status-action-admin-delete"><?= Yii::t('app','Gelöscht') ?></li>
                    <li class="status-action-user-active-validation"><?= Yii::t('app','Frei (Ident. best.)') ?></li>
                    <li class="status-action-user-delete"><?= Yii::t('app','Selbst gelöscht') ?></li>
                </ul>
            </div>

            <div class="col-sm-9 text-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsive'=>false,
        //'filterModel' => $searchModel,
        //'pjax'=>true,
        'columns' => [
            [
                'attribute'=>'status',
                'value'=>function($model) {
                    if($model->status==\app\models\User::STATUS_ACTIVE && $model->packet) {
                        return $model->statusLabel.'('.$model->packetLabel.')';
                    }
                    if($model->status==\app\models\User::STATUS_DELETED && $model->is_user_profile_delete) {
                        return Yii::t('app','Selbst gelöscht');
                    }
                    return $model->statusLabel;

                    //return $model->statusLabel.($model->status==\app\models\User::STATUS_ACTIVE && $model->packet ? ' ('.$model->packetLabel.')':'');
                },
                'width'=>'100px',
                'label'=>Yii::t('app','Status (Midgliedshaft)'),
                /*
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $searchModel->getStatusList(),
                    ['class' => 'form-control','prompt'=>'']
                )
                */
            ],
            [
                'label'=>Yii::t('app','Einladung erhalten'),
                'attribute'=>'registered_by_become_member',
                'format'=>'raw',
                'value'=>function($model) {
                    if (strval($model->registered_by_become_member)) {
                        return Yii::t('app','Über Jugl von<br/><a href="{link}">{name}</a>',[
                            'link'=>Url::to(['admin-user/index','UserSearch[id]'=>$model->parent_id]),
                            'name'=>Html::encode($model->parent->name!='' ? $model->parent->name:$model->parent->deleted_first_name.' '.$model->parent->deleted_last_name)
                        ]);
                    } {
                        return Yii::t('app','Direkt von<br/><a href="{link}">{name}</a>',[
                            'link'=>Url::to(['admin-user/index','UserSearch[id]'=>$model->parent_id]),
                            'name'=>Html::encode($model->parent->name!='' ? $model->parent->name:$model->parent->deleted_first_name.' '.$model->parent->deleted_last_name)
                        ]);
                    }
                }
            ],
            [
                'attribute'=>'registration_dt',
                //'format'=>['date', 'php:d.m.Y H:i:s'],
                'value'=>function($model) {return new EDateTime($model->registration_dt);},
                'label'=>Yii::t('app','R.-datum'),
                'width'=>'100px'
                /*
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'registration_dt_from',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                    ]).
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'registration_dt_to',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','To').'</span>'],
                    ]),
                */
            ],
            'registration_ip',
            [
                'attribute'=>'user_used_device.device_uuid',
                'label'=>Yii::t('app','Device ID'),
                'value'=>function($model) {
                    $result = '';
                    if(!empty($model->userUsedDevice)) {
                        $result.='<div class="td-device-id">'.$model->userUsedDevice->device_uuid.'</div>';
                    }
                    return $result;
                },
                'format'=>'raw',
                'contentOptions'=>[
                    'class'=>'td-device-id'
                ]
            ],
            'email',
            [
                'format'=>'raw',
                'label'=>Yii::t('app','Anzahl Einladungen'),
                'attribute'=>'invitations_all_cnt',
                'contentOptions'=>['class'=>'invitation-stats'],
                'value'=>function($model) {
                    return
                        '<div>'.
                        '<div><i class="fa fa-envelope-o"></i>'.$model->stat_invitations_sms.'</div>'.
                        '<div><i class="fa fa-at"></i>'.$model->stat_invitations_email.'</div>'.
                        '<div><i class="fa fa-whatsapp"></i>'.$model->stat_invitations_whatsapp.'</div>'.
                        '<div><i class="fa fa-facebook"></i>'.$model->stat_invitations_social.'</div>'.
                        '</div>';
                }
            ],
            [
                'format'=>'raw',
                'label'=>Yii::t('app','Erfolgr. Einlad.'),
                'width'=>'80px',
                'value'=>function($model) {
                    return
                        ($model->parent_id ? '<a href="'.Url::to(['admin-user/index','UserSearch[parent_id]'=>$model->id]).'">':'').
                        '<div class="invitations-standart"><span>B</span>'.$model->stat_referrals_standart.'</div>'.
                        '<div class="invitations-vip"><span>P</span>'.$model->stat_referrals_vip.'</div>'.
                        '<div class="invitations-vip-plus"><span>PP</span>'.$model->stat_referrals_vip_plus.'</div>'.
                        ($model->parent_id ? '</a>':'');
                }
            ],
            [
                'label'=>'',
                'value'=>function($model) {
                    return '<a href="'.Url::to(['admin-user/index','UserSearch[parent_id]'=>$model->parent_id]).'"><span style="font-size: 25px" class="glyphicon glyphicon-arrow-left"></span></a>';
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'network_size',
                'width'=>'105px'
            ],
            [
                'attribute'=>'avatar_file_id',
                'format'=>'raw',
                'value'=>function($model) {
                    return \yii\helpers\Html::a(
                        \yii\helpers\Html::img($model->getAvatarThumbUrl('avatar'),['style'=>'width:50px;height:50px;border-radius:25px;']),
                        \yii\helpers\Url::to(['update','id'=>$model->id]),['data-pjax'=>0]
                    );
                }
            ],
            'first_name',
            'last_name',
            'nick_name',
            'company_name',
            'balance',
            'stat_buyed_jugl',
            'payment_complaints',

            [
                'attribute'=>'balance_token',
                'label'=>Yii::t('app', 'Token-Stand'),
                'value'=>'balance_token'
            ],

            [
                'label'=>Yii::t('app', 'Token-Zahlmethode'),
                'value'=>function($model) {
                    $result = '';
                    foreach ($model->getPaymentMethodsBuyToken() as $paymentMethod) {
                        $result .= '<p>'.\app\models\PayInRequest::getPaymentMethodList()[$paymentMethod].'</p>';
                    }
                    return $result;
                },
                'format'=>'raw'
            ],

            [
                'label'=>Yii::t('app', 'App Login'),
                'value'=>function($model) {
                    return !empty($model->userUsedDevice) ? '<span class="glyphicon glyphicon-ok" style="color:green;"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;"></span>';
                },
                'format'=>'raw'
            ],

            [
                'label'=>Yii::t('app', 'Nummer verifiziert'),
                'value'=>function($model) {
                    return !empty($model->validation_phone_status==\app\models\User::VALIDATION_PHONE_STATUS_VALIDATED) ? '<span class="glyphicon glyphicon-ok" style="color:green;"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;"></span>';
                },
                'format'=>'raw'
            ],

            [
                'label'=>Yii::t('app', 'Letzter Login'),
                'value'=>function($model) {
                    $userActivityLog=\app\models\UserActivityLog::find()->andWhere(['user_id'=>$model->id])->orderBy('dt desc')->one();
                    $lastTimeWasOnline=$userActivityLog ? $userActivityLog->dt_full:$model->registration_dt;
                    return (new EDateTime($lastTimeWasOnline))->format('d.m.Y H:s');
                }
            ],

            [
                'attribute'=>'parent_registration_bonus',
                'label'=>Yii::t('app', 'Bonus für Parent'),
                'value'=>'parent_registration_bonus'
            ],

            [
                'attribute'=>'dt_status_change',
                'value'=>'dt_status_change',
                'format'=>['date', 'php:d.m.Y H:i:s'],
                'contentOptions' =>function ($model, $key, $index, $column){
                    $class = '';
                    $model->status==\app\models\User::STATUS_BLOCKED ? $class='dt-user-blocked':null;
                    $model->status==\app\models\User::STATUS_DELETED && $model->is_user_profile_delete ? $class='dt-user-delete':null;
                    $model->status==\app\models\User::STATUS_DELETED && !$model->is_user_profile_delete ? $class='dt-admin-delete':null;
                    $model->status==\app\models\User::STATUS_ACTIVE && $model->validation_status==\app\models\User::VALIDATION_STATUS_SUCCESS ? $class='dt-active-validation':null;
                    return ['class'=>$class];
                }
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} '.(hasAccess('admin-user/update','{block-user} {unblock-user} {delete-user}')),
                'buttons' => [
                    'unblock-user' => function($url, $model, $key) {
                        if ($model->status!=\app\models\User::STATUS_BLOCKED) return '';

                        $url.='&pjaxForcePost=1';
                        $params = [
                            'title' => Yii::t('app', 'Vorrübergehend aktivieren'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to unblock this user?') . '",this,event)',
                            //'onclick' => 'if (!confirm("' .  . '")) {event.preventDefault();event.stopPropagation();}',
                        ];

                        //$params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-ok-circle"></span>',
                            $url,
                            $params
                        );
                    },
                    'block-user' => function($url, $model, $key) {
                        if ($model->status!=\app\models\User::STATUS_ACTIVE) return '';

                        $url.='&pjaxForcePost=1';
                        $params = [
                            'title' => Yii::t('app', 'Vorrübergehend deaktivieren'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to block this user?') . '",this,event)',
                            //'onclick' => 'if (!confirm("' .  . '")) {event.preventDefault();event.stopPropagation();}',
                        ];

                        //$params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-ban-circle"></span>',
                            $url,
                            $params
                        );
                    },
                    'delete-user' => function($url, $model, $key) {
                        if ($model->status==\app\models\User::STATUS_DELETED) return '';

                        $url.='&pjaxForcePost=1';
                        $params = [
                            'title' => Yii::t('app', 'Endgültig löschen'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to delete this item?') . '",this,event)',
                            //'onclick' => 'if (!confirm("' .  . '")) {event.preventDefault();event.stopPropagation();}',
                        ];

                        //$params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            $params
                        );
                    }
                ]
            ],
        ],
    ]); ?>

</div>