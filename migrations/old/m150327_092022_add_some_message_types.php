<?php

use yii\db\Schema;
use yii\db\Migration;

class m150327_092022_add_some_message_types extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_message`
            CHANGE `content_type` `content_type` enum('TEXT','IMAGE','FILE','VIDEO','GEOLOCATION') COLLATE 'utf8_general_ci' NOT NULL AFTER `type`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150327_092022_add_some_message_types cannot be reverted.\n";

        return false;
    }
}
