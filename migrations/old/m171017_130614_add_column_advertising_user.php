<?php

use yii\db\Migration;

class m171017_130614_add_column_advertising_user extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `advertising_type` varchar(255) NOT NULL AFTER `advertising_name`,
			ADD `user_bonus` decimal(14,2) NULL DEFAULT '0' AFTER `link`;
        ");
    }

    public function down()
    {
        echo "m171017_130614_add_column_advertising_user cannot be reverted.\n";

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
