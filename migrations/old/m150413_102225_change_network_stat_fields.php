<?php

use yii\db\Schema;
use yii\db\Migration;

class m150413_102225_change_network_stat_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `network_size` `network_size` int(11) NOT NULL DEFAULT '0' AFTER `validation_photo2_file_id`,
            CHANGE `network_levels` `network_levels` int(11) NOT NULL DEFAULT '0' AFTER `network_size`,
            COMMENT='';
        ");

        $this->execute("
          update user set network_levels=network_levels-1, network_size=network_size-1
        ");
    }

    public function down()
    {
        echo "m150413_102225_change_network_stat_fields cannot be reverted.\n";

        return false;
    }
}
