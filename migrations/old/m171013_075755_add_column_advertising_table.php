<?php

use yii\db\Migration;

class m171013_075755_add_column_advertising_table extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `advertising_position` varchar(255) NOT NULL AFTER `advertising_total_bonus`;
        ");
    }

    public function down()
    {
        echo "m171013_075755_add_column_advertising_table cannot be reverted.\n";

        return false;
    }
}
