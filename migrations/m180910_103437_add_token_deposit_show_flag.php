<?php

use yii\db\Migration;

class m180910_103437_add_token_deposit_show_flag extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `token_deposit_guarantee`
            ADD `show` tinyint(1) NOT NULL DEFAULT '1';
        ");
    }

    public function down()
    {
        echo "m180910_103437_add_token_deposit_show_flag cannot be reverted.\n";

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
