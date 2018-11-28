<?php

use yii\db\Schema;
use yii\db\Migration;

class m150330_110423_add_user_device_index extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_device`
            ADD INDEX `type_device_uuid` (`type`, `device_uuid`);
        ");
    }

    public function down()
    {
        echo "m150330_110423_add_user_device_index cannot be reverted.\n";

        return false;
    }
}
