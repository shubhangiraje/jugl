<?php

use yii\db\Migration;

class m171013_072000_add_facebook_id_to_user_table extends Migration
{
    public function up()
    {
		$this->execute("
			ALTER TABLE `user`
			ADD `facebook_id` bigint(30) NULL DEFAULT NULL AFTER `id`
			;
		");
    }
    

    public function down()
    {
        echo "m171013_072000_add_facebook_id_to_user_table cannot be reverted.\n";

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
