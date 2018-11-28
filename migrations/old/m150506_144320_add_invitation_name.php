<?php

use yii\db\Schema;
use yii\db\Migration;

class m150506_144320_add_invitation_name extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `invitation`
            CHANGE `address` `address` varchar(128) COLLATE 'utf8_general_ci' NOT NULL AFTER `status`,
            ADD `name` varchar(256) COLLATE 'utf8_general_ci' NOT NULL AFTER `address`;
        ");

        $this->execute("
            ALTER TABLE `invitation`
            CHANGE `name` `name` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `address`;
        ");

        $this->execute("
            update invitation set name=address
        ");
    }

    public function down()
    {
        echo "m150506_144320_add_invitation_name cannot be reverted.\n";

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
