<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use app\components\User;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use kartik\widgets\Typeahead;
use yii\helpers\Url;
use kartik\select2\Select2;

use yii\bootstrap4\Modal;
use kartik\ActiveForm;
use kartik\date\DatePicker;

$this->title=Yii::t('app','Updating User').' "'.$model.'"';

$data=array(
'Abgabe ins Netzwerk'=>'Abgabe ins Netzwerk',
'Gewinn aus Netzwerk'=>'Gewinn aus Netzwerk',
'Werbebonus erhalten'=>'Werbebonus erhalten',
'Hat Dich zu jugl.net eingeladen'=>'Werbebonus erhalten',
'Werbebonus erhalten'=>'Werbebonus erhalten',
'Likes erhalten'=>'Likes erhalten',
'Tokenanlagen'=>'Tokenanlagen',
'Hast ein Video geschaut'=>'Hast ein Video geschaut',
'Extra-Cash'=>'Extra-Cash',
'Vermittlungsbonus erhalten'=>'Vermittlungsbonus erhalten',
'Vermittlungsbonus bezahlt'=>'Vermittlungsbonus bezahlt'
);

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Users'),
        'url'=>['admin-user/update']
    ],
    [
        'label'=>$this->title,
    ]
];?>



<div class="admin-update">
    <h1><?php echo Html::encode($this->title); ?></h1>
<?php //echo"<pre>";print_r($searchModelBalanceTokenLog);echo"</pre>";?>
    <?= 
    $this->render('_form', array(
            'model' => $model,
            'modelAddUserBalance'=>$modelAddUserBalance,
            'modelAddUserTokenBalance'=>$modelAddUserTokenBalance
    ));
    ?>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><?=Yii::t('app','Historie der Kontoaufladungen')?></a></li>
        <li role="presentation"><a href="#tab9" role="tab" data-toggle="tab"><?=Yii::t('app','Historie der Tokenkontoaufladungen')?></a></li>
        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><?=Yii::t('app','Spam reports')?></a></li>
        <?php if (Yii::$app->admin->identity->type==\app\models\Admin::TYPE_SUPERVISOR) { ?>
        <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><?=Yii::t('app','Transaktionsübersicht')?></a></li>
        <li role="presentation"><a href="#tab8" role="tab" data-toggle="tab"><?=Yii::t('app','Token Transaktionsübersicht')?></a></li>
        <?php } ?>
        <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><?=Yii::t('app','Werbung einstellen')?></a></li>
        <li role="presentation"><a href="#tab5" role="tab" data-toggle="tab"><?=Yii::t('app','Historie der Profiländerungen')?></a></li>
        <li role="presentation"><a href="#tab6" role="tab" data-toggle="tab"><?=Yii::t('app','Teamleaderbewertungen anzeigen')?></a></li>
        <li role="presentation"><a href="#tab7" role="tab" data-toggle="tab"><?=Yii::t('app','Handelsbewertung')?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1">
            <h1><?=Yii::t('app','Historie der Kontoaufladungen')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $userBalanceModProvider,
                'columns' => [
                    [
                        'attribute'=>'balanceLog.dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->balanceLog->dt));}
                    ],
                    [
                        'attribute'=>'admin.email',
                        'label'=>Yii::t('app','Admin'),
                    ],
                    [
                        'attribute'=>'balanceLog.sum',
                        'label'=>Yii::t('app','Betrag')
                    ],
                    'comments'
                ]
            ]); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab9">
            <h1><?=Yii::t('app','Historie der Tokenkontoaufladungen')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $userBalanceTokenModProvider,
                'columns' => [
                    [
                        'attribute'=>'balanceTokenLog.dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->balanceTokenLog->dt));}
                    ],
                    [
                        'attribute'=>'admin.email',
                        'label'=>Yii::t('app','Admin'),
                    ],
                    [
                        'attribute'=>'balanceTokenLog.sum',
                        'label'=>Yii::t('app','Betrag')
                    ],
                    'comments'
                ]
            ]); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab2">
            <h1><?=Yii::t('app','Spam reports')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $spamReportsProvider,
                'rowOptions' => function ($model, $index, $widget, $grid){
                    return $model->is_active ? []:['class'=>'striketrough'];
                },
                'columns' => [
                    [
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                    ],
                    [
                        'attribute'=>'user.email',
                        'format'=>'raw',
                        'value'=>function($model) {return Html::a($model->user->name.' '.$model->user->email,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);},
                        'label'=>Yii::t('app','Reporter'),
                    ],
                    [
                        'attribute'=>'object',
                        'format'=>'raw',
                        'label'=>Yii::t('app','Objekte')
                    ],
                    'comment',
                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{activate} {deactivate}',
                        'buttons' => [
                            'activate' => function($url, $model, $key) {
                                if (!$model->is_active) {
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-ok" style="color:green;"></span>',
                                        ['admin-user/activate-spam-report','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                        [
                                            'title' => Yii::t('app', 'Activate Spam Report'),
                                            //'data-confirm' => Yii::t('app', 'Do you really want to accept this payout?'),
                                            //'data-method' => 'post',
                                            //'data-pjax' => '0'
                                        ]
                                    );
                                }
                            },
                            'deactivate' => function($url, $model, $key) {
                                if ($model->is_active) {
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-remove" style="color:red;"></span>',
                                        ['admin-user/deactivate-spam-report','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                        [
                                            'title' => Yii::t('app', 'Deactivate Spam Report'),
                                            //'data-confirm' => Yii::t('app', 'Do you really want to decline this payout?'),
                                            //'data-method' => 'post',
                                            //'data-pjax' => '0'
                                        ]
                                    );
                                }
                            },
                        ]
                    ],
                ]
            ]); ?>
        </div>
        <?php if (Yii::$app->admin->identity->type==\app\models\Admin::TYPE_SUPERVISOR) { ?>
        <div role="tabpanel" class="tab-pane" id="tab3">
            <h1><?=Yii::t('app','Transaktionsübersicht')?></h1>
               <?php Pjax::begin();?>
            <?=\app\components\GridView::widget([
                'dataProvider' => $balanceLogProvider,
                'filterModel' => $searchModelBalanceLog,
                'pjax' => false,     
                'columns' => [
                    [
                        'attribute'=>'dt',
                        'label'=>yii::t('app','Datum'),
                        'format'=>'date',
                        'filter'=>
                         \kartik\date\DatePicker::widget([
                                'model'=>$searchModelBalanceLog,             
                                'attribute'=>'regist_dt_from',
                                'readonly'=>true,                      
                                'pluginOptions'=>[
                                //'clearBtn'=>true,
                                'autoclose' => true,
                                'todayHighlight' => true,
                                'endDate' => "0d"
                                ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','From').'</span>{input}{picker}{remove}'

                         ]).
                         \kartik\date\DatePicker::widget([
                        'model'=>$searchModelBalanceLog,
                        'attribute'=>'regist_dt_to',
                        'readonly'=>true,
                        'pluginEvents' => [             
                                    "changeDate" => "function(e) {   
                                            var fromdate = $('#balancetokenlogsearch-regist_dt_from').val().split('.');
                                            var f = new Date(fromdate[2], fromdate[1] - 1, fromdate[0]);
                                        
                                            var todate  =  $('#balancetokenlogsearch-regist_dt_to').val().split('.');
                                            var t = new Date(todate[2], todate[1] - 1, todate[0]);
                                            if(t < f)
                                            {
                                                alert('Bis datum muss größer sein als vom datum');
                                                $('#balancetokenlogsearch-regist_dt_to').val('');
                                            }                                           
                                             }",   
                                           ],
                        'pluginOptions'=>[
                           // 'clearBtn'=>true,
                            'autoclose' => true,
                            'todayHighlight' => true,
                            'endDate' => "0d"
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','To').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','To').'</span>{input}{picker}{remove}'
                        ]),
                  
                        'options'=>['style'=>'width: 150px;']
                           // 'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                        ],
                   
                    [
                        'attribute'=>'username_and_email',
                        'format'=>'raw',
                        'value'=>function($model) {return Html::a($model->initiatorUser->name.' '.$model->initiatorUser->email,['admin-user/update','id'=>$model->initiatorUser->id]);},
                        'label'=>Yii::t('app','Eingang durch'),
                        'options' => ['style' => 'width: 22%;']
                    ],
                    
            		[
			                'attribute'=>'type',
			                'value'=>function($model) { return $model->typeLabel; },
			                'label'=>Yii::t('app','Summe'),
			                'filter' => Html::activeDropDownList(
			                 $searchModelBalanceLog,
			                 'type',
			                 $searchModelBalanceLog->getTypeList(),		                
			                 ['class' => 'form-control']                 
			                ),
			                'options'=>['style'=>'width: 25%;']         
          		    ], 
          		    [
                                'attribute'=>'comment',
                                'label'=>Yii::t('app','Kommentar'),
                                'filter' => Html::activeDropDownList(
                                 $searchModelBalanceLog,
                                 'comment',
                                 $data,                     
                                 ['class' => 'form-control','prompt' => 'Wählen Sie Alle']                 
                                ),
                                'value'=>function($model) {
                                    $str = $model->comment;
                                    $str = preg_replace_callback('/\[([a-zA-Z]+):(\d+)*\]/', function($matches) {
                                        $link = '';
                                        switch (strtolower($matches[1])) {
                                            case 'offer':
                                                $link = '<a href="'.\yii\helpers\Url::to(['admin-offer/update','id'=>$matches[2]]).'">';
                                                break;
                                            case 'searchrequest':
                                                $link = '<a href="'.\yii\helpers\Url::to(['admin-search-request/update','id'=>$matches[2]]).'">';
                                                break;
                                        }
                                        return $link;
                                    }, $str);
                                    $str = preg_replace('/\[\/[a-zA-Z]+\]/', '</a>', $str);
                                    return $str;
                                },        
                            ]
                  
                  /* [
                        'attribute'=>'stat_count',
                    ],
                    [
                        'attribute'=>'stat_sum_plus',
                    ],
                    [
                        'attribute'=>'stat_sum_minus',
                    ],*/
                ]
            ]);  Pjax::end(); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab8">
                <h1><?=Yii::t('app','Token Transaktionsübersicht')?></h1>
                   <?php Pjax::begin();?>
                <?=\app\components\GridView::widget([
                    'dataProvider' => $balanceTokenLogProvider,
                    'filterModel' => $searchModelBalanceTokenLog,
                    'pjax' => false,      
				    'columns' => [
                        [
                        'attribute'=>'dt',
                        'label'=>yii::t('app','Datum'),
	                    'format'=>'date',
		                'filter'=>
		                 \kartik\date\DatePicker::widget([
		                        'model'=>$searchModelBalanceTokenLog,             
		                        'attribute'=>'registration_dt_from',
		                        'readonly'=>true,                      
                       	 		'pluginOptions'=>[
                        		//'clearBtn'=>true,
                        		'autoclose' => true,
								'todayHighlight' => true,
								'endDate' => "0d"
                        		],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','From').'</span>{input}{picker}{remove}'

                         ]).
		                 \kartik\date\DatePicker::widget([
                        'model'=>$searchModelBalanceTokenLog,
                        'attribute'=>'registration_dt_to',
                        'readonly'=>true,
                        'pluginEvents' => [				
    								"changeDate" => "function(e) {   
    										var fromdate = $('#balancetokenlogsearch-registration_dt_from').val().split('.');
											var f = new Date(fromdate[2], fromdate[1] - 1, fromdate[0]);
										
    										var todate  =  $('#balancetokenlogsearch-registration_dt_to').val().split('.');
    										var t = new Date(todate[2], todate[1] - 1, todate[0]);
    										if(t < f)
    										{
    											alert('Bis datum muss größer sein als vom datum');
    											$('#balancetokenlogsearch-registration_dt_to').val('');
    										}   										
    								         }",   
								           ],
                        'pluginOptions'=>[
                           // 'clearBtn'=>true,
                        	'autoclose' => true,
							'todayHighlight' => true,
							'endDate' => "0d"
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','To').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','To').'</span>{input}{picker}{remove}'
                        ]),
                  
                        'options'=>['style'=>'width: 150px;']
                           // 'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                        ],
                        
                        [
                            'attribute'=>'username_and_email',
                            'format'=>'raw',
                            'value'=>function($model) {return Html::a($model->initiatorUser->name.' '.$model->initiatorUser->email,['admin-user/update','id'=>$model->initiatorUser->id]);},
                            'label'=>Yii::t('app','Eingang durch'),
                            'options' => ['style' => 'width: 22%;']
                        ],
                        [
			                'attribute'=>'type',
			                'label'=>yii::t('app','Summe'),
			                'value'=>function($model) { return $model->typeLabel; },
			                'filter' => Html::activeDropDownList(
			                 $searchModelBalanceTokenLog,
			                 'type',
			                 $searchModelBalanceTokenLog->getTypeList(),		                
			                 ['class' => 'form-control']                 
	                       // 'value'=>function($model) {return 
			                ),
			                'options'=>['style'=>'width: 25%;']         
          				], 
          			
                        [
                                'attribute'=>'comment',
                                'label'=>Yii::t('app','Kommentar'),
                                'filter' => Html::activeDropDownList(
                                 $searchModelBalanceTokenLog,
                                 'comment',
                                 $data,                     
                                 ['class' => 'form-control','prompt' => 'Wählen Sie Alle']                 
                                ),
                                'value'=>function($model) {
                                    $str = $model->comment;
                                    $str = preg_replace_callback('/\[([a-zA-Z]+):(\d+)*\]/', function($matches) {
                                        $link = '';
                                        switch (strtolower($matches[1])) {
                                            case 'offer':
                                                $link = '<a href="'.\yii\helpers\Url::to(['admin-offer/update','id'=>$matches[2]]).'">';
                                                break;
                                            case 'searchrequest':
                                                $link = '<a href="'.\yii\helpers\Url::to(['admin-search-request/update','id'=>$matches[2]]).'">';
                                                break;
                                        }
                                        return $link;
                                    }, $str);
                                    $str = preg_replace('/\[\/[a-zA-Z]+\]/', '</a>', $str);
                                    return $str;
                                },        
                            ]

          		    /*,
          				
      
          				[
          					'attribute'=>'comment',
  							'filter'=>							
						    Typeahead::widget([
  						    'name' => 'comment',
   						    'options' => ['placeholder' => 'bitte eintreten'],
   							'pluginOptions' => ['highlight' => true],
    						'dataset' => [
    		    						[
          									  'local' => $commentdata,
          									//  'limit' => 20
       									]
   									 ]
							]),
						    'value'=>function($model) {
                                $str = $model->comment;
                                $str = preg_replace_callback('/\[([a-zA-Z]+):(\d+)*\]/', function($matches) {
                                    $link = '';
                                    switch (strtolower($matches[1])) {
                                        case 'offer':
                                            $link = '<a href="'.\yii\helpers\Url::to(['admin-offer/update','id'=>$matches[2]]).'">';
                                            break;
                                        case 'searchrequest':
                                            $link = '<a href="'.\yii\helpers\Url::to(['admin-search-request/update','id'=>$matches[2]]).'">';
                                            break;
                                    }
                                    return $link;
                                }, $str);
                                $str = preg_replace('/\[\/[a-zA-Z]+\]/', '</a>', $str);
                                return $str;
                            },
                            'format'=>'raw',     
          				],
                       
                       /* [
                            'attribute'=>'stat_count',
                        ],
                        [
                            'attribute'=>'stat_sum_plus',
                        ],
                        [
                            'attribute'=>'stat_sum_minus',
                        ],*/
                    ]
                ]);  Pjax::end();  ?>
            </div> <?php } ?>
        

        <div role="tabpanel" class="tab-pane" id="tab4">
            <h1>
                <?php if (hasCurrentActionPostAccess()) { ?>
                    <?= Html::a(Yii::t('app', 'Neue Werbung erfassen'), ['admin-offer/create','user_id'=>$model->id], ['class' => 'btn btn-success pull-right']) ?>
                <?php } ?>
                <?=Yii::t('app','Werbung einstellen')?>
            </h1>
            <?= \app\components\GridView::widget([
                'dataProvider' => $offersProvider,

                'pjax'=>false,
                'columns' => [
                    [
                        'attribute'=>'status',
                        'value'=>function($model) { return $model->statusLabel; },
                    ],
                    [
                        'label'=>Yii::t('app','Kategorie'),
                        'value'=>function($model) {return $model->offerInterests[0]->level1Interest->title;},
                    ],
                    [
                        'attribute'=>'create_dt',
                        'format'=>'date',
                        'options'=>['style'=>'width: 150px;']
                    ],
                    [
                        'attribute'=>'active_till',
                        'format'=>'date',
                        'options'=>['style'=>'width: 150px;']
                    ],
                    'title',
                    'view_bonus',
                    'view_bonus_total',
                    'view_bonus_used',
                    [
                        'class' => 'app\components\ActionColumn',
                        'controller' => 'admin-offer',
                        'template' => '{update}',
                    ]
                ],
            ]); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="tab5">
            <h1><?=Yii::t('app','Historie der Profiländerungen')?></h1>
            <?= \app\components\GridView::widget([
                'dataProvider' => $userModifyLogProvider,
                'columns' => [

                    [
                        'attribute'=>'modify_dt',
                        'filter'=>false,
                        'value'=>function($model) {return new \app\components\EDateTime($model->modify_dt);},
                        'options'=>['style'=>'width: 250px;']

                    ],
                    [
                        'attribute' => 'description',
                        'value' => function($model) {
                            return '<div style="white-space: pre-wrap; text-align: left;">'.$model->description.'</div>';
                        },
                        'format'=>'raw',
                        'enableSorting' => false,
                    ]

                ],
            ]); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="tab6">
            <h1><?=Yii::t('app','Teamleaderbewertungen anzeigen')?></h1>

            <?= \app\components\GridView::widget([
                'dataProvider' => $userTeamFeedbackDataProvider,
                'columns' => [
                    [
                        'attribute'=>'create_dt',
                        'filter'=>false,
                        'value'=>function($model) {return (new \app\components\EDateTime($model->create_dt));},
                        'options'=>['style'=>'width: 150px;']
                    ],
                    [
                        'attribute'=>'second_user_id',
                        'value'=>function($model) {
                            return Html::a($model->secondUser->name,['admin-user/update','id'=>$model->second_user_id], ['data-pjax'=>0]);
                        },
                        'format'=>'raw'
                    ],
                    [
                        'attribute'=>'rating',
                        'value'=>function($model) {
                            return '<div class="star-rating"><span style="width:'.$model->rating.'%"></span></div>';
                        },
                        'format'=>'raw',
                        'options'=>['style'=>'width: 120px;']
                    ],
                    [
                        'attribute'=>'feedback',
                        'value'=>function($model) {
                            return '<div class="description-break-word" style="width: 300px">'.$model->feedback.'<div>';
                        },
                        'format'=>'raw'
                    ],
                    [
                        'attribute'=>'response',
                        'value'=>function($model) {
                            return '<div class="description-break-word" style="width: 300px">'.$model->response.'<div>';
                        },
                        'format'=>'raw'
                    ],
                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{edit-feedback} {delete-feedback}',
                        'buttons'=>[
                            'edit-feedback' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Update'),
                                    'class'=>'action-btn',
                                    'data-pjax'=>0
                                ];
                                $url = \yii\helpers\Url::to(['/admin-user-team-feedback/update', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $params);

                            },
                            'delete-feedback' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Delete'),
                                    'onclick' => 'if (!confirm("'.Yii::t('app','Do you really want to delete this item?').'")) {event.preventDefault();event.stopPropagation();}',
                                    'class'=>'action-btn'
                                ];
                                $url = \yii\helpers\Url::to(['/admin-user-team-feedback/delete', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url.'&pjaxForcePost=1', $params);

                            }
                        ],
                        'options'=>['style'=>'width: 70px; text-align: center']
                    ],
                ],
            ]); ?>

        </div>
        <div role="tabpanel" class="tab-pane" id="tab7">
            <h1><?=Yii::t('app','Handelsbewertung')?></h1>

            <?= \app\components\GridView::widget([
                'dataProvider' => $userFeedbackDataProvider,
                'columns' => [
                    [
                        'attribute'=>'create_dt',
                        'filter'=>false,
                        'value'=>function($model) {return (new \app\components\EDateTime($model->create_dt));},
                        'options'=>['style'=>'width: 150px;']
                    ],
                    [
                        'attribute'=>'second_user_id',
                        'value'=>function($model) {
                            return Html::a($model->secondUser->name,['admin-user/update','id'=>$model->second_user_id], ['data-pjax'=>0]);
                        },
                        'format'=>'raw'
                    ],
                    [
                        'attribute'=>'rating',
                        'value'=>function($model) {
                            return '<div class="star-rating"><span style="width:'.$model->rating.'%"></span></div>';
                        },
                        'format'=>'raw',
                        'options'=>['style'=>'width: 120px;']
                    ],
                    [
                        'attribute'=>'feedback',
                        'value'=>function($model) {
                            return '<div class="description-break-word" style="width: 300px">'.$model->feedback.'<div>';
                        },
                        'format'=>'raw'
                    ],
                    [
                        'attribute'=>'response',
                        'value'=>function($model) {
                            return '<div class="description-break-word" style="width: 300px">'.$model->response.'<div>';
                        },
                        'format'=>'raw'
                    ],
                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{edit-feedback} {delete-feedback}',
                        'buttons'=>[
                            'edit-feedback' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Update'),
                                    'class'=>'action-btn',
                                    'data-pjax'=>0
                                ];
                                $url = \yii\helpers\Url::to(['/admin-user-feedback/update', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $params);

                            },
                            'delete-feedback' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Delete'),
                                    'onclick' => 'if (!confirm("'.Yii::t('app','Do you really want to delete this item?').'")) {event.preventDefault();event.stopPropagation();}',
                                    'class'=>'action-btn'
                                ];
                                $url = \yii\helpers\Url::to(['/admin-user-feedback/delete', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url.'&pjaxForcePost=1', $params);

                            }
                        ],
                        'options'=>['style'=>'width: 70px; text-align: center']
                    ],
                ],
            ]); ?>

        </div>

    </div>
</div>
