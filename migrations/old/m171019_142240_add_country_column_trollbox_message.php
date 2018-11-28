<?php

use yii\db\Migration;

class m171019_142240_add_country_column_trollbox_message extends Migration
{
    public function up()
    {
		$this->execute("
				ALTER TABLE `trollbox_message`
				ADD `country` int(3) NULL DEFAULT NULL AFTER `user_id`
				;
		");
		$this->execute("
				UPDATE `trollbox_message` 
				SET `country` = 64 
				;
		");
    
    }

    public function down()
    {
        echo "m171019_142240_add_country_column_trollbox_message cannot be reverted.\n";

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
