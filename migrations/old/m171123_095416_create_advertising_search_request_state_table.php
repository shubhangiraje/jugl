<?php

use yii\db\Migration;

/**
 * Handles the creation of table `advertising_search_request_state`.
 */
class m171123_095416_create_advertising_search_request_state_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
         $this->execute("CREATE TABLE `advertising_search_request_state` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `search_request_id` bigint(20) NOT NULL,
		  `conversion_id` bigint(20) NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `provider_id` int(11) NOT NULL,
		  `campaign_name` varchar(255) NULL DEFAULT '',
		  `transactionType` varchar(255) NULL DEFAULT '',
		  `transactionStatus` varchar(255) NULL DEFAULT '',
		  `numTouchPointsTotal` int(11) NULL DEFAULT '0',
		  `numTouchPointsAttributed` int(11) NULL DEFAULT '0',
		  `attributableCommission` decimal(14,2) NULL DEFAULT '0',
		  `description` text NULL,
		  `currency` varchar(20) NULL DEFAULT '',
		  `commission` decimal(14,2) NULL DEFAULT '0',
		  `orderAmount` decimal(14,2) NULL DEFAULT '0',
		  `IP` varchar(255) NULL DEFAULT '',
		  `registrationDate` DATETIME,
		  `assessmentDate` DATETIME,
		  `clickToConversion` varchar(255) NULL DEFAULT '',
		  `originatingClickDate` DATETIME,
		  `rejectionReason` varchar(255) NULL DEFAULT '',
		  `paidOut` tinyint(1) NULL DEFAULT '0',
		  `countryCode` varchar(20) NULL DEFAULT '',
		  `attributionModel` varchar(255) NULL DEFAULT '',
          PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`),
		  CONSTRAINT `advertising_search_request_state_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
		  CONSTRAINT `advertising_search_request_state_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `advertising_search_request_provider ` (`provider_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('advertising_search_request_state');
    }
}
