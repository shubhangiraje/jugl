<?php

use yii\db\Migration;

class m170209_130658_add_offer_search_request_id_to_spam_report extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_spam_report`
ADD `offer_id` bigint(20) NULL AFTER `object`,
ADD `search_request_id` bigint(20) NULL AFTER `offer_id`,
ADD FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`),
ADD FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`);
        ");
    }

    public function down()
    {
        echo "m170209_130658_add_offer_search_request_id_to_spam_report cannot be reverted.\n";

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
