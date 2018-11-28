<?php

use yii\db\Schema;
use yii\db\Migration;

class m150623_123032_add_user_rating extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `rating` tinyint NOT NULL DEFAULT '0',
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150623_123032_add_user_rating cannot be reverted.\n";

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
