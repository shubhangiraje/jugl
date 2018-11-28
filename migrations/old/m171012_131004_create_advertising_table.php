<?php

use yii\db\Migration;

/**
 * Handles the creation of table `advertising`.
 */
class m171012_131004_create_advertising_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
         $this->execute("CREATE TABLE `advertising` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `advertising_name` varchar(255) NOT NULL,
		  `advertising_total_bonus` int(11) NOT NULL,
          `dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `status` tinyint(1) NULL DEFAULT '0',
          PRIMARY KEY (`id`)
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
