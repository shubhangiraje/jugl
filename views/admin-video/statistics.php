<?php
use app\models\Video;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\GridView;
use app\components\EDateTime;
use app\components\ActiveForm;

$this->params['fullWidth']=true;

$this->registerCssFile("@web/static/admin/datatables.min.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],   
]);

$this->registerJsFile(
		'@web/static/admin/datatables.min.js',
		['depends' => [\yii\web\JqueryAsset::className()]]
		);


$this->title=Yii::t('app','Video statistics');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Video'),
        'url'=>['admin-video/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>
 

<!--<script src="https://code.highcharts.com/highcharts.js"></script>-->
<!--<script src="https://code.highcharts.com/modules/exporting.js"></script>-->



<h1><?= Html::encode(Yii::t('app','Video Statistik')) ?></h1>

<table id="video_table"></table>


 <?php

 $this->registerJs(
    "
    jQuery('#video_table').DataTable( {
        data: ".$data.",
		'order':[3,'desc'],
        columns: [
            { title: 'Kategorie','defaultContent':'0' },
            { title: 'Videotitel','defaultContent':'0' },
            { title: 'Klicks Gesamt','defaultContent':'0' },
            { title: 'Klicks Monat','defaultContent':'0' },
            { title: 'Klicks letzter Monat','defaultContent':'0'},
            { title: 'Klicks Heute ','defaultContent':'0' },
            { title: 'Klicks Gestern ' ,'defaultContent':'0'},
            { title: 'Bonus Gesamt','defaultContent':'0' },
            { title: 'Bonus Monat','defaultContent':'0' },
            { title: 'Bonus letzter Monat','defaultContent':'0'},
            { title: 'Bonus Heute ','defaultContent':'0' },
            { title: 'Bonus Gestern ','defaultContent':'0' },
           
        ],
		'language': {
            'lengthMenu': '_MENU_ Datensätze pro Seite',
            'zeroRecords': 'Kein Eintrag vorhanden',
            'info': 'Zeige Seite _PAGE_ von _PAGES_',
            'infoEmpty': 'Keine Datensätze verfügbar',
            'infoFiltered': '(gefiltert aus _MAX_ total Datensätzen)',
			'search':'suchen',
			'paginate':{
				'previous':'zurück',
				'next':'vor'
			}
        }
    } );
"
);
?>
	

