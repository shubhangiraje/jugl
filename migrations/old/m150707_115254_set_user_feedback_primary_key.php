<?php

use yii\db\Schema;
use yii\db\Migration;

class m150707_115254_set_user_feedback_primary_key extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_feedback`
            CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150707_115254_set_user_feedback_primary_key cannot be reverted.\n";

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
