<?php

use yii\db\Migration;

class m171212_082135_add_column_advertising extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `advertising`
			ADD `popup_interval` int(11) NULL DEFAULT '45' AFTER `click_interval`,
			ADD `display_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `popup_interval`,
			ADD `country_id` int(11) NULL DEFAULT '0' AFTER `popup_interval`;
        ");
    }

    public function down()
    {
        echo "m171212_082135_add_column_advertising cannot be reverted.\n";

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
