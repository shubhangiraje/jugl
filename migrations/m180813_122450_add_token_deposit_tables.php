<?php

use yii\db\Migration;

class m180813_122450_add_token_deposit_tables extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `token_deposit_guarantee` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `title` varchar(256) NOT NULL,
              `description` mediumtext NOT NULL,
              `price` decimal(10,2) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->execute("
            CREATE TABLE `token_deposit` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `sum` decimal(10,2) NOT NULL,
              `period_months` int(11) NOT NULL,
              `contribution_percentage` decimal(10,2) NOT NULL,
              `created_at` timestamp NULL DEFAULT NULL,
              `completion_dt` timestamp NULL DEFAULT NULL,
              `token_deposit_guarantee_id` bigint(20) NOT NULL,
              `status` enum('ACTIVE','COMPLETED') NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `token_deposit_guarantee_id` (`token_deposit_guarantee_id`),
              KEY `completion_dt` (`completion_dt`),
              CONSTRAINT `token_deposit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `token_deposit_ibfk_2` FOREIGN KEY (`token_deposit_guarantee_id`) REFERENCES `token_deposit_guarantee` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->execute("
            CREATE TABLE `token_deposit_guarantee_file` (
              `token_deposit_guarantee_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` bigint(20) DEFAULT NULL,
              PRIMARY KEY (`token_deposit_guarantee_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `token_deposit_guarantee_file_ibfk_1` FOREIGN KEY (`token_deposit_guarantee_id`) REFERENCES `token_deposit_guarantee` (`id`),
              CONSTRAINT `token_deposit_guarantee_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TOKEN_DEPOSIT_PERCENT_12_MONTHS',	'Token deposit: percent for 1 year deposit',	'float',	'5'),
            ('TOKEN_DEPOSIT_PERCENT_24_MONTHS',	'Token deposit: percent for 2 year deposit',	'float',	'10'),
            ('TOKEN_DEPOSIT_PERCENT_36_MONTHS',	'Token deposit: percent for 3 year deposit',	'float',	'15');
        ");

        $this->execute("
            ALTER TABLE `token_deposit_guarantee`
            CHANGE `title` `title_de` varchar(256) COLLATE 'utf8_general_ci' NOT NULL AFTER `id`,
            ADD `title_en` varchar(256) COLLATE 'utf8_general_ci' NOT NULL AFTER `title_de`,
            ADD `title_ru` varchar(256) COLLATE 'utf8_general_ci' NOT NULL AFTER `title_en`,
            CHANGE `description` `description_de` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `title_ru`,
            ADD `description_en` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `description_de`,
            ADD `description_ru` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `description_en`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit_guarantee`
            CHANGE `title_en` `title_en` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `title_de`,
            CHANGE `title_ru` `title_ru` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `title_en`,
            CHANGE `description_en` `description_en` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `description_de`,
            CHANGE `description_ru` `description_ru` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `description_en`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit_guarantee`
            CHANGE `price` `sum` decimal(10,2) NOT NULL AFTER `description_ru`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            CHANGE `status` `status` enum('AWAITING_PAYMENT','ACTIVE','COMPLETED') COLLATE 'utf8_general_ci' NOT NULL AFTER `token_deposit_guarantee_id`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            ADD `pay_in_request_id` bigint(20) NULL,
            ADD FOREIGN KEY (`pay_in_request_id`) REFERENCES `pay_in_request` (`id`);
        ");

        $this->execute("
            ALTER TABLE `pay_in_request`
            CHANGE `type` `type` enum('PAY_IN','PACKET','PACKET_VIP_PLUS','PAY_IN_TOKEN','PAY_IN_TOKEN_DEPOSIT') COLLATE 'utf8_general_ci' NOT NULL AFTER `confirm_status`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit_guarantee`
            ADD `status` enum('ACTIVE','DELETED') NOT NULL DEFAULT 'ACTIVE';
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            ADD `payout_type` enum('TOKENS','JUGLS') NULL DEFAULT 'TOKENS';
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            ADD INDEX `status_completion_dt` (`status`, `completion_dt`),
            DROP INDEX `completion_dt`;
        ");
    }

    public function down()
    {
        echo "m180813_122450_add_token_deposit_tables cannot be reverted.\n";

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
