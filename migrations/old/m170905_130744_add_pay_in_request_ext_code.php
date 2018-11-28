<?php

use yii\db\Migration;

class m170905_130744_add_pay_in_request_ext_code extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            ADD `ext_code` varchar(32) COLLATE 'utf8_general_ci' NULL;
        ");

        $this->execute("
            ALTER TABLE `pay_in_request`
            ADD INDEX `ext_code` (`ext_code`);        
        ");
    }

    public function down()
    {
        echo "m170905_130744_add_pay_in_request_ext_code cannot be reverted.\n";

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
