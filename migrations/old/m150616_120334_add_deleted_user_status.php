<?php

use yii\db\Schema;
use yii\db\Migration;

class m150616_120334_add_deleted_user_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `status` `status` enum('AWAITING_MEMBERSHIP_PAYMENT','ACTIVE','BLOCKED','DELETED') COLLATE 'utf8_general_ci' NULL AFTER `nick_name`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150616_120334_add_deleted_user_status cannot be reverted.\n";

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
