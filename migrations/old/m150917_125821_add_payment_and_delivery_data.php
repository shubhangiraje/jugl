<?php

use yii\db\Schema;
use yii\db\Migration;

class m150917_125821_add_payment_and_delivery_data extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_bank_data` (
              `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` bigint(20) NOT NULL,
              `bic` varchar(256) NULL,
              `iban` varchar(256) NULL,
              `owner` varchar(256) NULL,
              `sort_order` int NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `paypal_email` varchar(128) NULL AFTER `validation_photo2_file_id`,
            COMMENT='';
        ");

        $this->execute("
            CREATE TABLE `user_delivery_address` (
              `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` bigint(20) NOT NULL,
              `street` varchar(128) NULL,
              `house_number` varchar(128) NULL,
              `city` varchar(128) NULL,
              `zip` varchar(128) NULL,
              `sort_order` int NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150917_125821_add_payment_data cannot be reverted.\n";

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
