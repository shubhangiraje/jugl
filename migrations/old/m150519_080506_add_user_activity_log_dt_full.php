<?php

use yii\db\Schema;
use yii\db\Migration;

class m150519_080506_add_user_activity_log_dt_full extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_user`
            ADD INDEX `online` (`online`);
        ");
    }

    public function down()
    {
        echo "m150519_080506_add_user_activity_log_dt_full cannot be reverted.\n";

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
