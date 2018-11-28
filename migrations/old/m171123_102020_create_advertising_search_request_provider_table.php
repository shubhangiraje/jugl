<?php

use yii\db\Migration;

/**
 * Handles the creation of table `advertising_search_request_provider`.
 */
class m171123_102020_create_advertising_search_request_provider_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
         $this->execute("CREATE TABLE `advertising_search_request_provider` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `provider_id` int(11) NOT NULL,
		  `auth_token` varchar(32) NOT NULL,
          PRIMARY KEY (`id`),
		  KEY `provider_id` (`provider_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		
		$this->execute("
			INSERT INTO `advertising_search_request_provider` (`provider_id`, `auth_token`) VALUES ('1', 'Ibj8p9QHypHdyMiWvZJb');
			INSERT INTO `advertising_search_request_provider` (`provider_id`, `auth_token`) VALUES ('2', 'uy28oM4AlAcIaeSNRUhH');
		");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('advertising_search_request_provider');
    }
}
