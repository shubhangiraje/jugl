<?php

use yii\db\Migration;

/**
 * Handles the creation of table `advertising_user`.
 */
class m171012_123131_create_advertising_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
         $this->execute("CREATE TABLE `advertising_user` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `advertising_id` int(11) NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `status` tinyint(1) NULL DEFAULT '0',
          PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`),
		  KEY `advertising_id` (`advertising_id`),
		  CONSTRAINT `advertising_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m171012_123254_create_advertising_table cannot be reverted.\n";

        return false;
    }
}
