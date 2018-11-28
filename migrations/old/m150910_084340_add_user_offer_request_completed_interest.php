<?php

use yii\db\Schema;
use yii\db\Migration;

class m150910_084340_add_user_offer_request_completed_interest extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_offer_request_completed_interest` (
              `user_id` bigint(20) NOT NULL,
              `interest_id` int(11) NOT NULL,
              PRIMARY KEY (`user_id`,`interest_id`),
              KEY `interest_id` (`interest_id`),
              CONSTRAINT `user_offer_request_completed_interest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_offer_request_completed_interest_ibfk_2` FOREIGN KEY (`interest_id`) REFERENCES `interest` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        \app\models\User::updateUserOfferRequestCompletedInterest();
    }

    public function down()
    {
        echo "m150910_084340_add_user_offer_request_completed_interest cannot be reverted.\n";

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
