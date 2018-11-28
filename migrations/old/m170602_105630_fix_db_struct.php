<?php

use yii\db\Migration;

class m170602_105630_fix_db_struct extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_feedback`
CHANGE `response` `response` varchar(4096) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `create_dt`;
        ");
    }

    public function down()
    {
        echo "m170602_105630_fix_db_struct cannot be reverted.\n";

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
