<?php

use yii\db\Schema;
use yii\db\Migration;

class m150402_124525_log_message_can_be_empty extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `remote_log`
            CHANGE `message` `message` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `type`;
        ");
    }

    public function down()
    {
        echo "m150402_124525_log_message_can_be_empty cannot be reverted.\n";

        return false;
    }
}
