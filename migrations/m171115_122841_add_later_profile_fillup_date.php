<?php

use yii\db\Migration;

class m171115_122841_add_later_profile_fillup_date extends Migration
{
    public function up()
    {
		$this->execute("
			ALTER TABLE `user`
			ADD `later_profile_fillup_date` timestamp  NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `show_friends_invite_popup`
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
