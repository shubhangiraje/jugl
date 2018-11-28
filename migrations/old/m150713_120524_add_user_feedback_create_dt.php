<?php

use yii\db\Schema;
use yii\db\Migration;

class m150713_120524_add_user_feedback_create_dt extends Migration
{
    public function up()
    {
    	$this->execute("
            ALTER TABLE `user_feedback`
			ADD `create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
        ");
    }

    public function down()
    {
        echo "m150713_120524_add_user_feedback_create_dt cannot be reverted.\n";

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
