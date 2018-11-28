<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_060653_add_auto_spam_block_setting extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('AUTO_SPAM_BLOCK_AFTER_REPORTS', 'Automatically block user after specified amount of spam reports', 1, '10');
        ");
    }

    public function down()
    {
        echo "m150818_060653_add_auto_spam_block_setting cannot be reverted.\n";

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
