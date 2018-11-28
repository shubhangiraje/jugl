<?php

use yii\db\Schema;
use yii\db\Migration;

class m161205_124007_registration_process_changes extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `status` `status` enum('AWAITING_MEMBERSHIP_PAYMENT','ACTIVE','BLOCKED','DELETED','REGISTERED') COLLATE 'utf8mb4_unicode_ci' NULL AFTER `company_name`;
        ");

        $this->execute("
            update user set status='REGISTERED' where status='AWAITING_MEMBERSHIP_PAYMENT' ;
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `sex` `sex` enum('','M','F') COLLATE 'utf8_general_ci' NOT NULL AFTER `country_id`,
            CHANGE `first_name` `first_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `avatar_file_id`,
            CHANGE `last_name` `last_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `first_name`,
            CHANGE `status` `status` enum('REGISTERED','LOGINED_IN_APP','ACTIVE','BLOCKED','DELETED') COLLATE 'utf8_general_ci' NULL AFTER `company_name`,
            CHANGE `birthday` `birthday` date NULL AFTER `status`;
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `status` `status` enum('REGISTERED','LOGINED','ACTIVE','BLOCKED','DELETED') COLLATE 'utf8_general_ci' NULL AFTER `company_name`;
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `sex` `sex` enum('M','F') COLLATE 'utf8_general_ci' NULL AFTER `country_id`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `show_in_become_member` tinyint(1) NOT NULL DEFAULT '0' AFTER `spam_reports`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `show_in_become_member` (`show_in_become_member`);
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `packet` `packet` enum('STANDART','VIP') COLLATE 'utf8_general_ci' NULL AFTER `deleted_last_name`;
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `packet` `packet` enum('','STANDART','VIP') COLLATE 'utf8_general_ci' NULL AFTER `deleted_last_name`;
        ");
    }

    public function down()
    {
        echo "m161205_124007_registration_process_changes cannot be reverted.\n";

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
