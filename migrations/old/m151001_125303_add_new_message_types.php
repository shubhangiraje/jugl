<?php

use yii\db\Schema;
use yii\db\Migration;

class m151001_125303_add_new_message_types extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_message`
            CHANGE `content_type` `content_type` enum('TEXT','IMAGE','FILE','VIDEO','GEOLOCATION','AUDIO','CONTACT') COLLATE 'utf8_general_ci' NOT NULL AFTER `type`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151001_125303_add_new_message_types cannot be reverted.\n";

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
