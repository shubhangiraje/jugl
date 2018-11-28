<?php

use yii\db\Schema;
use yii\db\Migration;

class m160212_133159_auction extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `type` `type` enum('STANDARD','ADS','AUCTION') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'STANDARD' AFTER `create_dt`,
            ADD `notify_if_price_bigger` decimal(14,2) NULL AFTER `price`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            ADD `bet_price` decimal(14,2) NULL AFTER `status`,
            ADD `bet_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `bet_price`,
            ADD `bet_active_till` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `bet_dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            CHANGE `type` `type` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `create_dt`,
            COMMENT='';
        ");

        $this->execute("
          update offer set type='AD' where type='ADS'
        ");

        $this->execute("
          update offer set type='AUCTION' where type='STANDARD';
        ");

        $this->execute("
          update offer set type='AUTOSELL' where auto_accept=1;
        ");

        $this->execute("
            ALTER TABLE `offer`
            CHANGE `type` `type` enum('AUCTION','AD','AUTOSELL') COLLATE 'utf8_general_ci' NOT NULL AFTER `create_dt`,
            DROP `auto_accept`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            ADD `bet_period` varchar(32) NULL AFTER `bet_dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            ADD `bet_modifications` int NOT NULL DEFAULT '0',
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            CHANGE `bet_modifications` `modifications` int(11) NOT NULL DEFAULT '0' AFTER `user_feedback_id`,
            COMMENT='';
        ");

        $this->execute("
            CREATE TABLE `offer_request_modification` (
              `id` bigint NOT NULL,
              `offer_request_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL,
              `price` decimal(14,2) NOT NULL,
              FOREIGN KEY (`offer_request_id`) REFERENCES `offer_request` (`id`)
            ) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';
        ");

        $this->execute("
            ALTER TABLE `offer_request_modification`
            CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
            COMMENT='';
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('SELLBONUS_SELLER_PARENTS_PERCENT', 'Kaufbonus-Provision', 2, '3');
        ");
    }

    public function down()
    {
        echo "m160212_133159_auction cannot be reverted.\n";

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
