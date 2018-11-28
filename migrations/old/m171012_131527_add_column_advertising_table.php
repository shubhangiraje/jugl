<?php

use yii\db\Migration;

class m171012_131527_add_column_advertising_table extends Migration
{	
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `advertising_total_views` int(11) NULL DEFAULT '0' AFTER `advertising_total_bonus`;
        ");
    }

    public function down()
    {
        echo "m171012_131527_add_column_advertising_table cannot be reverted.\n";

        return false;
    }
}
