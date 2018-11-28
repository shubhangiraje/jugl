<?php

use yii\db\Migration;

class m180131_095553_add_advertising_colum extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `release_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `display_date`;
        ");
    }

    public function down()
    {
        echo "m180131_095553_add_advertising_colum cannot be reverted.\n";

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
