<?="<?php\n";?>

namespace <?= $generator->ns ?>;

use Yii;

class <?=$className?> extends \<?= $generator->ns ?>\base\<?=$className."\n"?>
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
    <?php foreach ($labels as $name => $label): ?>
        <?= "'$name' => Yii::t('app','" . addslashes($label) . "'),\n" ?>
    <?php endforeach; ?>
    ];
    }
}
