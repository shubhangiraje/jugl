<?php

use yii\db\Schema;
use yii\db\Migration;

class m151204_140203_change_user_spam_report extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_spam_report`
ADD `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151204_140203_change_user_spam_report cannot be reverted.\n";

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
