<?php

use yii\db\Migration;

class m171012_135905_add_column_advertising_table extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
            ADD `advertising_total_clicks` int(11) NULL DEFAULT '0' AFTER `advertising_total_views`;
        ");
    }

    public function down()
    {
        echo "m171012_135905_add_column_advertising_table cannot be reverted.\n";

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
