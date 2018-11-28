<?php

use yii\db\Migration;

class m170523_105357_add_publish_deals_fields extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user`
ADD `publish_offer_wo_validation` tinyint(1) NOT NULL DEFAULT '0',
ADD `publish_search_request_wo_validation` tinyint(1) NOT NULL DEFAULT '0' AFTER `publish_offer_wo_validation`;
        ");
    }

    public function down()
    {
        echo "m170523_105357_add_publish_deals_fields cannot be reverted.\n";

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
