<?php

use yii\db\Migration;

class m170120_163511_add_user_parent_got_bonus_flag extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `parent_got_registration_bonus` tinyint(1) NOT NULL DEFAULT '0';        
        ");

        $this->execute("
            update user set parent_got_registration_bonus=1 where packet in ('STANDART','VIP'); 
        ");
    }

    public function down()
    {
        echo "m170120_163511_add_user_parent_got_bonus_flag cannot be reverted.\n";

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
