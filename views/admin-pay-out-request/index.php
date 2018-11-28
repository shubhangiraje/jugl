<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Payout Requests'),
        'url'=>['admin-pay-out-request/index']
    ]
];


$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Payout Requests')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'pay_out_method_num',
                'value'=>function($model) { return $model->getDefinedId(); },
                'options'=>['style'=>'width:120px;']
            ],
            'dt:date',
            [
                'attribute'=>'type',
                'value'=>function($model) { return $model->typeLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type',
                    $searchModel->getTypeList(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'attribute'=>'status',
                'value'=>function($model) { return $model->statusLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $searchModel->getStatusList(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'attribute'=>'payment_method',
                'value'=>function($model) { return $model->paymentMethodLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'payment_method',
                    $searchModel->getPaymentMethodList(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'attribute'=>'user.first_name',
                'label'=>'Nutzer',
                'value'=>function($model) {
                    return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);
                },
                'format'=>'raw',
                'filter'=>
                    Html::activeTextInput($searchModel,'user_name',['class'=>'form-control'])
            ],
            [
                'attribute'=>'user.packet',
                'value'=>function($model) { return $model->user->packet; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'packet',
                    \app\models\User::getPacketList(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            'jugl_sum',
            'user.balance',
            'user.balance_buyed',
            'user.balance_earned',
            'user.balance_token_deposit_percent',
            [
                'attribute'=>'currency_sum',
                'format'=>'html',
                'value'=>function($model) {return $model->currency_sum.'&euro;';}
            ],
            [
                'attribute'=>'payment_data',
                'label'=>Yii::t('app','Payment data'),
                'format'=>'html',
                'value'=>function($model) {
                    $code='<table>';

                    $dataModel=$model->payOutDataModel;
                    foreach($dataModel as $k=>$v) {
                        $code.='<tr><td style="text-align:right;"><nobr><b>'.$dataModel->getEncodedAttributeLabel($k).':&nbsp;</b></nobr></td><td style="text-align:left;"><nobr>'.Html::encode($v).'</nobr></td></tr>';
                    }

                    $code.='</table>';

                    return $code;
                }
            ],

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{accept} {decline} {process}',
                'buttons' => [
                    'accept' => function($url, $model, $key) {
                        if ($model->status==\app\models\PayOutRequest::STATUS_NEW && $model->jugl_sum<=$model->user->balance) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-ok" style="color:green;"></span>',
                                ['admin-pay-out-request/accept','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                [
                                    'title' => Yii::t('app', 'Accept Payout'),
                                    'data-confirm' => Yii::t('app', 'Do you really want to accept this payout?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0'
                                ]
                            );
                        }
                    },
                    'decline' => function($url, $model, $key) {
                        if ($model->status==\app\models\PayOutRequest::STATUS_NEW) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-remove" style="color:red;"></span>',
                                ['admin-pay-out-request/decline','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                [
                                    'title' => Yii::t('app', 'Decline Payout'),
                                    'data-confirm' => Yii::t('app', 'Do you really want to decline this payout?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0'
                                ]
                            );
                        }
                    },
                    'process' => function($url, $model, $key) {
                        if ($model->status==\app\models\PayOutRequest::STATUS_ACCEPTED) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-inbox"></span>',
                                ['admin-pay-out-request/process','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                [
                                    'title' => Yii::t('app', 'Mark payout as processed'),
                                    'data-confirm' => Yii::t('app', 'Mark payout as processed?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0'
                                ]
                            );
                        }
                    },
                ]
            ],
        ],
    ]); ?>

</div>