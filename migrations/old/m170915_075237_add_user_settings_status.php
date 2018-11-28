<?php

use yii\db\Migration;

class m170915_075237_add_user_settings_status extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `user`
            ADD `allow_ad_auto_active` tinyint(1) NOT NULL DEFAULT '0' AFTER `allow_send_message_to_all_users`;
        ");
    }

    public function down()
    {
        echo "m170915_075237_add_user_settings_status cannot be reverted.\n";

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
