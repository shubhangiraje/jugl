<?php

use yii\db\Schema;
use yii\db\Migration;

class m160601_111438_add_sms_limit extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `sms_limit` int(11) NOT NULL DEFAULT '10' AFTER `closed_deals`,
            ADD `sms_sent` int(11) NOT NULL DEFAULT '0' AFTER `sms_limit`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160601_111438_add_sms_limit cannot be reverted.\n";

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
