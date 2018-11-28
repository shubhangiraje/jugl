<?php

use yii\db\Schema;
use yii\db\Migration;

class m151019_120842_add_user_backup_file extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `delete_backup` mediumtext NULL
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `deleted_dt` timestamp NULL AFTER `stat_offers_view_buy_ratio`,
            CHANGE `delete_backup` `deleted_backup` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `deleted_dt`,
            ADD `deleted_email` varchar(128) COLLATE 'utf8_general_ci' NULL,
            ADD `deleted_first_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `deleted_email`,
            ADD `deleted_last_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `deleted_first_name`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151019_120842_add_user_backup_file cannot be reverted.\n";

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
