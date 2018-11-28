<div class="close" ng-click="modalService.hide()"></div>
<div class="content">
    <div class="lang-popup-box">
        <form action="<?= \yii\helpers\Url::to(['/site/set-language']) ?>" method="post">
            <div class="field-lang">
                <input class="lang en" name="language" type="submit" value="en"/>
                <label>en</label>
            </div>
            <div class="field-lang">
                <input class="lang de" name="language" type="submit" value="de"/>
                <label>de</label>
            </div>
            <div class="field-lang">
                <input class="lang ru" name="language" type="submit" value="ru"/>
                <label>ru</label>
            </div>
        </form>
    </div>
</div>