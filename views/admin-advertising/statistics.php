<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Advertising statistics'),
        'url'=>['admin-advertising/statistics']
    ]
];
$this->params['fullWidth']=true;
?>

<div class="admin-statistics">
		
    <h1>	<?= Html::encode(Yii::t('app','Advertising statistics')) ?></h1>

</div>

<div class="row" style="margin-top: 30px">
	
		<?php 
		
		echo '	<div class="col-sm-4">
						<table class="table">
						 <thead>
							<tr>
								<th colspan="2" style="height:60px;">GESAMTAUSWERTUNG</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>Klicks Gesamt</td>
							<td>'.$model['advertising_all']['advertising_clicks_all'].'</td>
						</tr>
						<tr>
							<td>Ausgaben Gesamt (Jugl)</td>
							<td>'.$model['advertising_all']['advertising_outgoings_all_jugl'].' Jugl</td>
						</tr>
						<tr>
							<td>Ausgaben Gesamt (EURO)</td>
							<td>'.$model['advertising_all']['advertising_outgoings_all_eur'].' €</td>
						</tr>
						</tbody>
						</table>
					</div>';
		
		
		
			foreach($model['advertising_data'] as $key => $val){
				echo '
					<div class="col-sm-4">
						<table class="table">
						 <thead>
							<tr>
								<th colspan="2" style="height:60px;">'.$val['advertising_name'].'</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>Klicks Heute</td>
							<td>'.$val['advertising_clicks_today'].'</td>
						</tr>
						<tr>
							<td>Einnahmen Heute</td>
							<td>'.$val['advertising_earnings_today'].' €</td>
						</tr>
						<tr>
						<td></td>
						<td></td>
						</tr>
						<tr>
							<td>Klicks Gestern</td>
							<td>'.$val['advertising_clicks_yesterday'].'</td>
						</tr>
						<tr>
							<td>Einnahmen Gestern</td>
							<td>'.$val['advertising_earnings_yesterday'].'</td>
						</tr>
						<tr>
							<td>Ausgaben Gestern</td>
							<td>'.$val['advertising_outgoings_yesterday'].' €</td>
						</tr>
						<tr>
						<td></td>
						<td></td>
						</tr>
						<tr>
							<td>Einnahmen Gesamt</td>
							<td>'.$val['advertising_earnings_total'].'</td>
						</tr>
						<tr>
							<td>Ausgaben Gesamt</td>
							<td>'.$val['advertising_outgoings_total'].' €</td>
						</tr>
						
						</tbody>
						</table>
					</div>';
						


			}
		?>
	</div>