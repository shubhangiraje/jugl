<?php

use yii\db\Migration;

class m180515_131747_add_token_balance_tables extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `balance_token_log` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `type` enum('PAYIN','PAYOUT','IN','IN_REF','IN_REF_REF','IN_REG_REF','IN_REG_REF_REF','OUT') NOT NULL,
              `sum` decimal(17,5) NOT NULL,
              `sum_earned` decimal(17,5) NOT NULL,
              `sum_buyed` decimal(17,5) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              `initiator_user_id` bigint(20) NOT NULL,
              `comment` varchar(512) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `initiator_user_id` (`initiator_user_id`),
              KEY `user_id_dt` (`user_id`,`dt`),
              KEY `user_id_type` (`user_id`,`type`),
              KEY `user_id_sum` (`user_id`,`sum`),
              CONSTRAINT `balance_token_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `balance_token_log_ibfk_2` FOREIGN KEY (`initiator_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
        $this->execute("
            CREATE TABLE `balance_token_log_mod` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `admin_id` bigint(20) NOT NULL,
              `balance_token_log_id` bigint(20) NOT NULL,
              `comments` mediumtext NOT NULL,
              PRIMARY KEY (`id`),
              KEY `admin_id` (`admin_id`),
              KEY `balance_token_log_id` (`balance_token_log_id`),
              CONSTRAINT `balance_token_log_mod_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
              CONSTRAINT `balance_token_log_mod_ibfk_2` FOREIGN KEY (`balance_token_log_id`) REFERENCES `balance_token_log` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `balance_token` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance`,
            ADD `balance_token_buyed` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance_buyed`,
            ADD `balance_token_earned` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance_earned`,
            ADD `earned_token_total` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `earned_total`;
        ");

        $this->execute("
            CREATE TABLE `user_token_earned_by_date` (
              `user_id` bigint(20) NOT NULL,
              `dt` date NOT NULL,
              `sum` decimal(17,5) NOT NULL,
              PRIMARY KEY (`user_id`,`dt`),
              CONSTRAINT `user_token_earned_by_date_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TOKEN_DISTRIBUTION_PARENTS_PERCENT_TOKEN',	'Anteil zur Token-Gewinnausschüttung in Prozent an Jugl (Jugls)',	'float',	'7'),
            ('TOKEN_DISTRIBUTION_PARENTS_PERCENT_JUGL',	'Anteil zur Token-Gewinnausschüttung in Prozent nach oben im Netzwerk (Tokens)',	'float',	'3');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TOKEN_TO_JUGL_EXCHANGE_RATE',	'Token to Jugl exchange rate',	'float',	'10')
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TOKEN_TO_EURO_EXCHANGE_RATE',	'Euro to Token exchange rate',	'float',	'0.1')
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('TOKEN_MIN_BUY_QUANTITY',	'Token minimum buy quantity',	'float',	'1000')
        ");

        $this->execute("
            ALTER TABLE `pay_in_request`
            CHANGE `payment_method` `payment_method` enum('PAYONE_CC','PAYONE_ELV','PAYONE_PAYPAL','PAYONE_GIROPAY','PAYONE_SOFORT','ELV','JUGL') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            CHANGE `type` `type` enum('PAY_IN','PACKET','PACKET_VIP_PLUS','PAY_IN_TOKEN') COLLATE 'utf8_general_ci' NOT NULL AFTER `confirm_status`;
        ");
    }

    public function down()
    {
        echo "m180515_131747_add_token_balance_token_tables cannot be reverted.\n";

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
