<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_122420_registrations_limit extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `setting` SET
            `name` = 'STANDARD_FREE_REGISTRATIONS_LIMIT',
            `title` = 'STANDARD: Einladungskontingent',
            `type` = 1,
            `value` = '100'
            WHERE `name` = 'FREE_REGISTRATIONS_LIMIT' AND `name` = 'FREE_REGISTRATIONS_LIMIT' COLLATE utf8_bin;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('VIP_FREE_REGISTRATIONS_LIMIT',	'PREMIUM: Einladungskontingent',	'int',	'999999');
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `free_registrations_limit` int NULL,
            COMMENT='';
        ");

    }

    public function down()
    {
        echo "m151216_122420_registrations_limit cannot be reverted.\n";

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
