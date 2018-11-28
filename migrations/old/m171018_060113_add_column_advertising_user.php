<?php

use yii\db\Migration;

class m171018_060113_add_column_advertising_user extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising_user`
            ADD `advertising_bonus` decimal(14,2) NULL DEFAULT '0' AFTER `user_id`;
        ");
    }

    public function down()
    {
        echo "m171018_060113_add_column_advertising_user cannot be reverted.\n";

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
