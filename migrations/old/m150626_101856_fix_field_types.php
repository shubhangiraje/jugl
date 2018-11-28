<?php

use yii\db\Schema;
use yii\db\Migration;

class m150626_101856_fix_field_types extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_offer`
            DROP FOREIGN KEY `search_request_offer_ibfk_2`;

            ALTER TABLE `search_request_offer`
            CHANGE `user_id` `user_id` bigint(20) NOT NULL AFTER `search_request_id`,
            ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
            COMMENT='';

            ALTER TABLE `user_device`
            CHANGE `id` `id` bigint NOT NULL AUTO_INCREMENT FIRST,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150626_101856_fix_field_types cannot be reverted.\n";

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
