<?php

use yii\helpers\Html;
use app\components\AdminAsset;
use yii\bootstrap\NavBar;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AdminAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->id) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
NavBar::begin([
    'brandLabel' => Yii::$app->id,
    'brandUrl' => Url::to(['admin-site/index']),
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
    'innerContainerOptions' => ['class' => 'container container-nav']
]);

if (hasAccess('admin-admin/index')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Adminstratoren'),
                'active' => preg_match('%^admin-admin$%', $this->context->id),
                'items' => [
                    [
                        'label' => Yii::t('app', 'Admins'),
                        'url' => ['admin-admin/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Session log'),
                        'url' => ['admin-admin/session-log'],
                    ],
                    [
                        'label' => Yii::t('app', 'Action log'),
                        'url' => ['admin-admin/action-log'],
                    ]
                ]
            ],
        ]
    ]);
}

if (hasAccess('admin-user/index')) {

    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Users'),
                'active' => preg_match('%^admin-(user|spammer|registration-help-request)$%', $this->context->id),
                'items' => array_merge(
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Users'),
                        'url' => ['admin-user/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Spammers'),
                        'url' => ['admin-spammer/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Einladungskontingent'),
                        'url' => ['admin-registrations-limit/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Registration IPs'),
                        'url' => ['admin-registration-ip/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Blockierte Device IDs'),
                        'url' => ['admin-device/index'],
                    ]),
                    menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Deleted Users'),
                    'url' => ['admin-user/deleted'],
                    ]),
                    menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Registration Help Requests'),
                    'url' => ['admin-registration-help-request/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Midglied Werden'),
                        'url' => ['admin-invite-me/index'],
                    ])
                ),
            ]
        ]
    ]);
}

if (hasAccess('admin-user-validation/index')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Users Validation'),
                'url' => ['admin-user-validation/index'],
            ]
        ]
    ]);
}

if (hasAccess('admin-pay-out-request/index')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Payout Requests'),
                'url' => ['admin-pay-out-request/index'],
            ]
        ]
    ]);
}


if (hasAccess('admin-setting/index') || hasAccess('admin-broadcast/index')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Settings'),
                'active' => preg_match('%^admin-pay-(in|out)-packet|admin-setting$%', $this->context->id),
                'items' => array_merge(
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Payout Packets'),
                        'url' => ['admin-pay-out-packet/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Payin Packets'),
                        'url' => ['admin-pay-in-packet/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Settings'),
                        'url' => ['admin-setting/index'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Send message to all users'),
                        'url' => ['admin-broadcast/index'],
                    ]),
                    menuItemWithAccessCheck(['label' => 'Texte bearbeiten', 'items' => [
                        ['label' => 'Suchaufträge löschen', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::SEARCH_REQUEST_DELETE]],
                        ['label' => 'Werbung löschen', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::OFFER_DELETE]],
                        ['label' => 'Kategorie ändern', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::INTERESTS_UPDATE]],
                        ['label' => 'Werbung freigegeben', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::OFFER_VALIDATION_ACCEPTED]],
                        ['label' => 'Werbung abgelehnt', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::OFFER_VALIDATION_REJECTED]],
                        ['label' => 'Suchauftrag freigegeben', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::SEARCH_REQUEST_VALIDATION_ACCEPTED]],
                        ['label' => 'Suchauftrag abgelehnt', 'url' => ['admin-default-text/index', 'category'=>\app\models\DefaultText::SEARCH_REQUEST_VALIDATION_REJECTED]],
                    ]]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Interessen für Werbung'),
                        'url' => ['admin-interest/index','type'=>'OFFER'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Interessen für Suchaufträge'),
                        'url' => ['admin-interest/index','type'=>'SEARCH_REQUEST'],
                    ]),
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Kategorien für Forumbeiträge'),
                        'url' => ['admin-trollbox-category/index'],
                    ])
                ),
            ]
        ]
    ]);
}

if (hasAccess('admin-search-request/index') || hasAccess('admin-search-request/control')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Suchanzeige'),
                'items' =>array_merge(
                    menuItemWithAccessCheck([
                        'label' => Yii::t('app', 'Suchanzeige'),
                        'url' => ['admin-search-request/index'],
                    ]),
                    menuItemWithAccessCheck(                    [
                            'label' => Yii::t('app', 'Zu kontrollieren'),
                            'url' => ['admin-search-request/control'],
                        ]
                    )
                ),
            ]
        ]
    ]);
}

if (hasAccess('admin-offer/index') || hasAccess('admin-offer/control')) {
    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => Yii::t('app', 'Angebote'),
                'items' => array_merge(
                    menuItemWithAccessCheck([
                            'label' => Yii::t('app', 'Angebote'),
                            'url' => ['admin-offer/index'],
                    ]),
                    menuItemWithAccessCheck([
                            'label' => Yii::t('app', 'Zu kontrollieren'),
                            'url' => ['admin-offer/control'],
                    ])
                ),
            ]
        ]
    ]);
}

echo \kartik\nav\NavX::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        [
            'label' => Yii::t('app', 'Others'),
            'items' => array_merge(
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'News'),
                    'url' => ['admin-news/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Gruppenchat'),
                    'url' => ['admin-trollbox-message/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Fragen / Antworten'),
                    'url' => ['admin-faq/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'i-Informationen'),
                    'url' => ['admin-info/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Videos'),
                    'url' => ['admin-video/index'],
                ]),
				menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Advertising'),
                    'url' => ['admin-advertising/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Cash for Likes'),
                    'url' => ['admin-cfr-distribution/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Immobilien für Tokens'),
                    'url' => ['admin-token-deposit-guarantee/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Tokenanlagen'),
                    'url' => ['admin-token-deposit/index'],
                ]),
                menuItemWithAccessCheck([
                    'label' => Yii::t('app', 'Verifizierungsvideos'),
                    'url' => ['admin-video-identification/index'],
                ])
            )
        ]
    ]
]);

    echo \kartik\nav\NavX::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => Yii::t('app', 'Logout'),
                'url' => ['admin-site/logout'],
            ]
        ]
    ]);

    NavBar::end();

?>


<div class="<?=$this->params['fullWidth'] ? 'container-fluid admin-container-full-width':'container'?> admin-container">
    <?php
        echo yii\widgets\Breadcrumbs::widget([
            'homeLink'=>[
                'label'=>Yii::t('app','Home'),
                'url'=>['admin-admin/index']
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
    ?>
    <?=$content?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

