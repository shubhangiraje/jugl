<?php

use yii\db\Migration;

class m171129_112416_add_column_advertising extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
			ADD `click_interval` bigint(20) NULL DEFAULT '0' AFTER `status`;
        ");
    }

    public function down()
    {
        echo "m171129_112416_add_column_advertising cannot be reverted.\n";

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
