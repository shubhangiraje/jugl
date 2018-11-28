<?php

use yii\db\Schema;
use yii\db\Migration;

class m150706_140039_new_user_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `company_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `nick_name`,
            ADD `visibility_birthday` enum('none','friends','all') NOT NULL DEFAULT 'none' AFTER `birthday`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150706_140039_new_user_fields cannot be reverted.\n";

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
