<?php

use yii\db\Migration;

class m171013_114015_add_column_advertising_table extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `provider` varchar(255) NOT NULL AFTER `advertising_total_bonus`,
			ADD `banner_height` varchar(255) NOT NULL AFTER `provider`,
			ADD `banner_width` varchar(255) NOT NULL AFTER `banner_height`;
        ");
    }

    public function down()
    {
        echo "m171013_114015_add_column_advertising_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
