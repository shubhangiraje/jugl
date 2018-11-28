<?php

use yii\db\Migration;

class m171214_063815_advertising_interest_table extends Migration
{
    public function up()
    {
		
	$this->execute("ALTER TABLE `advertising`
					CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT FIRST;");
						
	$this->execute("CREATE TABLE `advertising_interest` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `advertising_id` bigint(20) NOT NULL,
					  `level1_interest_id` int(11) NOT NULL,
					  `level2_interest_id` int(11) DEFAULT NULL,
					  `level3_interest_id` int(11) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `level1_interest_id` (`level1_interest_id`),
					  KEY `level2_interest_id` (`level2_interest_id`),
					  KEY `level3_interest_id` (`level3_interest_id`),
					  KEY `advertising_id` (`advertising_id`),
					  CONSTRAINT `advertising_interest_ibfk_2` FOREIGN KEY (`level1_interest_id`) REFERENCES `interest` (`id`),
					  CONSTRAINT `advertising_interest_ibfk_3` FOREIGN KEY (`level2_interest_id`) REFERENCES `interest` (`id`),
					  CONSTRAINT `advertising_interest_ibfk_4` FOREIGN KEY (`level3_interest_id`) REFERENCES `interest` (`id`),
					  CONSTRAINT `advertising_interest_ibfk_5` FOREIGN KEY (`advertising_id`) REFERENCES `advertising` (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
	}
    public function down()
    {
        echo "m171214_063815_advertising_interest_table cannot be reverted.\n";

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
