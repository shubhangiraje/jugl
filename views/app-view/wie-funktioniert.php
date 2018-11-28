<?php


use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="content">
    <div class="container clearfix">
        <div class="page-title">
            <h1><?=Yii::t('app', 'So funktioniert Jugl')?></h1>
        </div>

        <div class="page-content how_does_it_work">
			<div class="how_does_it_work_btn">
				<a href="<?=Url::to(['@web/static/pdf/Das_ist_Jugl.net.pdf'])?>" class="btn btn-blue" target="_blank">Das ist Jugl.net - PDF</a>
				<a href="<?=Url::to(['@web/static/powerpoint/Das_ist_Jugl.net.pptx'])?>" class="btn btn-orange" download="Das_ist_Jugl.net.pptx">Das ist Jugl.net - Powerpoint</a>
			</div>

			<div class="youtube_video"><iframe width="560" height="315" src="https://www.youtube.com/embed/E4ORePMCacI" frameborder="0" allowfullscreen></iframe></div>
			<h3><?= Yii::t('app', 'Dein persönlicher Assistent in der Hosentasche!') ?></h3>
	 
			<p><?= Yii::t('app', 'Jugl.net beinhaltet einen Marktplatz mit einzigartigen Verdienstmöglichkeiten. Kinderleicht und profitabel!') ?>
			 
			<?= Yii::t('app', 'Spare Zeit - Lass Dir helfen, nutze Deine Zeit und Energie effektiver - für schöne Dinge. Die menschliche Suchmaschine ist einzigartig.') ?></p> 
			 
			<p><?= Yii::t('app', 'Was auch immer Dein Begehren ist, ob die günstigste Reise, die coolste Party oder der beste Handwerker. Lehn Dich zurück und lass es geschehen.') ?></p>
			 
			<p><b><?= Yii::t('app', 'Machen oder machen lassen – profitiere! Ob geschäftlich oder privat – Jugl.net ist allumfassend:') ?></b></p>
			
			<ul>
				<li><?= Yii::t('app', 'Alltagshilfe') ?></li>
				<li><?= Yii::t('app', 'Dienstleistungen') ?></li>
				<li><?= Yii::t('app', 'Networking') ?></li>
				<li><?= Yii::t('app', 'Geschäftskontakte') ?></li>
				<li><?= Yii::t('app', 'Soziales Netzwerk') ?></li>
			</ul>
			<br />
			<p><?= Yii::t('app', 'Jugl.net ist eine große wirtschaftliche und soziale Gemeinschaft, die sich gegenseitig hilft und fördert!') ?></p>
			 
			<p><?= Yii::t('app', '60.000 Helfer, jede erdenkliche Hilfe und Dienstleitung ist möglich. Die Jugler sind 24 Stunden an 7 Tagen der Woche für Dich da!') ?></p>
			 
			<p><?= Yii::t('app', 'So einzigartig wie Deine Nachfrage ist, sind die Lösungen Deiner persönlichen Helfer. Die Jugler wissen alles und haben auch auf schwierige Anfragen die passende Antwort!
			Geht nicht, gibt es nicht!') ?></p>
			 
			<p><?= Yii::t('app', 'Egal ob geschäftlich oder privat - bei Jugl.net kannst Du uneingeschränkt kostenlos annoncieren! Bring Dich und Dein Business nach vorn. Gewinne neue Kunden und nimm Aufträge an.') ?></p>
			 
			<p><?= Yii::t('app', 'Du kannst auch anderen Mitgliedern assistieren bzw. für sie recherchieren und dafür eine Provision erhalten (Vermittlungsbonus). Auch für´s Informieren über neue Produkte wirst Du bei uns belohnt (Werbebonus)!') ?></p>
			 
			<p><b><?= Yii::t('app', 'Deine Vorteile auf einen Blick:') ?></b></p>
			
			<ul>
				<li><?= Yii::t('app', 'Persönliche Assistenz 24/7 – 90.000 Helfer stark') ?></li>
				<li><?= Yii::t('app', 'Enorme Reichweite /Beliebt bei Netzwerkern') ?></li>
				<li><?= Yii::t('app', 'Umsatz steigern /Passiv-Einkommen aufbauen') ?></li>
				<li><?= Yii::t('app', 'Ortsunabhängig agieren') ?></li>
				<li><?= Yii::t('app', 'Weltweite Kontakte') ?></li>
				<li><?= Yii::t('app', 'Immer auf dem Laufendem über Neuheiten') ?></li>
				<li><?= Yii::t('app', 'Eine App für alles - Einzigartig und unerreicht!') ?></li>
			</ul>
			 <br />
			<p><?= Yii::t('app', 'Wir freuen uns darauf, mit Dir ein neues, gigantisches Netzwerk aufzubauen, bei dem nicht immer nur die Großen profitieren, sondern auch jeder einzelne User für sein Engagement belohnt wird.') ?></p>
			 
			<p><?= Yii::t('app', 'Je früher Du einsteigst, desto erfolgreicher kannst Du werden. ') ?>
			<?= Yii::t('app', 'Keine Abos! Keine Abzocke! Alles echt! - Einfach kostenlos ausprobieren.') ?></p>
			 
			<p><?= Yii::t('app', 'Für weitere Informationen sieh Dir bitte auch noch unser Video und die Präsentation an.') ?></p>
			<div class="jugl_pras_pdf">
				<object data="<?=Url::to(['@web/static/pdf/Das_ist_Jugl.net.pdf'])?>" type="application/pdf" style="width:100%;height:670px">
					<a href="<?=Url::to(['@web/static/pdf/Das_ist_Jugl.net.pdf'])?>">PDF laden</a>
				</object>
			</div>
			
			<div class="bottom-corner"></div>
        </div>
    </div>
</div>
