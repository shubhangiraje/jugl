<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_113421_deleted_users_avatar extends Migration
{
    public function up()
    {
        $this->execute("update user set avatar_file_id=1 where status='DELETED'");
    }

    public function down()
    {
        echo "m151116_113421_deleted_users_avatar cannot be reverted.\n";

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
